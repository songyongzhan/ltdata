<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/12/28
 * Time: 15:54
 * Email: 574482856@qq.com
 *
 * 数据分析 模型
 */
defined('APP_PATH') OR exit('No direct script access allowed');

class ExportdataModel extends BaseModel {

  //protected $output_time_format = 'Y-m-d';
  public function getExportDataList($where = [], $fileds = [], $pageNum = 1, $pageSize = PAGESIZE, $order = '', $table = NULL) {

    $result = $this->getListPage($where, $fileds, $pageNum, $pageSize, $order, $table);

    foreach ($result['list'] as $key => &$value) {
      $value['export_ciq'] = $this->getCiq($value['export_ciq']);
      $value['dist_country'] = $this->getCountry($value['dist_country']);
      $value['trade_mode'] = $this->getTrade($value['trade_mode']);
      $value['transport_mode'] = $this->getTransport($value['transport_mode']);
      $value['madein'] = $this->getMade($value['madein']);
    }

    return $result;
  }

  public function getExportOne($where, $fileds = []) {
    $result = $this->getOne($where, $fileds);
    $result['export_ciq'] = $this->getCiq($result['export_ciq']);
    $result['dist_country'] = $this->getCountry($result['dist_country']);
    $result['trade_mode'] = $this->getTrade($result['trade_mode']);
    $result['transport_mode'] = $this->getTransport($result['transport_mode']);
    $result['madein'] = $this->getMade($result['madein']);
    $result['export_date'] = date($this->output_time_format, $result['export_date']);
    return $result;
  }


  public function getCiq($id) {
    $result = $this->redisModel->redis->hGet('ciq', $id);
    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'ciq');
      if ($data) {
        $this->redisModel->redis->hSet('ciq', $id, $data['title']);
        $result = $data['title'];
      }
    }
    return $result ?: $id;
  }


  public function getCountry($id) {
    $result = $this->redisModel->redis->hGet('country', $id);
    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'country');

      if ($data) {
        $this->redisModel->redis->hSet('country', $id, $data['title']);
        $result = $data['title'];
      }
    }
    return $result ?: $id;
  }


  public function getTrade($id) {

    $result = $this->redisModel->redis->hGet('trade', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'trade');

      if ($data) {
        $this->redisModel->redis->hSet('trade', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: $id;
  }

  public function getTransport($id) {

    $result = $this->redisModel->redis->hGet('transport', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'transport');

      if ($data) {
        $this->redisModel->redis->hSet('transport', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: $id;
  }


  public function getMade($id) {

    $result = $this->redisModel->redis->hGet('made', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'made');

      if ($data) {
        $this->redisModel->redis->hSet('made', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: $id;
  }


  /**
   * 根据相关搜索 检索到对应数据
   * @param $where
   * @param $field
   * @param $groupBy
   * @param $orderBy
   * @param null $limit
   */
  public function getReportData($where, $field, $groupBy, $orderBy, $limit = NULL) {


    foreach ($groupBy as $groupByVal) {
      $this->_db->groupBy($groupByVal);
    }


    foreach ($orderBy as $orderField => $orderSortVal) {
      $this->_db->orderBy($orderField, $orderSortVal);
    }

    $result = [];

    return $result;

  }


}
