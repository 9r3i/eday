<?php
/* eday
 * ~ 9r3i's 5th php framework
 * ~ code name: echo
 * started at august 24th 2018
 * continue at november 13th 2019 -- version 2.0.0 -- add method databaseKDB for testing
 * continue at november 28th 2019 -- version 2.1.0 -- being library namespace eday
 */
namespace eday;
class eday{
  const version='2.0.0';
  public static $error=false;
  /* error statements */
  public static function error($s=null,int $c=200){
    $s=is_string($s)?$s:'Unknown error.';
    $h=self::httpStatus($c);
    $s='Error: '.$s;
    if(!headers_sent()){
      header("HTTP/1.1 {$c} {$h}");
      header('Content-Type: text/plain;charset=utf-8;');
      header('Content-Length: '.strlen($s));
    }exit($s);
  }
  /* http response status by code */
  public static function httpStatus(int $code=200){
    $httpStatus=[
      100=>"Continue",
      101=>"Switching Protocols",
      102=>"Processing",
      200=>"OK",
      201=>"Created",
      202=>"Accepted",
      203=>"Non-Authoritative Information",
      204=>"No Content",
      205=>"Reset Content",
      206=>"Partial Content",
      207=>"Multi-Status",
      300=>"Multiple Choices",
      301=>"Moved Permanently",
      302=>"Found",
      303=>"See Other",
      304=>"Not Modified",
      305=>"Use Proxy",
      306=>"(Unused)",
      307=>"Temporary Redirect",
      308=>"Permanent Redirect",
      400=>"Bad Request",
      401=>"Unauthorized",
      402=>"Payment Required",
      403=>"Forbidden",
      404=>"Not Found",
      405=>"Method Not Allowed",
      406=>"Not Acceptable",
      407=>"Proxy Authentication Required",
      408=>"Request Timeout",
      409=>"Conflict",
      410=>"Gone",
      411=>"Length Required",
      412=>"Precondition Failed",
      413=>"Request Entity Too Large",
      414=>"Request-URI Too Long",
      415=>"Unsupported Media Type",
      416=>"Requested Range Not Satisfiable",
      417=>"Expectation Failed",
      418=>"I'm a teapot",
      419=>"Authentication Timeout",
      420=>"Enhance Your Calm",
      422=>"Unprocessable Entity",
      423=>"Locked",
      424=>"Failed Dependency",
      424=>"Method Failure",
      425=>"Unordered Collection",
      426=>"Upgrade Required",
      428=>"Precondition Required",
      429=>"Too Many Requests",
      431=>"Request Header Fields Too Large",
      444=>"No Response",
      449=>"Retry With",
      450=>"Blocked by Windows Parental Controls",
      451=>"Unavailable For Legal Reasons",
      494=>"Request Header Too Large",
      495=>"Cert Error",
      496=>"No Cert",
      497=>"HTTP to HTTPS",
      499=>"Client Closed Request",
      500=>"Internal Server Error",
      501=>"Not Implemented",
      502=>"Bad Gateway",
      503=>"Service Unavailable",
      504=>"Gateway Timeout",
      505=>"HTTP Version Not Supported",
      506=>"Variant Also Negotiates",
      507=>"Insufficient Storage",
      508=>"Loop Detected",
      509=>"Bandwidth Limit Exceeded",
      510=>"Not Extended",
      511=>"Network Authentication Required",
      598=>"Network read timeout error",
      599=>"Network connect timeout error"
    ];
    return isset($httpStatus[$code])?$httpStatus[$code]:'Unknown';
  }
  /* initialize */
  public static function config(){
    /* check config */
    if(!defined('EDAY_CONFIG')
      ||!preg_match('/^O:\d+:"stdClass":\d+:{/',EDAY_CONFIG)){
      return self::setError('Failed to get configuration.');
    }
    /* return parsed config object */
    return @unserialize(EDAY_CONFIG);
  }
  /* initialize */
  public static function initialize(){
    /* check initialized */
    if(defined('EDAY_INITIALIZED')){
      return self::setError('The engine has been initialized.');
    }
    /* initilize definition */
    if(!self::definition()){
      if(self::$error){return false;}
      return self::setError('Failed to initialize engine definition.');
    }
    /* initilize apps */
    self::apps();
    /* initilize config */
    if(!self::configInit()){
      if(self::$error){return false;}
      return self::setError('Failed to initialize configuration.');
    }
    /* globalize eday database
     * for object database, for only once connected
     */
    global $_EDB;
    /* initilize database */
    $_EDB=self::databaseSDB();
    if(!$_EDB){
      if(self::$error){return false;}
      return self::setError('Failed to initialize database.');
    }
    /* define access token */
    $token='eday-'.preg_replace('/[^a-z0-9]/i','',base64_encode(hash('sha1',date('YmdHi'),true)));
    defined('EDAY_ACCESS_TOKEN') or define('EDAY_ACCESS_TOKEN',$token);
    /* request direct access to files directory using token get */
    if(isset($_GET[EDAY_ACCESS_TOKEN])){
      return self::read(EDAY_INDEX_DIR.'files/'.$_GET[EDAY_ACCESS_TOKEN]);
    }
    /* set that eday has been initialized */
    define('EDAY_INITIALIZED',true);
    /* check api server request */
    if(EDAY_ALLOW_API_ACCESS&&isset($_POST['eday'])){
      return (new api)->serverStart(30);
    }
    /* return as true */
    return true;
  }
  /* ------- PRIVATE STATIC METHODS ------- */
  /* prepare database for kdb */
  private static function databaseKDB(){
    /* defined database location */
    defined('KDB_CLI_DIR') or define('KDB_CLI_DIR',EDAY_INDEX_DIR.'databases/');
    /* get database config */
    $config=self::config();
    if(!$config){
      if(self::$error){return false;}
      return self::setError('Failed to get database config.');
    }$data=$config->database;
    /* set timezone */
    @date_default_timezone_set($config->site->timezone);
    /* check database config */
    if(!isset($data->dbhost,$data->dbuser,$data->dbpass,$data->dbname)){
      return self::setError('Failed to get database configuration.');
    }
    /* connect into database */
    $db=new \kdb($data->dbhost,$data->dbuser,$data->dbpass,$data->dbname,$config->site->timezone);
    $setup=true;
    if($db->error){
      if($db->error!=='database does not exist'){
        return self::setError($db->error);
      }$db=self::buildDatabaseKDB($data,$config->site->timezone);
      if(!$db){return false;}
      $setup=false;
    }
    /* define eday has been setup */
    defined('EDAY_SETUP') or define('EDAY_SETUP',$setup);
    /* set database api server if given */
    if($config->site->dbapi&&isset($_POST['kdb'])){
      return kdbServer::start();
    }
    /* return as database object */
    return $db;
  }
  /* build database for kdb */
  private static function buildDatabaseKDB(object $data,string $timezone='Asia/Jakarta'){
    /* create new database */
    $db=new \kdb($data->dbhost,'root','','root',$timezone);
    $db->query("create database user={$data->dbuser}&pass={$data->dbpass}&db={$data->dbname}");
    if($db->error){return self::setError($db->error);}
    $db=new \kdb($data->dbhost,$data->dbuser,$data->dbpass,$data->dbname,$timezone);
    /* prepare tables query */
    $tables=[
      'products'=>'create table products id=INT,KDB_AID&name&description=STRING,KDB_BLANK,1048576'
        .'&currency&price=INT&discount=INT&picture&ribbon&order_to',
      'menus'=>'create table menus id=INT,KDB_AID&name&uri&type&parent=INT',
      'posts'=>'create table posts id=INT,KDB_AID&title&content=STRING,KDB_BLANK,1048576'
        .'&picture&datetime',
      'users'=>'create table users id=INT,KDB_AID&username&password&type',
      'logs'=>'create table logs id=INT,KDB_AID&username&token',
      'tags'=>'create table tags id=INT,KDB_AID&tag&name&tid=INT&type',
    ];
    /* get existing tables */
    $tbl=$db->query('show tables');
    $tbl=is_array($tbl)?$tbl:[];
    $error=false;
    /* check table one-by-one */
    foreach($tables as $k=>$v){
      if(in_array($k,$tbl)){continue;}
      /* create table if not exist */
      if(!$db->query($v)){
        $error='Failed to create table "'.$k.'".';break;
      }
    }
    /* check error */
    if($error){return self::setError($error);}
    /* return database object */
    return $db;
  }
  /* prepare database for sdb */
  private static function databaseSDB(){
    /* defined database location */
    defined('SDB_CLI_DIR') or define('SDB_CLI_DIR',EDAY_INDEX_DIR.'databases/');
    /* get database config */
    $config=self::config();
    if(!$config){
      if(self::$error){return false;}
      return self::setError('Failed to get database config.');
    }$data=$config->database;
    /* set timezone */
    @date_default_timezone_set($config->site->timezone);
    /* check database config */
    if(!isset($data->dbhost,$data->dbuser,$data->dbpass,$data->dbname)){
      return self::setError('Failed to get database configuration.');
    }
    /* connect into database */
    $sdb=new \sdb($data->dbhost,$data->dbuser,$data->dbpass,$data->dbname);
    $setup=true;
    if($sdb->error){
      if($sdb->error!=='database does not exist'){
        return self::setError($sdb->error);
      }$sdb=self::buildDatabaseSDB($data);
      if(!$sdb){return false;}
      $setup=false;
    }
    /* define eday has been setup */
    defined('EDAY_SETUP') or define('EDAY_SETUP',$setup);
    /* set database api server if given */
    if($config->site->dbapi&&isset($_POST['sdb'])){
      return (new \sdb($data->dbhost,'root','','root'))->server();
    }
    /* return as database object */
    return $sdb;
  }
  /* build database for sdb */
  private static function buildDatabaseSDB(object $data){
    /* create new database */
    $sdb=new \sdb($data->dbhost,'root','','root');
    $sdb->query("create database user={$data->dbuser}&pass={$data->dbpass}&db={$data->dbname}");
    if($sdb->error){return self::setError($sdb->error);}
    $sdb=new \sdb($data->dbhost,$data->dbuser,$data->dbpass,$data->dbname);
    /* prepare tables query */
    $tables=[
      'site'=>'create table site id=aid()&name=string()&description=string()'
        .'&keyword=string()&app=string()&api=int(1)&dbapi=int(1)&timezone=string()',
      'products'=>'create table products id=aid()&name=string()&description=string(1048576)'
        .'&currency=string()&price=int(11)&discount=int(11)'
        .'&picture=string()&ribbon=string()&order_to=string()',
      'menus'=>'create table menus id=aid()&name=string()&uri=string()&type=string()&parent=int()',
      'posts'=>'create table posts id=aid()&title=string()&content=string(1048576)'
        .'&picture=string()&datetime=string()',
      'users'=>'create table users id=aid()&username=string()&password=string()&type=string()',
      'logs'=>'create table logs id=aid()&username=string()&token=string()',
      'tags'=>'create table tags id=aid()&tag=string()&name=string()&tid=int()&type=string()',
    ];
    /* get existing tables */
    $tbl=$sdb->query('show tables');
    $tbl=is_array($tbl)?$tbl:[];
    $error=false;
    /* check table one-by-one */
    foreach($tables as $k=>$v){
      if(in_array($k,$tbl)){continue;}
      /* create table if not exist */
      if(!$sdb->query($v)){
        $error='Failed to create table "'.$k.'".';break;
      }
    }
    /* check error */
    if($error){return self::setError($error);}
    /* check site row */
    $row=$sdb->query('select count(id) as row from site');
    if($row[0]['row']==0){
      /* insert innitial data */
      $ins=$sdb->query('insert into site name=string(e-Day Site)'
        .'&description=string(e-Commerce Site)'
        .'&keyword=string(e-Day, Commerce, Shopping)'
        .'&app=string(default)&api=int(1)&dbapi=int(1)'
        .'&timezone=string(Asia/Jakarta)');
      if(!$ins){return self::setError('Failed to insert initial data site.');}
    }
    /* check users row */
    $row=$sdb->query('select count(id) as row from users');
    if($row[0]['row']==0){
      /* insert innitial data */
      $ins=$sdb->query('insert into users username=string(root)&type=string(admin)'
        .'&password=string('.password_hash(uniqid(),PASSWORD_BCRYPT).')');
      if(!$ins){return self::setError('Failed to insert initial data users.');}
    }
    /* return database object */
    return $sdb;
  }
  /* initialize eday config */
  private static function configInit(){
    /* prepare and check config file */
    $file=EDAY_ROOT.'config.ini';
    if(!is_file($file)||!is_readable($file)){
      return self::setError('Configuration file is missing.');
    }
    /* get config data */
    $ini=@parse_ini_file($file,true);
    if(!is_array($ini)||!isset($ini['info'],$ini['config'])
      ||!isset($ini['database'],$ini['site'])){
      return self::setError('Invalid config file.');
    }
    /* parse, check and define config site */
    $site=$ini['site'];
    if(!is_array($site)||!isset($site['name'],$site['description'])
      ||!isset($site['keyword'],$site['api'],$site['dbapi'])
      ||!isset($site['app'],$site['timezone'])){
      return self::setError('Invalid config site.');
    }
    /* parse all config as object */
    $config=(object)[
      'info'=>(object)$ini['info'],
      'config'=>(object)$ini['config'],
      'database'=>(object)$ini['database'],
      'site'=>(object)$ini['site'],
    ];
    /* define eday global config */
    defined('EDAY_CONFIG') or define('EDAY_CONFIG',@serialize($config));
    /* allow api access */
    defined('EDAY_ALLOW_API_ACCESS') or define('EDAY_ALLOW_API_ACCESS',$config->site->api);
    /* return all parsed config as is */
    return true;
  }
  /* initialize apps */
  private static function apps(){
    /* set app directory */
    $d=EDAY_APP_DIR;
    if(!is_dir($d)){@mkdir($d,0755,true);}
    /* scan directory */
    $s=(array)@array_diff(@scandir($d),['.','..']);
    /* parse apps */
    $t=[];$def=[
      'namespace'=>'',
      'name'=>'',
      'description'=>'',
      'version'=>'1.0.0',
      'uri'=>'',
      'author_name'=>'',
      'author_email'=>'',
      'author_uri'=>'',
      'update_uri'=>'',
      'index'=>'index.php',
      'config'=>'config.ini',
    ];
    foreach($s as $f){
      if(!is_dir($d.$f)||!preg_match('/^[a-z0-9_-]+$/i',$f)){continue;}
      $h=[];$r=[];
      if(is_file($d.$f.'/info.ini')){
        $h=@parse_ini_file($d.$f.'/info.ini',true);
        $h=is_array($h)?$h:[];
      }$g=isset($h['info'])?$h['info']:[];
      foreach($def as $k=>$v){
        $r[$k]=isset($g[$k])?$g[$k]:$v;
      }
      $r['namespace']=$f;
      $r['name']=$r['name']==''?$f:$r['name'];
      if(!is_file($d.$f.'/'.$r['index'])){continue;}
      $r['config']=is_file($d.$f.'/'.$r['config'])?$r['config']:'info.ini';
      $t[$r['namespace']]=(object)$r;
    }
    /* set constant */
    defined('EDAY_APPS') or define('EDAY_APPS',serialize($t));
    /* return as true */
    return true;
  }
  /* prepare definition */
  private static function definition(){
    /* check defined constants */
    if(!defined('EDAY_ROOT')||!defined('EDAY_INDEX_DIR')){
      return self::setError('Require EDAY_ROOT and EDAY_INDEX_DIR are not defined.');
    }
    /* checking server compatibility */
    if(!isset($_SERVER['REQUEST_URI'],$_SERVER['SERVER_PORT'],$_SERVER['SERVER_NAME'],$_SERVER['REMOTE_ADDR'],$_SERVER['REQUEST_TIME_FLOAT'])){
      return self::setError('Server is not compatible for e-Day engine.');
    }
    /* define server ip */
    $serverIP=isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']
      :(preg_match('/^\d+(\.\d+){3}$/',$_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'0.0.0.0');
    defined('EDAY_SERVER_IP') or define('EDAY_SERVER_IP',$serverIP);
    /* define user-agent */
    $ua=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Unknown User Agent/+engine';
    defined('EDAY_USER_AGENT') or define('EDAY_USER_AGENT',$ua);
    /* define remote ip */
    defined('EDAY_REMOTE_IP') or define('EDAY_REMOTE_IP',$_SERVER['REMOTE_ADDR']);
    /* define port */
    defined('EDAY_PORT') or define('EDAY_PORT',$_SERVER['SERVER_PORT']);
    /* define protocol */
    $is_ssl=isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on'?true:false;
    $protocol=isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'http'.($is_ssl?'s':'');
    defined('EDAY_PROTOCOL') or define('EDAY_PROTOCOL',$protocol);
    /* define host */
    defined('EDAY_HOST') or define('EDAY_HOST',$_SERVER['SERVER_NAME']);
    /* define referer */
    defined('EDAY_REF') or define('EDAY_REF',isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'');
    /* define index path and document root */
    defined('EDAY_INDEX_PATH') or 
      define('EDAY_INDEX_PATH',self::findPath($_SERVER['REQUEST_URI'],EDAY_INDEX_DIR));
    defined('EDAY_DOC_ROOT') or define('EDAY_DOC_ROOT',substr(EDAY_INDEX_DIR,0,-strlen(EDAY_INDEX_PATH)));
    /* set port */
    $eport=':'.EDAY_PORT;
    if((EDAY_PORT==80&&!$is_ssl)||(EDAY_PORT==443&&$is_ssl)){$eport='';}
    /* define addr */
    defined('EDAY_ADDR') or define('EDAY_ADDR',EDAY_PROTOCOL.'://'.EDAY_HOST.$eport.EDAY_INDEX_PATH);
    /* define uri */
    defined('EDAY_URI') or define('EDAY_URI',EDAY_PROTOCOL.'://'.EDAY_HOST.$eport.$_SERVER['REQUEST_URI']);
    /* define path */
    $path1=preg_replace('/\?(.*)$/i','',$_SERVER['REQUEST_URI']);
    $path2=substr($path1,strlen(EDAY_INDEX_PATH));
    defined('EDAY_PATH') or define('EDAY_PATH',$path2);
    /* eday request uri */
    defined('EDAY_REQUEST_URI') or define('EDAY_REQUEST_URI',substr($_SERVER['REQUEST_URI'],strlen(EDAY_INDEX_PATH)));
    /* define time start */
    defined('EDAY_TIME_START') or define('EDAY_TIME_START',$_SERVER['REQUEST_TIME_FLOAT']);
    /* initialize global get __EDAY_REQUEST__ */
    $request=isset($_GET['__EDAY_REQUEST__'])?$_GET['__EDAY_REQUEST__']:'';
    defined('EDAY_REQUEST') or define('EDAY_REQUEST',$request);
    unset($_GET['__EDAY_REQUEST__']);
    /* define app directory */
    defined('EDAY_APP_DIR') or define('EDAY_APP_DIR',EDAY_INDEX_DIR.'apps/');
    defined('EDAY_APP_PATH') or define('EDAY_APP_PATH',EDAY_ADDR.'apps/');
    /* return as true */
    return true;
  }
  /* find path from request uri */
  private static function findPath($uri=null,$index=null){
    $uri=is_string($uri)&&preg_match('/^\//',$uri)?$uri:'/';
    $index=is_string($index)?$index:'/';
    $k=preg_replace('/[^\/]*(\?.*)?$/','',$uri);
    $x=explode('/',$k);
    $r=[];$b=[];$z='/';
    foreach($x as $v){
      $b[]=$v;$c=implode('/',$b);
      $r[]=$c.(substr($c,-1)!='/'?'/':'');
    }$i=count($r);
    while($i--){
      $c=strpos($index,$r[$i]);
      if($c===false||$r[$i]=='/'){continue;}
      $z=substr($index,$c);break;
    }return $z;
  }
  /* set error */
  private static function setError($s=null){
    self::$error=is_string($s)?$s:'Unknown error.';
    return false;
  }
  /* read file */
  private static function read($f=null){
    if(!is_string($f)||!is_file($f)){
      header('HTTP/1.1 404 Not Found');
      exit('Error: 404 Not Found');
    }
    $q=sprintf('"%s"',addcslashes(basename($f),'"\\'));
    $s=@filesize($f);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$q);
    header('Last-Modified: '.@gmdate('D, d M Y H:i:s',@filemtime($f)).' GMT');
    header('Content-Transfer-Encoding: binary');
    header('Connection: Keep-Alive');
    header('Cache-Control: must-revalidate, max-age=0, post-check=0, pre-check=0');
    header('Expires: '.@gmdate('D, d M Y H:i:s',time()-(3*24*60*60)).' GMT');
    header('Pragma: no-cache');
    header('Accept-Ranges: bytes');
    $o=0;$t=$s;
    if(isset($_SERVER['HTTP_RANGE'])&&preg_match('/bytes=(\d+)-(\d+)?/',$_SERVER['HTTP_RANGE'],$a)){
      $o=intval($a[1]);
      $t=isset($a[2])?intval($a[2]):$s;
    }
    header('Content-Range: bytes '.$o.'-'.$t.'/'.$s);
    if($o>0||$t<$s){
      header('HTTP/1.1 206 Partial Content');
    }else{
      header('HTTP/1.1 200 OK');
    }
    header('Content-Length: '.($t-$o));
    self::readChunk($f,true,$o,$t,32);
    exit;
  }
  /* read as chunk -- helper for read */
  private static function readChunk($f=null,$r=true,$x=null,$y=null,$p=null,$u=true){
    if(!is_string($f)||!is_file($f)){return false;}
    $b='';$c=0;$o=fopen($f,'rb');$w=1024*(is_int($p)?$p:4);
    if($o===false){return false;}
    if(isset($x)){fseek($o,$x);}
    while(!feof($o)){
      $b=fread($o,$w);
      if($u){usleep(1000);}
      print($b);flush();
      if($r){$c+=strlen($b);}
      if(isset($y)&&ftell($o)>=$y){break;}
    }$s=fclose($o);
    if($r&&$s){return $c;}
    return $s;
  }
}


