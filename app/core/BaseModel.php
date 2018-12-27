<?php
/**
 * Created by PhpStorm.
 * User: songyongzhan
 * Date: 2018/10/17
 * Time: 14:08
 * Email: songyongzhan@qianbao.com
 */

defined('APP_PATH') OR exit('No direct script access allowed');

class BaseModel extends CoreModel {

  use TraitCommon;

  /**
   * @var MysqliDb
   */
  protected $_db;

  /**
   * @var table
   */
  protected $table;

  protected $id = 'id'; //表主键

  private $_querySqls; //执行过的sql语句

  protected $prefix = ''; //表前缀
  /**
   * 创建时间的字段  设置成 protected 在子类中修改
   * @var string
   */
  protected $createtime = 'createtime';
  /**
   * 更新时的字段  设置成 protected 在子类中修改
   * @var string
   */
  protected $updatetime = 'updatetime';

  /**
   * 是否自动添加 createtime  和 updatetime
   * 如果设置true 则所有表中必须包含此字段，否则报错
   * @var bool
   */
  protected $autoaddtime = TRUE;

  /**
   * 是否真实删除，默认为false  逻辑删除
   * @var bool
   */
  protected $realDelete = FALSE;

  public static $header = [

  ];

  protected function _init() {
    $this->_db = Yaf_Registry::has('db') ? Yaf_Registry::get('db') : NULL;
    $this->table = $this->prefix . strtolower(substr(get_class($this), 0, -5));
    $this->autoaddtime = Tools_Config::getConfig('db.mysql.auto_addtime');
    $this->prefix = Tools_Config::getConfig('db.mysql.prefix');
  }

  /**
   * 添加数据到数据库
   * @param array $data
   * @param null $table
   * @return bool
   */
  public function insert($data, $table = NULL) {
    is_null($table) || $this->table = $table;
    $result = $this->_db->insert($this->table, $this->autoAddtimeData($data, 'insert'));
    $this->_querySqls[] = $this->getLastQuery();
    return $result ? $this->_db->getInsertId() : 0;
  }

  /**
   * 批量插入数据
   * @param $data
   * @param null $table
   * @return bool|string
   */
  public function inserMulti($data, $table = NULL) {
    is_null($table) || $this->table = $table;
    $ids = $this->_db->insertMulti($this->table, $data);
    $this->_logSql();
    if (!$ids)
      return FALSE;
    else
      return implode(',', $ids);
  }


  public function update($where, $data, $table = NULL) {
    is_null($table) || $this->table = $table;
    $data = $this->autoAddtimeData($data);
    $this->setCond($where);
    $result = $this->_db->update($this->table, $data);
    $this->_logSql();
    return $result;
  }


  /**
   * 删除
   * @param $where
   * @param null $table
   * @return bool
   * @throws InvalideException
   */
  public function delete($where, $table = NULL) {
    is_null($table) || $this->table = $table;
    $this->setCond($where);
    $result = $this->realDelete ? $this->_db->delete($this->table) : $this->update($where, ['status' => -1]);
    $this->_logSql();
    return $result;
  }

  public function getOne($where, $fileds = [], $table = NULL) {
    is_null($table) || $this->table = $table;
    empty($fileds) && $fileds = '*';
    $this->setCond($where);
    $result = $this->_db->getOne($this->table, $fileds);
    $this->_logSql();
    return $result;
  }

  /**
   * 按条件返回返回条件中的数据 最大条数限制100条
   * @param $where
   * @param array $fileds
   * @param null $table
   * @param int $maxSize 系统默认做了一个限制，如果不限制请传递0
   * @return array
   * @throws InvalideException
   */
  public function getList($where, $fileds = [], $order = '', $table = NULL, $maxSize = 1000) {
    is_null($table) || $this->table = $table;
    empty($fileds) && $fileds = '*';
    $this->setCond($where);
    empty($order) && $order = $this->id . ' desc';
    list($orderField, $orderType) = explode(' ', $order);
    $this->_db->orderBy($orderField, $orderType);
    $rowNum = [0, abs($maxSize)];
    $maxSize === 0 && $rowNum = NULL;
    $result = $this->_db->get($this->table, $rowNum, $fileds);
    $this->_logSql();
    return $result;
  }


  /**
   * 返回搜索条件中的总数量
   * @param $where
   * @param null $table
   * @return mixed
   * @throws InvalideException
   */
  public function getCount($where, $table = NULL) {
    is_null($table) || $this->table = $table;
    $this->setCond($where);
    $result = $this->_db->getValue($this->table, "count(id)");
    $this->_logSql();
    return $result;
  }

