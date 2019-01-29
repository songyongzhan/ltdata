<?php

/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class CliExportdataModel extends BaseModel {

  /**
   * @var MyRedis
   */
  protected $redis;


  protected function _init() {
    parent::_init(); // TODO: Change the autogenerated stub

    $this->redis = MyRedis::getInstance(
      [
        'ip' => REDIS_HOST,
        'port' => REDIS_PORT,
        'passwd' => REDIS_PASSWORD,
        'prefix' => PREFIX,
        'expire' => REDIS_EXPIRE,
      ]
    );
  }

  /**
   * 执行数据导入
   * @param array $data
   * @return bool|string
   */
  public function import($data) {
    return $this->insert($data, 'exportdata');
  }

  public function importMulti($multiData) {
    return $this->inserMulti($multiData, 'exportdata');
  }

  public function delMulti($multi_time) {
    $this->setCond(
      [getWhereCondition('multidata', $multi_time)]
    );

    $this->_db->delete('exportdata');
  }

  public function ciq($ciq) {
    if (!$ciq) return '';
    static $ciqDatas;
    if (is_null($ciqDatas)) {
      $result = $this->redis->hGetAll('ciq');
      $ciqDatas = array_flip($result);
    }

    if (isset($ciqDatas[$ciq])) {
      return $ciqDatas[$ciq];
    } else {
      //插入数据库，并在ciq中添加
      $one = $this->getOne([getWhereCondition('title', $ciq)], ['id', 'title'], 'ciq');
      if (!$one) {
        //插入数据库，并在ciq中添加
        $insertId = $this->insert(['title' => $ciq, 'status' => 1], 'ciq');
        if ($insertId) {
          //如果插入成功，并写入redis
          $this->redis->hSet('ciq', $insertId, $ciq);
        }
        $ciqDatas[$ciq] = $insertId;
        $id = $insertId;
      } else {
        $id = $one['id'];
      }
      return $id;
    }
  }

  public function country($country) {
    if (!$country) return '';
    static $countryDatas;
    if (is_null($countryDatas)) {
      $result = $this->redis->hGetAll('country');
      $countryDatas = array_flip($result);
    }

    if (isset($countryDatas[$country])) {
      return $countryDatas[$country];
    } else {


      $one = $this->getOne([getWhereCondition('title', $country)], ['id', 'title'], 'country');
      if (!$one) {
        //插入数据库，并在ciq中添加
        $insertId = $this->insert(['title' => $country, 'status' => 1], 'country');
        if ($insertId) {
          //如果插入成功，并写入redis
          $this->redis->hSet('country', $insertId, $country);
        }
        $countryDatas[$country] = $insertId;
        $countryId = $insertId;
      } else {
        $countryId = $one['id'];
      }

      return $countryId;


    }
  }

  public function trade($trade) {
    if (!$trade) return '';
    static $tradeDatas;
    if (is_null($tradeDatas)) {
      $result = $this->redis->hGetAll('trade');
      $tradeDatas = array_flip($result);
    }

    if (isset($tradeDatas[$trade])) {
      return $tradeDatas[$trade];
    } else {
      //插入数据库，并在ciq中添加
      $one = $this->getOne([getWhereCondition('title', $trade)], ['id', 'title'], 'trade');
      if (!$one) {
        //插入数据库，并在ciq中添加
        $insertId = $this->insert(['title' => $trade, 'status' => 1], 'trade');
        if ($insertId) {
          //如果插入成功，并写入redis
          $this->redis->hSet('trade', $insertId, $trade);
        }
        $tradeDatas[$trade] = $insertId;
        $id = $insertId;
      } else {
        $id = $one['id'];
      }
      return $id;
    }
  }

  public function transport($transport) {
    if (!$transport) return '';
    static $transportDatas;
    if (is_null($transportDatas)) {
      $result = $this->redis->hGetAll('transport');
      $transportDatas = array_flip($result);
    }

    if (isset($transportDatas[$transport])) {
      return $transportDatas[$transport];
    } else {
      //插入数据库，并在ciq中添加


      $one = $this->getOne([getWhereCondition('title', $transport)], ['id', 'title'], 'transport');
      if (!$one) {
        //插入数据库，并在ciq中添加
        $insertId = $this->insert(['title' => $transport, 'status' => 1], 'transport');
        if ($insertId) {
          //如果插入成功，并写入redis
          $this->redis->hSet('transport', $insertId, $transport);
        }
        $transportDatas[$transport] = $insertId;
        $id = $insertId;
      } else {
        $id = $one['id'];
      }
      return $id;
    }
  }

  public function madein($madein) {
    if (!$madein) return '';
    static $madeDatas;
    if (is_null($madeDatas)) {
      $result = $this->redis->hGetAll('made');
      $madeDatas = array_flip($result);
    }

    if (isset($madeDatas[$madein])) {
      return $madeDatas[$madein];
    } else {

      $one = $this->getOne([getWhereCondition('title', $madein)], ['id', 'title'], 'made');
      if (!$one) {
        //插入数据库，并在ciq中添加
        $insertId = $this->insert(['title' => $madein, 'status' => 1], 'made');
        if ($insertId) {
          //如果插入成功，并写入redis
          $this->redis->hSet('made', $insertId, $madein);
        }
        $madeDatas[$madein] = $insertId;
        $madeId = $insertId;
      } else {
        $madeId = $one['id'];
      }

      return $madeId;
    }

  }


  /**
   * 初始化未生成的文件
   * @throws InvalideException
   */
  public function initCsvList() {

    $csvList = $this->getList([getWhereCondition('status', 0)], ['id', 'manage_id', 'where_condition', 'ydyl_param', 'date_type', 'report_id'], '', 'csvlist');

    foreach ($csvList as $val) {
      $this->redis->lpush('csvlist', serialize($val), FALSE);
    }
  }

  //初始化reids数据
  public function initRedisData($array) {

    foreach ($array as $ke => $val) {

      $isInit = FALSE;
      if ($this->redis->exists($val)) {
        $redisCount = $this->redis->hLength($val);
        $tableCount = $this->getCount([], $val);
        if ($redisCount != $tableCount)
          $isInit = TRUE;
        else
          $this->redis->expire($val, REDIS_EXPIRE);


      } else
        $isInit = TRUE;

      if ($isInit) {
        //清空key
        $this->redis->del($val);

        $data = $this->getList([], ['id', 'title'], '', $val, 0);
        $this->redis->hmSet($val, array_column($data, 'title', 'id'));
      }

    }

  }


  /**
   * 初始化数据到redis
   */
  public function initMp() {

    $mpPinpaiList = $this->getList([
      getWhereCondition('status', 1)
    ], ['id', 'ppname'], 'id desc', 'mppinpai', 0);

    if ($mpPinpaiList) {
      $this->redis->del('mppinpai');
      $this->redis->hmSet('mppinpai', array_column($mpPinpaiList, 'ppname', 'id'));
    }

  }


}
