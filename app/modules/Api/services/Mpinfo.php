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

  protected $field = ['id', 'wn_np_type', 'sheng_id', 'shi', 'title', 'legal_person', 'partner', 'address', 'person', 'tel', 'mobile', 'contract_year', 'manufacturer', 'mppinpaiId', 'sell_area', 'year_sell', 'verification_date', 'contract', 'createtime'];

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getListPage(array $where, $page_num, $page_size) {

    $field = [];
    if ($this->tokenService->isadmin)
      $field = $this->field;
    else {
      $field = ['id', 'wn_np_type', 'sheng_id', 'shi', 'title', 'legal_person', 'partner', 'address', 'person', 'mppinpaiId', 'contract_year', 'contract', 'createtime'];
      $field = $this->getAuthField($field);
    }

    $result = $this->mpinfoModel->getListPage($where, $field, $page_num, $page_size);
    foreach ($result['list'] as $key => &$value) {
      $this->_format($value);
    }
    return $this->show($result);
  }

  /**
   * 获取权限字段
   * @param $field
   * @return array
   */
  private function getAuthField($field) {
    //通过权限获取到权限，与字段合并返回。
    $permission = $this->permissionService->getManagePermission($this->tokenService->manage_id);

    $authorityField = [];
    if (isset($permission['result']['permission_data']['mpinfo']) && $permission['result']['permission_data']['mpinfo'])
      $authorityField = $permission['result']['permission_data']['mpinfo'];

    return array_merge($field, $authorityField);
  }

  /**
   * 下载代理商列表
   * @param $where
   * @throws InvalideException
   */
  public function downloadCsv($where, $date_type, $report_id) {
    set_time_limit(0);
    ini_set('memory_limit', '1000M');

    $result = $this->getReportData($where, $date_type, $report_id);

    $table_column = trim($result['result']['table_column'], '|');

    if ($table_column == '')
      showApiException('导出数据表头为空', StatusCode::TABLE_COLUMN_EMPTY);
    $table_column = explode('|', $table_column);

    $field = [];
    $csvHeader = [];
    foreach ($table_column as $val) {
      list($v_field, $v_header) = explode(',', $val);
      if (!$v_field || !$v_header)
        continue;

      $field[] = $v_header;
      $csvHeader[] = $v_field;
    }

    $csvDatas = $result['result']['list'];

    $header = [];
    $footer = [];
    export_csv(['header' => $header, 'title' => $csvHeader, 'data' => $csvDatas, 'footer' => $footer], '代理商名录数据_' . time());
  }

  /**
   * @param $where
   * @param $date_type
   * @param int $report_id <require|number> 分析id不能为空
   * @return array
   * @throws Exception
   * @throws InvalideException
   */
  public function getReportData($where, $date_type, $report_id) {
    //结合权限，组合字段
    $srcAuthorityField = $authorityField = [];

    /*$permission = $this->permissionService->getManagePermission($this->tokenService->manage_id);

    //print_r($permission);exit;

    if (isset($permission['result']['permission_data']['mpinfo']) && $permission['result']['permission_data']['mpinfo'])
      $srcAuthorityField = $authorityField = $permission['result']['permission_data']['mpinfo'];
    else {
      debugMessage('mpinfo getReportData 权限为空,不能显示价格数字，请设置相关权限');
      showApiException('不能显示相关信息，请设置相关权限');
    }

    if (!$authorityField)
      showApiException('不能显示相关信息，请设置相关权限');*/


    //$authorityField = $srcAuthorityField = ['pf_pricle', 'stls_pricle', 'th_pricle', 'jd_pricle', 'gfqj_pricle'];
    //$authorityField = ['pf_pricle', 'stls_pricle'];

    //字段从权限中获取

    //$field = array_merge($field, $authorityField);

    //是否平均值
    /*$avg = [41, 42, 43];
    if (in_array($report_id, $avg)) {
      $authorityField = array_map(function ($val) {
        return 'truncate(avg(' . $val . '),2) as ' . $val;
      }, $authorityField);
    }*/

    //连续12月指定规格价格指定城市的总体趋势分析
    /*if ($report_id == 42) {
      $authorityField = array_map(function ($val) {
        return 'truncate(avg(' . $val . '),2) as ' . $val;
      }, $authorityField);
    }*/


    $result = $this->mpinfoModel->getReportData($where, $authorityField, $date_type, $report_id);


    //处理显示表格数据
    /*$permissionText = $this->permissionModel->viewPermission()['mpinfo']['data'];
    $authorityTableColumn = '';
    foreach ($srcAuthorityField as $tab_column) {
      if (array_key_exists($tab_column, $permissionText))
        $authorityTableColumn .= $permissionText[$tab_column] . ',' . $tab_column . '|';
    }
    $result['table_column'] = trim($result['table_column'], '|') . '|' . trim($authorityTableColumn, '|');*/


    switch ($result['viewtype']) {
      case 'bar':
        $result = $this->_createBar($result, $srcAuthorityField);
        break;
      case 'line':
        $result = $this->_createLine($result, $srcAuthorityField);
        break;
      default:
        showApiException('无法处理这类数据请求');
    }
    //echo jsonencode($result['option']);
    //
    //exit;
    return $this->show($result);
  }

  /**
   * @param $result
   * @param $authorityField
   * @return mixed
   */
  private function _createBar($result, $authorityField) {

    //组合 图表 数据
    $viewPricle = [];
    $permissionText = $this->permissionModel->viewPermission()['mpinfo']['data'];

    foreach ($authorityField as $key) {
      if (array_key_exists($key, $permissionText))
        $viewPricle[$key] = $permissionText[$key];
    }

    $legendData = $viewPricle;

    $xAxisData = [];
    $reportListHorizontal = explode('|', trim($result['horizontal'], '|'));


    $seriesBarData = [];
    $seriesData = [];//获取显示数据
    foreach ($result['list'] as $value) {
      //$temp = $value;
      $this->_format($value);

      $horizontalVal = '';
      foreach ($reportListHorizontal as $hval) {
        $horizontalVal .= $value[$hval] . ' ';
      }

      $xAxisData[md5($horizontalVal)] = $horizontalVal;

      $seriesBarData[] = $value['val'];
      /*foreach ($viewPricle as $k => $v) {
        $seriesBarData[$k][] = isset($value[$k]) ? $value[$k] : 0;
      }*/
      //$tempData = [
      //  'name' => $legendData[$legendKey],
      //  'type' => $viewtype,
      //];
      //$tempData_data = [];
      //foreach ($viewPricle as $k => $v) {
      //  $tempData_data[] = isset($value[$k]) ? $value[$k] : 0;
      //}
      //$tempData['data'] = $tempData_data;
      //
      //$seriesData[] = $tempData;
    }

    /*foreach ($viewPricle as $k => $v) {
      $tempData = [
        'name' => $v,
        'type' => 'bar',
        'data' => $seriesBarData[$k]
      ];
      $seriesData[] = $tempData;
    }*/


    $seriesData[] = [
      'name' => $result['title2'],
      'type' => 'bar',
      'data' => $seriesBarData,
    ];

    $option = [
      'title' => [
        'text' => $result['title'],
        //'subtext' => $result['title2'] //副标题
        'subtext' => '' //副标题
      ],
      'tooltip' => [ //鼠标放上去是否信息显示
        'trigger' => 'axis',
        'axisPointer' => [ // 坐标轴指示器，坐标轴触发有效
          'type' => 'shadow' // 默认为直线，可选为：'line' | 'shadow'
        ]
      ],
      'legend' => [ //栏目显示
        'data' => array_values($legendData),
        'show' => TRUE,
        'bottom' => 0,
        'type' => 'scroll',
        'padding' => [15, 0, 0, 0]
      ],

      'grid' => [ //位置调整
        'left' => '3%',
        'right' => '4%',
        'bottom' => '6%',
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
      'calculable' => TRUE,
      'xAxis' => [
        'type' => 'category',
        'data' => array_values($xAxisData)
      ],

      'yAxis' => [
        'type' => 'value',
        'axisLabel' => [
          'formatter' => '{value} ' . $result['unit']
        ]
      ],

      'series' => $seriesData
    ];

    //echo jsonencode($option);
    //exit;
    $result['option'] = $option;

    return $result;
  }

  private function _createLine($result, $authorityField) {
    //组合 图表 数据
    $viewPricle = [];
    $permissionText = $this->permissionModel->viewPermission()['mpinfo']['data'];

    foreach ($authorityField as $key) {
      if (array_key_exists($key, $permissionText))
        $viewPricle[$key] = $permissionText[$key];
    }


    $legendData = $viewPricle;

    $xAxisData = [];
    $reportListHorizontal = explode('|', trim($result['horizontal'], '|'));


    $seriesBarData = [];
    $seriesData = [];//获取显示数据
    foreach ($result['list'] as $value) {
      //$temp = $value;
      $this->_format($value);

      $horizontalVal = '';
      foreach ($reportListHorizontal as $hval) {
        $horizontalVal .= $value[$hval] . ' ';
      }

      $xAxisData[md5($horizontalVal)] = $horizontalVal;

      $seriesBarData[] = $value['val'];
      /*foreach ($viewPricle as $k => $v) {
        $seriesBarData[$k][] = isset($value[$k]) ? $value[$k] : 0;
      }*/
      //$tempData = [
      //  'name' => $legendData[$legendKey],
      //  'type' => $viewtype,
      //];
      //$tempData_data = [];
      //foreach ($viewPricle as $k => $v) {
      //  $tempData_data[] = isset($value[$k]) ? $value[$k] : 0;
      //}
      //$tempData['data'] = $tempData_data;
      //
      //$seriesData[] = $tempData;
    }

    $seriesData[] = [
      'name' => $result['title2'],
      'type' => 'line',
      'data' => $seriesBarData,
    ];


    /* $xAxisData = [];

     $seriesBarData = [];
     $seriesData = [];//获取显示数据
     foreach ($result['list'] as $value) {
       $this->_format($value);
       $xAxisData[$value['export_month']] = $value['export_month'] . '月';

       foreach ($viewPricle as $k => $v) {
         $seriesBarData[$k][] = isset($value[$k]) ? $value[$k] : 0;
       }
     }

     foreach ($viewPricle as $k => $v) {
       $tempData = [
         'name' => $v,
         'type' => 'line',
         'data' => $seriesBarData[$k]
       ];
       $seriesData[] = $tempData;
     }*/

    $option = [
      'title' => [
        'text' => $result['title'],
        'subtext' => $result['title2'] //副标题
      ],
      'tooltip' => [ //鼠标放上去是否信息显示
        'trigger' => 'axis'
      ],

      'legend' => [ //栏目显示
        'data' => array_values($legendData),
        'show' => TRUE,
        'bottom' => 0,
        'type' => 'scroll',
        'padding' => [15, 0, 0, 0]
      ],

      'grid' => [ //位置调整
        'left' => '3%',
        'right' => '4%',
        'bottom' => '6%',
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
      'calculable' => TRUE,
      'xAxis' => [
        'type' => 'category',
        'data' => array_values($xAxisData)
      ],

      'yAxis' => [
        'type' => 'value',
        'axisLabel' => [
          'formatter' => '{value} ' . $result['unit']
        ]
      ],

      'series' => $seriesData
    ];

    $result['option'] = $option;

    return $result;
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
  public function getOne($id) {
    $field = [];
    if ($this->tokenService->isadmin)
      $field = $this->field;
    else {
      $field = ['id', 'wn_np_type', 'sheng_id', 'shi', 'title', 'legal_person', 'partner', 'address', 'person', 'mppinpaiId', 'contract_year', 'contract', 'createtime'];
      $field = $this->getAuthField($field);
    }
    
    $result = $this->mpinfoModel->getOne($id, $field);
    if ($result)
      $this->_format($result);

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

    if (isset($value['mppinpaiId']))
      isset($pinpaiData[$value['mppinpaiId']]) && $value['mppinpaiId'] = $pinpaiData[$value['mppinpaiId']];


    static $shengData;
    if (!$shengData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', 1)
      ]);
      $shengData = array_column($data['result'], 'text', 'id');
    }

    if (isset($value['sheng_id']))
      isset($shengData[$value['sheng_id']]) && $value['sheng_id'] = $shengData[$value['sheng_id']];

    //switch (trim($value['qylx_id'])) {
    //  case 49:
    //    $value['qylx_id'] = '厂商';
    //    break;
    //  case 50:
    //    $value['qylx_id'] = '代理商';
    //    break;
    //}

    if (isset($value['wn_np_type'])) {
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

  /**
   * 获取生产企业所有列表  从视图中获取
   */
  public function getManufacturer() {

    $mpinfomanufacturer_view = $this->exportdataModel->getViewData('mpinformanufacturer_view');
    $result = [];
    foreach ($mpinfomanufacturer_view as $key => $val) {
      $result[] = [
        'id' => $val['manufacturer'],
        'text' => $val['manufacturer']
      ];
    }
    return $this->show($result);

  }

  /**
   * 获取所有代理商
   * @return array
   */
  public function getTitle() {
    $mpinfotitle_view = $this->exportdataModel->getViewData('mpinfotitle_view');
    $result = [];
    foreach ($mpinfotitle_view as $key => $val) {
      $result[] = [
        'id' => $val['title'],
        'text' => $val['title']
      ];
    }
    return $this->show($result);

  }

  /**
   * 获取代理商名录中存在的省份列表
   */
  public function getSheng() {

    $shengId = $this->mppinpaiModel->query('select DISTINCT sheng_id from ' . $this->mppinpaiModel->prefix . 'mpinfo');

    $shengId = array_column($shengId, 'sheng_id');

    $sheng = $this->regionModel->getList([
      getWhereCondition('region_id', $shengId, 'in')
    ], ['region_id as id', 'region_name as text']);

    return $this->show($sheng);
  }

}