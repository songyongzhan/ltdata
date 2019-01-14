<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/14
 * Time: 14:45
 * Email: 574482856@qq.com
 *
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class CsvlistController extends ApiBaseController {
  
  /**
   * 用户提交的下载任务列表
   * @return mixed
   */
  public function getListAction() {
    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);


    $result = $this->csvlistService->getListPage([], $page_num, $page_size);
    return $result;
  }

}