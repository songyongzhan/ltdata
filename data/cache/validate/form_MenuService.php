<?php return array (
  'rules' => 
  array (
    'add' => 
    array (
      'title' => 'require',
      'pid' => 'require|number',
      'url' => 'require',
    ),
    'update' => 
    array (
      'id' => 'require|number',
    ),
    'getOne' => 
    array (
      'id' => 'require|number',
    ),
    'delete' => 
    array (
      'id' => 'require',
    ),
    'batchSort' => 
    array (
      'sortStr' => 'require',
    ),
  ),
  'params' => 
  array (
    'add' => 
    array (
      0 => 'data',
    ),
    'update' => 
    array (
      0 => 'id',
      1 => 'data',
    ),
    'getOne' => 
    array (
      0 => 'id',
      'field' => 
      array (
        0 => 'id',
        1 => 'title',
        2 => 'pid',
        3 => 'url',
        4 => 'relation_url',
        5 => 'ext',
        6 => 'type_id',
        7 => 'status',
        8 => 'sort_id',
        9 => 'updatetime',
        10 => 'createtime',
      ),
    ),
    'delete' => 
    array (
      0 => 'id',
    ),
    'batchSort' => 
    array (
      0 => 'sortStr',
    ),
  ),
  'msg' => 
  array (
    'add' => 
    array (
      'title.require' => '栏目标题',
      'pid.require|number' => '父级id',
      'url.require' => '栏目url地址',
    ),
    'update' => 
    array (
      'id.require|number' => '栏目ID',
    ),
    'getOne' => 
    array (
      'id.require|number' => '栏目id',
    ),
    'delete' => 
    array (
      'id.require' => '栏目ids',
    ),
    'batchSort' => 
    array (
      'sortStr.require' => '更新数据',
    ),
  ),
  'file' => '/usr/local/nginx/html/ltdata/app/modules/Api/services/Menu.php',
);