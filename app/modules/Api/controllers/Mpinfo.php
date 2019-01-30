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

  /**
   * 获取名片列表
   * @return mixed
   */
  public function getListAction() {
    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);
    $qylx_id = $this->_post('qylx_id', '');
    $wn_np_type = $this->_post('wn_np_type', '');
    $mppinpaiId = $this->_post('mppinpaiId', '');
    $sheng_id = $this->_post('sheng_id', '');
    $shi_id = $this->_post('shi_id', '');
    $person = $this->_post('person', '');
    $title = $this->_post('title', '');
    $rules = [
      ['condition' => 'like',
        'key_field' => ['title', 'person'],
        'db_field' => ['title', 'person']
      ],
      [
        'condition' => '=',
        'key_field' => ['qylx_id', 'wn_np_type', 'mppinpaiId', 'sheng_id', 'shi_id'],
        'db_field' => ['qylx_id', 'wn_np_type', 'mppinpaiId', 'sheng_id', 'shi_id'],
      ]
    ];
    $data = [
      'qylx_id' => $qylx_id,
      'wn_np_type' => $wn_np_type,
      'mppinpaiId' => $mppinpaiId,
      'sheng_id' => $sheng_id,
      'shi_id' => $shi_id,
      'person' => $person,
      'title' => $title
    ];
    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));
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


  public function import2dbAction() {

    Yaf_Loader::import(APP_PATH . '/app/helpers/helperCsv.php');

    foreach (glob(APP_PATH . '/data/uploads/mpinfo/*.csv') as $file) {

      debugMessage(" $file 开始自动导入...");
      try {
        $csv = new helperCsv($file, 0, FALSE);
        $csv->addFilter('\""', ' ');
        $multi_time = time();

        //$importFlag = TRUE;
        $multiData = [];
        foreach ($csv as $row => $data) {
          if (!$data) continue;

          $this->format($data);

          $data['updatetime'] = time();
          $data['createtime'] = time();
          $data['multidata'] = $multi_time;
          $multiData[] = $data;

          if (count($multiData) >= 1000) {
            $result = $this->cliExportdataModel->importMulti($multiData);
            debugMessage("$file 插入一次...:" . count($multiData));
            if (!$result) {
              debugMessage("$file 导入出错...");
              $this->cliExportdataModel->delMulti($multi_time);
              break;
            }
            $multiData = [];
          }
        }
        //执行退出后，如果multiData 还有数据，则再次添加
        if (count($multiData) > 0) {
          $result = $this->cliExportdataModel->importMulti($multiData);
          debugMessage("$file 最后一次...:" . count($multiData));
          if (!$result) {
            debugMessage("$file 导入出错...");
            $this->cliExportdataModel->delMulti($multi_time);
            break;
          }
        }

        $this->mvFiletoDist($file);

      } catch (Exception $e) {

        debugMessage('Cli import error' . $e->getMessage() . ' code ' . $e->getCode() . var_export($e->getTrace(), TRUE));
        $this->cliExportdataModel->delMulti($multi_time);
      }
    }

    die();

  }


  /**
   * 获取post中数据
   * @return array
   */
  private function _getData() {

    return [
      'qylx_id' => $this->_post('qylx_id'),
      'wn_np_type' => $this->_post('wn_np_type'),
      'mppinpaiId' => $this->_post('mppinpaiId'),
      'sheng_id' => $this->_post('sheng_id'),
      'shi_id' => $this->_post('shi_id'),
      'person' => $this->_post('person'),
      'title' => $this->_post('title'),
      'jypp' => $this->_post('jypp'),
      'tel' => $this->_post('tel'),
      'mobile' => $this->_post('mobile'),
      'email' => $this->_post('email'),
      'weburl' => $this->_post('weburl'),
      'indexshow' => $this->_post('indexshow'),
      'gongkai' => $this->_post('isfufei'),
      'allshow' => $this->_post('allshow'),
      'gongkai' => $this->_post('gongkai')
    ];
  }


}