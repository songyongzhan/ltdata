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

    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);
    $ppname = $this->_post('ppname', '');
    $pid = $this->_post('pid', '');

    $rules = [
      ['condition' => 'like',
        'key_field' => ['ppname'],
        'db_field' => ['ppname']
      ],
      ['condition' => '=',
        'key_field' => ['pid'],
        'db_field' => ['pid']
      ]
    ];
    $data = ['ppname' => $ppname, 'pid' => $pid];

    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));

    $result = $this->mppinpaiService->getListPage($where, $page_num, $page_size);
    return $result;


  }


}