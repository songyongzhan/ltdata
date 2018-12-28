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
    'total_amount',
    'weight',
    'price_amount',
    'trade_mode',
    'transport_mode',
    'madein',
    'shipper'
  ];


  /**
   * php index.php index/data2db/import
   *
   * @param string $date
   */
  public function importAction() {

    var_dump($this->exportdataModel);
    exit;

    Yaf_Loader::import(APP_PATH . '/app/helpers/helperCsv.php');

    foreach (glob(APP_PATH . '/data/uploads/csv/*.csv') as $file) {


      //debugMessage("$file 开始自动导入...");
      $csv = new helperCsv($file, 3, FALSE);



      var_dump($this->exportdataModel);
      exit;

      $exportdataModel->startTransaction();
      try {
        foreach ($csv as $row => $data) {
          if (!$data) continue;

          $this->format($data);
          $result = $exportdataModel->import($data);
          if (!$result) {
            debugMessage("$file 第 $row 行，导入出错...");

            break;
          }
        }
        $exportdataModel->commit();
        debugMessage("$file 导入成功...");
        $this->mvFiletoDist($file);

      } finally {
        $exportdataModel->_transaction_status_check();
      }


      /*foreach ($csv as $row => $data) {
        if (!$data) continue;

        $this->format($data);
        var_dump($data);

      }*/

      //$this->mvFiletoDist($file);
      //debugMessage("$file 导入成功...");

    }


    die();
  }

  /**
   *
   */
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

    //$data['export_data'] = strtotime();
    $data['export_date'] = strtotime(str_replace('//', '-', $data['export_date']));
    $data['specification'] = $data['specification_title'];
    $data['status'] = 1;

    //正则处理

    //'specification',
    //'status'


  }


}
