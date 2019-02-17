<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/29
 * Time: 16:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class PcrdataModel extends BaseModel {

  protected $realDelete = TRUE;

  /**
   * @param $where
   * @param $field
   * @param $report_id
   * @return array
   * @throws InvalideException
   */
  public function getReportData($where, $authorityField, $date_type = '', $report_id) {

    $reportRules = $this->reportlistModel->getOne($report_id);
    $this->_logSql();
    if (!$reportRules)
      showApiException('reportlist data 不存在', StatusCode::REPORTLIST_NOT_EXISTS);

    $orderBy = [];
    if ($reportRules['order_str'] && $reportRules['order_str'] != '-') {
      foreach (explode('|', $reportRules['order_str']) as $val) {
        $_temp = explode(',', $val);
        $orderBy[$_temp[0]] = isset($_temp[1]) ? $_temp[1] : 'asc';
      }
    }

    if (is_null($date_type) || $date_type == '')
      $date_type = $reportRules['date_type'];

    $groupBy = [];
    if ($reportRules['group_str'] && $reportRules['group_str'] != '-') {
      $group_str = (explode('|', $reportRules['group_str']))[$date_type - 1];
      foreach (explode(',', $group_str) as $val) {
        $groupBy[] = $val;
      }
    }

    foreach ($groupBy as $groupByVal) {
      $this->_db->groupBy($groupByVal);
    }

    foreach ($orderBy as $orderField => $orderSortVal) {
      $this->_db->orderBy($orderField, $orderSortVal);
    }

    $having = [];
    if ($reportRules['having_str'] && $reportRules['having_str'] != '-') {
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

    foreach ($having as $key => $havingVal) {
      if (!isset($havingVal['field']) || !isset($havingVal['val']))
        continue;

      $this->_db->having($havingVal['field'], $havingVal['val'], isset($havingVal['operator']) ? $havingVal['operator'] : '=', isset($havingVal['cond']) ? $havingVal['cond'] : 'and');
    }

    $field = [];
    if ($reportRules['field_str'] === '')
      debugMessage('reportList field_str 字段为空');
    else {
      foreach (explode(',', $reportRules['field_str']) as $val) {
        $field[] = $val;
      }
      //如果不需要合并，传递空数组
      is_array($authorityField) && $field = array_merge($field, $authorityField);
    }
    $this->setCond($where);
    $result = $this->_db->get($this->table, NULL, $field);
    $this->_logSql();
    $reportRules['result'] = $result;

    return $reportRules;
  }
}