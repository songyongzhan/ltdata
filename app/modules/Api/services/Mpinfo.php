<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: 574482856@qq.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

/**
 * 名片 操作服务类
 * Class TransportService
 */
class MpinfoService extends BaseService {

  protected $field = ['id', 'wn_np_type', 'mppinpaiId', 'sheng_id', 'shi_id', 'qylx_id', 'title', 'jypp', 'tel', 'mobile', 'person', 'email', 'weburl', 'indexshow', 'isfufei', 'allshow', 'status', 'gongkai', 'createtime'];

  /**
   * 获取列表
   * @param array $where
   * @param $field
   * @return mixed
   */
  public function getListPage(array $where, $page_num, $page_size) {
    $result = $this->mpinfoModel->getListPage($where, $this->field, $page_num, $page_size);
    return $this->show($result);
  }


  /**
   * 添加
   * @param int $wn_np_type <require|number> 类别不能为空
   * @param int $mppinpaiId <require|number> 品牌不能为空
   * @param int $sheng_id <require|number> 省份不能为空
   * @param int $shi_id <require|number> 所在城市不能为空
   * @param int $qylx_id <require|number> 企业类型不能为空
   * @param string $title <require|number> 企业名称不能为空
   * @return mixed 返回最后插入的id
   */

  public function add($data) {
    $lastInsertId = $this->mpinfoModel->insert($data);
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
    $result = $this->mpinfoModel->delete($id);
    return $result > 0 ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 获取单个信息
   * @param int $id <require|number> id
   * @param string $fileds
   * @return mixed
   */
  public function getOne($id, $fileds = '*') {
    $fileds === '*' && $fileds = $this->field;
    $result = $this->mpinfoModel->getOne($id, $fileds);
    return $result ? $this->show($result) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }

  /**
   * 分组更新数据
   * @param int $id <require|number> id
   * @param int $wn_np_type <require|number> 类别不能为空
   * @param int $mppinpaiId <require|number> 品牌不能为空
   * @param int $sheng_id <require|number> 省份不能为空
   * @param int $shi_id <require|number> 所在城市不能为空
   * @param int $qylx_id <require|number> 企业类型不能为空
   * @param string $title <require|number> 企业名称不能为空
   * @return array mixed 返回用户数据
   */

  public function update($id, $data) {
    $result = $this->mpinfoModel->update($id, $data);
    if ($result) {
      $data['id'] = $id;
    }
    return $result ? $this->show($data) : $this->show([]);
  }

  public function getSelData() {

    $data = [
      ''
    ];


  }

}