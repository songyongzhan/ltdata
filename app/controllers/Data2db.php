<?php

/**
 *
 * csv数据到数据库
 * Class Data2dbController
 */
class Data2dbController extends BaseController {

  const FIELDS = [
    'export_date',
    'export_ciq',
    'dist_country',
    'goods_code',
    'specification_title',
    'transaction_mode',
    'total_amount',
    'weight',
    'price_amount',
    'trade_mode',
    'transport_mode',
    'madein',
    'shipper'
  ];


  const  SEPCIFICATION_PATTERN = '/([0-9\/]+)?([0-9\.]{1,})?R[0-9\.]{1,}[a-z]?/im';


  public function testAction() {

    $data = [
      'export_date' => 76543,
      'export_ciq' => 14,
      'dist_country' => 17,
      'goods_code' => 40112000,
      'specification_title' => '车|12.5-18|R-4|人|12.5-18|品牌：华鲁车|12.5-18|R-4|人|12.5-18|品牌：华鲁车|12.5-18|R-4|人|12.5-18|品牌：华鲁车|12.5-18|R-4|人|12.5-18|品牌：华鲁车|12.5-18|R-4|人|12.5-18|品牌：华鲁车|12.5-18|R-4|人|12.5-18|品牌：华鲁车|12.5-18|R-4|人|12.5-18|品牌：华鲁',
      'transaction_mode' => 'FOB',
      'total_amount' => 1561,
      'weight' => 192,
      'price_amount' => 8.1,
      'trade_mode' => 60,
      'transport_mode' => 55,
      'madein' => 24,
      'shipper' => '北京城建集团有限责任公司',
      'specification' => '',
      'status' => 1,
      'createtime' => 1546669680,
      'updatetime' => 1546669680
    ];


    $this->cliExportdataModel->startTransaction();
    $result = $this->cliExportdataModel->import($data);

    if ($result) {

      var_dump($result);

      $this->cliExportdataModel->commit();
    } else {
      var_dump($result);
      $this->cliExportdataModel->_transaction_status_check();
    }



    exit;


  }


  /**
   * php index.php index/data2db/import
   * HTTP_ENV=develop php index.php index/data2db/import
   *
   * @param string $date
   */
  public function importAction() {


    Yaf_Loader::import(APP_PATH . '/app/helpers/helperCsv.php');

    foreach (glob(APP_PATH . '/data/uploads/csv/*.csv') as $file) {

      debugMessage(" $file 开始自动导入...");

      $csv = new helperCsv($file, 0, FALSE);

      $this->cliExportdataModel->startTransaction();
      try {
        $importFlag = TRUE;
        foreach ($csv as $row => $data) {
          if (!$data) continue;

          $this->format($data);

          $result = $this->cliExportdataModel->import($data);
          if (!$result) {
            $importFlag = FALSE;
            debugMessage("$file 第 $row 行，导入出错...");
            break;
          }
        }
        if ($importFlag) {
          $this->cliExportdataModel->commit();
          debugMessage("$file 导入成功...");
          $this->mvFiletoDist($file);
        } else
          debugMessage("$file 导入失败...");
      } catch (Exception $e) {

        debugMessage('Cli import error' . $e->getMessage() . ' code ' . $e->getCode() . var_export($e->getTrace(), TRUE));

      } finally {
        $this->cliExportdataModel->_transaction_status_check();
      }
    }

    die();
  }


  private function mvFiletoDist($file) {
    $distPath = APP_PATH . '/data/uploads/csv/dist/';
    if (!is_writeable($distPath))
      debugMessage(sprintf('移动文件失败  ===目录 %s 不可写', $distPath));
    else {
      copy($file, $distPath . basename($file)); //拷贝到新目录
      //删除旧目录下的文件
      if (!unlink($file))
        debugMessage(sprintf('%s  所在目录不可操作 ', $file));
    }
  }

  private function format(&$data) {
    $data = convert_encodeing($data, 'gbk', 'utf-8');

    $data = array_combine(self::FIELDS, $data);

    //从这里进行一系列替换  文字到数字的转换

    $data['export_ciq'] = $this->cliExportdataModel->ciq($data['export_ciq']);
    $data['dist_country'] = $this->cliExportdataModel->country($data['dist_country']);
    $data['trade_mode'] = $this->cliExportdataModel->trade($data['trade_mode']);
    $data['transport_mode'] = $this->cliExportdataModel->transport($data['transport_mode']);
    $data['madein'] = $this->cliExportdataModel->madein($data['madein']);

    $data['specification_title'] = mb_strlen($data['specification_title'], 'utf-8') > 200 ? mb_substr($data['specification_title'], 0, 200) : $data['specification_title'];

    //$data['export_data'] = strtotime();
    $data['export_date'] = strtotime(str_replace('//', '-', $data['export_date']));

    //匹配一个正则表达式规格，存放于数据库 用于模糊搜索
    if (preg_match_all(self::SEPCIFICATION_PATTERN, $data['specification_title'], $result)) {
      $data['specification'] = isset($result[0][0]) ? $result[0][0] : '';
    } else
      $data['specification'] = '';

    $data['status'] = 1;
  }


}
