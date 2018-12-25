<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/18
 * Time: 15:17
 * Email: songyongzhan@qianbao.com
 */

class MenuService extends BaseService {

  const FIELD = ['id', 'title', 'pid', 'url', 'relation_url', 'ext', 'type_id', 'status', 'sort_id', 'createtime'];


  /**
   * 获取栏目列表
   * @param array $where 搜索条件
   * @param int useType 用途 如果是1 显示栏目列表用于修改 ,不传值 或传0 用于左侧栏目显示
   */
  public function getList($where, $useType) {

  }

  /**
   * 添加栏目
   * @param string $title <required> 栏目标题
   * @param int $pid <required|numeric> 父级id
   * @param string $url <required> 栏目url地址
   * @param string $ext 扩展信息
   * @param array $data 需要添加到的数据
   * @return mixed
   */
  public function add($data) {

    if ($data['type_id'] == 2 && $data['pid'] == 0) showApiException('方法不能放在顶级菜单下', StatusCode::PARAMS_ERROR);


    $lastInsertId = $this->menuModel->add($data);
    if ($lastInsertId) {
      $data['id'] = $lastInsertId;
      return $this->show($data);
    } else {
      return $this->show([], StatusCode::INSERT_FAILURE);
    }
  }

  /**
   * 栏目更新
   * @param int $id <required|numeric> 栏目ID
   * @param array $data 需要更新的数据
   * @return mixed
   * @throws Exception
   */
  public function update($id, $data) {
    if (empty($data)) {
      showApiException('请求参数错误', StatusCode::PARAMS_ERROR);
    }
    if ($data['type_id'] == 2 && $data['pid'] == 0) showApiException('方法不能放在顶级菜单下', StatusCode::PARAMS_ERROR);
    $result = $this->menuModel->update($id, $data);
    if ($result) {
      $data['id'] = $id;
    }
    return $result ? $this->show($data) : $this->show([]);
  }

  /**
   * 获取单个栏目
   * @param int $id <required|numeric> 栏目id
   * @param string $field 获取栏目的信息
   * @return mixed
   */
  public function getOne($id, $field = "*") {
    $result = $this->menuModel->getOne($id, $field);
    return $result ? $this->show($result) : $this->show([]);
  }


  /**
   * 获取指定栏目
   * @param string $menu_ids 栏目ids 如果为空 获取全部  如果指定id 根据id返回
   * @param int $platform_id <required|numeric> 平台id
   * @return mixed
   */
  public function getAppointMenuList($menu_ids, $platform_id) {
    $menu_id_arr = [];
    if (!empty($menu_ids)) {
      $menu_id_arr = explode(',', $menu_ids);
    }

  }

  /**
   * 删除栏目
   * @param int $id <required> 栏目ids 多个栏目使用,分割
   * @return mixed
   */
  public function delete($id) {
    $result = $this->menuModel->delete($id, platform_where());
    return $result ? $this->show(['row' => $result, 'id' => $id]) : $this->show([], StatusCode::DATA_NOT_EXISTS);
  }



  /**
   * 批量排序
   * @param string $sortStr <required> 更新数据
   * @return array
   */
  public function batchSort($sortStr) {
    $sortStr = trim($sortStr, '|');
    $sort = explode('|', $sortStr);
    $data = [];
    foreach ($sort as $key => $val) {
      list($id, $sortId) = explode(':', $val);
      $data[] = [
        'id' => $id,
        'sort_id' => $sortId
      ];
    }
    $result = $this->menuModel->batchSort($data);
    return $this->show(['row' => $result]);
  }


}