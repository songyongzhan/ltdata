<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/1/10
 * Time: 11:45
 * Email: 574482856@qq.com
 *
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class ReportListController extends ApiBaseController {

  /**
   * 获取分组列表
   * @return mixed
   */
  public function getListAction() {
    //如果传递了page_size 就分页
    $page_size = $this->_post('page_size', PAGESIZE);
    $page_num = $this->_post('page_num', 1);
    $title = $this->_post('title', '');
    $utype = $this->_post('utype', '');

    $rules = [
      ['condition' => 'like',
        'key_field' => ['title'],
        'db_field' => ['title']
      ],
      [
        'condition' => '=',
        'key_field' => ['utype'],
        'db_field' => ['utype']
      ]
    ];

    $data = [
      'title' => $title,
      'utype' => $utype,
    ];

    $where = $this->where($rules, array_filter($data, 'filter_empty_callback'));
    $result = $this->reportlistService->getList($where, $page_num, $page_size);
    return $result;
  }

  /**
   * 获取可以分析的模型
   * @return mixed
   */
  public function getListByreportAction() {
    $utype = $this->_post('utype', '');
    $result = $this->reportlistService->getListByreport($utype);
    return $result;
  }


  /**
   * 添加分组
   * @param string $title <POST> 名称
   * @return array
   */
  public function addAction() {
    $data = $this->_getPostData();
    $result = $this->reportlistService->add($data);
    return $result;
  }


  /**
   * 更新分组名称
   * @param string $title <POST> 名称
   * @param string $id <POST> id
   * @return array
   */
  public function updateAction() {
    $data = $this->_getPostData();
    $id = $this->_post('id');
    $result = $this->reportlistService->update($id, $data);
    return $result;
  }

  /**
   * 得到一个分组信息
   * @param int $id <POST> 用户id
   * @return array|mixed
   */
  public function getOneAction() {
    $id = $this->_post('id');
    $result = $this->reportlistService->getOne($id);
    return $result;
  }

  /**
   * 分组删除
   * @param string $id <POST> 数据id ，如果删除多个，请使用逗号分隔
   * @return 删除数据的id
   */
  public function deleteAction() {
    $id = $this->_post('id');
    $result = $this->reportlistService->delete($id);
    return $result;
  }

  /**
   * 获取add update 提交的参数
   * @return array
   */
  private function _getPostData() {
    $title = $this->_post('title');
    $title2 = $this->_post('title2');
    $utype = $this->_post('utype');
    $viewtype = $this->_post('viewtype');
    $field_str = $this->_post('field_str');
    $group_str = $this->_post('group_str');
    $order_str = $this->_post('order_str');
    $limit_str = $this->_post('limit_str');
    $having_str = $this->_post('having_str');
    $date_type = $this->_post('date_type');
    $remarks = $this->_post('remarks');
    $table_column = $this->_post('table_column');
    $data = [
      'title' => $title,
      'title2' => $title2,
      'utype' => $utype,
      'viewtype' => $viewtype,
      'field_str' => $field_str,
      'group_str' => $group_str,
      'order_str' => $order_str,
      'limit_str' => $limit_str,
      'having_str' => $having_str,
      'date_type' => $date_type,
      'remarks' => $remarks,
      'table_column' => $table_column,
      'status' => 1
    ];
    return $data;
  }

}