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
    $manage_id = $this->_post('manage_id');
    $rules = [
      ['condition' => 'like',
        'key_field' => ['manage_id'],
        'db_field' => ['manage_id']
      ]
    ];
    $data = ['manage_id' => $manage_id];

    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));

    $result = $this->csvlistService->getListPage($where, $page_num, $page_size);
    return $result;
  }

}