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
   * @param $id
   * @return array
   */
  public function downloadCsv($id) {
    //$result, $where
    $downloadInfo = $this->csvlistModel->getOne($id, ['id', 'manage_id', 'download_file', 'where_condition', 'date_type', 'report_id']);

    if (!$downloadInfo)
      showApiException('此信息不存在', StatusCode::DATA_NOT_EXISTS);

    if ($downloadInfo['download_file'] == '')
      showApiException('下载文件不存在', StatusCode::DATA_NOT_EXISTS);

    return $this->show(['filepath' => $downloadInfo['download_file']]);
  }

  /**
   * 分析相关数据，并返回
   * @param array $where 条件
   * @param int $report_id 根据这个可以做报表 汇总、平均值、及求和
   * @param $type 使用类型 1 json  2导出文件 只是记录到列表 当时并不下载
   */
  public function getReportData($where, $report_id, $date_type = '', $type) {
    if ($type == 1) {
      $result = $this->exportdataModel->getReportDataByReportlist($where, $report_id, $date_type, $type);
      switch (strtolower($result['viewtype'])) {
        case 'pie':
          $result['option'] = $this->_createPie($result, $date_type);;
          break;
        case 'line':
          $result['option'] = $this->_createLine($result, $date_type);
          break;
        case 'bubble':
          $result['option'] = $this->_createBubble($result, $date_type);
          break;

      }
      $result['list'] = array_slice($result['list'], 0, 12);
    } else if ($type == 2) {
      //下载csv文件
      $csvData = [
        'manage_id' => $this->tokenService->manage_id,
        'where_condition' => serialize($where),
        'download_num' => 0,
        'download_file' => '',
        'date_type' => $date_type,
        'report_id' => $report_id
      ];

      $lastInsertId = $this->csvlistModel->insert($csvData);

      if ($lastInsertId) {
        $result['id'] = $lastInsertId;
        $csvData['id'] = $lastInsertId;
        //写入到redis
        $this->redisModel->redis->lpush('csvlist', serialize($csvData), FALSE);
      } else {
        return $this->show([], StatusCode::INSERT_FAILURE);
      }
    }

    return $this->show($result);
  }

  private function _createPie(&$result, $date_type) {
    $series_data = [];
    $legend_data = [];
    $series_data_selected = [];
    $defaultSelected = 10;
    foreach ($result['list'] as $key => &$value) {

      if (isset($value['dist_country'])) { //国家处理

        $country_name = $this->exportdataModel->getCountry($value['dist_country']);
        $series_data[] = [
          'value' => $result['is_siglepricle'] == 1 ? $value['val'] :
            (sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) > 0 ? sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) : 0.01),
          'name' => $country_name,
          'selected' => $key == 0 ? TRUE : FALSE
        ];
        $series_data_selected[$country_name] = $key < $defaultSelected ? TRUE : FALSE;
        $legend_data[] = $country_name;
        $value['dist_country'] = $country_name;

      } elseif (isset($value['export_ciq'])) { //关区处理

        $export_ciq = $this->exportdataModel->getCiq($value['export_ciq']);
        $series_data[] = [
          'value' => $result['is_siglepricle'] == 1 ? $value['val'] :
            (sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) > 0 ? sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) : 0.01),
          'name' => $export_ciq,
          'selected' => $key == 0 ? TRUE : FALSE
        ];
        $series_data_selected[$export_ciq] = $key < $defaultSelected ? TRUE : FALSE;
        $legend_data[] = $export_ciq;
        $value['export_ciq'] = $export_ciq;

      } elseif (isset($value['trade_mode'])) { //贸易方式处理

        $trade_mode = $this->exportdataModel->getTrade($value['trade_mode']);

        $series_data[] = [
          'value' => $result['is_siglepricle'] == 1 ? $value['val'] :
            (sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) > 0 ? sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) : 0.01),
          'name' => $trade_mode,
          'selected' => $key == 0 ? TRUE : FALSE
        ];
        $series_data_selected[$trade_mode] = $key < $defaultSelected ? TRUE : FALSE;
        $legend_data[] = $trade_mode;
        $value['trade_mode'] = $trade_mode;

      } elseif (isset($value['specification'])) { //规格处理

        //$num = number_format($value['val'] / $result['sum_val'], 2);
        $series_data[] = [
          'value' => $result['is_siglepricle'] == 1 ? $value['val'] :
            (sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) > 0 ? sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) : 0.01),
          'name' => $value['specification'],
          'selected' => $key == 0 ? TRUE : FALSE
        ];
        $series_data_selected[$value['specification']] = $key < $defaultSelected ? TRUE : FALSE;
        $legend_data[] = $value['specification'];

      } elseif (isset($value['shipper'])) { //出口企业
        $series_data[] = [
          'value' => $result['is_siglepricle'] == 1 ? $value['val'] :
            (sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) > 0 ? sprintf("%.2f", $value['val'] / $result['sum_val'] * 100) : 0.01),
          'name' => $value['shipper'],
          'selected' => $key == 0 ? TRUE : FALSE
        ];
        $series_data_selected[$value['shipper']] = $key < $defaultSelected ? TRUE : FALSE;
        $legend_data[] = $value['shipper'];

      } else {
        echo '不支持此规则';
        exit;
      }

    }

    $seriesData = [
      [
        'name' => $result['title2'],
        'type' => 'pie',
        'selectedMode' => 'single',
        'radius' => '55%',
        'center' => ['50%', '60%'],
        'data' => $series_data,
        'itemSytle' => [
          'emphasis' => [
            'shadowBlur' => 10,
            'shadowOffsetX' => 0,
            'shadowColor' => 'rgba(0, 0, 0, 0.5)'
          ]
        ]
      ]
    ];

    $option = [
      'title' => [
        'text' => $result['title'],
        'subtext' => $result['title2'], //副标题,
        'x' => 'center'
      ],
      'tooltip' => [ //鼠标放上去是否信息显示
        'trigger' => 'item',
        'formatter' => "{a} <br/>{b} : {c} " . $result['prompt_sign']
      ],

      'legend' => [ //栏目显示
        'orient' => 'vertical',
        'left' => 'left',
        'top' => '10%',
        'type' => 'scroll',
        'data' => $legend_data,
        'selected' => $series_data_selected
        //'data' => ['直接访问', '邮件营销', '联盟广告', '视频广告', '搜索引擎']
      ],
      'toolbox' => [
        'feature' => [
          'restore' => [],
          'saveAsImage' => []
        ]
      ],
      'series' => $seriesData
    ];

    return $option;
  }

  /**
   * 生成线形分析图
   * @param $result
   * @param $date_type
   * @return array
   */
  private function _createLine(&$result, $date_type) {
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


    if (is_null($date_type) || $date_type == '')
      $date_type = $result['date_type'];

    $seriesData = [];
    $xAxisMax = [];
    foreach (array_keys($resultData) as $yearval) {
      $temp = $resultData[$yearval];
      if ($date_type == 1) { //1 年 月   2年  当等于2 的时候不考虑排序
        usort($temp, function ($a, $b) {
          if ($a['month'] == $b['month'])
            return 0;

          return $a['month'] > $b['month'] ? 1 : -1;
        });


        foreach ($temp as $k => $val) {
          if (isset($val['month']))
            $xAxisMax[] = $val['month'] - 1;
        }


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

      foreach ($xAxisMax as $val) {
        $xAxisData[] = $xAxisDataText[$val];
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
          'formatter' => '{value} ' . $result['unit']
        ]
      ],

      'series' => $seriesData
    ];

    return $option;
  }


  private function _createBubble($result, $date_type) {


    $option = [
      'title' => [
        'text' => $result['title'],
        'subtext' => $result['title2'], //副标题,
        'x' => 'center'
      ],
      'tooltip' => [ //鼠标放上去是否信息显示
        'trigger' => 'item',
        'formatter' => "{a} <br/>{b} : {c} " . $result['prompt_sign']
      ],

      'legend' => [ //栏目显示
        'orient' => 'vertical',
        'left' => 'left',
        'top' => '10%',
        'type' => 'scroll',
        'data' => $legend_data,
        'selected' => $series_data_selected
        //'data' => ['直接访问', '邮件营销', '联盟广告', '视频广告', '搜索引擎']
      ],
      'toolbox' => [
        'feature' => [
          'restore' => [],
          'saveAsImage' => []
        ]
      ],
      'series' => $seriesData
    ];

    return $option;

  }

  /**
   * 从redis队列中读取数据，生成csv 并把文件路径保存到数据库
   */
  public function createCsv() {

    $createCsvJob = $this->csvlistModel->readCsvListFromRedis();
    //$createCsvJob = unserialize("a:7:{s:9:\"manage_id\";i:15;s:15:\"where_condition\";s:87:\"a:1:{i:0;a:3:{s:5:\"field\";s:10:\"export_ciq\";s:3:\"val\";s:1:\"3\";s:8:\"operator\";s:1:\"=\";}}\";s:12:\"download_num\";i:0;s:13:\"download_file\";s:0:\"\";s:9:\"date_type\";s:1:\"1\";s:9:\"report_id\";s:1:\"2\";s:2:\"id\";i:2;}");

    if ($createCsvJob) {
      $where = unserialize($createCsvJob['where_condition']);

      $result = $this->exportdataModel->getReportDataByReportlist($where, $createCsvJob['report_id'], $createCsvJob['report_id'], $createCsvJob['date_type'], 2);

      $table_column = $result['table_column'];

      $csvHeader = [];
      $csvField = [];
      foreach (explode('|', $table_column) as $column) {
        list($csvHeader[], $csvField[]) = explode(',', $column);
      }

      $whereData = array_column($where, 'field', 'val');

      if (in_array('export_date', $whereData)) {
        foreach ($where as $val) {
          if ($val['field'] == 'export_date' && $val['operator'] == '>=')
            $whereData[$val['val']] = 'start_date';

          if ($val['field'] == 'export_date' && $val['operator'] == '<=')
            $whereData[$val['val']] = 'end_date';
        }
      }

      $whereData = array_flip($whereData);

      $header = [
        [
          $result['title'] . ((isset($whereData['start_date']) && isset($whereData['end_date'])) ? '(' . date('Y/m', $whereData['start_date']) . '-' . date('Y/m', $whereData['end_date']) . ')' : '')
        ]
      ];

      /*$footer = [
        [
          '时间范围:',
          isset($whereData['start_date']) ? date('Y-m-d', $whereData['start_date']) : '',
          isset($whereData['end_date']) ? date('Y-m-d', $whereData['end_date']) : ''
        ]
      ];*/

      $footer = [];
      //$csv = implode(',', $csvHeader);

      $csvDatas = [];
      foreach ($result['list'] as $val) {
        $temp = [];
        foreach ($csvField as $field) {

          if (isset($val['dist_country'])) { //国家处理
            $country_name = $this->exportdataModel->getCountry($val['dist_country']);
            $val['dist_country'] = $country_name;
          } elseif (isset($val['export_ciq'])) { //关区处理
            $export_ciq = $this->exportdataModel->getCiq($val['export_ciq']);
            $val['export_ciq'] = $export_ciq;
          } elseif (isset($val['trade_mode'])) { //贸易方式处理
            $trade_mode = $this->exportdataModel->getTrade($val['trade_mode']);
            $val['trade_mode'] = $trade_mode;
          } elseif (isset($value['specification'])) { //规格处理 不需要做处理

          }

          if ($field != 'val')
            $temp[] = (isset($val[$field]) ? $val[$field] : '') . "\t";
          else
            $temp[] = $val[$field];

        }
        $csvDatas[] = $temp;
      }


      $filename = (isset($header[0][0]) ? $header[0][0] : date('Y-m-d')) . '_' . time() . '.csv';
      $filePath = export_csv(['header' => $header, 'title' => $csvHeader, 'data' => $csvDatas, 'footer' => $footer], $filename, TRUE);

      if ($filePath) {

        //保存到数据库
        $isUpdate = $this->csvlistModel->update($createCsvJob['id'], ['status' => 1, 'download_file' => $filePath]);

        if ($isUpdate) return $this->show(['file_path' => $filePath], API_SUCCESS);

      }

      //如果上边的任意一个操作没有成功，则反向插入到队列中
      $this->redisModel->redis->lpush('csvlist', serialize($createCsvJob), FALSE);
    }

    return $this->show([], API_FAILURE);
  }


}