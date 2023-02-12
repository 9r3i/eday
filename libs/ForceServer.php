<?php
/**
 * ForceServer
 * ~ the 6th generation -- project F (foxtrot)
 * ~ server for Force (client)
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 13th 2022
 * continued at december 8th 2022 -- 1.2.0
 */
class ForceServer{
  const version='1.2.3';
  private $dir=null;
  private $dirPlugins=null;
  private $postMethods=[
    'test',
  ];
  private $getMethods=[
    'test',
  ];
  /* construct */
  public function __construct(){
    /* prepare header */
    $this->header();
    /* prepare directory */
    $forceRoot=defined('FORCE_CLI_DIR')
      ?str_replace('\/','/',FORCE_CLI_DIR)
      :str_replace('\/','/',__DIR__);
    $forceRoot.=substr($forceRoot,-1)!='/'?'/':"";
    $this->dir=$forceRoot.'/force/data/';
    if(!is_dir($this->dir)){
      @mkdir($this->dir,0755,true);
    }
    $this->dirPlugins=$forceRoot.'/force/plugins/';
    if(!is_dir($this->dirPlugins)){
      @mkdir($this->dirPlugins,0755,true);
    }
    if(!is_dir($this->dir)
      ||!is_dir($this->dirPlugins)){
      header('HTTP/1.1 500 Internal Server Error');
      $text='Error: 500 Internal Server Error, '
        .'cannot create new directory.';
      header('Content-Length: '.strlen($text));
      exit($text);
    }
    /* write user log */
    if(!$this->userlog()){
      header('HTTP/1.1 500 Internal Server Error');
      $text='Error: 500 Internal Server Error, '
        .'cannot write a file.';
      header('Content-Length: '.strlen($text));
      exit($text);
    }
    /* check request */
    if(isset($_POST['token'],$_POST['method'])
      &&$this->validToken($_POST['token'])
      &&in_array($_POST['method'],$this->postMethods)
      &&method_exists($this,$_POST['method'])){
      $method=$_POST['method'];
      unset($_POST['token']);
      unset($_POST['method']);
      $res=@\call_user_func_array([$this,$method],[$_POST,'POST']);
      $json=@json_encode($res);
      $err='Error: Something is going wrong.';
      $out=$json?$json:$err;
      return $this->output($out);
    }elseif(isset($_GET['method'])
      &&in_array($_GET['method'],$this->getMethods)
      &&method_exists($this,$_GET['method'])){
      $method=$_GET['method'];
      unset($_GET['method']);
      $res=@\call_user_func_array([$this,$method],[$_GET,'GET']);
      $json=@json_encode($res,JSON_PRETTY_PRINT);
      $err='Error: Something is going wrong.';
      $out=$json?$json:$err;
      return $this->output($out);
    }elseif(isset($_GET['client'])){
      return $this->client($_GET);
    }
    /* initialize plugins */
    $this->pluginsInit();
    /* final error handler */
    header('HTTP/1.1 401 Unauthorized');
    $text='Error: 401 Unauthorized.';
    header('Content-Length: '.strlen($text));
    exit($text);
  }
  /* test */
  private function test($req,$method){
    return 'ok';
  }
  /* client service */
  private function client($get){
    $file=dirname($this->dir).'/js/force.min.js';
    $get=is_file($file)?@file_get_contents($file):false;
    $out=$get?$get
      :'alert("Error: Client service is not available.")';
    /* set content type of response header */
    header('Content-Type: application/javascript; charset=utf-8;');
    header('Content-Length: '.strlen($out));
    header('HTTP/1.1 200 OK');
    exit($out);
  }
  /* =============== helpers =============== */
  /* initialize plugins */
  private function pluginsInit(){
    $ptrn='/^([0-9a-z_]+)\.([0-9a-z_]+)$/i';
    $pre=(object)[
      'dir'=>$this->dir,
      'className'=>null,
      'method'=>null,
      'output'=>function(string $out=''){
        header('Content-Length: '.strlen($out));
        header('HTTP/1.1 200 OK');
        exit($out);
      },
    ];
    if(isset($_GET['method'])
      &&preg_match($ptrn,$_GET['method'],$ak)){
      $cname=$ak[1];
      $method=$ak[2];
      $pre->className=$cname;
      $pre->method=$method;
      unset($_GET['method']);
      $file=$this->dirPlugins.$cname.'.force.php';
      if(!is_file($file)){return false;}
      @require_once($file);
      if(!class_exists($cname,false)){return false;}
      $obj=new $cname($_GET,'GET',$pre);
      if(!is_array($obj->getMethods)
        ||!in_array($method,$obj->getMethods)
        ||!method_exists($obj,$method)
        ||!(new ReflectionMethod($obj,$method))->isPublic()){
        return false;
      }
      $res=@\call_user_func_array([$obj,$method],[$_GET,'GET',$pre]);
      $json=@json_encode($res,JSON_PRETTY_PRINT);
      $err='Error: Something is going wrong.';
      $out=$json?$json:$err;
      return $this->output($out);
    }elseif(isset($_POST['token'],$_POST['method'])
      &&$this->validToken($_POST['token'])
      &&preg_match($ptrn,$_POST['method'],$ak)){
      $cname=$ak[1];
      $method=$ak[2];
      $pre->className=$cname;
      $pre->method=$method;
      unset($_POST['method']);
      unset($_POST['token']);
      $file=$this->dirPlugins.$cname.'.force.php';
      if(!is_file($file)){return false;}
      @require_once($file);
      if(!class_exists($cname,false)){return false;}
      $obj=new $cname($_POST,'POST',$pre);
      if(!is_array($obj->postMethods)
        ||!in_array($method,$obj->postMethods)
        ||!method_exists($obj,$method)
        ||!(new ReflectionMethod($obj,$method))->isPublic()){
        return false;
      }
      $res=@\call_user_func_array([$obj,$method],[$_POST,'POST',$pre]);
      $json=@json_encode($res);
      $err='Error: Something is going wrong.';
      $out=$json?$json:$err;
      return $this->output($out);
    }
    return false;
  }
  /* =============== stand-alone =============== */
  /* validate token */
  private function validToken(string $token):bool{
    $time=intval(base_convert(strtolower($token),36,10));
    return $time>time()?true:false;
  }
  /* basic response output */
  private function output(string $out=''){
    header('Content-Length: '.strlen($out));
    header('HTTP/1.1 200 OK');
    exit($out);
  }
  /* user log -- requires @this->dir */
  private function userlog(){
    $ip=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
    $time=date('ymd-His');
    $o=@fopen($this->dir.'userlog.txt','ab');
    if(!is_resource($o)){
      return false;
    }
    $get=@json_encode($_GET);
    $post=@json_encode($_POST);
    $ua=@json_encode($_SERVER['HTTP_USER_AGENT']);
    $w=@fwrite($o,$time.'|'.$ip.'|'
      .$_SERVER['REQUEST_METHOD'].'|'
      .($get?$get:'FAILED').'|'
      .($post?$post:'FAILED').'|'
      .($ua?$ua:'FAILED')
      ."\n");
    @fclose($o);
    return true;
  }
  /* default response headers */
  private function header(){
    /* set time limit */
    @set_time_limit(false);
    /* set default timezone */
    date_default_timezone_set('Asia/Jakarta');
    /* access control - to allow the access via ajax */
    header('Access-Control-Allow-Origin: *'); // allow origin
    header('Access-Control-Request-Method: POST, GET, OPTIONS'); // request method
    header('Access-Control-Request-Headers: X-PINGOTHER, Content-Type'); // request header
    header('Access-Control-Max-Age: 86400'); // max age (24 hours)
    header('Access-Control-Allow-Credentials: true'); // allow credentials
    /* set content type of response header */
    header('Content-Type: text/plain;charset=utf-8;');
    /* checking options */
    if(isset($_SERVER['REQUEST_METHOD'])
      &&strtoupper($_SERVER['REQUEST_METHOD'])=='OPTIONS'){
      header('Content-Language: en-US');
      header('Content-Encoding: gzip');
      header('Content-Length: 0');
      header('Vary: Accept-Encoding, Origin');
      header('HTTP/1.1 200 OK');
      exit;
    }
  }
}
