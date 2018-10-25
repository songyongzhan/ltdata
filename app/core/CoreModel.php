<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/17
 * Time: 14:08
 * Email: songyongzhan@qianbao.com
 */

class CoreModel {

  private $_db = NULL;

  protected $_host = NULL;
  //private   $_curl      = NULL;
  private $_multipart = FALSE;


  public final function __construct() {
    $this->_db = Yaf_Registry::has('db') ? Yaf_Registry::get('db') : NULL;
    $this->setHost(); //设置远程接口地址
    $this->init();
  }

  /**
   * 构造方法 PS：仿照构造方法
   */
  protected function init() {

  }

  public function fetchPost($url, $data, $multipart = FALSE) {
    $this->_multipart = $multipart;

    return $this->_fetch($url, $data, 'POST');
  }

  private function _fetch($url, $data, $method) {

    if (FETCH_DUMMY) {
      //去查找json文件，直接返回

      $file = str_replace('/', '_', trim($url, '/')) . '.json';
      if (is_file($file = APP_PATH . DS . 'data/json/' . $file)) {
        return $this->_fetchDummy($file, $data);
      }

      debugMessage('接口文件不存在. [' . $file . ']');


    }

    return $this->_fetchEx($url, $data, $method, $this->_multipart);
  }

  private function _fetchEx($url, $data, $method, $multiple) {
    isset($this->_curl) || $this->_initCurl($url = $this->_parseUrl($url));

    //isset($this->_curl) || $this->_curl = new Curl();
    if ($multiple) { //批量
      foreach ($data as &$item) {
        switch ($item['method'] = strtoupper($item['method'])) {
          case 'GET':
            $item['url'] .= (strpos($item['url'], '?') === FALSE ? '?' : '&')
              . (is_array($item['data']) ? http_build_query($item['data']) : $item['data']);

            $item['data'] = NULL;

            break;
        }

        $item['data'] = $this->fetchBefore($item['url'], $item['data'] ?: '');
      }

      $result = jsonencode($data);
    } else $result = $this->fetchBefore($url, $data ?: '');

    $time_begin = microtime(TRUE);
    switch (strtoupper($method)) {
      case 'GET':
        $result = $this->_curl->getJson($url, $result);
        break;
      case 'POST':
        $result = $this->_curl->postJson($url, $result, $this->_multipart);
        break;
      default:
        show_error('method error.');
    }

    $time_end = microtime(TRUE) - $time_begin;
    if ($result) {
      if ($multiple) {
        foreach ($result['result'] as $key => &$item) {
          $item = $this->fetchAfter($data[$key]['url'], $item);
        }
      } else $result = $this->fetchAfter($url, $result);
    }

    $this->fetchLog($url, $data, $result, $time_end);

    if ($multiple) {
      foreach ($result['result'] as &$item) {
        $item = $this->fetchFinish($item);
      }
    } else $result = $this->fetchFinish($result);

    //$this->setCache($hash, $result);

    return $result;

  }


  public function setHost($host = REMOTE_HOST) {
    $this->_host = $host;
  }


  public function setRequestHeader($header) {
    isset($this->_curl) || $this->_initCurl();

    $this->_curl->setHeader($header);
  }

  public function setRequestOptions($key, $value) {
    isset($this->_curl) || $this->_initCurl();

    $this->_curl->setOption($key, $value);
  }

  public function setRequestProxy($address, $port = 8080) {
    isset($this->_curl) || $this->_initCurl();

    $this->_curl->setProxy($address, $port);
  }

