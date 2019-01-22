<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/21
 * Time: 22:45
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

/**
 * 一带一路 操作服务类
 * Class TransportService
 */
class YdylareachinaService extends BaseService {

  protected $field = ['china_id'];

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getList(array $where) {
    $result = $this->ydylareachinaModel->getList($where, $this->field,'china_id desc');
    return $this->show($result);
  }


  public function createTemporaryTable() {
    $result = $this->ydylareachinaModel->createTemporaryTable('ydylareachina');
    return $this->show($result);
  }

  /**
   * 填充临时表数据
   */
  public function fillTemporaryTable($id = '') {

    if ($id == '') {
      //查询所有的数据填充
      $result = $this->ydylareaModel->getListPage([], ['id','china_ids'], 1, 10);;
      $chinaids = [];

      foreach ($result['list'] as $val) {
        array_push($chinaids, $val['china_ids']);
      }

      $chinaids = implode(',', $chinaids);

    } else {
      $result = $this->ydylareaModel->getOne($id, ['id', 'china_ids']);
      $chinaids = $result['china_ids'];
    }

    $data =array_map(function($val){
      return ['china_id'=>$val];
    },explode(',', $chinaids));

    $result = $this->ydylareachinaModel->inserMulti($data);
    return $this->show($result);
  }
}