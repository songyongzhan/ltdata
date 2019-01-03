<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/17
 * Time: 13:32
 * Email: 574482856@qq.com
 */

define('TEMPLATE_DIR', APP_PATH . '/static/views');

define('DS', DIRECTORY_SEPARATOR);
define('APP_CONFIG_PATH', APP_PATH . DS . 'app/configs');

define('MODULES_PATH', APP_PATH . DS . 'app/modules'); //多模块位置


define('COOKIE_KEY', 'asfd654987'); //10

define('API_SUCCESS', 20000000);
define('API_FAILURE', 99999999);
define('API_FAILURE_MSG', '系统错误请联系管理员!');

//是否模拟数据 如果没有定义(index.php入口文件)，则这里定义
defined('FETCH_DUMMY') || define('FETCH_DUMMY', FALSE);


switch (ENVIRONMENT) {
  case 'develop': //开发配置文件
    define('REMOTE_HOST', 'http://sit1-apis.qianbao.com');


    define('JSPHP_PWD_PUBLIC',
      'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDjIu3vIZX0D0PxZ2BGvLbwZCfiC2ptfOPk9/qqOcQ4Knz2WLyTBZtVHORwRofACGOosH8d15+u2zKvh/oU5tGp2STiGAJkHVS5Zneta6Xolp/MdHn65sOxPhVPWO0qtpBAP4M7ORD4nstK38IiF/XKlXcVJXiNOIezj8ioZinXowIDAQAB');
    define('JSPHP_PWD_PRIVKEY',
      'MIICWgIBAAKBgQDjIu3vIZX0D0PxZ2BGvLbwZCfiC2ptfOPk9/qqOcQ4Knz2WLyTBZtVHORwRofACGOosH8d15+u2zKvh/oU5tGp2STiGAJkHVS5Zneta6Xolp/MdHn65sOxPhVPWO0qtpBAP4M7ORD4nstK38IiF/XKlXcVJXiNOIezj8ioZinXowIDAQABAn8h3oIW6SOrn/ByEnBdN5Q9jbL3aVsEaFCZRPYMOZ4oDaAOCeGmIFssHWm5Pp18d0uYvKf29Bb3P0AqWeF/r8ZFdGMz6bywx1eGLLf2h4CquEHihLEykv/Vl4pOR6XUov1WtVD14Oc+0Qal9gvjaIDj8Zzd5M6RjgC8fjyrs9VRAkEA+Ohjz6TXdovjFltBZ9DOgeaGCEgYlwTBCv9IxQ6KCkf1b/j/Bf0b6ci3nQRCWIRZ666MDhb7L19y1HC8s8tJ+wJBAOmbu5UWSE2nqx2GY2T02ExsjfLDK2UgKVhu5kC/ULcnXf7wi7a+/81amw+cmOGe+p6Z0tTOtj3/OqqdiT3VoHkCQQCDXEnIvQtFMgBPvStgea9ymNFlr37ivIyQnDewX8L0OBPM21DjvTetAtP8VtIY2wiFvGGH0hMQZkQ436KHFKNfAkAlsswCsyXX4kbq7NT9ZcXCD4KYuoY2O4pwivT9XeJYrDGvAoKJayk8qnJ7gnnpbw5iqdAsJ2+hcZ62CdYr9F2BAkBXLaaSuXcAmJf6dtUDLhhakLRdnJJqQPjBe3ubTHf7EIOWxNk4Wasjla/dhEy34fhIyN6nNUPByVV1ONqTIuwV');


    define('REDIS_HOST', '127.0.0.1'); //连接redis
    define('REDIS_PORT', 6379); //连接redis
    define('REDIS_PASSWORD', ''); //reids密码
    define('REDIS_EXPIRE', 300); //reids默认过期时长


    break;
  case 'testing': //测试配置文件
    define('REMOTE_HOST', '');

    define('JSPHP_PWD_PUBLIC', '');
    define('JSPHP_PWD_PRIVKEY', '');


    define('REDIS_HOST', '127.0.0.1'); //连接redis
    define('REDIS_PORT', ''); //连接redis
    define('REDIS_PASSWORD', ''); //reids密码
    define('REDIS_EXPIRE', 300); //reids默认过期时长
    break;
  case 'product': //生产配置文件

    define('REMOTE_HOST', '');

    define('JSPHP_PWD_PUBLIC', '');
    define('JSPHP_PWD_PRIVKEY', '');

    define('REDIS_HOST', '127.0.0.1'); //连接redis
    define('REDIS_PORT', ''); //连接redis
    define('REDIS_PASSWORD', ''); //reids密码
    define('REDIS_EXPIRE', 300); //reids默认过期时长

    break;
}


/**
 * 配置型常量
 */
define('TWIG_INIT_FLAG', TRUE);
define('DB_INIT_FLAG', TRUE);

//定义错误页面存放路径
define('ERROR_TEMPLATE_PATH', APP_PATH . DS . 'static/_common/errors');
define('ERROR_FILENAME', 'error_general.php');
define('EXCEPTION_FILENAME', 'exception_general.php');

define('PREFIX', 'api_'); //设置时，前缀是app_

define('PWD_SALT', 'ltdata20181226');

define('TOKEN_EXPIRE_LONG', 1800); //token存在30分钟

define('PAGESIZE', 10);

define('POST_TRANSTION_ACCESS_TOKEN', 'post_access_token'); //如果特殊情况，需要需要通过post传递参数，需要传递 post_access_token

define('IS_CHECK_TOKEN', TRUE);

define('IS_CHECK_MENU', TRUE);


/**
 * 数据库其他配置
 */

define("DB_AUTOADDTIME", TRUE);