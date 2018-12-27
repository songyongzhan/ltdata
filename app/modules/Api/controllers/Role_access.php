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

class Role_accessController extends ApiBaseController {


  /**
   * 获取到对应的栏目列表
   * 无需传递参数，直接调用
   */
  public function getAccessMenu() {
    $result = $this->role_accessService->get_access_menu();
    return $result;
  }

  /**
   * 显示栏目列表 用于添加子类方法使用的 type为1的
   * 用于 展示页面使用的
   * (显示页面分配权限使用)
   * @return mixed
   */
  public function viewRoleAccess() {
    $manage_id = $this->_post('manage_id');
    $result = $this->role_accessService->viewRoleAccess($manage_id);
    return $result;
  }

  /**
   * 获取用户可以操作的权限 判断权限使用
   * $key=>$menu_id   $val=>url
   * @return mixed
   */
  public function getRoleAccess() {
    $type_id = 2; //方法
    $result = $this->role_accessService->get_access_menu($type_id);
    return $result;
  }

  /**
   * 验证一个url是否可以访问  传递栏目 id  或 url 任意一项都可以
   * @name 验证url 权限验证
   * @param string $url <POST> 需要验证的url
   * @return mixed
   */
  public function checkUrl() {
    $url = $this->_post('url');
    $result = $this->role_accessService->checkUrl($url);
    return $result;
  }

  /**
   * 权限更新
   * @name 更新组权限
   * @param string $menu_ids <POST> 更新的栏目id  多个请使用，进行分隔
   * @param int $role_id <POST> 分组id
   * @return mixed 成功或false
   */
  public function update() {
    $menu_ids = $this->_post('menu_ids');
    $role_id = $this->_post('role_id');
    $result = $this->role_accessService->update($role_id, $menu_ids);
    return $result;
  }

}