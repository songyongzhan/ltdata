<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: songyongzhan@qianbao.com
 */

class ManageService extends BaseService {


  /**
   * @param string $user <require>
   * @param string $password <require>
   * @param int $type <require|lt:25>
   * @param string $message <require>
   * @return mixed
   */
  public function index($user, $password, $type, $message) {

  }

  const EMAIL_PREG = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';
  const FIELD = ['id', 'username', 'fullname', 'timeout', 'status', 'department', 'ext', 'last_logintime', 'remarks', 'createtime'];

  /**
   * 用户添加
   * @param string $username <required> 用户名
   * @param string $fullname <required> 姓名
   * @return true|false 返回添加结果
   */
  public function add($data) {
    //判断此平台下是否存在这个账号，如果存在直接返回
    $hasManage = $this->Manage_model->getList(['username' => $data['username'], 'platform_id' => $this->Token->platform_id], ['id']);
    if ($hasManage) showApiException('此用户已存在，不能重复添加', StatusCode::SAME_USERNAME_ERROR);
    $createTime = time();
    if ($this->Token->isadmin != ISADMIN_STATUS) {
      $this->_commonLdadCheck($data);
    }
    if (isset($data['password']) && $data['password'] != '') {

      if ($data['password'] !== $data['re_password'])
        showApiException('两次输入密码不一致', StatusCode::INCONSISTENT_PASSWORD);

      //拿到平台信息 获取加密 盐
      //$platform_info = $this->Platform_service->getOne($data['platform_id'], 'id,salt');
      //$data['password'] = password_encrypt($data['password'], $platform_info['result']['salt']);
      $data['password'] = password_encrypt($data['password']);
    } else
      unset($data['password']);
    unset($data['re_password']);
    $data['last_logintime'] = $createTime;
    $data['createtime'] = $createTime;
    $data['platform_id'] = $this->Token->platform_id;
    $lastInsertId = $this->Manage_model->add($data);
    if ($lastInsertId) {
      unset($data['password']);
      $data['id'] = $lastInsertId;
      $data['createtime'] = dateFormat($createTime);
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }
  }

  /**
   * 用户更新数据
   * @param int $id <required|numeric> 用户ID
   * @param string $fullname <required> 姓名
   * @param array $data 更新到数据库的数据
   * @return array mixed 返回用户数据
   * @throws Exception
   */
  public function update($id, $data) {
    if ($this->Token->isadmin != ISADMIN_STATUS) {
      $this->_commonLdadCheck($data);
    }
    if (empty($data)) {
      showApiException('请求参数错误', StatusCode::PARAMS_ERROR);
    }
    if (isset($data['password']) && $data['password'] != '') {
      if ($data['password'] !== $data['re_password'])
        showApiException('两次输入密码不一致', StatusCode::INCONSISTENT_PASSWORD);

      //拿到平台信息 获取加密 盐
      //$platform_info = $this->Platform_service->getOne($data['platform_id'], 'id,salt');
      //$data['password'] = password_encrypt($data['password'], $platform_info['result']['salt']);
      $data['password'] = password_encrypt($data['password']);
    } else
      unset($data['password']);
    unset($data['re_password']);
    $result = $this->Manage_model->update($id, $data, platform_where());
    $result && $data['id'] = $id;
    return $result ? $this->show($data) : $this->show([]);
  }

  /**
   * 获取用户列表
   * @param array $where 搜索条件
   * @param int $pageNo 页码
   * @param int $pageSize 每页显示条数
   * @return array
   */
  public function getList(array $where = [], $page_num, $page_size) {
    $like_arr = [];
    if (isset($where['username'])) {
      $like_arr['username'] = $where['username'];
      unset($where['username']);
    }

    $result = $this->Manage_model->getListPage($where, self::FIELD, $page_num, $page_size, [], 'createtime desc');
    return $result ? $this->show($result) : $this->show([]);
  }

