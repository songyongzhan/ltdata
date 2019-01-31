<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/12/28
 * Time: 11:45
 * Email: 574482856@qq.com
 *
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class PcrdataController extends ApiBaseController {

  const IMPORT_FIELD = ['export_date', 'city', 'brand', 'specification', 'huawen', 'grade', 'pf_pricle', 'stls_pricle', 'th_pricle', 'jd_pricle', 'gfqj_pricle'];


  /**
   * 获取名片列表
   * @return mixed
   */
  public function getListAction() {
    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);
    $where = $this->_where();
    $result = $this->pcrdataService->getListPage($where, $page_num, $page_size);
    return $result;
  }


  /**
   * 文件上传，需要进行授权才可以上传
   * @return array
   */
  public function uploadAction() {
    $file = new File($_FILES['uploadFile']['tmp_name']);

    $file->rule('');
    $result = $file->setUploadInfo($_FILES['uploadFile'])->validate(['size' => 26214400, 'ext' => 'csv'])->move(APP_PATH . DS . 'data/uploads/pcrdata', '');

    if ($result) {
      $data = [
        'path' => $result->getSaveName(),
        'extension' => $result->getExtension(),
        'filename' => $result->getFilename()
      ];
    } else
      $data = ['errMsg' => $result->getError()];

    return $this->baseService->show($data, $data ? API_SUCCESS : API_FAILURE);
  }

  /**
   * 下载代理商名录列表
   * @throws InvalideException
   */
  public function downloadAction() {
    $where = $this->_where();
    $result = $this->pcrdataService->downloadCsv($where);
    return $result;
  }

  /**
   * 自动入库
   */
  public function import2dbAction() {

    Yaf_Loader::import(APP_PATH . '/app/helpers/helperCsv.php');


    foreach (glob(APP_PATH . '/data/uploads/pcrdata/*.csv') as $file) {

      debugMessage(" $file 开始自动导入...");
      $tmpTable = $this->pcrdataService->cloneTmpTable();
      debugMessage(" 创建临时表..." . $tmpTable);

      try {
        $csv = new helperCsv($file, 0, FALSE);
        //$importFlag = TRUE;
        $multiData = [];
        foreach ($csv as $row => $data) {
          if (!$data) continue;

          $this->format($data);

          $multiData[] = $data;

          if (count($multiData) >= 1000) {
            $result = $this->pcrdataModel->inserMulti($multiData, $tmpTable);
            debugMessage("$file 插入一次...:" . count($multiData));
            if (!$result) {
              debugMessage("$file 导入出错...");
              break;
            }
            $multiData = [];
          }
        }
        //执行退出后，如果multiData 还有数据，则再次添加
        if (count($multiData) > 0) {
          $result = $this->pcrdataModel->inserMulti($multiData, $tmpTable);
          debugMessage("$file 最后一次...:" . count($multiData));
          if (!$result) {
            debugMessage("$file 导入出错...");
            break;
          }
        }

        //临时表数据 移动到 mpinfo 表
        $this->pcrdataService->copyData($tmpTable, 'pcrdata');

        $this->mvFiletoDist($file);

      } catch (Exception $e) {
        echo $e->getCode() . $e->getMessage();
        debugMessage('Cli import error' . $e->getMessage() . ' code ' . $e->getCode() . var_export($e->getTrace(), TRUE));
      }
    }

    die();
  }

  private function format(&$data) {
    $data = convert_encodeing($data, 'gbk', 'utf-8');

    $data = array_combine(self::IMPORT_FIELD, array_slice($data, 0, 11));

    $data['export_date'] = strtotime(str_replace('/', '-', $data['export_date']));
    $data['export_year'] = date('Y', $data['export_date']);
    $data['export_month'] = date('m', $data['export_date']);

    $data['city'] = $this->pcrdataService->sheng_id($data['city']);
    $data['brand'] = $this->pcrdataService->mppinpaiId($data['brand']);

    $data['status'] = 1;

    $data['updatetime'] = time();
    $data['createtime'] = time();

  }

  private function mvFiletoDist($file) {
    $distPath = APP_PATH . '/data/uploads/pcrdata/dist/';

    if (!is_dir($distPath))
      mkdir($distPath, 0755, TRUE);

    if (!is_writeable($distPath))
      debugMessage(sprintf('移动文件失败  ===目录 %s 不可写', $distPath));
    else {
      copy($file, $distPath . basename($file)); //拷贝到新目录
      //删除旧目录下的文件
      if (!unlink($file))
        debugMessage(sprintf('%s  所在目录不可操作 ', $file));
    }
  }

  private function _where() {
    $export_date = $this->_post('export_date', '');
    $city = $this->_post('city', '');
    $brand = $this->_post('brand', '');
    $specification = $this->_post('specification', '');

    $start_date = $export_date ? strtotime($export_date) : '';
    $end_date = $export_date ? (strtotime('+1 month', strtotime($export_date)) - 1) : '';


    $rules = [
      ['condition' => 'like',
        'key_field' => ['title', 'person'],
        'db_field' => ['title', 'person']
      ],
      ['condition' => 'between',
        'key_field' => ['start_date', 'end_date'],
        'db_field' => ['export_date', 'export_date']
      ],
      [
        'condition' => '=',
        'key_field' => ['city', 'brand', 'specification'],
        'db_field' => ['city', 'brand', 'specification'],
      ]
    ];
    $data = [
      'city' => $city,
      'brand' => $brand,
      'specification' => $specification,
      'start_date' => $start_date,
      'end_date' => $end_date

    ];
    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));
    return $where;
  }


}