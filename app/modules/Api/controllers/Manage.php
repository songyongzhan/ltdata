<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 9:44
 * Email: songyongzhan@qianbao.com
 */

class ManageController extends ApiBaseController {

  /**
   * 添加后台用户
   * @param string $username <POST> 登录账户
   * @param string $password <POST> 登录密码
   * @param string $re_password <POST> 确认密码
   * @param string $department <POST> 部门
   * @param string $ext <POST> 扩展信息
   * @param string $remarks <POST> 备注
   * @param string $fullname <POST> 姓名
   * @param string $status <POST> 状态
   * @return array 返回用户注册信息 否则返回空数组
   */
  public function addAction() {
    $username = $this->_post('username');
    $password = $this->_post('password', '');
    $re_password = $this->_post('re_password', '');
    $department = $this->_post('department', '');
    $fullname = $this->_post('fullname', '');
    $ext = $this->_post('ext', '');
    $remarks = $this->_post('remarks', '');
    $status = $this->_post('status', 0);
    $data = [
      'username' => $username,
      'password' => $password,
      're_password' => $re_password,
      'department' => $department,
      'ext' => $ext,
      'fullname' => $fullname,
      'remarks' => $remarks,
      'status' => $status
    ];
    $result = $this->manageService->add($data);
    return $result;
  }


  /**
   * 更新用户信息
   * @param string $username <POST> 登录账户
   * @param string $password <POST> 登录密码
   * @param string $re_password <POST> 确认密码
   * @param string $department <POST> 部门
   * @param string $ext <POST> 扩展信息
   * @param string $remarks <POST> 备注
   * @param string $fullname <POST> 姓名
   * @param string $status <POST> 状态
   * @param string $id <POST> id
   * @return array
   */
  public function updateAction() {
    $username = $this->_post('username');
    $password = $this->_post('password', '');
    $re_password = $this->_post('re_password', '');
    $department = $this->_post('department', '');
    $ext = $this->_post('ext', '');
    $remarks = $this->_post('remarks', '');
    $fullname = $this->_post('fullname', '');
    $status = $this->_post('status', '');
    $id = $this->_post('id');
    $data = [
      'username' => $username,
      'password' => $password,
      're_password' => $re_password,
      'department' => $department,
      'ext' => $ext,
      'fullname' => $fullname,
      'remarks' => $remarks,
      'status' => $status
    ];
    $result = $this->manageService->update($id, $data);
    return $result;
  }

  /**
   * 更新用户权限
   * @name 更新用户权限
   * @param int $id <POST> 用户id
   * @param int $role_access <POST> 权限id集合
   * @return mixed
   */
  public function updateManageAccessAction() {
    $id = $this->_post('id');
    $role_access = $this->_post('role_access');
    $result = $this->manageService->updateManageAccess($id, $role_access);
    return $result;
  }

  /**
   * 更新用户权限所属分组
   * @name 更新用户属组
   * @param int $id <POST> 用户id
   * @param string $role_ids <POST> 管理权限
   * @return mixed
   */
  public function updateManageRoleAction() {
    $id = $this->_post('id');
    $role_ids = $this->_post('role_ids');
    $result = $this->manageService->updateManageRole($id, $role_ids);
    return $result;
  }

  /**
   * 获取当前用户拥有的权限
   * @name 获取当前用户权限
   * @param int $id <POST> 用户id
   */
  public function getManageRoleAction() {
    $id = $this->_post('id');
    $result = $this->manageService->getManageRole($id);
    return $result;
  }

  /**
   * 得到一个用户信息
   * @param int $id <POST> 用户id
   * @return array|mixed
   */
  public function getOneAction() {
    $id = $this->_post('id');
    $result = $this->manageService->getOne($id);
    return $result;
  }

