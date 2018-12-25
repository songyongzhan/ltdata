<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/4/28
 * Time: 11:40
 * Email: songyongzhan@qianbao.com
 *
 * 后台用户管理 模型
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class ManageModel extends BaseModel {

  /**
   * 普通用户登录
   * @param $username
   * @param $password
   * @param $platform_id
   * @return array|bool code 1 认证失败   2 用户不存在
   */
  public function login($username, $password, $platform_id) {
    $login = $this->_login($username, $platform_id);
    if ($login) {
      if ($login['password'] == $password) {
        unset($login['password']);
        $login['login'] = TRUE;
        return $login;
      } else {
        return $this->_returnLoginStatus(1);
      }
    } else {
      return $this->_returnLoginStatus(2);
    }
  }

  /**
   * 根据用户ID得到验证token的数据
   */
  public function check_token($manage_id) {
    $result = $this->db->select(['id', 'token', 'timeout'])->get_where($this->table, ['id' => $manage_id], 1);
    if ($result->num_rows() > 0) {
      return $result->row_array();
    }
  }

  /**
   * 更新token时间
   * @param $manage_id
   * @param $timeout
   * @return int
   */
  public function update_token_timeout($manage_id, $timeout) {
    $this->db->update($this->table, ['timeout' => $timeout], ['id' => $manage_id]);
    return $this->db->affected_rows();
  }



  /**
   * 返回登录失败信息
   * @param $status
   * @return array
   */
  private function _returnLoginStatus($status) {
    return ['login' => FALSE, 'code' => $status];
  }


}