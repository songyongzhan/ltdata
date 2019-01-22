<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/21
 * Time: 22:45
 * Email: 574482856@qq.com
 *
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class YdylareaController extends ApiBaseController {

  /**
   * 获取列表
   * @return mixed
   */
  public function getListAction() {
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

    $result = $this->ydylareaService->getListPage($where, $page_num, $page_size);
    return $result;
  }

  /**
   * 获取区域国家
   * @return array
   * @throws InvalideException
   */
  public function getChinaListAction() {
    $ydylarea = $this->_post('ydylarea');
    $result = $this->ydylareaService->getChinaList($ydylarea);
    return $result;
  }

  /**
   * 添加贸易方式
   * @param string $title <POST> 名称
   * @return array
   */
  public function addAction() {
    $title = $this->_post('title');
    $result = $this->ydylareaService->add($title);
    return $result;
  }

  /**
   * 更新贸易方式
   * @param string $title <POST> 名称
   * @param string $id <POST> id
   * @return array
   */
  public function updateAction() {
    $title = $this->_post('title');
    $id = $this->_post('id');
    $result = $this->ydylareaService->update($id, $title);
    return $result;
  }

  /**
   * 得到一个贸易方式信息
   * @param int $id <POST> 用户id
   * @return array|mixed
   */
  public function getOneAction() {
    $id = $this->_post('id');
    $result = $this->ydylareaService->getOne($id);
    return $result;
  }

  /**
   * 贸易方式删除
   * @param string $id <POST> 数据id ，如果删除多个，请使用逗号分隔
   * @return 删除数据的id
   */
  public function deleteAction() {
    $id = $this->_post('id');
    $result = $this->ydylareaService->delete($id);
    return $result;
  }


}