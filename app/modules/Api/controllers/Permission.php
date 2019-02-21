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
  //id=16&permission=pcr:pf_pricle,stls_pricle|export:40111000
  public function setPermissionAction() {
    $id = $this->_post('id');
    $permission = $this->_post('permission');
    $result = $this->permissionService->setPermission($id, $permission);
    return $result;
  }


  /**
   * 获取当前用户横向的权限
   * 用于显示当前用户可以查询的功能。
   * @return array
   */
  public function getSelfPermissionAction() {
    $result = $this->permissionService->getManagePermission($this->tokenService->manage_id);
    return $result;
  }


}