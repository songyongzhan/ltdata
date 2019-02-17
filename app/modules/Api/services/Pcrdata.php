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

  protected $field = ['id', 'export_date', 'export_year', 'export_month', 'city', 'brand', 'specification', 'huawen', 'grade', 'pf_pricle', 'stls_pricle', 'th_pricle', 'jd_pricle', 'gfqj_pricle', 'createtime'];

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
   * @throws Exception
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
    export_csv(['header' => $header, 'title' => $csvHeader, 'data' => $csvDatas, 'footer' => $footer], 'PCR数据_' . time());
  }


  /**
   * 城市转化成id
   * @param $sheng_id
   * @return string
   */
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

  /**
   * 品牌 转换 成id
   * @param $mppinpaiId
   * @return bool
   */
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
    else {
      $lastId = $this->mppinpaiModel->insert([
        'ppname' => $mppinpaiId,
        'pid=' => 45,
        'status' => 1
      ]);
      $this->redisModel->redis->hSet('mppinpai', $lastId, $mppinpaiId);
      return $lastId;
    }
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

    if (isset($value['brand']))
      isset($pinpaiData[$value['brand']]) && $value['brand'] = $pinpaiData[$value['brand']];

    static $shengData;
    if (!$shengData) {
      $data = $this->regionService->getList([
        getWhereCondition('region_type', [1, 2], 'in')
      ]);
      $shengData = array_column($data['result'], 'text', 'id');
    }

    if (isset($value['city']))
      isset($shengData[$value['city']]) && $value['city'] = $shengData[$value['city']];

  }

  /**
   * 没有限制，直接显示出pcrdata城市
   */
  public function getCity($where) {
    $cityIds = $this->pcrdataModel->getList($where, ['city'], '', '', 0, 'city');
    $cityIds = array_column($cityIds, 'city');
    $result = $this->regionModel->getList(
      [getWhereCondition('region_id', $cityIds, 'in')],
      ['region_id as id', 'region_name as text'], '', '', 0
    );
    return $this->show($result);
  }

  /**
   * 获取所有规格
   * @param $where
   */
  public function getSpecification($where) {
    $result = $this->pcrdataModel->getList($where, ['specification as id', 'specification as text'], '', '', 0, 'specification');
    return $this->show($result);
  }

  /**
   * @param $where
   * @return array
   */
  public function getGrade($where) {
    $result = $this->pcrdataModel->getList($where, ['grade as id', 'grade as text'], '', '', 0, 'grade');
    return $this->show($result);
  }

  /**
   * @param $where
   * @return array
   * @throws InvalideException]
   */
  public function getBrand($where) {
    $result = $this->pcrdataModel->getList($where, ['DISTINCT brand']);
    $brandIds = array_column($result, 'brand');
    $result = $this->mppinpaiModel->getList(
      [getWhereCondition('id', $brandIds, 'in')],
      ['id', 'ppname as text']
    );
    return $this->show($result);
  }

  /**
   * 从条件得到数据
   * @param $where
   * @param int $report_id <require|number> 分析项目report_id
   */
  public function getReportData($where, $date_type, $report_id) {

    //$field = ['id', 'export_date', 'city', 'brand', 'specification', 'huawen', 'grade'];

    //结合权限，组合字段
    $authorityField = [];

    /*$permission = $this->permissionService->getManagePermission($this->tokenService->manage_id);

    print_r($permission);exit;

    if (isset($permission['result']['pcr']) && $permission['result']['pcr'])
      $authorityField = $permission['result']['pcr'];
    else {
      debugMessage('pcr getReportData 权限为空,不能显示价格数字，请设置相关权限');
      showApiException('不能显示相关信息，请设置相关权限');
    }*/

    $authorityField = $srcAuthorityField = ['pf_pricle', 'stls_pricle', 'th_pricle', 'jd_pricle', 'gfqj_pricle'];
    //$authorityField = ['pf_pricle', 'stls_pricle'];

    //字段从权限中获取

    //$field = array_merge($field, $authorityField);

    //是否平均值
    $avg = [41, 42, 43];
    if (in_array($report_id, $avg)) {
      $authorityField = array_map(function ($val) {
        return 'truncate(avg(' . $val . '),2) as ' . $val;
      }, $authorityField);
    }

    //连续12月指定规格价格指定城市的总体趋势分析
    /*if ($report_id == 42) {
      $authorityField = array_map(function ($val) {
        return 'truncate(avg(' . $val . '),2) as ' . $val;
      }, $authorityField);
    }*/


    $result = $this->pcrdataModel->getReportData($where, $authorityField, $date_type, $report_id);


    //处理显示表格数据
    $permissionText = $this->permissionModel->viewPermission()['pcr']['data'];
    $authorityTableColumn = '';
    foreach ($srcAuthorityField as $tab_column) {
      if (array_key_exists($tab_column, $permissionText))
        $authorityTableColumn .= $permissionText[$tab_column] . ',' . $tab_column . '|';
    }
    $result['table_column'] = trim($result['table_column'], '|') . '|' . trim($authorityTableColumn, '|');


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
    $permissionText = $this->permissionModel->viewPermission()['pcr']['data'];

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

      foreach ($viewPricle as $k => $v) {
        $seriesBarData[$k][] = isset($value[$k]) ? $value[$k] : 0;
      }
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

    foreach ($viewPricle as $k => $v) {
      $tempData = [
        'name' => $v,
        'type' => 'bar',
        'data' => $seriesBarData[$k]
      ];
      $seriesData[] = $tempData;
    }

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

    //echo jsonencode($option);
    //exit;
    $result['option'] = $option;

    return $result;
  }

  private function _createLine($result, $authorityField) {
    //组合 图表 数据
    $viewPricle = [];
    $permissionText = $this->permissionModel->viewPermission()['pcr']['data'];

    foreach ($authorityField as $key) {
      if (array_key_exists($key, $permissionText))
        $viewPricle[$key] = $permissionText[$key];
    }

    $legendData = $viewPricle;

    $xAxisData = [];

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
    }

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

}