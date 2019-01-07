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

class ExportdataController extends ApiBaseController {

  /**
   * 获取关区列表
   * @return mixed
   */
  public function getListAction() {
    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);

    $where = $this->_search();

    $result = $this->exportdataService->getListPage($where, '*', $page_num, $page_size);
    return $result;
  }


  /**
   * 添加出口数据
   * @param string $title <POST> 名称
   * @return array
   */
  public function addAction() {
    $title = $this->_post('title');
    $result = $this->exportdataService->add($title);
    return $result;
  }

  /**
   * 得到一个出口数据信息
   * @param int $id <POST> 用户id
   * @return array|mixed
   */
  public function getOneAction() {
    $id = $this->_post('id');
    $result = $this->exportdataService->getOne($id);
    return $result;
  }

  /**
   * 出口数据删除
   * @param string $id <POST> 数据id ，如果删除多个，请使用逗号分隔
   * @return 删除数据的id
   */
  public function deleteAction() {
    $id = $this->_post('id');
    $result = $this->exportdataService->delete($id);
    return $result;
  }


  /**
   * 充值订单导出
   *
   */
  public function exportAction() {
    $where = $this->_search();
    $result = $this->exportdataService->export($where);
    if ($result['result']) {
      $file_content = $result['result']['csv'];
      export_csv($file_content, 'recharge_' . date('Y-m-d'));
    }
    return $result;
  }


  /**
   * 公开的 生成报表的时候，生成数据分析图的类型/非常有用
   */
  public function reportAction() {

    $where = $this->_search();

    $report_type = ''; //生成数据分析图的类型

    $type = $this->_post('type', 1);

    $result = $this->exportdataService->getReportData($where, $report_type, $type);
    return $result;
  }


  /**
   * 公共搜索条件
   * @return array|bool|mixed
   */
  private function _search() {

    $shipper = $this->_post('shipper', '');
    $export_ciq = $this->_post('export_ciq', '');
    $dist_country = $this->_post('dist_country', '');
    $goods_code = $this->_post('goods_code', '');
    $transaction_mode = $this->_post('transaction_mode', '');
    $trade_mode = $this->_post('trade_mode', '');
    $transport_mode = $this->_post('transport_mode', '');
    $madein = $this->_post('madein', '');

    $rules = [
      ['condition' => 'like',
        'key_field' => ['shipper'],
        'db_field' => ['shipper']
      ],

      ['condition' => 'after like',
        'key_field' => ['specification'],
        'db_field' => ['specification']
      ],
      [
        'condition' => '=',
        'key_field' => ['export_ciq', 'dist_country', 'goods_code', 'transaction_mode', 'trade_mode', 'transport_mode', 'madein'],
        'db_field' => ['export_ciq', 'dist_country', 'goods_code', 'transaction_mode', 'trade_mode', 'transport_mode', 'madein']
      ]
    ];
    $data = [
      'shipper' => $shipper,
      'export_ciq' => $export_ciq,
      'dist_country' => $dist_country,
      'goods_code' => $goods_code,
      'transaction_mode' => $transaction_mode,
      'trade_mode' => $trade_mode,
      'transport_mode' => $transport_mode,
      'madein' => $madein,
    ];

    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));

    return $where;

  }


}
