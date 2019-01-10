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
   * @param int $report_id 根据这个可以做报表 汇总、平均值、及求和
   * @param $type 使用类型 1 json  2导出文件 只是记录到列表 当时并不下载
   */
  public function getReportData($where, $report_id, $date_type = 1, $type = 1) {


    $result = $this->exportdataModel->getReportDataByReportlist($where, $report_id, $date_type);

    switch (strtolower($result['viewtype'])) {
      case 'pie':

        break;
      case 'line':
        $result['option'] = $this->_createLine($result, $date_type);
        break;
    }

    print_r($result);
    exit;
    /* $v=$this->exportdataModel->getTableScnema('exportdata',['COLUMN_NAME','COLUMN_COMMENT']);

     var_dump($v);

     exit;*/


    //目的国销售量占比分析
    //目的国销售额占比分析（排名）	一定时间内出口销售额各目的国占比	 美元总价/时间（年、月两种）
    /**
     * 出口企业数量增减趋势  3年度1-12月对比趋势分析  货主单位数量/时间（年、月两种）
     * count(shipper) as val,export_year,export_month
     *
     *出口企业销量占比分析（排名）  一定时间内出口销售量各目的国占比  法定重量/目的国/时间（年、月两种）
     *
     * sum(weight) as val,export_year,export_month,dist_country
     *
     *
     *
     *
     * 10  出口企业销售额占比分析（排名）  一定时间内出口销售额各目的国占比  美元总价/时间（年、月两种）
     * sum(price_amount) as val,export_year,export_month,dist_country
     * 11  出口企业销售单价排名分析（排名）  一定时间内出口销售单价的排名  美元单价/时间（年、月两种）
     *
     * avg(price_amount) as val,export_year,export_month
     * 12  出口关区销量占比分析（排名）  一定时间内出口总量各关区占比  法定重量/出口关区/时间（年、月两种）
     * sum(weight) as val,export_year,export_month,export_ciq
     *
     * 13  贸易方式销量占比分析  一定时间内出口总量各贸易方式占比  法定重量/贸易方式/时间（年、月两种）
     *
     * sum(weight) as val,export_year,export_month,trade_mode
     *
     * 14  规格销量占比分析（排名）  一定时间内出口总量指定规格占比  法定重量/指定规格/时间（年、月两种）
     *
     *
     * sum(weight) as val,export_year,export_month,specification
     *
     * 15  规格销量单价占比分析（排名）  一定时间内出口销售单价的排名  美元单价/时间（年、月两种）
     *
     *
     */

    /*  $field = ['sum(weight) as val', 'export_year', 'export_month', 'dist_country'];

      $orderBy = [
        'total' => 'val desc'
      ];

      $groupBy = [
        'dist_country',
        'export_year',
        'export_month'
      ];

      $result = $this->exportdataModel->getReportData($where, $field, $groupBy, $orderBy, []);

    */


    /*
        //目的国数量增减趋势分析 count(dist_country) as val,export_year,export_month



        $field = ['count(dist_country) as country', 'export_year', 'export_month'];

        $orderBy = [
          'total' => 'country asc'
        ];

        $groupBy = [
          'dist_country',
          'export_year',
          'export_month'
        ];

        $result = $this->exportdataModel->getReportData($where, $field, $groupBy, $orderBy, []);*/


    //销售单价趋势分析 'avg(price_amount) as price,export_year,export_month'
    /*   $field = ['avg(price_amount) as price', 'export_year', 'export_month'];

       $orderBy = [
         'total' => 'price asc'
       ];

       $groupBy = [
         'export_year',
         'export_month'
       ];

       $result = $this->exportdataModel->getReportData($where, $field, $groupBy, $orderBy, []);*/


    /*  // 销售金额趋势分析  'sum(price_amount) as val,export_year,export_month'
      $field = ['sum(price_amount) as val', 'export_year', 'export_month'];

      $orderBy = [
        'total' => 'val asc'
      ];

      $groupBy = [
        'export_year',
        'export_month'
      ];

      $result = $this->exportdataModel->getReportData($where, $field, $groupBy, $orderBy, []);*/


    /*
    销售量趋势分析
    $field = ['sum(weight) as val', 'export_year', 'export_month'];

    $orderBy = [
      'total' => 'val asc'
    ];

    $groupBy = [
      'export_year',
      //'export_month'
    ];

    $result = $this->exportdataModel->getReportData($where, $field, $groupBy, $orderBy, []);
*/

    /*$field = ['count(id) as total', 'GROUP_CONCAT(id)'];

    $orderBy = [
      'total' => 'desc'
    ];

    $groupBy = [
      'export_ciq'
    ];


    $having = [

      ['field' => 'total', 'val' =>'1906','operator' => ' >= ','cond' => ' and ']

    ];


    $limit = 10;
      $result = $this->exportdataModel->getReportData($where, $field, $groupBy, $having, $orderBy, $limit);

    */
  }


  /**
   * 生成线形分析图
   * @param $result
   * @param $date_type
   * @return array
   */
  private function _createLine($result, $date_type) {
    $resultData = [];
    foreach ($result['list'] as $val) {
      $resultData[$val['export_year']][] = [
        'title' => $val['export_year'],
        'numval' => $val['val'],
        'month' => isset($val['export_month']) ? $val['export_month'] : '',
        'data' => $val
      ];
    }

    $legendData = array_map(function ($val) {
      return $val . '年';
    }, array_keys($resultData));

    $seriesData = [];
    $xAxisMax = 0;
    foreach (array_keys($resultData) as $yearval) {
      $temp = $resultData[$yearval];
      if ($date_type == 1) { //1 年 月   2年  当等于2 的时候不考虑排序
        usort($temp, function ($a, $b) {
          if ($a['month'] == $b['month'])
            return 0;

          return $a['month'] > $b['month'] ? 1 : -1;
        });

        if (count($temp) > $xAxisMax)
          $xAxisMax = count($temp);

        $seriesData[] = [
          'name' => $yearval . '年',
          'type' => 'line',
          'data' => array_column($temp, 'numval')
        ];

      } else if ($date_type == 2) { //年处理逻辑

        if (empty($seriesData)) {
          $seriesData[] = [
            'type' => 'line',
            'data' => array_column($temp, 'numval')
          ];
        } else {
          $seriesData[0]['data'][] = array_column($temp, 'numval')[0];
        }

      }
    }

    $xAxisData = [];
    if ($date_type == 1) {
      $xAxisDataText = [
        '一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'
      ];

      for ($i = 0; $i < $xAxisMax; $i++) {
        $xAxisData[] = $xAxisDataText[$i];
      }
    } elseif ($date_type == 2) {
      $xAxisData = $legendData;
    }

    $option = [
      'title' => [
        'text' => $result['title'],
        'subtext' => '' //副标题
      ],
      'tooltip' => [ //鼠标放上去是否信息显示
        'trigger' => 'axis'
      ],

      'legend' => [ //栏目显示
        'data' => $legendData
      ],

      'grid' => [ //位置调整
        'left' => '3%',
        'right' => '4%',
        'bottom' => '4%',
        'containLabel' => TRUE
      ],

      'toolbox' => [
        'feature' => [

          'magicType' => [
            'type' => ['line', 'bar']
          ],
          'restore' => [],
          'saveAsImage' => []
        ]
      ],

      'xAxis' => [
        'type' => 'category',
        'boundaryGap' => FALSE,
        'data' => $xAxisData
      ],

      'yAxis' => [
        'type' => 'value',
        'axisLabel' => [
          'formatter' => '{value} kg'
        ]
      ],

      'series' => $seriesData
    ];

    return $option;
  }


}