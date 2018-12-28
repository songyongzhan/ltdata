<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/17
 * Time: 14:21
 * Email: 574482856@qq.com
 */

//项目相关函数

function getToken() {
  $instance = getInstance();
  $token = $instance->_server('X_Access_Token');

  return $token;
}


/**
 * 生成唯一token
 * @param string $phone 传递用户id
 * @return string 加密后的token
 */
function create_token($id = '') {
  $_token = md5(md5(uniqid(microtime(TRUE), TRUE) . $id));
  return sha1($_token . $id);
}


function getTokenData() {
  $token = getToken();
  if (!$token)
    showApiException('TOKEN 为空', StatusCode::TOKEN_IS_EMPTY);

  if ($data = AESDecrypt($token))
    return jsondecode($data) ?: [];
  else
    showApiException('token传递错误', StatusCode::TOKEN_ERROR);
}


/**
 * 栏目分级显示
 * @param array $list 资源列表
 * @param int $pid
 * @param string $parent_name 列表中父类的字段名称
 * @param string $sun_name
 * @return array|bool
 */
function menu_group_list($list, $pid = 0, $parent_name = 'pid', $sun_name = 'sun') {
  if (empty($list)) {
    return FALSE;
  }
  $data = [];
  foreach ($list as $key => $val) {
    if ($val[$parent_name] == $pid) {
      $_temp = menu_group_list($list, $val['id']);
      count($_temp) > 0 ? $val[$sun_name] = $_temp : '';
      $data[] = $val;
    }
  }
  return $data;
}

/**
 * 栏目分级显示2
 * @param array $list 资源列表
 * @param int $pid
 * @param string $parent_name 列表中父类的字段名称
 * @param int $level
 * @return array|bool
 */
function menu_sort($list, $pid = 0, $parent_name = 'pid', $level = 0) {
  if (empty($list)) {
    return FALSE;
  }
  $data = [];
  foreach ($list as $key => $val) {
    if ($val[$parent_name] == $pid) {
      $val['level'] = $level;
      $data[] = $val;
      $data = array_merge($data, menu_sort($list, $val['id'], $parent_name, $level + 1));
    }
  }
  return $data;
}

/**
 * 从子类向父类查找
 * @param array $list 资源列表
 * @param int $current_id 数字
 * @param string $parent_name 列表中父类的字段名称
 * @return array
 */
function get_parent($list, $current_id, $parent_name = 'pid') {
  if (empty($list)) {
    return FALSE;
  }
  $arr = array();
  foreach ($list as $key => $val) {
    if ($val['id'] == $current_id) {
      $arr[] = $val;
      $arr = array_merge($arr, get_parent($list, $val[$parent_name], $parent_name));
    }
  }
  return $arr;
}

/**
 * 数组排重 针对二维数组
 * @param $arr
 * @param $key
 * @return array
 */
function array_unset_unique($arr, $key) {
  //建立一个目标数组
  $res = array();
  foreach ($arr as $value) {
    //查看有没有重复项

    if (isset($res[$value[$key]])) {
      //有：销毁

      unset($value[$key]);

    } else {

      $res[$value[$key]] = $value;
    }
  }
  return $res;
}


/**
 * 排序
 * @param $a
 * @param $b
 * @return int
 */
function menu_sort_cmp_asc($a, $b) {
  if ($a['sort_id'] == $b['sort_id']) {
    return 0;
  }
  return ($a['sort_id'] < $b['sort_id']) ? -1 : 1;
}

function menu_sort_cmp_desc($a, $b) {
  if ($a['sort_id'] == $b['sort_id']) {
    return 0;
  }
  return ($a['sort_id'] < $b['sort_id']) ? 1 : -1;
}

/**按照menu中 sort_id 升序
 * @param $arr
 * @return mixed
 */
function sort_by_sort_id($arr, $sort_type = 'asc') {
  if ($sort_type == 'asc')
    usort($arr, 'menu_sort_cmp_asc');
  else
    usort($arr, 'menu_sort_cmp_desc');
  return $arr;
}

function get_client_token_data() {
  return FALSE;
}


/**
 * 返回分页格式
 * @param $list
 * @param $total
 * @param $page_num
 * @param $page_size
 * @param $total_page
 * @return array
 */
function page_data($list = [], $total = 0, $pageNum, $pageSize, $total_page) {
  $data = array(
    'list' => $list,
    'total' => intval($total),
    'page_num' => intval($pageNum),
    'page_size' => intval($pageSize),
    'total_page' => intval($total_page),
  );
  return $data;
}


function convert_encodeing($data, $from_encoding = 'UTF-8', $to_encoding = 'GBK') {
  if (!$data)
    return $data;

  if (is_array($data)) {
    foreach ($data as $key => $val) {
      if (is_array($val))
        $data[$key] = convert_encodeing($val, $from_encoding, $to_encoding);
      else
        $data[$key] = mb_convert_encoding($val, $to_encoding, $from_encoding);
    }
  } else {
    return mb_convert_encoding($data, $to_encoding, $from_encoding);
  }
  return $data;
}