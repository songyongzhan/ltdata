<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/14
 * Time: 14:45
 * Email: 574482856@qq.com
 *
 */

defined('APP_PATH') OR exit('No direct script access allowed');

/**
 * 下载任务 操作服务类
 * Class TransportService
 */
class CsvlistService extends BaseService {

  protected $field = ['id', 'download_file', 'status', 'createtime'];

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getListPage(array $where, $page_num, $page_size) {
    $result = $this->csvlistModel->getListPage($where, $this->field, $page_num, $page_size);
    return $this->show($result);
  }



}