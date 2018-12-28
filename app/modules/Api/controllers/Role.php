<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/12/28
 * Time: 11:45
 * Email: 574482856@qq.com
 *
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class RoleController extends ApiBaseController {

  /**
   * 获取分组列表
   * @return mixed
   */
  public function getList() {
    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);
    $title = $this->_post('title', '');

    $rules = [
      ['condition' => 'like',
        'key_field' => ['title'],
        'db_field' => ['title']
      ]
    ];
    $data = ['title' => $title];

    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));

    $result = $this->roleService->getListPage($where, 'id,title,createtime', $page_num, $page_size);
    return $result;
  }

  /**
   * 添加分组
   * @param string $title <POST> 名称
   * @return array
   */
  public function add() {
    $title = $this->_post('title');
    $result = $this->roleService->add($title);
    return $result;
  }

  /**
   * 更新分组名称
   * @param string $title <POST> 名称
   * @param string $id <POST> id
   * @return array
   */
  public function update() {
    $title = $this->_post('title');
    $id = $this->_post('id');
    $result = $this->roleService->update($id, $title);
    return $result;
  }

  /**
   * 得到一个分组信息
   * @param int $id <POST> 用户id
   * @return array|mixed
   */
  public function getOne() {
    $id = $this->_post('id');
    $result = $this->roleService->getOne($id);
    return $result;
  }

  /**
   * 分组删除
   * @param string $id <POST> 数据id ，如果删除多个，请使用逗号分隔
   * @return 删除数据的id
   */
  public function delete() {
    $id = $this->_post('id');
    $result = $this->roleService->delete($id);
    return $result;
  }


}