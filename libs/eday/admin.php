<?php
/* class admin for e-day
 * started at august 25th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday
 */
namespace eday;
class admin{
  const version='1.1.0';
  private $page=false;
  private $method=false;
  private $args=[];
  private $loaded=[];
  private $errors=[];
  public $error=false;
  public $error_level=0;
  function __construct(){
    $path=defined('EDAY_ADMIN_PATH')?EDAY_ADMIN_PATH:'';
    $exp=explode('/',$path);
    $this->page=$exp[0]?$exp[0]:false;
    $this->method=isset($exp[1])&&$exp[1]?$exp[1]:false;
    $this->args=array_slice($exp,2);
    return $this;
  }
  /* ----- static functions ----- */
  public static function hasAccess(){
    return defined('EDAY_ADMIN_PATH')?true:false;
  }
  public static function isLogin(){
    if(!isset($_COOKIE['eday-token'])
      ||!preg_match('/^eday\-[a-z0-9]{5,7}$/',$_COOKIE['eday-token'])){
      return false;
    }
    if(defined('EDAY_ADMIN_TOKEN')
      &&defined('EDAY_ADMIN_USERNAME')
      &&defined('EDAY_ADMIN_TYPE')
      &&defined('EDAY_ADMIN_ID')
      &&password_verify($_COOKIE['eday-token'],EDAY_ADMIN_TOKEN)){
      return (object)[
        'username'=>EDAY_ADMIN_USERNAME,
        'token'=>EDAY_ADMIN_TOKEN,
        'type'=>EDAY_ADMIN_TYPE,
        'id'=>EDAY_ADMIN_ID,
      ];
    }
    $db=site::db();
    $sel=$db->query('select * from logs where token="'.$_COOKIE['eday-token'].'"');
    if(!$sel||!isset($sel[0])){
      return false;
    }
    $user=$db->query('select * from users where username="'.$sel[0]['username'].'"');
    if(!$user||!isset($user[0])){
      return false;
    }
    define('EDAY_ADMIN_TOKEN',password_hash($sel[0]['token'],PASSWORD_BCRYPT));
    define('EDAY_ADMIN_USERNAME',$sel[0]['username']);
    define('EDAY_ADMIN_TYPE',$user[0]['type']);
    define('EDAY_ADMIN_ID',$user[0]['id']);
    return (object)[
      'username'=>$sel[0]['username'],
      'token'=>$sel[0]['token'],
      'type'=>$user[0]['type'],
      'id'=>$user[0]['id'],
    ];
  }
  /* ----- static functions - require access ----- */
  public static function editorPath(){
    if(!self::hasAccess()){return false;}
    $editor=self::config('editor');
    return EDAY_EDITOR_PATH.$editor.'/'.$editor.'.js';
  }
  public static function config($k=null,$c='config'){
    if(!self::hasAccess()){return false;}
    $ini=@parse_ini_file(EDAY_ADMIN_DIR.'config.ini',true);
    $ini=is_array($ini)?$ini:[];
    $config=is_string($c)&&isset($ini[$c])?$ini[$c]:$ini;
    return is_string($k)&&isset($config[$k])?$config[$k]:$config;
  }
  public static function token(){
    if(!self::hasAccess()){return false;}
    return 'eday-'.base_convert(mt_rand(),10,36);
  }
  public static function appURL($p=''){
    if(!self::hasAccess()){return false;}
    if(!defined('EDAY_ACCESS_TOKEN')){return false;}
    if(preg_match('/^js/i',$p)){
      $p=preg_replace('/^js/i','script',$p);
      $g=base64_encode(@file_get_contents(EDAY_ROOT.$p));
      return 'data:application/javascript;base64,'.$g;
    }return EDAY_ADDR.'files/kitchen/'.$p;
  }
  public static function redirect($k=null){
    if(!self::hasAccess()||!is_string($k)){return false;}
    header('location: '.site::url.'?'.EDAY_ADMIN_KEY.'='.$k);
    exit;
  }
  public static function html($p=''){
    if(!self::hasAccess()){return false;}
    $f=EDAY_ADMIN_DIR.'pages/'.$p.'.php';
    return is_file($f)?@require($f):false;
  }
  /* ----- non-static functions ----- */
  public function isLoaded($k=null){
    return is_string($k)&&in_array($k,$this->loaded)?true:false;
  }
  public function start(){
    if(!self::isLogin()&&$this->page!='log'){
      return self::redirect('log/in');
    }
    $page=$this->page($this->page);
    if(!$page){return false;}
    if(!is_string($this->method)||!method_exists($page,$this->method)){
      return $this->error('Admin page "'.$this->page
        .'" method "'.$this->method.'" is not available.',4);
    }
    $call=@\call_user_func_array([$page,$this->method],$this->args);
    return $call!==false?true:$this->error('Failed to execute admin page "'.$this->page.'".',2);
  }
  private function page(string $k){
    if(in_array($k,$this->loaded)){
      return $this->error('Admin page "'.$k.'" has been loaded.',1);
    }$this->loaded[]=$k;
    $cn="\\eday\\kitchen\\{$k}";
    if(!class_exists($cn)){
      return $this->error('Failed to load admin page "'.$k.'".',8);
    }return new $cn;
  }
  private function pageOld($k=null){
    if(!is_string($k)){return $this->error('Invalid admin page.',16);}
    $f=EDAY_ADMIN_DIR.'kitchen/'.$k.'.php';
    if(!is_file($f)){return $this->error('Admin page "'.$k.'" is not available.',8);}
    if(in_array($k,$this->loaded)){
      return $this->error('Admin page "'.$k.'" has been loaded.',1);
    }$this->loaded[]=$k;
    if(require_once($f)){
      return new $k;
    }return $this->error('Failed to load admin page "'.$k.'".',8);
  }
  private function error($s=null,$l=1){
    $this->error_level=$l;
    $this->error=is_string($s)?$s:'Unknown error.';
    $this->errors[]=$this->error;
    return false;
  }
}
