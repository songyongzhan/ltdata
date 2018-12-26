<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/5/7
 * Time: 16:35
 * Email: songyongzhan@qianbao.com
 *
 * 栏目相关操作
 */

class LogsController extends ApiBaseController {

  public function getListAction() {
    $page_size = $this->_post('page_size', PAGE_SIZE_DEFAULT);
    $page_num = $this->_post('page_num', 1);
    $ip = $this->_post('ip');
    $data = [
      'manage_id' => $this->_post('manage_id'),
      'controller' => $this->_post('controller'),
      'method' => $this->_post('method'),
      'exe_type' => $this->_post('exe_type')
    ];
    $rules = [
      [
        'condition' => '=',
        'key_field' => ['manage_id', 'controller', 'method', 'exe_type'],
        'db_field' => ['log.manage_id', 'log.controller', 'log.method', 'log.exe_type']
      ]
    ];
    if ($ip != '') {
      $start_ip = rtrim($ip, '.') . '.0.0.0';
      $start_ip = ip_long(implode('.', array_slice(explode('.', $start_ip), 0, 4)));
      $end_ip = rtrim($ip, '.') . '.255.255.255';
      $end_ip = ip_long(implode('.', array_slice(explode('.', $end_ip), 0, 4)));
      $data['start_ip'] = $start_ip;
      if ($start_ip == $end_ip) {
        $rules[] = ['condition' => '=', 'key_field' => ['start_ip'], 'db_field' => ['log.ip']];
      } else {
        $data['end_ip'] = $end_ip;
        $rules[] = ['condition' => 'between', 'key_field' => ['start_ip', 'end_ip'], 'db_field' => ['log.ip', 'log.ip']];
      }
    }
    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));
    $result = $this->Logs_service->getList($where, $page_num, $page_size);
    return $result;
  }

  /**
   * 获取单提条日志记录
   * @param int $id <POST> id
   * @return mixed
   */
  public function getOneAction() {
    $id = $this->_post('id');
    $result = $this->Logs_service->getOne($id);
    return $result;
  }

}