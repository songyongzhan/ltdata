<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/14
 * Time: 11:40
 * Email: 574482856@qq.com
 *
 * 文件csv下载 模型
 */
defined('APP_PATH') OR exit('No direct script access allowed');

class CsvlistModel extends BaseModel {


  public function readCsvListFromRedis() {

    if ($result = $this->redisModel->redis->rpop('csvlist')) {
      return unserialize($result);
    }

    return FALSE;
  }


}