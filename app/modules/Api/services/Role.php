<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: songyongzhan@qianbao.com
 */

class RoleService extends BaseService {

  /**
   * 获取权限列表 分页
   * @param array $where
   * @param int $page_num <numeric>
   * @param int $page_size <numeric>
   */
  public function getListPage(array $where, $field = '*', $page_num, $page_size) {
    $where['platform_id'] = $this->Token->platform_id;
    //判断是不是管理员 如果不是，则访问其有权限访问的功能：
    //稍后完成
    $result = $this->Role_model->getListPage($where, $field, $page_num, $page_size, [], 'createtime desc');
    return $this->show($result);
  }

  /**
   * 获取栏目列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getList(array $where, $field = '*') {
    $where['platform_id'] = $this->Token->platform_id;
    //判断是不是管理员 如果不是，则访问其有权限访问的功能：
    //稍后完成
    $result = $this->Role_model->getList($where, $field);
    return $this->show($result);
  }

  /**
   * 添加权限
   * @param string $title <required> 分组名称
   * @return mixed 返回最后插入的id
   */
  public function add($title) {
    $data = [
      'title' => $title,
      'platform_id' => $this->Token->platform_id,
      'createtime' => time()
    ];
    $lastInsertId = $this->Role_model->add($data);
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
    $result = $this->Role_model->delete($id, platform_where());
    if ($result['type'] == 'search' && $result['row'] > 0) {
      showApiException('请先删除此分组下的用户，再删除分组', StatusCode::HAS_MANAGE);
    } else {
      return $result['row'] > 0 ? $this->show(['row' => $result['row'], 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
    }
  }

  /**
   * 获取单个信息
   * @param int $id <required> 分组id
   * @param string $fileds
   * @return mixed
   */
  public function getOne($id, $fileds = '*') {
    $result = $this->Role_model->getOne($id, $fileds, platform_where());
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
      'title' => $title,
      'platform_id' => $this->Token->platform_id
    ];
    $result = $this->Role_model->update($id, $data, platform_where());
    if ($result) {
      $data['id'] = $id;
    }
    return $result ? $this->show($data) : $this->show([]);
  }

}