  /**
   * 分页
   * @param $where
   * @param array $fileds
   * @param int $pageNum
   * @param int $pageSize
   * @param null $table
   */
  public function getListPage($where = [], $fileds = [], $pageNum = 1, $pageSize = PAGESIZE, $order = '', $table = NULL) {
    is_null($table) || $this->table = $table;
    empty($fileds) && $fileds = '*';
    $this->setCond($where);
    empty($order) && $order = $this->id . ' desc';
    list($orderField, $orderType) = explode(' ', $order);
    $this->_db->orderBy($orderField, $orderType);
    $this->_db->pageLimit = $pageSize;
    $result = $this->_db->paginate($this->table, $pageNum, $fileds);
    $this->_logSql();
    return page_data($result, $this->_db->totalCount, $pageNum, $pageSize, $this->_db->totalPages);
  }

  /**
   * 记录并处理sql
   */
  protected final function _logSql() {
    $lastQuerySql = $this->getLastQuery();
    $this->_querySqls[] = $lastQuerySql;
    isEnv() && debugMessage($lastQuerySql);
    debugMessage('Sql execute result:' . $this->_db->getLastErrno() . ' ErrMessage:' . $this->_db->getLastError());
  }

  /**
   * 拼装where条件
   * @param $where
   * @throws InvalideException
   */
  protected final function setCond($where) {

    if (!$this->realDelete) {
      $dbwhere = $this->_db->getWhere();
      $dbwhere = array_column($dbwhere, 1);
      $flag = FALSE;
      foreach ($dbwhere as $val) {
        if (stristr($val, 'status')) {
          $flag = TRUE;
          break;
        }
      }
      //如果逻辑删除，需要拼装status
      if (!$flag) {
        $this->_db->where('status', -1, '>');
        debugMessage('系统自动添加了逻辑删除过滤值 status ');
      }
    }

    if (!$where) return;
    $map = [];
    if (is_numeric($where))
      $this->_db->where($this->id, $where);
    else if (is_array($where))
      $map = $where;
    else
      throw new InvalideException('$where param error.', 500);

    //切记，这里只是实现了where 条件 其他的条件，请在业务中 自行实现
    if ($map) {
      foreach ($map as $key => $val) {
        $this->_db->where($val['field'], $val['val'], isset($val['operator']) ? $val['operator'] : '=', isset($val['condition']) ? $val['condition'] : 'AND');
      }
    }
  }

  /**
   * 判断数据库中表是否存在
   * @param $table
   * @param bool $autoAddPrefix 是否自动添加表前缀
   * @return bool
   */
  private function tableExists($table, $autoAddPrefix = TRUE) {
    $table = $autoAddPrefix ? $this->prefix . $table : $table;
    return $this->_db->tableExists($table);
  }

  /**
   * 自动处理添加 createtime  updatetime
   * @param array $data
   * @param string $fun
   * @return array
   */
  private function autoAddtimeData($data, $fun = NULL) {
    if ($this->autoaddtime) {
      debugMessage('开启自动添加时间戳 updatetime  createtime');
      if (!is_null($fun) && $fun === 'insert') {
        $data[$this->createtime] = time();
        $data[$this->updatetime] = time();
      } else
        $data[$this->updatetime] = time();
    }
    return $data;
  }

  /**
   * 支持执行sql语句并返回结果
   * @param string $sql sql中需要带问号的语句
   * @param array $params 数组 与sql 中问号一一对应
   * @return array
   * @throws InvalideException
   */
  public function query($sql, $params = []) {
    if (empty($sql)) throw new InvalideException('sql param error.', 500);
    $result = $this->_db->rawQuery($sql, $params);
    $this->_querySqls[] = $this->getLastQuery();
    return $result;
  }

  /**
   * 返回影响的条数
   * @param string $sql sql中需要带问号的语句
   * @param array $params 数组 与sql 中问号一一对应
   * @return string
   * @throws InvalideException
   */
  public function exec($sql, $params = []) {
    if (empty($sql)) throw new InvalideException('sql param error.', 500);
    $this->_db->rawQuery($sql, $params);
    $this->_querySqls[] = $this->getLastQuery();
    return $this->_db->count;
  }

  /**
   * 获取此次执行的sql
   * @return mixed
   */
  public function getSqls() {
    return $this->_querySqls;
  }

  /**
   * 放回当前处理的url
   * @return string
   */
  public function getLastQuery() {
    return $this->_db->getLastQuery();
  }

  /**
   * 开启数据库事务
   */
  public function startTransaction() {
    return $this->_db->startTransaction();
  }

  /**
   * 回滚事务
   * @return bool
   */
  public function rollback() {
    return $this->_db->rollback();
  }

  /**
   * 提交事务
   * @return bool
   */
  public function commit() {
    return $this->_db->commit();
  }

  public function chooseConnection($name) {
    return $this->_db->connection($name);
  }

  /**
   * 开启sql调试
   * @param bool $enable
   * @param null $stripPrefix
   */
  public function startTrace($enable = TRUE, $stripPrefix = NULL) {
    $this->_db->setTrace($enable, $stripPrefix);
  }

  /**
   * 获取调试信息
   * @return array
   */
  public function getTrace() {
    return $this->_db->trace;
  }

  public function setTable($table) {
    if (!$table) return FALSE;
    $this->table = $this->prefix . strtolower($table);
    return TRUE;
  }

}