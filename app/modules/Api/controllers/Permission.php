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

class PermissionController extends ApiBaseController {


  /**
   * 获取当前用户的横向权限
   * @param string $id <POST> 用户id
   * @return array
   */
  public function getPermissionAction() {
    $id = $this->_post('id');
    $result = $this->permissionService->getPermission($id);
    return $result;
  }

  /**
   * 设置用户权限
   * @param string $id <POST> id
   * @param string $permission <POST> 权限
   * @return array
   */
  public function setPermissionAction() {
    $id = $this->_post('id');
    $permission = $this->_post('permission');
    $result = $this->permissionervice->setPermission($id, $permission);
    return $result;
  }


}