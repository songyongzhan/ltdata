<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/21
 * Time: 22:45
 * Email: 574482856@qq.com
 *
 * 一带一路 国家模型
 */
defined('APP_PATH') OR exit('No direct script access allowed');

class YdylareachinaModel extends BaseModel {


  protected $autoaddtime = FALSE;

  public function createTemporaryTable($tableName) {

    $sql = 'CREATE TEMPORARY TABLE ' . $this->prefix . $tableName . '(china_id int not null,status tinyint default 1) default charset utf8;';

    return $this->exec($sql);

  }

  public function getChinaList($ydylarea) {

  }


}