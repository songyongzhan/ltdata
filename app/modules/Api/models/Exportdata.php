<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/12/28
 * Time: 15:54
 * Email: 574482856@qq.com
 *
 * 数据分析 模型
 */
defined('APP_PATH') OR exit('No direct script access allowed');

class ExportdataModel extends BaseModel {

  public function getExportDataList($where = [], $fileds = [], $pageNum = 1, $pageSize = PAGESIZE, $order = '', $table = NULL) {

    $result = $this->getList($where, $fileds, $pageNum, $pageSize, $order, $table);

    foreach ($result as $key => $value) {


    }
    return $result;
  }


  public function getCiq($id) {

    $result = $this->redisModel->redis->hGet('ciq', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'ciq');

      if ($data) {
        $this->redisModel->redis->hSet('ciq', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: '';
  }


  public function getCountry($id) {

    $result = $this->redisModel->redis->hGet('country', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'country');

      if ($data) {
        $this->redisModel->redis->hSet('country', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: '';
  }


  public function getTrade($id) {

    $result = $this->redisModel->redis->hGet('trade', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'trade');

      if ($data) {
        $this->redisModel->redis->hSet('trade', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: '';
  }

  public function getTransport($id) {

    $result = $this->redisModel->redis->hGet('transport', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'transport');

      if ($data) {
        $this->redisModel->redis->hSet('transport', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: '';
  }


  public function getMade($id) {

    $result = $this->redisModel->redis->hGet('made', $id);

    if (!$result) {
      $data = $this->getOne([
        getWhereCondition('id', $id)
      ], ['id', 'title'], 'made');

      if ($data) {
        $this->redisModel->redis->hSet('made', $id, $data['title']);
        $result = $data['title'];
      }
    }

    return $result ?: '';
  }


}