  /**
   * 分页
   * @param int $page_num <POST> 分页
   * @param int $page_size <POST> 每页显示几条
   * @param string $username <POST> 根据用户名模糊搜索
   * @param string $fullname <POST> 根据用户名模糊搜索
   * @param string $department <POST> 根据用户名模糊搜索
   * @param [] $where <POST> 查询条件
   * @return array
   */
  public function getListAction() {
    $pageNo = $this->_post('page_num', 1);
    $pageSize = $this->_post('page_size', PAGE_SIZE_DEFAULT);

    //提供搜索
    $username = $this->_post('username');
    $fullname = $this->_post('fullname');
    $department = $this->_post('department');

    $rules = [
      ['condition' => 'like',
        'key_field' => ['username', 'fullname', 'department'],
        'db_field' => ['username', 'fullname', 'department']
      ]
    ];
    $data = [
      'username' => $username,
      'fullname' => $fullname,
      'department' => $department,
    ];

    $where = $this->where($rules, array_filter($data));
    $result = $this->manageService->getList($where, $pageNo, $pageSize);
    return $result;
  }

  /**
   * 切换平台
   * @name 切换平台
   * @param int $platformId <POST> 平台id
   * @return mixed
   */
  public function changePlatformAction() {
    $platformId = $this->_post('platform_id');
    $result = $this->manageService->changePlatform($platformId);
    return $result;
  }

  /**
   * 用户登录
   * @param string $username <POST> 用户名
   * @param string $password <POST> 密码
   * @param int $platform_id <POST> 平台id
   * @return array
   */
  public function loginAction() {
    $username = $this->_post('username');
    $password = $this->_post('password');
    $platform_id = $this->_post('platform_id');
    $platform_id && $platform_id = intval($platform_id);
    $result = $this->manageService->login($username, $password, $platform_id);
    return $result;
  }

  /**
   * 根据输入账号 域控 获取用户真实姓名
   * @name 获取域控用户信息
   * @param string $username <POST>
   * @return mixed
   * @throws Exception
   */
  public function getLdapUserinfoAction() {
    $username = $this->_post('username');
    $result = $this->manageService->getLdapUserinfo($username);
    return $result;
  }

  /**
   * 设置用户是否特殊用户
   * @name 设置用户特殊标识
   * @param int $id <POST> 用户id
   * @param int $isspecial <POST> 特殊用户标识
   * @return mixed
   */
  public function setSpecialAction() {
    $id = $this->_post('id');
    $isspecial = $this->_post('isspecial');
    $result = $this->manageService->setSpecial($id, $isspecial);
    return $result;
  }

  /**
   * 显示ip到客户端 供添加ip 访问私有平台使用
   * @name 显示ip地址
   */
  public function getClientIpAction() {
    $result = $this->manageService->getClientIp();
    return $result;
  }

  //test
  public function aaAction() {

    exit;
  }





  /**
   * 使用token换取用户详细信息
   * @name token换取用户信息
   * @return array
   */
  public function getUserInfoAction() {
    $result = $this->manageService->getUserInfo();
    return $result;
  }

  /**
   * @name 修改用户密码
   * @param int $id <POST> 用户id
   * @param string $oldPassword <POST> 旧密码
   * @param string $newPassword <POST> 新密码
   * @param string $rePassword <POST> 确认密码
   * @return mixed
   */
  public function passwordAction() {
    $id = $this->_post('id');
    $oldPassword = $this->_post('oldPassword');
    $newPassword = $this->_post('newPassword');
    $rePassword = $this->_post('rePassword');
    return $this->manageService->password($id, $oldPassword, $newPassword, $rePassword);

  }

  /**
   * 用户退出
   * @return mixed
   */
  public function logoutAction() {
    $result = $this->manageService->logout();
    return $result;
  }

  /**
   * 用户删除
   * @param string $id <POST> 数据id ，如果删除多个，请使用逗号分隔
   * @return 删除数据的id
   */
  public function deleteAction() {
    $ids = $this->_post('id');
    $result = $this->manageService->delete($ids);
    return $result;
  }


}