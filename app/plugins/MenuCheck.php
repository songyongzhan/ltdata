<?php

/**
 * @name SamplePlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author root
 */
class MenuCheckPlugin extends Yaf_Plugin_Abstract {
  public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    if (!IS_CHECK_MENU) return;


    $data = _parseCurrentUri();


    //不需要验证的接口
    $whiteList = [
      'Manage' => ['logout', 'checkToken', 'getClientIp', 'login', 'getUserInfo', 'changePlatform', 'getLdapUserinfo', 'password', 'aa'],
      'Dictionaries' => '*'
    ];
    $whiteList = array_change_value_case_recursive($whiteList);
    $whiteList = array_change_key_case_recursive($whiteList);

    $controller = strtolower($data['controller']); //控制器
    $action = strtolower($data['action']); //方法


  }
}
