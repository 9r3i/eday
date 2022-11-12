<?php
/* class eday
 * started at august 24th 2018
 */
class eday{
  const version='1.2.1';
  public static $error=false;
  public static function error($s=null,$h='200 OK'){
    $s=is_string($s)?$s:'Unknown error.';
    $s='Error: '.$s;
    header('HTTP/1.1 '.$h);
    header('Content-Type: text/plain;charset=utf-8;');
    header('Content-Length: '.strlen($s));
    exit($s);
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
    /* initilize database */
    if(!self::database()){
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
    /* initilize themes */
    self::themes();
    /* set that eday has been initialized */
    define('EDAY_INITIALIZED',true);
    /* check api server request */
    if(EDAY_ALLOW_API_ACCESS){(new api)->serverStart(30);}
    /* return as true */
    return true;
  }
  /* initialize theme */
  private static function themes(){
    /* set theme directory */
    $d=EDAY_THEME_DIR;
    if(!is_dir($d)){@mkdir($d,0755,true);}
    /* scan directory */
    $s=(array)@array_diff(@scandir($d),['.','..']);
    /* parse themes */
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
      $h=@parse_ini_file($d.$f.'/info.ini',true);
      $h=is_array($h)?$h:[];
      $g=isset($h['info'])?$h['info']:[];
      $r=[];
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
    defined('EDAY_THEMES') or define('EDAY_THEMES',serialize($t));
    /* return as true */
    return true;
  }
  /* prepare databse */
  private static function database(){
    /* defined database location */
    defined('SDB_CLI_DIR') or define('SDB_CLI_DIR',EDAY_INDEX_DIR.'databases/');
    /* connect into database */
    $sdb=new sdb('localhost','eday','9r3i','site');
    if($sdb->error){
      if($sdb->error!=='database does not exist'){
        return self::setError($sdb->error);
      }
      $sdb=new sdb('localhost','root','','root');
      $sdb->query('create database user=eday&pass=9r3i&db=site');
      if($sdb->error){return self::setError($sdb->error);}
      $sdb=new sdb('localhost','eday','9r3i','site');
    }
    /* prepare tables query */
    $tables=[
      'site'=>'create table site id=aid()&name=string()&description=string()'
        .'&keyword=string()&theme=string()&api=int(1)&dbapi=int(1)&timezone=string()',
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
        .'&theme=string(default)&api=int(1)&dbapi=int(1)'
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
    /* get site data */
    $sel=$sdb->query('select * from site where id=1');
    if(!$sel||!isset($sel[0])){
      return self::setError('Failed to get site configuration.');
    }
    /* set config */
    unset($sel[0]['id']);
    $config=(object)$sel[0];
    /* set timezone */
    @date_default_timezone_set($config->timezone);
    /* set database api server if given */
    if($config->dbapi){(new sdb('localhost','root','','root'))->server();}
    /* set global config site */
    defined('EDAY_CONFIG') or define('EDAY_CONFIG',serialize($sel[0]));
    /* allow api access */
    defined('EDAY_ALLOW_API_ACCESS') or define('EDAY_ALLOW_API_ACCESS',$config->api);
    /* return as true */
    return true;
  }
  /* prepare definition */
  private static function definition(){
    /* check defined constants */
    if(!defined('EDAY_ROOT')||!defined('EDAY_INDEX_DIR')){
      return self::setError('Require EDAY_ROOT and EDAY_INDEX_DIR defined.');
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
    /* define theme directory */
    defined('EDAY_THEME_DIR') or define('EDAY_THEME_DIR',EDAY_INDEX_DIR.'themes/');
    defined('EDAY_THEME_PATH') or define('EDAY_THEME_PATH',EDAY_ADDR.'themes/');
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
    self::readchunk($f,true,$o,$t,32);
    exit;
  }
  private static function readchunk($f=null,$r=true,$x=null,$y=null,$p=null,$u=true){
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
