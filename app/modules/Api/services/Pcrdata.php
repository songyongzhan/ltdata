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

    $csvHeader = ['日期', '城市', '品牌', '花纹', '等级', '-', '联系人', '经营品牌', '手机', '电话', '网址', '邮箱', '是否首页显示', '是否公开', '名片形式展示', '是否付费'];

    $header = [];
    $footer = [];
    export_csv(['header' => $header, 'title' => $csvHeader, 'data' => $csvDatas, 'footer' => $footer], '代理商名录_' . time());
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
    $avg = [41, 43];
    if (in_array($report_id, $avg)) {
      $authorityField = array_map(function ($val) {
        return 'truncate(avg(' . $val . '),2) as ' . $val;
      }, $authorityField);
    }


    $result = $this->pcrdataModel->getReportData($where, $authorityField, $date_type, $report_id);

    switch ($result['viewtype']) {
      case 'bar':
        $result = $this->_createBar($result, $srcAuthorityField, 'bar');
        break;
      case 'line':
        $result = $this->_createBar($result, $srcAuthorityField, 'line');
        break;
      default:
        showApiException('无法处理这类数据请求');
    }
    //echo jsonencode($result['option']);
    //
    //exit;
    return $this->show($result);

    /**
     * option = {
     * title : {
     * text: '某地区蒸发量和降水量',
     * subtext: '纯属虚构'
     * },
     * tooltip : {
     * trigger: 'axis'
     * },
     * legend: {
     * data:['蒸发量','降水量']
     * },
     * toolbox: {
     * show : true,
     * feature : {
     * dataView : {show: true, readOnly: false},
     * magicType : {show: true, type: ['line', 'bar']},
     * restore : {show: true},
     * saveAsImage : {show: true}
     * }
     * },
     * calculable : true,
     * xAxis : [
     * {
     * type : 'category',
     * data : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
     * }
     * ],
     * yAxis : [
     * {
     * type : 'value'
     * }
     * ],
     * series : [
     * {
     * name:'蒸发量',
     * type:'bar',
     * data:[2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3]
     * },
     * {
     * name:'降水量',
     * type:'bar',
     * data:[2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3]
     * },
     * {
     * name:'温度量',
     * type:'bar',
     * data:[2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3]
     * }
     * ,
     * {
     * name:'条例量',
     * type:'bar',
     * data:[2.6]
     * }
     * ,
     * {
     * name:'风速量',
     * type:'bar',
     * data:[2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3]
     * }
     * ]
     * };
     */

  }

  /**
   * @param $result
   * @param $authorityField
   * @return mixed
   */
  private function _createBar($result, $authorityField, $viewtype) {

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
    foreach ($result['result'] as $value) {
      //$temp = $value;
      $this->_format($value);

      $horizontalVal = '';
      foreach ($reportListHorizontal as $hval) {
        $horizontalVal .= $value[$hval] . ' ';
      }

      //$horizontalKey = $temp['horizontal'] . (isset($temp['horizontal2']) ? $temp['horizontal2'] : '');
      //$horizontalVal = $value['horizontal'] . (isset($value['horizontal2']) ? ' ' . $value['horizontal2'] : '');
      //$horizontalVal = $value['brand'] . (isset($value['horizontal2']) ? ' ' . $value['horizontal2'] : '');

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
        'type' => $viewtype,
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
        'type' => 'value'
      ],

      'series' => $seriesData
    ];


    echo jsonencode($option);
    exit;
    $result['option'] = $option;

    return $result;
    //echo jsonencode($option);
  }

  private function _createLine($result, $authorityField) {
    return $result;
  }

}