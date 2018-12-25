<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/5/29
 * Time: 14:25
 * Email: songyongzhan@qianbao.com
 */

class TokenService extends BaseService {

  public $manage_id; //用户id
  public $src_token; //客户端发来的token
  public $remote_ip; //当时登录的ip
  public $isadmin; //是否管理员 标识
  public $token_data; //token_data 数组 包含以上信息

  public function init() {
    parent::init();
    $token_data = get_client_token_data();
    if (!$token_data) return FALSE;
    $this->token_data = $token_data;
    $this->manage_id = $token_data['manage_id'];
    $this->remote_ip = $token_data['remote_ip'];
    $this->isadmin = $token_data['isadmin'];
    $this->src_token = $token_data['src_token'];
  }


}