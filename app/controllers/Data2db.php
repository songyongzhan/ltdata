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
    'price_amount',
    'total_amount',
    'weight',
    'trade_mode',
    'transport_mode',
    'madein',
    'shipper'
  ];


  const  SEPCIFICATION_PATTERN = '/([0-9\/]+)?([0-9\.]{1,})?R[0-9\.]{1,}[a-z]?/im';


  /**
   * HTTP_ENV=develop php index.php index/data2db/initRedisData
   */

  // */30 * * * * php index.php index/data2db/initRedisData
  public function initRedisDataAction() {

    $this->cliExportdataModel->initRedisData(
      [
        'ciq',
        'country',
        'made',
        'trade',
        'transport'
      ]
    );


  }


  /**
   * php index.php index/data2db/import
   * HTTP_ENV=develop php index.php index/data2db/import
   *
   * @param string $date
   */

  // */30 * * * * php index.php index/data2db/import
  // */30 * * * * php index.php index/data2db/importUnTransaction
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


  /**
   * 没有事务的情况下执行操作
   * @throws Exception

   **
   * php index.php index/data2db/import
   * HTTP_ENV=develop php index.php index/data2db/importUnTransaction
   *

   */
  public function importUnTransactionAction() {

    Yaf_Loader::import(APP_PATH . '/app/helpers/helperCsv.php');

    foreach (glob(APP_PATH . '/data/uploads/csv/*.csv') as $file) {

      debugMessage(" $file 开始自动导入...");


      try {
        $csv = new helperCsv($file, 0, FALSE);
        $multi_time = time();

        //$importFlag = TRUE;
        $multiData = [];
        foreach ($csv as $row => $data) {
          if (!$data) continue;
          $this->format($data);

          if (count($multiData) < 1000) {

            $data['updatetime'] = time();
            $data['createtime'] = time();
            $data['multidata'] = $multi_time;

            $multiData[] = $data;

          } else {
            $result = $this->cliExportdataModel->importMulti($multiData);
            if (!$result) {
              debugMessage("$file 导入出错...");
              $this->cliExportdataModel->delMulti($multi_time);
              break;
            }
            $multiData = [];
          }
        }
        //执行退出后，如果multiData 还有数据，则再次添加
        if (count($multiData)) {
          $result = $this->cliExportdataModel->importMulti($multiData);
          if (!$result) {
            debugMessage("$file 导入出错...");
            $this->cliExportdataModel->delMulti($multi_time);
            break;
          }
        }

        /*if ($importFlag) {
          debugMessage("$file 导入成功...");

        } else {

          debugMessage("$file 导入失败...");
        }*/

        $this->mvFiletoDist($file);

      } catch (Exception $e) {

        debugMessage('Cli import error' . $e->getMessage() . ' code ' . $e->getCode() . var_export($e->getTrace(), TRUE));
        $this->cliExportdataModel->delMulti($multi_time);
      }
    }

    die();
  }

  private function mvFiletoDist($file) {
    $distPath = APP_PATH . '/data/uploads/csv/dist/';

    if (!is_dir($distPath))
      mkdir($distPath, 0755, TRUE);

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

    if(strtolower($data['specification_title'])=='null'){
      $data['specification_title'] = '';
    }else{
      $data['specification_title'] = mb_strlen($data['specification_title'], 'utf-8') > 200 ? mb_substr($data['specification_title'], 0, 200) : $data['specification_title'];
    }


    //$data['export_data'] = strtotime();
    $data['export_date'] = strtotime(str_replace('//', '-', $data['export_date']));
    $data['export_year'] = date('Y', $data['export_date']);
    $data['export_month'] = date('m', $data['export_date']);

    $data['price_amount'] = sprintf("%.2f",substr(sprintf("%.3f", $data['price_amount']), 0, -1));
    $data['total_amount'] = sprintf("%.2f",substr(sprintf("%.3f", $data['total_amount']), 0, -1));


    //匹配一个正则表达式规格，存放于数据库 用于模糊搜索
    if (preg_match_all(self::SEPCIFICATION_PATTERN, $data['specification_title'], $result)) {
      $data['specification'] = isset($result[0][0]) ? $result[0][0] : '';
    } else
      $data['specification'] = '';

    $data['status'] = 1;
  }


}
