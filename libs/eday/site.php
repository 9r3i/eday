<?php
/* class site for e-day
 * started at august 24th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday
 */
namespace eday;
class site{
  const version='1.1.0';
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
    global $_EDB;
    return $_EDB;
  }
  public static function appURL($p=''){
    $ns=self::config('app');
    return EDAY_APP_PATH.$ns.'/'.$p;
  }
  public static function defined(){
    return get_defined_constants(true)['user'];
  }
  public static function config($k=null){
    $c=eday::config();
    if(!is_string($k)){return $c->site;}
    if(!isset($c->site->$k)){return eday::error('Unknown config key "'.$k.'".');}
    return $c->site->$k;
  }
  public static function adminKey(){
    if(defined('EDAY_ADMIN_KEY')){return EDAY_ADMIN_KEY;}
    $config=eday::config();
    $res='admin';
    if($config&&isset($config->config->akey)){
      $res=trim($config->config->akey);
    }define('EDAY_ADMIN_KEY',$res);
    return $res;
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
  /* private static ignition */
  private static function ignite(){
    if(!defined('EDAY_APPS')){
      return eday::error('Required constant "EDAY_APPS" is undefined.');
    }
    if(isset($_GET[self::adminKey()])){
      $ns='admin';
      define('EDAY_ADMIN_PATH',$_GET[self::adminKey()]);
      define('EDAY_ADMIN_DIR',EDAY_ROOT);
      define('EDAY_EDITOR_PATH',EDAY_ADDR.'files/editors/');
      $index=EDAY_ADMIN_DIR.'kitchen.php';
    }elseif(self::isRootUser()){
      header('location: '.self::url.'?'.self::adminKey().'=log/setup');
      exit;
    }else{
      $t=(object)@unserialize(EDAY_APPS);
      $ns=self::config('app');
      if(!is_string($ns)||!isset($t->$ns)){
        return eday::error('Application "'.$ns.'" is not available.');
      }$index=EDAY_APP_DIR.$ns.'/'.$t->$ns->index;
    }
    if(!is_file($index)){
      return eday::error('Failed to load index file of "'.$ns.'".');
    }
    header('Content-Type: text/html;charset=utf-8;');
    $stime=number_format(microtime(true)-self::start,3,'.','');
    try{
      if(!@require_once($index)){
        throw new Exception('Failed to load application "'.$ns.'".');
      }echo "\r\n".'<!-- generated by e-Day in '.$stime.' sec -->';
    }catch(Exception $e){
      return eday::error($e->getMessage());
    }return true;
  }
  /* tell is root user */
  private static function isRootUser(){
    return defined('EDAY_SETUP')&&EDAY_SETUP?false:true;
  }
}
