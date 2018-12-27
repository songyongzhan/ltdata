<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/12/28
 * Time: 11:40
 * Email: 574482856@qq.com
 *
 * 管理权限分组 模型
 */
defined('APP_PATH') OR exit('No direct script access allowed');

class Manage_roleModel extends BaseModel {

  /**
   * 获取分组中的权限
   * @param $manage_id
   */
  public function getRoleGroupAccess($manage_id, $useType, $field = []) {
    //实现联表查询

    $this->_db->join('role_access ra', 'ra.menu_id=m.id', 'left');
    $this->_db->join('manage_role mr', 'ra.role_id=mr.role_id AND mr.manage_id=' . $manage_id, 'left');

    //#可以替换下面那两行
    #$this->_db->join('manage_role mr', 'ra.role_id=mr.role_id AND mr.manage_id=' . $manage_id, 'left');
    //$this->_db->join('manage_role mr', 'ra.role_id=mr.role_id', 'left');
    //$this->_db->joinWhere('mr.manage_id', $manage_id);

    $useType == 0 && $this->_db->where('m.type_id', '1');
    $this->_db->where('m.status', '-1', '>');

    $field = array_map(function ($val) {
      return 'm.' . $val;
    }, $field);

    $result = $this->_db->get('menu m', NULL, $field);
    $this->_logSql();
    return $result;
  }

}