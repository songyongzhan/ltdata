<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class ReportlistService extends BaseService {
  /**
   * 默认取出字段
   * @var array
   */
  protected $field = ['id', 'title', 'utype', 'field_str', 'group_str', 'order_str', 'limit_str', 'having_str', 'date_type', 'status', 'updatetime', 'createtime'];

  /**
   * 获取权限列表 分页
   * @param array $where
   * @param int $page_num <number>
   * @param int $page_size <number>
   */
  public function getList(array $where, $page_num, $page_size) {
    $result = $this->reportlistModel->getListPage($where, $this->field, $page_num, $page_size);
    return $this->show($result);
  }

  /**
   * 添加权限
   * @param string $title <require> 名称不能为空
   * @param string $field_str <require> 查询的字段名不能为空
   * @param string $group_str <require> 分组规则不能为空
   * @return mixed 返回最后插入的id
   */
  public function add($data) {
    $lastInsertId = $this->reportlistModel->insert($data);
    if ($lastInsertId) {
      $data['id'] = $lastInsertId;
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }

  }

  /**
   * 删除一个角色
   * @param int $id <require|number> id
   */
  public function delete($id) {
    $result = $this->reportlistModel->delete($id);
    return $result > 0 ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 获取单个信息
   * @param int $id <require|number> id不能为空|id不是数字
   * @return mixed
   */
  public function getOne($id, $fileds = '*') {
    $result = $this->reportlistModel->getOne($id, $fileds);
    return $result ? $this->show($result) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 分组更新数据
   * @param int $id <require|number> id不能为空|id不是数字
   * @param string $title <require> 名称不能为空
   * @param string $field_str <require> 查询的字段名不能为空
   * @param string $group_str <require> 分组规则不能为空
   * @return array mixed 返回用户数据
   */

  public function update($id, $data) {

    $result = $this->reportlistModel->update($id, $data);
    if ($result) {
      $data['id'] = $id;
    }
    return $result ? $this->show($data) : $this->show([]);
  }















}