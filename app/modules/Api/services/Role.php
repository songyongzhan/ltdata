<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: songyongzhan@qianbao.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class RoleService extends BaseService {

  /**
   * 获取权限列表 分页
   * @param array $where
   * @param int $page_num <numeric>
   * @param int $page_size <numeric>
   */
  public function getListPage(array $where, $field = '*', $page_num, $page_size) {
    $result = $this->roleModel->getListPage($where, $field, $page_num, $page_size, [], 'createtime desc');
    return $this->show($result);
  }

  /**
   * 获取栏目列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getList(array $where, $field = '*') {
    $result = $this->roleModel->getList($where, $field);
    return $this->show($result);
  }

  /**
   * 添加权限
   * @param string $title <required> 分组名称
   * @return mixed 返回最后插入的id
   */
  public function add($title) {
    $data = [
      'title' => $title
    ];
    $lastInsertId = $this->roleModel->insert($data);
    if ($lastInsertId) {
      $data['id'] = $lastInsertId;
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }

  }

  /**
   * 删除一个角色
   * @param int $id <required> ID
   */
  public function delete($id) {
    $result = $this->roleModel->delete($id);
    if (isset($result['type'])) {
      showApiException('请先删除此分组下的用户，再删除分组', StatusCode::HAS_MANAGE);
    } else {
      return $result > 0 ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
    }
  }

  /**
   * 获取单个信息
   * @param int $id <required> 分组id
   * @param string $fileds
   * @return mixed
   */
  public function getOne($id, $fileds = '*') {
    $result = $this->roleModel->getOne($id, $fileds, platform_where());
    return $result ? $this->show($result) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 分组更新数据
   * @param int $id <required|numeric> ID
   * @param string $title <required> 名称
   * @return array mixed 返回用户数据
   */

  public function update($id, $title) {
    $data = [
      'title' => $title
    ];
    $result = $this->roleModel->update($id, $data, platform_where());
    if ($result) {
      $data['id'] = $id;
    }
    return $result ? $this->show($data) : $this->show([]);
  }

}