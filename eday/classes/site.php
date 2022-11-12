<?php
/* class site for e-day
 * started at august 24th 2018
 */
class site{
  const host=EDAY_HOST;            // luthfie.com
  const port=EDAY_PORT;            // 443 (ssl) or 80
  const protocol=EDAY_PROTOCOL;    // https (ssl) or http
  const referer=EDAY_REF;          // (referer)
  const start=EDAY_TIME_START;     // (true microtime)
  const request=EDAY_REQUEST;      // [from $_GET] __EDAY_REQUEST__
  const path=EDAY_PATH;            // uri-path [if uri] https://luthfie.com/uri-path
  const url=EDAY_ADDR;             // https://luthfie.com/ [if has path] https://luthfie.com/has-path/
  const uri=EDAY_URI;              // https://luthfie.com/uri-path?test=testing
  const ruri=EDAY_REQUEST_URI;     // uri-path?test=testing
  /* public static - [require: login] */
  public static function user($id=null,&$error=false,$start=0,$limit=10){
    if(!admin::isLogin()){return false;}
    $db=self::db();
    $pr=$db->query('select id,username,type from users'.(is_int($id)?' where id='.$id:'')
      .' order by id desc limit '.$start.','.$limit);
    if(!$pr||$db->error){
      $error=$db->error;
      return false;
    }return is_int($id)&&isset($pr[0])?$pr[0]:$pr;
  }
  public static function userRow(&$error=false){
    if(!admin::isLogin()){return false;}
    $db=self::db();
    $pr=$db->query('select count(id) as row from users '
      .(EDAY_ADMIN_TYPE!=='master'?'where type!="master"':''));
    if(!$pr||$db->error){
      $error=$db->error;
      return false;
    }return $pr[0]['row'];
  }
  /* public static */
  public static function postRow(&$error=false){
    $db=self::db();
    $pr=$db->query('select count(id) as row from posts');
    if(!$pr||$db->error){
      $error=$db->error;
      return false;
    }return $pr[0]['row'];
  }
  public static function productRow(&$error=false){
    $db=self::db();
    $pr=$db->query('select count(id) as row from products');
    if(!$pr||$db->error){
      $error=$db->error;
      return false;
    }return $pr[0]['row'];
  }
  public static function post($id=null,&$error=false,$start=0,$limit=10){
    $db=self::db();
    $pr=$db->query('select * from posts'.(is_int($id)?' where id='.$id:'')
      .' order by datetime desc limit '.$start.','.$limit);
    if(!$pr||$db->error){
      $error=$db->error;
      return false;
    }return is_int($id)&&isset($pr[0])?$pr[0]:$pr;
  }
  public static function product($id=null,&$error=false,$start=0,$limit=9){
    $db=self::db();
    $pr=$db->query('select * from products'.(is_int($id)?' where id='.$id:'')
      .' order by ribbon desc limit '.$start.','.$limit);
    if(!$pr||$db->error){
      $error=$db->error;
      return false;
    }return is_int($id)&&isset($pr[0])?$pr[0]:$pr;
  }
  public static function db(){
    $sdb=new sdb('localhost','eday','9r3i','site');
    if($sdb->error){
      return eday::error($sdb->error);
    }return $sdb;
  }
  public static function themeURL($p=''){
    $ns=self::config('theme');
    return EDAY_THEME_PATH.$ns.'/'.$p;
  }
  public static function defined(){
    return get_defined_constants(true)['user'];
  }
  public static function config($k=null){
    if(!defined('EDAY_CONFIG')){
      return eday::error('Required constant "EDAY_CONFIG" is undefined.');
    }
    $c=(object)@unserialize(EDAY_CONFIG);
    if(!is_string($k)){return $c;}
    if(!isset($c->$k)){return eday::error('Unknown config key "'.$k.'".');}
    return $c->$k;
  }
  /* public static - [once called] */
  public static function engine(){
    $e=new engine((object)[
      'engine_name'=>'e-Day',
      'engine_version'=>eday::version,
      'engine_description'=>'e-Commerce CMS',
      'author_name'=>'9r3i',
      'author_email'=>'luthfie@y7mail.com',
      'author_uri'=>'https://github.com/9r3i',
    ]);
    $e->start=function(){
      if(!defined('EDAY_ENGINE_STARTED')){
        define('EDAY_ENGINE_STARTED',true);
        return self::ignite();
      }return false;
    };return $e;
  }
  /* private static */
  private static function ignite(){
    if(!defined('EDAY_THEMES')){
      return eday::error('Required constant "EDAY_THEMES" is undefined.');
    }
    if(isset($_GET['admin'])){
      $ns='admin';
      define('EDAY_ADMIN_PATH',$_GET['admin']);
      define('EDAY_ADMIN_DIR',EDAY_ROOT);
      define('EDAY_EDITOR_PATH',EDAY_ADDR.'files/editors/');
      $index=EDAY_ADMIN_DIR.'kitchen.php';
    }elseif(self::isRootUser()){
      header('location: '.self::url.'?admin=log/setup');
      exit;
    }else{
      $t=(object)@unserialize(EDAY_THEMES);
      $ns=self::config('theme');
      if(!is_string($ns)||!isset($t->$ns)){
        return eday::error('Theme "'.$ns.'" is not available.');
      }$index=EDAY_THEME_DIR.$ns.'/'.$t->$ns->index;
    }
    if(!is_file($index)){
      return eday::error('Failed to load index file of "'.$ns.'".');
    }
    header('Content-Type: text/html;charset=utf-8;');
    $stime=number_format(microtime(true)-self::start,3,'.','');
    try{
      if(!@require_once($index)){
        throw new Exception('Failed to load theme "'.$ns.'".');
      }echo "\r\n".'<!-- generated by e-Day in '.$stime.' sec -->';
    }catch(Exception $e){
      return eday::error($e->getMessage());
    }return true;
  }
  private static function isRootUser(){
    $db=self::db();
    $user=$db->query('real single select * from users where id=1');
    if($user&&isset($user['username'])&&$user['username']=='root'){
      return true;
    }return false;
  }
}
