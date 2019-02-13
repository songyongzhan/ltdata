<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

/**
 * 横向权限控制
 * Class TransportService
 */
class PermissionService extends BaseService {

  protected $field = ['manage_id', 'permission'];

  /**
   * 获取当前用户的权限
   * @param int $id <require|number> 用户id不能为空
   */
  public function getPermission($id) {

    $viewData = $this->permissionModel->viewPermission();

    $permissionResult = $this->getManagePermission($id);
    if (isset($permissionResult['result']) && $permissionResult['result'])
      $permissionResult = $permissionResult['result'];

    return $this->show([
      'view_data' => $viewData,
      'permission' => $permissionResult
    ]);
  }

  /**
   * 获取用户横向权限
   * @param int $id <require|number> 用户id不能为空
   * @return array
   */
  public function getManagePermission($id) {
    $result = $this->permissionModel->getPermission($id);
    $permissionData = [];
    if ($result['permission'] != '') {
      $permission = explode('|', trim($result['permission'], '|'));
      foreach ($permission as $key => $val) {
        list($pdKey, $pdVal) = explode(':', $val);
        $permissionData[$pdKey] = explode(',', trim($pdVal, ','));
      }
    }
    $result['permission_data'] = $permissionData;

    return $this->show($result);
  }


  /**
   * 设置用户权限
   * @param int $id <require|number> 用户id不能为空
   * @param string $permission 权限必须设置
   * @return array
   */
  public function setPermission($id, $permission) {

    $result = $this->permissionModel->setPermission($id, $permission);
    return $result ? $this->show($result) : $this->show([], API_FAILURE);
  }


}