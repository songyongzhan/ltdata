<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends BaseController {

  public function detailAction() {

    //url

    //http://k.yt99.com/article/thumb/201809/20/thumb_1015395ba302cb024232ataQm.jpg!94
    //$this->newsModel->geturl("/article/thumb/201809/20/thumb_1015395ba302cb024232ataQm.jpg!94");


    //https://k.yt99.com/6c15947fe2ca9ed96ee79d72cb2a67f9/5be4ead6/article/201809/06/1142265b90a22286ec6oolIJP.gif
    //$this->newsModel->geturl('/fbb982125724a79e8ef9e7f79a23ed95/5be5234d/article/201809/06/1142275b90a223797bbTvEkFv.gif');
    $this->newsModel->geturl('/f658a2c48968c591cc0ced92192ae17b/5be52dfa/meiwen/aaez6kgg46qo0u0rx5pwehwf.m4a');




    P($this->newsModel->getResponseHeaders());

    //P($this->newsModel->getRequestHeaders());
    exit;

    $data = [
      'nav' => 3,
      'title' => '321',
      'bodycontent' => '123123123123123123content',
      'mtime' => time()
    ];

    $result=$this->newsModel->insert($data,'bb');




    $multidata = [

      [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ], [
        'nav' => 3,
        'title' => rand(100, 999),
        'bodycontent' => '123123123123123123content',
        'mtime' => time()
      ]

    ];

    //$result=$this->NewsModel->inserMulti($multidata,'bb');




    /* $newdata=['title'=>'songsong'];
     $result=$this->NewsModel->update(3,$newdata,'bb');*/


    //$result=$this->NewsModel->getOne(10,[],'bb');


    //$result=$this->NewsModel->del(44,'bb');
  /*$newModel=$this->NewsModel;
   $result = $newModel->getListPage([
      'id' => [
        'val' => 30,
        'operator' => '>=',
        'condition' => 'and'
      ],



    $db->where ("fullName", 'John%', 'like');


      'title'=>['val'=>'386']
    ], ['*'], 1, 3, '','bb');
    P($result);*/

    $newModel=$this->NewsModel;
    $result = $newModel->getListPage([
      'id' => [
        'val' => 30,
        'operator' => '>=',
        'condition' => 'and'
      ]
    ], ['*'], 1, 3, '','bb');
    P($result);

    //$result=$this->NewsModel->getLastQuery();


   /* P(spl_object_id($newModel));
    P(spl_object_id($this->NewsModel));

    P($this->NewsModel);*/

    /*$sqls=$this->NewsModel->getSqls();

    Pv($sqls);*/


    //P($this->NewsModel->getLasqQuery());

    $result=$this->NewsModel->query('select * from bb where id >? order by id desc limit 3',[30]);
    P($result);


    /**
     *
     *
     *
     *
    帮助文档 https://packagist.org/packages/joshcam/mysqli-database-class
     *
     *
     *
     * $db->setQueryOption ('SQL_NO_CACHE');
    $db->get("users");
    // GIVES: SELECT SQL_NO_CACHE * FROM USERS;
    Optionally you can use method chaining to call where multiple times without referencing your object over and over:

    $results = $db
    ->where('id', 1)
    ->where('login', 'admin')
    ->get('users');
     *
     *
     *
     *
     如果设置字段a=b

     需要这样设置 $db->where('a=b');
     不能使用 $db->where('a','b');
     *
     *
     *
     *
     *
     *
     *
     *
     * BETWEEN / NOT BETWEEN:

    $db->where('id', Array (4, 20), 'BETWEEN');
    // or $db->where ('id', Array ('BETWEEN' => Array(4, 20)));

    $results = $db->get('users');
    // Gives: SELECT * FROM users WHERE id BETWEEN 4 AND 20
    IN / NOT IN:

    $db->where('id', Array(1, 5, 27, -1, 'd'), 'IN');
    // or $db->where('id', Array( 'IN' => Array(1, 5, 27, -1, 'd') ) );

    $results = $db->get('users');
    // Gives: SELECT * FROM users WHERE id IN (1, 5, 27, -1, 'd');
    OR CASE:

    $db->where ('firstName', 'John');
    $db->orWhere ('firstName', 'Peter');
    $results = $db->get ('users');
    // Gives: SELECT * FROM users WHERE firstName='John' OR firstName='peter'
    NULL comparison:

    $db->where ("lastName", NULL, 'IS NOT');
    $results = $db->get("users");
    // Gives: SELECT * FROM users where lastName IS NOT NULL
    LIKE comparison:

    $db->where ("fullName", 'John%', 'like');
    $results = $db->get("users");
    // Gives: SELECT * FROM users where fullName like 'John%'
    Also you can use raw where conditions:

    $db->where ("id != companyId");
    $db->where ("DATE(createdAt) = DATE(lastLogin)");
    $results = $db->get("users");
    Or raw condition with variables:

    $db->where ("(id = ? or id = ?)", Array(6,2));
    $db->where ("login","mike")
    $res = $db->get ("users");
    // Gives: SELECT * FROM users WHERE (id = 6 or id = 2) and login='mike';
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *Order by values example:

    $db->orderBy('userGroup', 'ASC', array('superuser', 'admin', 'users'));
    $db->get('users');
    // Gives: SELECT * FROM users ORDER BY FIELD (userGroup, 'superuser', 'admin', 'users') ASC;
    If you are using setPrefix () functionality and need to use table names in orderBy() method make sure that table names are escaped with ``.

    $db->setPrefix ("t_");
    $db->orderBy ("users.id","asc");
    $results = $db->get ('users');
    // WRONG: That will give: SELECT * FROM t_users ORDER BY users.id ASC;

    $db->setPrefix ("t_");
    $db->orderBy ("`users`.id", "asc");
    $results = $db->get ('users');
     *
     *
    A subquery with an alias specified to use in JOINs . Eg. (select * from users) sq

    $sq = $db->subQuery("sq");
    $sq->get ("users");
    Subquery in selects:

    $ids = $db->subQuery ();
    $ids->where ("qty", 2, ">");
    $ids->get ("products", null, "userId");

    $db->where ("id", $ids, 'in');
    $res = $db->get ("users");
    // Gives SELECT * FROM users WHERE id IN (SELECT userId FROM products WHERE qty > 2)
    Subquery in inserts:

    $userIdQ = $db->subQuery ();
    $userIdQ->where ("id", 6);
    $userIdQ->getOne ("users", "name"),

    $data = Array (
    "productName" => "test product",
    "userId" => $userIdQ,
    "lastUpdated" => $db->now()
    );
    $id = $db->insert ("products", $data);
    // Gives INSERT INTO PRODUCTS (productName, userId, lastUpdated) values ("test product", (SELECT name FROM users WHERE id = 6), NOW());
    Subquery in joins:

    $usersQ = $db->subQuery ("u");
    $usersQ->where ("active", 1);
    $usersQ->get ("users");

    $db->join($usersQ, "p.userId=u.id", "LEFT");
    $products = $db->get ("products p", null, "u.login, p.productName");
    print_r ($products);
    // SELECT u.login, p.productName FROM products p LEFT JOIN (SELECT * FROM t_users WHERE active = 1) u on p.userId=u.id;
    EXISTS / NOT EXISTS condition
    $sub = $db->subQuery();
    $sub->where("company", 'testCompany');
    $sub->get ("users", null, 'userId');
    $db->where (null, $sub, 'exists');
    $products = $db->get ("products");
    // Gives SELECT * FROM products WHERE EXISTS (select userId from users where company='testCompany')
    Has method
    A convenient function that returns TRUE if exists at least an element that satisfy the where condition specified calling the "where" method before this one.

    $db->where("user", $user);
    $db->where("password", md5($password));
    if($db->has("users")) {
    return "You are logged";
    } else {
    return "Wrong user/password";
    }
     *
     *
     *
     * 总数
     * $offset = 10;
     *
    $count = 15;
    $users = $db->withTotalCount()->get('users', Array ($offset, $count));
    echo "Showing {$count} from {$db->totalCount}";
     */

    exit;


    /* $model=new SampleModel();
     $model->selectSample();*/
    $this->SampleModel->selectSample();


    //$arr=['id'=>'iser','name'=>'aa'];
    //$this->_setCookie('vv',$arr);

    //$this->_setCookie('user','james');
    //$this->_delCookie('vv');

    //P($this->_getCookie('vv'));
    //var_dump($this->_getCookie('user'));

    //$this->assign('user', 'james');

    $this->getView()->display('/index/detail.html');
    //echo $this->_render('/index/detail.html');


  }


  public function bbAction() {

    /*   debugMessage('debug 日志');
       logMessage('info','asdfadsfas');
       logMessage('warning','warningwarningwarningwarningwarningwarningwarningwarningwarningwarning');
       logMessage('debug',['name'=>'james','age'=>'33']);*/


    //return json_encode(['name'=>'james','age'=>'33']);

    echo json_encode(['name' => 'james', 'age' => '33']);
    return TRUE;
    //$this->getResponse()->setBody('content', ['name' => 'james', 'age' => '33']);


    //var_dump($vv instanceof $this);
    /*   $requet = Yaf_Application::app()->getDispatcher()->getRouter();
       $relation = new ReflectionClass($this);

       P($relation->getReflectionConstants());*/

    //throw new Exception('132456');
  }

  public function testAction() {
    echo 'test';
  }


  public function vvAction() {
    //var_dump(Tools_Request::getRequest()->getModuleName());
    $this->_name;
    $model = new SampleModel();

    Yaf_Application::app()->getDispatcher()->getRouter();


    P($this->getParams());

    P(Yaf_Application::app()->getLastErrorMsg(), 'var_dump');
    P(Yaf_Application::app()->getLastErrorNo());

    $request = $this->getRequest();
    $yafRequest = Yaf_Application::app()->getDispatcher()->getRequest();
    var_dump($request instanceof $yafRequest);

    printf("<br>====================================");
    //P($this->_setSession('user',['user'=>'name','age'=>33]),'var_dump');
    P($this->_setSession('user', 'kkkkkkkkkk'), 'var_dump');

    P($this->_hasSession('user'), 'var_dump');

    P($this->_getSession('user'), 'var_dump');
    //
    //P($this->_delSession('user'),'var_dump');
    //
    //P($this->_getSession('user'),'var_dump');

    printf("====================================");


    var_dump($this->getRequest()->getException());

    //throw new Exception('asdfasdfasdf');

    throw new Exception('132456');


    /*

      ini_set('yaf.environ','develop');


      P(getDispatcher()->getRouter());*/

    P(ini_get('yaf.environ'));


    P(Yaf_Application::app()->environ());

    //P(isAjax());

    /*  $route=new Yaf_Route_Rewrite('a',['controller'=>'index','action'=>'vv']);


      var_dump($this->getView());


      P(app()->getConfig());*/

    //P( Yaf_Loader::getInstance());


    //Yaf_Loader::getInstance()->registerLocalNamespace(array(APPLICATION_PATH.'/tuozhan/smarty/sysplugins', "Bar","vv"));
    //P(Yaf_Loader::getInstance()->getLocalNamespace());
    //var_dump($this->getView()->display('/index/index.html'));

    //$this->assign('username','james');
    $this->getView()->assign('username', 'james');


    $this->getView()->display('/index/index.html');


    /* P(TEMPLATE_DIR);

     P(ENVIRONMENT);

     P($this->_get('username'));
     var_dump($route);*/

  }

  /**
   * 默认动作
   * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
   * 对于如下的例子, 当访问http://yourhost/web/index/index/index/name/root 的时候, 你就会发现不同
   */
  public function indexAction($name = "Stranger") {
    //1. fetch query
    $get = $this->getRequest()->getQuery("get", "default value");

    echo 111;
    var_dump($get);
    exit;
    //2. fetch model
    $model = new SampleModel();

    //3. assign
    $this->getView()->assign("content", $model->selectSample());
    $this->getView()->assign("name", $name);

    //4. render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
    return TRUE;
  }
}
