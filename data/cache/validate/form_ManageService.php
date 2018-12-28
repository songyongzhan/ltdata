<?php return array (
  'rules' => 
  array (
    'index' => 
    array (
      'user' => 'require',
      'password' => 'require',
      'type' => 'require|lt:25',
      'message' => 'require',
    ),
    'add' => 
    array (
      'username' => 'require',
      'password' => 'require',
      're_password' => 'require|confirm:password',
      'fullname' => 'require',
    ),
    'update' => 
    array (
      'id' => 'require|number',
      'fullname' => 'require',
    ),
    'getOne' => 
    array (
      'id' => 'require|number',
    ),
    'login' => 
    array (
      'username' => 'require',
      'password' => 'require',
    ),
    'check_token' => 
    array (
      'manage_id' => 'require|number',
      'token' => 'require',
    ),
    'delete' => 
    array (
      'id' => 'require',
    ),
    'password' => 
    array (
      'id' => 'require|number',
      'oldPassword' => 'require',
      'newPassword' => 'require',
      'rePassword' => 'require|matches[newPassword]',
    ),
    'updateManageAccess' => 
    array (
      'id' => 'require|number',
      'role_access' => 'require',
    ),
  ),
  'params' => 
  array (
    'index' => 
    array (
      0 => 'user',
      1 => 'password',
      2 => 'type',
      3 => 'message',
    ),
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
    ),
    'login' => 
    array (
      0 => 'username',
      1 => 'password',
    ),
    'check_token' => 
    array (
      0 => 'token_data',
    ),
    'delete' => 
    array (
      0 => 'id',
    ),
    'password' => 
    array (
      0 => 'id',
      1 => 'oldPassword',
      2 => 'newPassword',
      3 => 'rePassword',
    ),
    'updateManageAccess' => 
    array (
      0 => 'id',
      1 => 'role_access',
    ),
  ),
  'msg' => 
  array (
    'index' => 
    array (
      'user.require' => '',
      'password.require' => '',
      'type.require|lt:25' => '',
      'message.require' => '',
    ),
    'add' => 
    array (
      'username.require' => '用户名',
      'password.require' => '密码',
      're_password.require' => '确认密码',
      're_password.confirm' => '两次密码输入不一致',
      'fullname.require' => '姓名',
    ),
    'update' => 
    array (
      'id.require|number' => '用户ID',
      'fullname.require' => '姓名',
    ),
    'getOne' => 
    array (
      'id.require|number' => '用户ID',
    ),
    'login' => 
    array (
      'username.require' => '用户名',
      'password.require' => '密码',
    ),
    'check_token' => 
    array (
      'manage_id.require|number' => '用户ID',
      'token.require' => 'token验证',
    ),
    'delete' => 
    array (
      'id.require' => '删除id不能为空',
    ),
    'password' => 
    array (
      'id.require|number' => '用户id',
      'oldPassword.require' => '旧密码',
      'newPassword.require' => '新密码',
      'rePassword.require|matches[newPassword]' => '确认密码',
    ),
    'updateManageAccess' => 
    array (
      'id.require|number' => '用户id',
      'role_access.require' => '权限id',
    ),
  ),
  'file' => '/usr/local/nginx/html/ltdata/app/modules/Api/services/Manage.php',
);