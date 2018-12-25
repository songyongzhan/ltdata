<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/23
 * Time: 9:38
 * Email: songyongzhan@qianbao.com
 */

class ApiBaseController extends BaseController {

  /**
   * API 统一入口
   * @param $method
   * @param array $params
   * @throws Exception
   */
  public function _remapAction() {
    try {
      $parseUri = $this->_parseUri();
      $controller = getInstance($parseUri['controller'], $parseUri['module']);
      $data = call_user_func_array([$controller, $parseUri['action'] . 'Action'], $this->getRequest()->getParams());
      $this->showJson($data['result'], $data['code'], $data['msg']);
    } catch (Exception $e) {
      //$this->showJson([], API_FAILURE, $e->getMessage());
      show_error($e->getMessage(), $e->getCode());
      //showApiException($e->getMessage(), $e->getCode());
    }
  }


  private function showJson($result, $code = API_SUCCESS, $msg = NULL) {
    $data = [
      'status' => empty($code) ? API_SUCCESS : $code,
      'message' => empty($msg) ? '' : $msg,
      'result' => $result ?: []
    ];
    //header('Content-Type: application/json; charset=utf-8');
    header('Content-Type:text/plain; charset=utf-8');
    echo jsonencode($data);
  }

  /**
   *
   * @param $rules
   * @param null $data
   * @return array|bool|mixed
   */
  public function where($rules, $data = NULL) {
    if (!is_array($rules)) {
      return FALSE;
    }
    $where = [];

    foreach ($rules as $key => $val) {
      $condition_type = $val['condition'];
      switch ($condition_type) {
        case 'not null':
          $where = $this->_null_condition($val, $where, 'not null', $data);
          break;
        case 'null':
          $where = $this->_null_condition($val, $where, 'null', $data);
          break;
        case 'in':
          $where = $this->_in_condition($val, $where, 'in', $data);
          break;
        case 'not in':
          $where = $this->_in_condition($val, $where, 'not in', $data);
          break;
        case 'like':
          $where = $this->_like_condition($val, $where, 'like', $data);
          break;
        case 'after like':
          $where = $this->_like_condition($val, $where, 'after like', $data);
          break;
        case 'before like':
          $where = $this->_like_condition($val, $where, 'before like', $data);
          break;
        case 'between':
          if (count($val['key_field']) < 2) {
            break;
          }
          //就循环2次
          for ($f_key = 0; $f_key < 2; $f_key++) {
            $f_filed = $val['key_field'][$f_key];
            if (!isset($data[$f_filed])) break;
            $key_value = isset($data[$f_filed]) ? $data[$f_filed] : '';

            if (isset($val['db_field'][$f_key])) {
              $_db_fields = $val['db_field'][$f_key];
              $condition = $f_key == 0 ? '>=' : '<=';
              $where[$_db_fields . ' ' . $condition] = trim($key_value);
            }
          }
          break;
        default :
          foreach ($val['key_field'] as $f_key => $f_filed) {
            if (!isset($data[$f_filed])) continue;
            $key_value = isset($data[$f_filed]) ? $data[$f_filed] : '';
            isset($val['db_field'][$f_key]) && $where[trim($val['db_field'][$f_key]) . ' ' . $condition_type] = trim($key_value);
          }
          break;
      }
    }
    return $where;
  }


  /**
   * 处理like 或 not like
   * @param array $val
   * @param array $where 条件
   * @param string $condition_type 处理类型
   * @return mixed
   */
  private function _like_condition($val, $where, $condition_type, $data) {
    foreach ($val['key_field'] as $f_key => $f_filed) {
      if (!isset($data[$f_filed])) continue;
      $key_value = isset($data[$f_filed]) ? $data[$f_filed] : '';
      if (isset($val['db_field'][$f_key])) {
        $_db_fields = $val['db_field'][$f_key];
        switch ($condition_type) {
          case 'like':
            $where[$_db_fields . ' like'] = '%' . trim($key_value) . '%';
            break;
          case 'after like':
            $where[$_db_fields . ' like'] = trim($key_value) . '%';
            break;
          case 'before like':
            $where[$_db_fields . ' like'] = '%' . trim($key_value);
            break;
        }
      }
    }
    return $where;
  }

  /**
   * 处理null或 not null
   * @param array $val
   * @param array $where 条件
   * @param string $condition_type 处理类型
   * @return mixed
   */
  private function _null_condition($val, $where, $condition_type, $data) {
    foreach ($val['key_field'] as $f_key => $f_filed) {
      if (isset($val['db_field'][$f_key])) {
        $_db_fields = $val['db_field'][$f_key];
        if (isset($data[$_db_fields]))
          $condition_type == 'null' ? $where[$_db_fields . ' is null'] = NULL : $where[$_db_fields . ' is not null'] = NULL;
      }
    }
    return $where;
  }

  /**
   * 处理in 或not in
   * @param array $val
   * @param array $where 条件
   * @param string $condition_type 处理类型
   * @return mixed
   */
  private function _in_condition($val, $where, $condition_type, $data) {
    isset($val['db_field'][0]) && $_db_fields = $val['db_field'][0];
    if (!$_db_fields || !isset($data[$_db_fields])) return $where;
    if (is_array($data[$_db_fields])) {
      $where[$_db_fields . ' ' . $condition_type . ' (' . implode(',', $data[$_db_fields]) . ')'] = NULL;
    } else if (is_string($data[$_db_fields])) {
      $where[$_db_fields . ' ' . $condition_type . ' (' . $data[$_db_fields] . ')'] = NULL;
    }
    return $where;
  }


  /**
   * 获取当前uri中控制器和方法
   * @return array
   * @throws Exceptions
   */
  private function _parseUri() {
    return _parseCurrentUri();
  }


}
