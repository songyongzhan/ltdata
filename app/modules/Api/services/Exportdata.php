<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/12/28
 * Time: 12:57
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class ExportdataService extends BaseService {

  /**
   * 获取列表 分页
   * @param array $where
   * @param int $page_num <number>
   * @param int $page_size <number>
   */
  public function getListPage(array $where, $field = '*', $page_num, $page_size) {
    $result = $this->exportdataModel->getExportDataList($where, $field, $page_num, $page_size);
    return $this->show($result);
  }

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getList($where, $field = '*') {
    $result = $this->exportdataModel->getList($where, $field);
    return $this->show($result);
  }

  /**
   * 添加权限
   * @param string $title <require> 分组名称
   * @return mixed 返回最后插入的id
   */
  public function add($title) {
    $data = [
      'title' => $title
    ];
    $lastInsertId = $this->exportdataModel->insert($data);
    if ($lastInsertId) {
      $data['id'] = $lastInsertId;
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }

  }

  /**
   * 删除一条数据
   * @param int $id <require|number> id
   */
  public function delete($id) {
    $result = $this->exportdataModel->delete($id);
    return $result > 0 ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }


  /**
   * 获取单个信息
   * @param int $id <require|number> id
   * @param string $fileds
   * @return mixed
   */
  public function getOne($id, $fileds = '*') {
    $result = $this->exportdataModel->getExportOne($id, $fileds);
    return $result ? $this->show($result) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }


  /**
   * 信息导出
   * @param $where
   * @param string $field
   * @return array
   */
  public function exort($where, $field = '*') {
    $result = [];
    $chanelArr = $this->Dictionaries_service->getList('recharge_chanel');
    $chanelArr = array_column($chanelArr['result'], 'title', 'id');
    $csv = "客户流水号,支付流水号,银行流水号,用户名,充值渠道(0-B2B；1-B2C;2-协议支付;3-微信收单;4-支付宝收单),充值金额,充值手续费,实际支付金额,充值时间,支付公司状态(0-成功;1-失败),银行返回状态(0-成功;1-失败)";
    $title = explode(',', $csv);
    $csvDatas = [];
    foreach ($result as $val) {
      $csvDatas[] = [
        $val['customer_flow_num'] . "\t",
        $val['pay_flow_num'] . "\t",
        $val['bank_flow_num'] . "\t",
        $val['username'],
        array_key_exists($val['channel'], $chanelArr) ? $chanelArr[$val['channel']] : '',
        $val['recharge_amount'],
        $val['recharge_charge'],
        $val['actual_payment_amount'],
        date('Y-m-d H:i:s', $val['payment_time']),
        $val['pay_status'],
        $val['bank_status']
      ];
    }
    return $this->show(['csv' => ['title' => $title, 'data' => $csvDatas]]);
  }

  /**
   * 分析相关数据，并返回
   * @param array $where 条件
   * @param int $report_type 根据这个可以做报表 汇总、平均值、及求和
   * @param $type 使用类型 1 json  2导出文件 只是记录到列表 当时并不下载
   */
  public function getReportData($where, $report_type, $type) {


    $this->exportdataModel->getReportData();

  }


}