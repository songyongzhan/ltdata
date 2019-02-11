<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2019/221
 * Time: 22:45
 * Email: 574482856@qq.com
 *
 * 横向权限模型
 */
defined('APP_PATH') OR exit('No direct script access allowed');

class PermissionModel extends BaseModel {


  protected $autoaddtime = FALSE;

  protected $id = 'manage_id';

  
  public function getPermission() {
    $data = [
      'export' => [
        'text' => '出口数据',
        'data' => [
          '40111000' => '40111000',
          '40112000' => '40112000'
        ]
      ],
      'pcr' => [
        'text' => 'PCR价格数据',
        'data' => [
          'pf_pricle' => '批发净价',
          'stls_pricle' => '实体零售均价',
          'th_pricle' => '途虎',
          'jd_pricle' => '京东',
          'gfqj_pricle' => '官方旗舰店'
        ]
      ],
      'mpinfo' => [
        'text' => '代理商信息',
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