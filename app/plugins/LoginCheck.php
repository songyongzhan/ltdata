<?php

/**
 * @name SamplePlugin
 * @desc Yaf定义了如下的6个Hook,插件之间的执行顺序是先进先Call
 * @see http://www.php.net/manual/en/class.yaf-plugin-abstract.php
 * @author root
 */
class LoginCheckPlugin extends Yaf_Plugin_Abstract {
  public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    if (!IS_CHECK_TOKEN) return;

    $data = _parseCurrentUri();




  }
}