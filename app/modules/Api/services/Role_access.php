<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class Role_accessService extends BaseService {


  /**
   * 更新用户群组权限
   *
   * @param int $id <require|number> 用户id
   * @param string $role_ids <require> 权限信息
   */
  public function updateManageRole($manage_id, $role_ids) {

    $role_ids = explode(',', trim($role_ids, ','));
    $result = $this->role_accessModel->updateManageRole($manage_id, $role_ids);
    return $result ? $this->show($result) : $this->show([]);

  }

  /**
   * 更新分组权限
   * @param $role_id <require|number> 用户id
   * @param $menu_ids <require> 栏目ids
   */
  public function updateRoleAccess($role_id, $menu_ids) {
    $menu_ids = explode(',', trim($menu_ids, ','));
    $result = $this->role_accessModel->updateRoleAccess($role_id, $menu_ids);
    return $result ? $this->show($result) : $this->show([]);
  }

  /**
   * 验证当前用户是否有权限登录
   * @param $url
   */
  public function checkUrl($url) {

    $roleResult = $this->menuService->getList([], 1);
    $roleResult = $roleResult['result'];

    $checkUrlData = [];
    foreach ($roleResult as $val) {
      $checkUrlData[$val['id']] = $val['url'];
    }

    $checkUrlData = array_change_value_case($checkUrlData);

    $is_have = FALSE;

    if (isUInt($url) && $url > 0) {
      //如果是扩展权限这里不能验证，需要使用url去判断
      $is_have = array_key_exists(intval($url), $checkUrlData);
    } else {
      $url = strtolower($url);
      $is_have = in_array($url, $checkUrlData);
    }

    return $is_have ? $this->show(['success' => 1]) : $this->show(['success' => 0]);

  }


}