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
 * 名片 操作服务类
 * Class TransportService
 */
class MpinfoService extends BaseService {

  protected $field = ['id','wn_np_type', 'sheng_id', 'shi', 'title', 'legal_person', 'partner', 'address', 'person', 'tel', 'mobile', 'contract_year', 'manufacturer', 'mppinpaiId', 'sell_area', 'year_sell', 'verification_date', 'contract','createtime'];

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getListPage(array $where, $page_num, $page_size) {
    $result = $this->mpinfoModel->getListPage($where, $this->field, $page_num, $page_size);
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

    $result = $this->mpinfoModel->getList($where, $field);
    foreach ($result as $key => &$value) {
      $this->_format($value);
    }
    $csvDatas = $result;

    $csvHeader = ['公司名称', '企业类型', '所属品类', '所属品牌', '省', '市', '联系人', '经营品牌', '手机', '电话', '网址', '邮箱', '是否首页显示', '是否公开', '名片形式展示', '是否付费'];

    $header = [];
    $footer = [];
    export_csv(['header' => $header, 'title' => $csvHeader, 'data' => $csvDatas, 'footer' => $footer], '代理商名录_' . time());
  }

  /**
   * 添加
   * @param int $wn_np_type <require|number> 类别不能为空
   * @param int $mppinpaiId <require|number> 品牌不能为空
   * @param int $sheng_id <require|number> 省份不能为空
   * @param int $shi_id <require|number> 所在城市不能为空
   * @param int $qylx_id <require|number> 企业类型不能为空
   * @param string $title <require> 企业名称不能为空
   * @return mixed 返回最后插入的id
   */

  public function add($data) {
    $lastInsertId = $this->mpinfoModel->insert($data);
    if ($lastInsertId) {
      $data['id'] = $lastInsertId;
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }

  }

  /**
   * 删除一个
   * @param int $id <require|number> id
   */
  public function delete($id) {
    $result = $this->mpinfoModel->delete($id);
    return $result > 0 ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 获取单个信息
   * @param int $id <require|number> id
   * @param string $fileds
   * @return mixed
   */
  public function getOne($id, $fileds = '*') {
    $fileds === '*' && $fileds = $this->field;
    $result = $this->mpinfoModel->getOne($id, $fileds);
    return $result ? $this->show($result) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 分组更新数据
   * @param int $id <require|number> id
   * @param int $wn_np_type <require|number> 类别不能为空
   * @param int $mppinpaiId <require|number> 品牌不能为空
   * @param int $sheng_id <require|number> 省份不能为空
   * @param int $shi_id <require|number> 所在城市不能为空
   * @param int $qylx_id <require|number> 企业类型不能为空
   * @param string $title <require> 企业名称不能为空
   * @return array mixed 返回用户数据
   */

  public function update($id, $data) {
    $result = $this->mpinfoModel->update($id, $data);
    if ($result) {
      $data['id'] = $id;
    }
    return $result ? $this->show($data) : $this->show([]);
  }


  /**
   * 转换品类 转换数据
   * @param $qylx_id
   * @return int
   */
  public function qylx_id($qylx_id) {
    $id = 0;
    switch (trim($qylx_id)) {
      case '厂商':
        $id = 49;
        break;
      case '代理商':
        $id = 50;
        break;
    }

    return $id;
  }

  public function wn_np_type($wn_np_type) {
    $id = 0;
    switch (trim($wn_np_type)) {

      case '轿车轮胎':
        $id = 45;
        break;
      case '卡客车轮胎':
        $id = 46;
        break;
    }

    return $id;
  }

  public function sheng_id($sheng_id) {
    static $shengData;
    $sheng_id = str_replace('省', '', trim($sheng_id));
    if (!$shengData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', 1)
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

  public function shi_id($shi_id) {
    static $shiData;
    $shi_id = str_replace('市', '', trim($shi_id));
    if (!$shiData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', 2)
      ]);
      $shiData = array_column($data['result'], 'id', 'text');
    }

    if (isset($shiData[$shi_id]))
      return $shiData[$shi_id];
    else
      return '';
  }

  /**
   * 创建临时表
   * @param string $srcTable
   * @return bool
   */
  public function cloneTmpTable($srcTable = '') {
    return $this->mpinfoModel->cloneTmpTable($srcTable);
  }

  /**
   * 复制表到另外一张表 确保数据格式一致性
   * @param $tmpTable
   * @param string $distTable
   * @return array|bool
   */
  public function copyData($tmpTable, $distTable = '') {
    return $this->mpinfoModel->copyData($tmpTable, $distTable);
  }


  private function _format(&$value) {

  /*  static $shiData;
    if (!$shiData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', 2)
      ]);
      $shiData = array_column($data['result'], 'text', 'id');
    }

    isset($shiData[$value['shi']]) && $value['shi'] = $shiData[$value['shi']];*/


    static $pinpaiData;
    if (!$pinpaiData) {
      $data = $this->mppinpaiService->getList();
      if (isset($data['result']['mppinpai'])) {
        $pinpaiData = array_column($data['result']['mppinpai'], 'text', 'id');
      }
    }

    isset($pinpaiData[$value['mppinpaiId']]) && $value['mppinpaiId'] = $pinpaiData[$value['mppinpaiId']];


    static $shengData;
    if (!$shengData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', 1)
      ]);
      $shengData = array_column($data['result'], 'text', 'id');
    }

    isset($shengData[$value['sheng_id']]) && $value['sheng_id'] = $shengData[$value['sheng_id']];

    //switch (trim($value['qylx_id'])) {
    //  case 49:
    //    $value['qylx_id'] = '厂商';
    //    break;
    //  case 50:
    //    $value['qylx_id'] = '代理商';
    //    break;
    //}

    switch (trim($value['wn_np_type'])) {
      case 45:
        $value['wn_np_type'] = '轿车轮胎';
        break;
      case 46:
        $value['wn_np_type'] = '载重卡客车轮胎';
        break;
    }

  }

}