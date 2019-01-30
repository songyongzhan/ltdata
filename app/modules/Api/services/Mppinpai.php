<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/29
 * Time: 16:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');


class MppinpaiService extends BaseService {

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getList() {

    $mppinpai = $this->redisModel->redis->hGetAll('mppinpai');
    $list = [];
    foreach ($mppinpai as $key => $val) {
      $list[] = [
        'id' => $key,
        'text' => $val
      ];
    }
    return $this->show(['mppinpai' => $list]);
  }


  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getListPage(array $where, $page_num, $page_size) {
    $result = $this->mppinpaiModel->getListPage($where, $this->field, $page_num, $page_size);
    return $this->show($result);
  }


}