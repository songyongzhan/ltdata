<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/29
 * Time: 16:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class MppinpaiController extends ApiBaseController {

  /**
   * 获取名片品牌列表
   * @return mixed
   */
  public function getListAction() {

    $result = $this->regionService->getList($where);
    return $result;
  }






}