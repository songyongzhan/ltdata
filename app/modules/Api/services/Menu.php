<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: songyongzhan@qianbao.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class MenuService extends BaseService {

  const FIELD = ['id', 'title', 'pid', 'url', 'relation_url', 'ext', 'type_id', 'status', 'sort_id', 'updatetime', 'createtime'];


  /**
   * 获取栏目列表
   * @param array $where 搜索条件
   * @param int useType 用途 如果是1 显示栏目列表用于修改 ,不传值 或传0 用于左侧栏目显示
   */
  public function getList($where, $useType) {
    //当前用户权限及群组用户
    if ($this->tokenService->isadmin) {
      $where = [];
      $useType == 0 && array_push($where, getWhereCondition('type_id', '1'));
      $result = $this->menuModel->getList($where, self::FIELD);
    } else {
      //不是超级管理员
      $manageRoleAccess = $this->manageModel->getOne($this->tokenService->manage_id, ['role_access']);
      if (!$manageRoleAccess['role_access']) {
        $manageResult = $this->getAppointMenuList($manageRoleAccess['role_access'], $useType);
        isset($manageResult['result']) && $manageResult = $manageResult['result'];
      }
      $where = [];
      $useType == 0 && array_push($where, getWhereCondition('type_id', '1'));
      //获取分组是否有权限
      $Roleresult = $this->manage_roleModel->getRoleGroupAccess($this->tokenService->manage_id, $useType, self::FIELD);
      $result = array_merge_recursive($manageResult, $Roleresult);
    }
    return menu_group_list($result);
  }

  /**
   * 添加栏目
   * @param string $title <require> 栏目标题
   * @param int $pid <require|number> 父级id
   * @param string $url <require> 栏目url地址
   * @param string $ext 扩展信息
   * @return mixed
   */
  public function add($data) {

    if ($data['type_id'] == 2 && $data['pid'] == 0) showApiException('方法不能放在顶级菜单下', StatusCode::PARAMS_ERROR);

    $lastInsertId = $this->menuModel->insert($data);
    if ($lastInsertId) {
      $data['id'] = $lastInsertId;
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }
  }

  /**
   * 栏目更新
   * @param int $id <require|number> 栏目ID
   * @param array $data 需要更新的数据
   * @return mixed
   * @throws Exception
   */
  public function update($id, $data) {
    if (empty($data)) {
      showApiException('请求参数错误', StatusCode::PARAMS_ERROR);
    }

    if ($data['type_id'] == 2 && $data['pid'] == 0) showApiException('方法不能放在顶级菜单下', StatusCode::PARAMS_ERROR);
    $result = $this->menuModel->update($id, $data);
    $result && $data['id'] = $id;
    return $result ? $this->show($data) : $this->show([]);
  }

  /**
   * 获取单个栏目
   * @param int $id <require|number> 栏目id
   * @param string $field 获取栏目的信息
   * @return mixed
   */
  public function getOne($id, $field = self::FIELD) {
    $result = $this->menuModel->getOne($id, $field);
    return $result ? $this->show($result) : $this->show([]);
  }

  /**
   * 获取指定栏目
   * @param string $menu_ids 栏目ids 如果为空 获取全部  如果指定id 根据id返回
   * @param null $useType 获取menu的用途
   * @return mixed
   */
  public function getAppointMenuList($menu_ids, $useType = NULL) {
    if (is_string($menu_ids))
      $menu_ids = explode(',', $menu_ids);

    if (!$menu_ids)
      return $this->show([]);

    $where = [
      getWhereCondition('id', $menu_ids, 'in')
    ];

    if (!is_null($useType) && $useType == 0)
      array_push($where, getWhereCondition('type_id', '1'));

    $result = $this->menuModel->getList($where, self::FIELD);
    return $this->show($result);
  }

  /**
   * 删除栏目
   * @param int $id <require> 栏目ids 多个栏目使用,分割
   * @return mixed
   */
  public function delete($id) {
    $result = $this->menuModel->delete($id);
    return $result ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }


  /**
   * 批量排序
   * @param string $sortStr <require> 更新数据
   * @return array
   */
  public function batchSort($sortStr) {
    $sort = explode('|', trim($sortStr, '|'));
    $data = [];
    foreach ($sort as $key => $val) {
      list($id, $sortId) = explode(':', $val);
      $data[] = [
        'id' => $id,
        'sort_id' => $sortId
      ];
    }
    $result = $this->menuModel->batchSort($data);
    return $this->show(['row' => $result]);
  }


}