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

class MpinfoController extends ApiBaseController {

  const IMPORT_FIELD = ['wn_np_type', 'sheng_id', 'shi', 'title', 'legal_person', 'partner', 'address', 'person', 'tel', 'mobile', 'contract_year', 'manufacturer', 'mppinpaiId', 'sell_area', 'year_sell', 'verification_date', 'contract'];

  /**
   * 获取名片列表
   * @return mixed
   */
  public function getListAction() {
    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);
    $where = $this->_where();
    $result = $this->mpinfoService->getListPage($where, $page_num, $page_size);
    return $result;
  }

  /**
   * 添加名片
   * @param string $title <POST> 名称
   * @return array
   */
  public function addAction() {
    $data = $this->_getData();
    $result = $this->mpinfoService->add($data);
    return $result;
  }

  /**
   * 更新名片
   * @param string $title <POST> 名称
   * @param string $id <POST> id
   * @return array
   */
  public function updateAction() {
    $id = $this->_post('id');
    $data = $this->_getData();
    $result = $this->mpinfoService->update($id, $data);
    return $result;
  }

  /**
   * 得到一个名片信息
   * @param int $id <POST> 用户id
   * @return array|mixed
   */
  public function getOneAction() {
    $id = $this->_post('id');
    $result = $this->mpinfoService->getOne($id);
    return $result;
  }

  /**
   * 名片删除
   * @param string $id <POST> 数据id ，如果删除多个，请使用逗号分隔
   * @return 删除数据的id
   */
  public function deleteAction() {
    $id = $this->_post('id');
    $result = $this->mpinfoService->delete($id);
    return $result;
  }

  /**
   * 获取初始数据 用于选择
   */
  public function getSelDataAction() {

    $result = $this->mpinfoService->getSelData();

    return $result;
  }

  /**
   * 文件上传，需要进行授权才可以上传
   * @return array
   */
  public function uploadAction() {
    $file = new File($_FILES['uploadFile']['tmp_name']);

    $file->rule('');
    $result = $file->setUploadInfo($_FILES['uploadFile'])->validate(['size' => 26214400, 'ext' => 'csv'])->move(APP_PATH . DS . 'data/uploads/mpinfo', '');

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
    $report_id = $this->_post('report_id');
    $date_type = $this->_post('date_type', '');
    $this->mpinfoService->downloadCsv($where, $date_type, $report_id);
  }


  /**
   * 获取代理商名片中省份
   */
  public function getShengAction() {
    $result = $this->mpinfoService->getSheng();
    return $result;
  }

  /**
   * 获取生产企业列表
   */
  public function getManufacturerAction() {
    $result = $this->mpinfoService->getManufacturer();
    return $result;
  }

  /**
   * 获取所有代理商
   * @return array
   */
  public function getTitleAction() {
    $result = $this->mpinfoService->getTitle();
    return $result;
  }


  /**
   * 生成图表文件
   * @return array
   */
  public function reportAction() {
    $where = $this->_where();
    $report_id = $this->_post('report_id');
    $date_type = $this->_post('date_type', '');
    $result = $this->mpinfoService->getReportData($where, $date_type, $report_id);
    return $result;
  }

  /**
   * 自动入库
   * HTTP_ENV=develop php index.php api/mpinfo/import2db
   */
  public function import2dbAction() {

    Yaf_Loader::import(APP_PATH . '/app/helpers/helperCsv.php');


    foreach (glob(APP_PATH . '/data/uploads/mpinfo/*.csv') as $file) {

      debugMessage(" $file 开始自动导入...");
      $tmpTable = $this->mpinfoService->cloneTmpTable();
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
            $result = $this->mpinfoModel->inserMulti($multiData, $tmpTable);
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
          $result = $this->mpinfoModel->inserMulti($multiData, $tmpTable);
          debugMessage("$file 最后一次...:" . count($multiData));
          if (!$result) {
            debugMessage("$file 导入出错...");
            break;
          }
        }

        //临时表数据 移动到 mpinfo 表
        $this->mpinfoService->copyData($tmpTable, 'mpinfo');

        $this->mvFiletoDist($file);

      } catch (Exception $e) {
        debugMessage('Cli import error' . $e->getMessage() . ' code ' . $e->getCode() . var_export($e->getTrace(), TRUE));
      }
    }

    die();
  }

  private function format(&$data) {
    $data = convert_encodeing($data, 'gbk', 'utf-8');

    $data = array_combine(self::IMPORT_FIELD, array_slice($data, 0, 17));

    $data['wn_np_type'] = $this->mpinfoService->wn_np_type($data['wn_np_type']);
    $data['sheng_id'] = $this->mpinfoService->sheng_id($data['sheng_id']);
    $data['mppinpaiId'] = $this->mpinfoService->mppinpaiId($data['mppinpaiId']);
    //$data['shi_id'] = $this->mpinfoService->shi_id($data['shi_id']);
    $data['contract'] = trim($data['contract']) == '是' ? 1 : 0;
    $data['updatetime'] = time();
    $data['createtime'] = time();
    $data['status'] = 1;
  }

  private function mvFiletoDist($file) {
    $distPath = APP_PATH . '/data/uploads/mpinfo/dist/';

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

  /**
   * 获取post中数据
   * @return array
   */
  private function _getData() {

    return [
      'wn_np_type' => $this->_post('wn_np_type'),
      'mppinpaiId' => $this->_post('mppinpaiId'),
      'sheng_id' => $this->_post('sheng_id'),
      'shi' => $this->_post('shi'),
      'person' => $this->_post('person'),
      'title' => $this->_post('title'),
      'legal_person' => $this->_post('legal_person'),
      'tel' => $this->_post('tel'),
      'mobile' => $this->_post('mobile'),
      'partner' => $this->_post('partner'),
      'address' => $this->_post('address'),
      'contract_year' => $this->_post('contract_year'),
      'manufacturer' => $this->_post('manufacturer'),
      'sell_area' => $this->_post('sell_area'),
      'verification_date' => $this->_post('verification_date'),
      'contract' => $this->_post('contract'),
      'year_sell' => $this->_post('year_sell')
    ];
  }

  private function _where() {
    $wn_np_type = $this->_post('wn_np_type', '');
    $mppinpaiId = $this->_post('mppinpaiId', '');
    $sheng_id = $this->_post('sheng_id', '');
    $shi = $this->_post('shi', '');
    $person = $this->_post('person', '');
    $title = $this->_post('title', '');
    $contract_year = $this->_post('contract_year', '');
    $manufacturer = $this->_post('manufacturer', '');
    $rules = [
      ['condition' => 'like',
        'key_field' => ['title', 'person'],
        'db_field' => ['title', 'person']
      ],
      [
        'condition' => '=',
        'key_field' => ['wn_np_type', 'manufacturer', 'mppinpaiId', 'sheng_id', 'shi', 'contract_year'],
        'db_field' => ['wn_np_type', 'manufacturer', 'mppinpaiId', 'sheng_id', 'shi', 'contract_year'],
      ]
    ];
    $data = [
      'wn_np_type' => $wn_np_type,
      'mppinpaiId' => $mppinpaiId,
      'sheng_id' => $sheng_id,
      'shi' => $shi,
      'manufacturer' => $manufacturer,
      'contract_year' => $contract_year,
      'person' => $person,
      'title' => $title
    ];
    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));
    return $where;
  }


}