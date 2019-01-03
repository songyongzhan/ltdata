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
    $email = $this->_post('email', '');
    $mobile = $this->_post('mobile', '');
    $status = $this->_post('status', '');
    $id = $this->_post('id');
    $data = [
      'username' => $username,
      'password' => $password,
      're_password' => $re_password,
      'department' => $department,
      'ext' => $ext,
      'email' => $email,
      'mobile' => $mobile,
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
    $result = $this->role_accessService->updateManageRole($id, $role_ids);
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
    $pageSize = $this->_post('page_size', PAGESIZE);

    //提供搜索
    $username = $this->_post('username');
    $fullname = $this->_post('fullname');
    $department = $this->_post('department');
    $mobile = $this->_post('mobile');
    $email = $this->_post('email');

    $rules = [
      ['condition' => 'like',
        'key_field' => ['username', 'fullname', 'department', 'mobile', 'email'],
        'db_field' => ['username', 'fullname', 'department', 'mobile', 'email']
      ]
    ];
    $data = [
      'username' => $username,
      'fullname' => $fullname,
      'department' => $department,
      'email' => $email,
      'mobile' => $mobile,
    ];

    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));
    $result = $this->manageService->getList($where, $pageNo, $pageSize);
    return $result;
  }


  /**
   * 用户登录
   * @param string $username <POST> 用户名
   * @param string $password <POST> 密码
   * @param string $code <POST> 验证码
   * @return array
   */
  public function loginAction() {
    $username = $this->_post('username');
    $password = $this->_post('password');
    $code = $this->_post('code');
    $result = $this->manageService->login($username, $password, $code);
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


  /**
   * 获取客户端ip地址
   * @return array
   */
  public function getCodeAction() {
    $ip = $this->_post('ip', '');
    $result = $this->manageService->getCode($ip);
    return $result;
  }

  public function checkCodeAction() {
    $code = $this->_post('code', '');
    $result = $this->manageService->checkCode(getClientIP(), $code);
    return $result;
  }

  //test
  public function aaAction() {

    //$v=class_exists('MyRedis',false);
    //
    //var_dump($v);
    //exit;
    //import('MyRedis.php', 'library');

    $redis = MyRedis::getInstance([

      'ip' => '127.0.0.1',
      'port' => 6379,
      'prefix' => PREFIX

    ]);

    var_dump($redis->hGet('pen', 1));
    var_dump($redis->hGet('pen', 11));

    exit;

    $redis->hSet('pen', 1, '铅笔');
    $redis->hSet('pen', 2, '圆珠笔');
    $redis->hSet('pen', 3, '钢笔');


    $openData = $redis->hGetAll('pen');

    var_dump($openData);

    exit;


    printf("1 是否存在于 pen  %s <br>", $redis->hExists('pen', '1'));

    printf("5 是否存在于 pen  %s <br>", $redis->hExists('pen', '5'));

    $redis->hSet('pen', 5, '毛笔');

    printf("5 是否存在于 pen  %s <br>", $redis->hExists('pen', '5'));

    printf("4 没有在 pen 添加返回 %s <br>", $redis->hSet('pen', 4, '画笔'));

    printf("1 存在于 pen 再次添加会出现 %s <br>", $redis->hSet('pen', 1, '铅笔======'));


    $openData = $redis->hGetAll('pen');

    var_dump($openData);


    exit;
    var_dump($this->_post('username'));

    var_dump($_POST);

    $t = Rsa::Encrypt('song', JSPHP_PWD_PUBLIC, TRUE);

    var_dump($t);

    echo Rsa::Decrypt($t, JSPHP_PWD_PRIVKEY, TRUE);
    exit;
    // $d=Rsa::Decrypt('CDAlyDR3R/lq35XcfbefSyoOokTSwHFHE1Dx9tWsUMA51BJ003nPWZLfQPq3VbMr7mOMEDbU72OkSo4SfaTA6jEjG+LkpkgzaN5mJbNWGZ+QD1H1hgDIHl93xIbU7VQO9bMUwqN810eagDDOICH124vhtj5k7hlKUx+zXBfEYts=', JSPHP_PWD_PRIVKEY);

    // var_dump($d);
    exit;


    print_r($_GET);
    print_r($_POST);

    //echo AESEncrypt('song',COOKIE_KEY);
    //
    //
    //echo "\n";
    //
    //echo Rsa::Encrypt('song',JSPHP_PWD_PUBLIC);
    //


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