  /**
   * 获取一个用户
   * @param int $id <required|numeric> 用户ID
   * @param string $fileds 获取字段名称||array
   * @return array
   */
  public function getOne($id) {
    $result = $this->Manage_model->getOne($id, self::FIELD, platform_where());
    unset($result['password']);
    return $result ? $this->show($result) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 用户登录
   * @param string $username <required> 用户名
   * @param string $password <required> 加密后的密码串
   * @param int $platform_id <required|numeric> 平台id
   * @return array mixed 返回用户信息
   */
  public function login($username, $password, $platform_id) {
    if (preg_match(self::EMAIL_PREG, $username)) { //域控登录
      $result = $this->Manage_model->login_ldap($username, $password, $platform_id);
    } else {//拿到平台信息 获取加密 盐

      //$platform_info = $this->Platform_service->getOne($platform_id, 'id,salt');
      //$password = password_encrypt($password, $platform_info['result']['salt']);
      $password = password_encrypt($password);
      $result = $this->Manage_model->login($username, $password, $platform_id);
    }
    if ($result['login']) {
      $result = $this->_loginSuccessData($result, $platform_id);
      return $this->show($result);
    } else {
      if ($result['code'] == 1) {
        return $this->show([], StatusCode::USER_AUTHENTICATION_FAILURE);
      } else {
        return $this->show([], StatusCode::USER_NOT_EXISTS);
      }
    }
  }

  /**
   * 验证token是否存在
   * @param array $token_data token数值
   * @param int $manage_id <required|numeric> 用户ID
   * @param string $token <required> token验证
   * @return bool
   */
  public function check_token($token_data) {
    $result = $this->Manage_model->check_token($token_data['manage_id']);
    $flag = FALSE;
    if ($result) {
      //判断token是否过期  token是否一致
      if ($result['token'] == $token_data['token'] && time() < $result['timeout']) {
        $timeout = time() + TOKEN_EXPIRE_LONG;
        $this->_updateTokenTimeout($result['id'], $timeout);
        $flag = TRUE;
        $data['token'] = $token_data['src_token'];
        $data['timeout'] = $timeout;
        $data['manage_id'] = $token_data['manage_id'];
      }
    }
    $data['success'] = $flag;
    return $this->show($data);
  }


  /**
   * 使用token换取用户数据
   * @return mixed
   */
  public function getUserInfo() {
    $manage_id = $this->Token->manage_id;
    $result = $this->Manage_model->getOne($manage_id, self::FIELD);
    return $this->show($result);
  }

  /**
   * 用户退出
   * @return mixed
   */
  public function logout() {
    $manage_id = $this->Token->manage_id;
    $this->_updateTokenTimeout($manage_id, time() - 1);
    return $this->show([], API_SUCCESS, '用户退出成功');
  }

  /**
   * 删除用户数据
   * @param int $id <required> 删除id不能为空
   * @return mixed
   */
  public function delete($id) {
    $result = $this->Manage_model->delete($id, platform_where());
    return $result ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 更新用户的过期时间
   * @param int $manage_id <required|numeric> 更新用户的ID
   * @param int $timeout <required|numeric> 更新过期时间戳
   * @return true|false
   */
  private function _updateTokenTimeout($manage_id, $timeout = NULL) {
    $timeout || $timeout = time();
    return $this->Manage_model->update_token_timeout($manage_id, $timeout);
  }


  /**
   * 显示两个ip 一个是getClentIp
   * 另一个是 https://apis.qianbao.com/origin 的ip
   */
  public function getClientIp() {
    $origin_data = $this->Apisip_model->fetchPost('/origin');
    $origin_ip = $origin_data ? $origin_data['result']['origin'] : '';
    $data = ['client_ip' => ip_long(getClientIP()), 'origin_ip' => ip_long($origin_ip)];
    return $this->show($data);
  }





  /**
   * 修改用户密码
   * @param int $id <required|numeric> 用户id
   * @param string $oldPassword <required> 旧密码
   * @param string $newPassword <required> 新密码
   * @param string $rePassword <required|matches[newPassword]> 确认密码
   */
  public function password($id, $oldPassword, $newPassword, $rePassword) {

    /*if ($newPassword != $rePassword)
      showApiException('两次输入密码不正确', StatusCode::INCONSISTENT_PASSWORD);*/
    $where = [];

    $manage = $this->Manage_model->getOne($id, 'id,username,password', $where);



    if (!$manage)
      showApiException('用户不存在', StatusCode::USER_NOT_EXISTS);

    if ($manage['password'] != password_encrypt($oldPassword))
      showApiException('旧密码输入错误', StatusCode::PASSWORD_ERROR);

    $result = $this->Manage_model->update($id, ['password' => password_encrypt($newPassword), 'last_logintime' => time()], $where);
    $result && $data['id'] = $id;
    return $result ? $this->show($data) : $this->show([]);
  }


}