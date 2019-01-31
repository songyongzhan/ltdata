<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

/**
 * 轮胎价格 操作服务类
 * Class TransportService
 */
class PcrdataService extends BaseService {

  protected $field = ['id', 'export_date', 'city', 'brand', 'specification', 'huawen', 'grade', 'pf_pricle', 'stls_pricle', 'th_pricle', 'jd_pricle', 'gfqj_pricle'];

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getListPage(array $where, $page_num, $page_size) {
    $result = $this->pcrdataModel->getListPage($where, $this->field, $page_num, $page_size);
    foreach ($result['list'] as $key => &$value) {
      $this->_format($value);
    }
    return $this->show($result);
  }

  /**
   * 下载代理商列表
   * @param $where
   * @throws InvalideException
   */
  public function downloadCsv($where) {
    set_time_limit(0);
    ini_set('memory_limit', '1000M');

    $field = ['title', 'qylx_id', 'wn_np_type', 'mppinpaiId', 'sheng_id', 'shi_id', 'person', 'jypp', 'mobile', 'tel', 'weburl', 'email', 'indexshow', 'gongkai', 'allshow', 'isfufei'];;

    $result = $this->pcrdataModel->getList($where, $field);
    foreach ($result as $key => &$value) {
      $this->_format($value);
    }
    $csvDatas = $result;

    $csvHeader = ['公司名称', '企业类型', '所属品类', '所属品牌', '省', '市', '联系人', '经营品牌', '手机', '电话', '网址', '邮箱', '是否首页显示', '是否公开', '名片形式展示', '是否付费'];

    $header = [];
    $footer = [];
    export_csv(['header' => $header, 'title' => $csvHeader, 'data' => $csvDatas, 'footer' => $footer], '代理商名录_' . time());
  }


  public function sheng_id($sheng_id) {
    static $shengData;
    $sheng_id = trim($sheng_id);
    if (!$shengData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', [1, 2], 'in')
      ]);
      $shengData = array_column($data['result'], 'id', 'text');
    }

    if (isset($shengData[$sheng_id]))
      return $shengData[$sheng_id];
    else
      return '';
  }

  public function mppinpaiId($mppinpaiId) {
    static $pinpaiData;
    $mppinpaiId = trim($mppinpaiId);
    if (!$pinpaiData) {
      $data = $this->mppinpaiService->getList();
      if (isset($data['result']['mppinpai'])) {
        $pinpaiData = array_column($data['result']['mppinpai'], 'id', 'text');
      }
    }

    if (isset($pinpaiData[$mppinpaiId]))
      return $pinpaiData[$mppinpaiId];
    else
      return '';
  }


  /**
   * 创建临时表
   * @param string $srcTable
   * @return bool
   */
  public function cloneTmpTable($srcTable = '') {
    return $this->pcrdataModel->cloneTmpTable($srcTable);
  }

  /**
   * 复制表到另外一张表 确保数据格式一致性
   * @param $tmpTable
   * @param string $distTable
   * @return array|bool
   */
  public function copyData($tmpTable, $distTable = '') {
    return $this->pcrdataModel->copyData($tmpTable, $distTable);
  }


  private function _format(&$value) {

    static $pinpaiData;
    if (!$pinpaiData) {
      $data = $this->mppinpaiService->getList();
      if (isset($data['result']['mppinpai'])) {
        $pinpaiData = array_column($data['result']['mppinpai'], 'text', 'id');
      }
    }

    isset($pinpaiData[$value['brand']]) && $value['brand'] = $pinpaiData[$value['brand']];


    static $shengData;
    if (!$shengData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', [1, 2], 'in')
      ]);
      $shengData = array_column($data['result'], 'text', 'id');
    }

    isset($shengData[$value['city']]) && $value['city'] = $shengData[$value['city']];

  }

}