<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/2/21
 * Time: 22:45
 * Email: 574482856@qq.com
 *
 * 横向权限模型
 */
defined('APP_PATH') OR exit('No direct script access allowed');

class PermissionModel extends BaseModel {


  protected $autoaddtime = FALSE;

  protected $id = 'manage_id';

  protected $realDelete = TRUE;

  /**
   * 设置权限
   * @param $id
   * @param $premission
   * @return int|string
   */
  public function setPermission($id, $permission) {
    return $this->update($id, ['permission' => serialize($permission)]);
  }

  /**
   * 获取当前用户权限
   * @param $id
   * @return array
   */
  public function getPermission($id) {
    $result = $this->getOne($id);
    if (!$result) {
      $result = ['manage_id' => $id, 'permission' => ''];
      $this->insert($result);
    }

    if (isset($result['permission']) && $result['permission'] != '')
      $result['permission'] = unserialize($result['permission']);

    return $result;
  }

  /**
   * 显示权限数据
   * @return array
   */
  public function viewPermission() {
    $data = [
      'export' => [
        'text' => '出口数据',
        'text_key' => 'export',
        'data' => [
          '40111000' => '轿车轮胎',
          '40112000' => '卡客车轮胎'
        ]
      ],
      'pcr' => [
        'text' => 'PCR价格数据',
        'text_key' => 'pcr',
        'data' => [
          //'pf_pricle' => '批发净价',
          //'stls_pricle' => '实体零售均价',
          //'th_pricle' => '途虎',
          //'jd_pricle' => '京东',
          //'gfqj_pricle' => '官方旗舰店'
          'pf_pricle' => '批发价',
          'stls_pricle' => '实体零售价',
          'th_pricle' => '途虎价',
          'jd_pricle' => '京东价',
          'gfqj_pricle' => '官方旗舰店价'
        ]
      ],
      'mpinfo' => [
        'text' => '代理商信息',
        'text_key' => 'mpinfo',
        'data' => [
          'mobile' => '手机',
          'xsarea' => '销售区域',
          'sell' => '销售量'
        ]
      ]
    ];
    return $data;
  }

}