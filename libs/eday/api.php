<?php
/* class api for e-day
 * started at august 24th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday
 */
namespace eday;
class api{
  const version='1.1.0';
  protected $errors=[];
  protected $methods=[];
  public $error=false;
  public function __construct(){
    /* check error */
    if(eday::$error){$this->error=eday::$error;}
    /* return this object */
    return $this;
  }
  public function serverStart($l=0){
    /* set time limit */
    @set_time_limit($l);
    /* set request header */
    $this->serverHeader();
    /* set registered method */
    $this->methods=[
      'draw'=>'serverDraw',
    ];
    /* check eday request */
    if(isset($_POST['eday'])){
      return $this->serverLoad();
    }return false;
  }
  private function serverDraw($class=null,$method=null){
    return false;
  }
  private function serverLoad(){
    /* check error */
    if($this->error){
      $res['message']=$this->error;
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* get client type */
    $ax=isset($_GET['client'])&&$_GET['client']=='ajax'?true:false;
    $res=array('status'=>'error','message'=>'Invalid request.');
    /* decode request */
    $get=$this->serverDecode($_POST['eday'],$ax);
    if(!$get){
      $res['message']='Failed to decode request.';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* check timezone request */
    if(isset($get['timezone'])){
      if(!@date_default_timezone_set($get['timezone'])){
        $res['message']='Invalid timezone "'.$get['timezone'].'".';
        return $this->serverResult($this->serverEncode($res,$ax));
      }
    }
    /* check username and password */
    if(!isset($get['username'],$get['password'])){
      $res['message']='Require username and password.';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* validation username and password */
    if(!preg_match('/^[a-z0-9]+$/',$get['username'])){
      $res['message']='Require username and password.';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    $db=site::db();
    $sel=$db->query('select * from users where username="'.$get['username'].'"');
    if(!$sel||$db->error||!isset($sel[0])||!password_verify($get['password'],$sel[0]['password'])){
      $res['message']=$db->error?$db->error:'Invalid username or password.';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* check request */
    if(!isset($get['request'])){
      $res['message']='Require method request.';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* set arguments */
    $args=isset($get['args'])&&is_array($get['args'])?$get['args']:[];
    /* check method */
    if(!@array_key_exists($get['request'],$this->methods)
      ||!method_exists($this,$this->methods[$get['request']])){
      $res['message']='Method request does not exist.';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* start execution request */
    $exec=false;
    try{
      $exec=@\call_user_func_array([$this,$this->methods[$get['request']]],$args);
      if(eday::$error){$this->error=eday::$error;}
      elseif(!$exec){
        throw new Exception('Failed to execute API request.');
      }
    }catch(Exception $e){
      $this->error=$e->getMessage();
    }
    /* check result */
    if(!$exec||$this->error){
      $res['message']=$this->error?$this->error:'Failed to execute request.';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* prepare result output */
    $res['status']=$this->error?'error':'OK';
    $res['message']=$this->error?$this->error:'connected';
    $res['result']=$exec;
    $res['errors']=$this->errors;
    $res['error']=$this->error;
    $res['info']=$this->serverInfo();
    /* return the result */
    return $this->serverResult($this->serverEncode($res,$ax));
  }
  private function serverEncode($s=null,$a=false){
    return @base64_encode($a?@json_encode($s):@serialize($s));
  }
  private function serverDecode($s=null,$a=false){
    $s=@base64_decode($s);
    return $a?@json_decode($s,true):@unserialize($s);
  }
  private function serverInfo(){
    return [
      'api::version'=>$this::version,
      'eday::version'=>eday::version,
      'php::version'=>PHP_VERSION,
      'request_length'=>strlen($_POST['eday']),
      'memory_usage'=>number_format(memory_get_usage()/1024,2,'.',''),
      'memory_peak_usage'=>number_format(memory_get_peak_usage()/1024,2,'.',''),
      'precess_time'=>number_format(microtime(true)-$_SERVER['REQUEST_TIME_FLOAT'],3,'.',''),
      'remote_addr'=>$_SERVER['REMOTE_ADDR'],
    ];
  }
  private function serverResult($s=null){
    header('HTTP/1.1 200 OK');
    header('Content-Length: '.strlen($s));
    exit($s);
  }
  private function serverHeader(){
    /* access control - to allow the access via ajax */
    header('Access-Control-Allow-Origin: *'); /* allow origin */
    header('Access-Control-Request-Method: POST, GET, OPTIONS'); /* request method */
    header('Access-Control-Request-Headers: X-PINGOTHER, Content-Type'); /* request header */
    header('Access-Control-Max-Age: 86400'); /* max age (24 hours) */
    header('Access-Control-Allow-Credentials: true'); /* allow credentials */
    /* set content type of response header */
    header('Content-Type: text/plain;charset=utf-8;');
    /* checking options */
    if(isset($_SERVER['REQUEST_METHOD'])&&strtoupper($_SERVER['REQUEST_METHOD'])=='OPTIONS'){
      header('Content-Language: en-US');
      header('Content-Encoding: gzip');
      header('Content-Length: 0');
      header('Vary: Accept-Encoding, Origin');
      header('HTTP/1.1 200 OK');
      exit;
    }
  }
}