  /**
   * 写入请求日志
   * @param string $url
   * @param array $data
   * @param array|false $result
   * @param integer $time
   */
  public function fetchLog($url, $data, $result, $time) {
    /*if (ENVIRONMENT !== 'develop' && isset($this->_filterStatus, $result['status'], $result['result'])
      && $this->_filterStatus == $result['status']) $result['result'] = '【】';*/

    $log[] = __METHOD__ . ' [' . date('Y-m-d H:i:s') . ']';
    $log[] = '请求方式 ' . (isAjax() ? 'Ajax ' : '') . $_SERVER['REQUEST_METHOD'] . ' [' . ENVIRONMENT . ']';
    $log[] = '访问链接 ' . $_SERVER['REQUEST_URI'] . ' [' . $_SERVER['REMOTE_ADDR'] . ']';

    if (ENVIRONMENT !== 'product') $log[] = '调用文件 ' . getCallerFromTrace();

    $log[] = '接口地址 ' . $url . (ENVIRONMENT === 'production' && !isLocalhost() ? '' :
        ' [' . gethostbyname(parse_url($url, PHP_URL_HOST)) . ']');
    $log[] = '浏览器头 ' . $_SERVER['HTTP_USER_AGENT'];
    $log[] = '头部参数 ' . jsonencode($this->_curl->getOption(CURLOPT_HTTPHEADER) ?: []);
    $log[] = '请求参数 ' . jsonencode($this->_filterLog($data)) . ' [' . $this->_curl->getOption(CURLOPT_CUSTOMREQUEST) . ']';
    $log[] = '响应时间 ' . number_format($time, 3) . ' sec';
    $log[] = '接口返回 ' . jsonencode($this->_filterLog($result));

    if (!$result) {
      $log[] = '';
      $log[] = '返回错误 ' . ($this->_curl->getError() ?: '未知错误 [' . $this->_curl->getCode() . ']');
      $log[] = '原始返回 ' . $this->_curl->getResonse();
    }

    debugMessage(implode(PHP_EOL, $log));
  }

  /**
   * 使用模拟数据返回 (忽略 fetchBefore、fetchAfter 方法)
   * @param string $file 接口文件
   * @param string|array $data 接口参数
   * @return array|false
   */
  private function _fetchDummy($file, $data = '') {
    $result = jsondecode(file_get_contents($file));


    //is_integer(key($result)) && $result = $result[array_rand($result)];

    $log[] = __METHOD__ . ' [' . date('Y-m-d H:i:s') . ']';
    $log[] = '请求方式 ' . (isAjax() ? 'Ajax ' : '') . $_SERVER['REQUEST_METHOD'] . ' [' . ENVIRONMENT . ']';
    $log[] = '访问链接 ' . $_SERVER['REQUEST_URI'] . ' [' . $_SERVER['REMOTE_ADDR'] . ']';

    if (ENVIRONMENT !== 'product') $log[] = '调用文件 ' . getCallerFromTrace();

    $log[] = '接口文件 ' . realpath($file);
    $log[] = '浏览器头 ' . $_SERVER['HTTP_USER_AGENT'];
    $log[] = '头部参数 ' . (isset($this->_curl) ? jsonencode($this->_curl->getOption(CURLOPT_HTTPHEADER) ?: []) : '[]');
    $log[] = '请求参数 ' . jsonencode($data);
    $log[] = '模拟返回 ' . jsonencode($result);
    $log[] = '不运行 fetchBefore、fetchAfter 方法!!!';

    debugMessage(implode(PHP_EOL, $log));

    return $this->fetchFinish($result);
  }

  //记录日志，需要过滤的字段，日后完善
  public function _filterLog($data) {


    return $data;
  }

  //解析url
  private function _parseUrl($url) {
    parse_url($url, PHP_URL_SCHEME) || ($this->_host && $url = rtrim($this->_host, '/') . '/' . ltrim($url, '/'));

    return $url;
  }

  private function _initCurl($url = '') {
    isset($this->_curl) || $this->_curl = new Curl();
    if (substr($url, 0, 6) === 'https:') {
      $this->_curl->setOptions([CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0]);
    }

    $this->_curl->setHeader([
      'Content-Type' => NULL,
      'Client-Ip' => getClientIP(),
      'ENV' => isset($_SERVER['HTTP_ENV']) ? $_SERVER['HTTP_ENV'] : NULL,
    ]);
  }

  /**
   * 请求前处理数据（加密或组装数据）
   * @param string $url
   * @param array|string $data
   * @return string|array
   */
  protected function fetchBefore($url, $data) {
    return $data;
  }

  /**
   * 请求成功后处理数据（处理解密数据，禁止在此处判断数据状态）
   * @param string $url
   * @param array|boolean $data
   * @return mixed
   */
  protected function fetchAfter($url, $data) {
    return $data;
  }

  /**
   * 请求完成后处理数据（判断数据状态或拼接数据）
   * @param array|boolean $data
   * @return mixed
   */
  protected function fetchFinish($data) {
    return $data;
  }

}