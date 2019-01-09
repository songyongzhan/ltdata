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
   * 获取view中的数据
   * @param $viewName
   * @param bool $prefix
   */
  public function getViewData($viewName) {
    $result = $this->_db->get($viewName, NULL);
    $this->_logSql();
    return $result;
  }


  /**
   * 根据相关搜索 检索到对应数据
   * @param $where
   * @param $field
   * @param $groupBy
   * @param $orderBy
   * @param null $limit
   */
  public function getReportData($where, $field, $groupBy = [], $having = [], $orderBy = [], $limit = NULL) {

    $this->setCond($where);

    foreach ($groupBy as $groupByVal) {
      $this->_db->groupBy($groupByVal);
    }

    foreach ($orderBy as $orderField => $orderSortVal) {
      $this->_db->orderBy($orderField, $orderSortVal);
    }

    // $havingVal ==> ['field' => '', 'val' =>'','operator' => '=','cond' => 'and']
    foreach ($having as $key => $havingVal) {
      if (!isset($havingVal['field']) || !isset($havingVal['val']))
        continue;

      $this->_db->having($havingVal['field'], $havingVal['val'], isset($havingVal['operator']) ? $havingVal['operator'] : '=', isset($havingVal['cond']) ? $havingVal['cond'] : 'and');
    }

    $result = $this->_db->get($this->table, NULL, $field);
    $this->_logSql();


    if (is_numeric($limit) && $limit > 0)
      $rowNum = $limit;
    else
      $rowNum = count($result);

    $data = [
      'sum_val' => array_sum(array_column($result, 'val')),
      'list' => array_slice($result, 0, $rowNum)
    ];

    return $data;
  }

  /**
   * @param $where 搜索条件
   * @param $report_id reportlist 中的一个id
   * @param null $date_type 1 年月  2年
   * @return array
   * @throws Exception
   */
  public function getReportDataByReportlist($where, $report_id, $date_type = NULL) {

    $reportRules = $this->reportlistModel->getOne($report_id);

    if (!$reportRules)
      showApiException('reportlist data 不存在', StatusCode::REPORTLIST_NOT_EXISTS);

    $orderBy = [];
    if ($reportRules['order_str']) {
      foreach (explode('|', $reportRules['order_str']) as $val) {
        $_temp = explode(',', $val);
        $orderBy[$_temp[0]] = isset($_temp[1]) ? $_temp[1] : 'asc';
      }
    }

    if (is_null($date_type) || $date_type == '')
      $date_type = $reportRules['date_type'];

    //按照占比显示
    $area = [5, 6, 9, 10, 12, 13, 14, 15];
    if (in_array($report_id, $area)) {
      //判断是否存在跨年 ，如果是 不管类型选择什么，都是2 按年统计
      $yearData = array_column($where, 'field');

      if (in_array('export_date', $yearData)) {

        $start_time = 0;
        $end_time = 0;
        foreach ($where as $val) {
          if ($val['field'] == 'export_date' && $val['operator'] == '>=')
            $start_time = $val['val'];

          if ($val['field'] == 'export_date' && $val['operator'] == '<=')
            $end_time = $val['val'];
        }

        if (date('Y', $start_time) != date('Y', $end_time))
          $date_type = 2;
      }
    }

    $groupBy = [];
    if ($reportRules['group_str']) {

      $group_str = (explode('|', $reportRules['group_str']))[$date_type - 1];
      foreach (explode(',', $group_str) as $val) {
        $groupBy[] = $val;
      }
    }

    $having = [];
    if ($reportRules['having_str']) {
      foreach (explode('|', $reportRules['having_str']) as $val) {
        $_temp = explode(',', $val);
        if (count($_temp) < 2)
          continue;

        $having[] = [
          'field' => $_temp[0],
          'val' => $_temp[1],
          'operator' => isset($_temp[2]) ? $_temp[2] : '=',
          'cond' => isset($_temp[3]) ? $_temp[3] : 'and'
        ];
      }
    }

    $field = [];
    if ($reportRules['field_str'] === '')
      debugMessage('reportList field_str 字段为空');
    else {
      foreach (explode(',', $reportRules['field_str']) as $val) {
        if ($date_type == 2 && $val == 'export_month')
          continue;

        $field[] = $val;
      }
    }

    $limit = $reportRules['limit_str'];

    $reportData = $this->getReportData($where, $field, $groupBy, $having, $orderBy, $limit);
    $data = [
      'title' => $reportRules['title'],
      'field' => $reportRules['field_str'],
      'list' => $reportData['list'],
      'sum_val' => $reportData['sum_val'],
      'limit_dscript' => $limit > 0 ? '返回了部分数据 仅' . $limit . '条' : '返回全部'
    ];
    return $data;
  }


}
