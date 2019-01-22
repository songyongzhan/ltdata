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
class YdylareaService extends BaseService {

  protected $field = ['id', 'title', 'china_ids', 'status', 'updatetime', 'createtime'];

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getListPage(array $where, $page_num, $page_size) {
    $result = $this->ydylareaModel->getListPage($where, $this->field, $page_num, $page_size);
    return $this->show($result);
  }

  /**
   * 添加
   * @param string $title <require> 国家名称
   * @return mixed 返回最后插入的id
   */
  public function add($title) {
    $data = [
      'title' => $title
    ];
    $lastInsertId = $this->ydylareaModel->insert($data);
    if ($lastInsertId) {
      $data['id'] = $lastInsertId;
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }

  }

  /**
   * 删除一个
   * @param int $id <require|number> id
   */
  public function delete($id) {
    $result = $this->ydylareaModel->delete($id);
    return $result > 0 ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 获取单个信息
   * @param int $id <require|number> id
   * @param string $fileds
   * @return mixed
   */
  public function getOne($id, $fileds = '*') {
    $result = $this->ydylareaModel->getOne($id, $fileds);
    return $result ? $this->show($result) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 分组更新数据
   * @param int $id <require|number> id
   * @param string $title <require> 名称
   * @return array mixed 返回用户数据
   */

  public function update($id, $title) {
    $data = [
      'title' => $title
    ];
    $result = $this->ydylareaModel->update($id, $data);
    if ($result) {
      $data['id'] = $id;
    }
    return $result ? $this->show($data) : $this->show([]);
  }


  /**
   * 获取国家列表
   * @param int $ydylarea <require|number> 地区区域不能为空
   * @return array
   * @throws InvalideException
   */
  public function getChinaList($ydylarea) {
    $result = $this->ydylareaModel->getOne($ydylarea, ['china_ids']);

    $chinaList = $this->countryModel->getList([
      getWhereCondition('id', explode(',', $result['china_ids']), 'in')
    ], ['id', 'title as text']);

    return $this->show(['list' => $chinaList]);
  }

}