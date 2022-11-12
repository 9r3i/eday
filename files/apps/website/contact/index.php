<?php
/* class api for contact
 * started at april 27th 2021
 */
(new class{
  const version='1.0.0';
  public function __construct(){
    /* return this object */
    return $this;
  }
  public function serverStart($l=0){
    /* set time limit */
    @set_time_limit($l);
    /* set request header */
    $this->serverHeader();
    /* set timezone */
    date_default_timezone_set('Asia/Jakarta');
    /* run server default */
    return $this->serverDefault();
  }
  private function serverDefault(){
    $result1=[
      'code'=>1,
      'status'=>'OK',
      'message'=>'Message has been sent.',
    ];
    $result2=[
      'code'=>2,
      'status'=>'ERROR',
      'message'=>'Failed to send the message.',
    ];
    $result3=[
      'code'=>3,
      'status'=>'ERROR',
      'message'=>'Invalid request.',
    ];
    if(!isset($_GET['name'],$_GET['email'],$_GET['message'])){
      return $this->serverResult(json_encode($result3));
    }
    if($this->serverWriteMessage($_GET['name'],$_GET['email'],$_GET['message'])){
      return $this->serverResult(json_encode($result1));
    }return $this->serverResult(json_encode($result2));
  }
  private function serverWriteMessage(string $name,string $email,string $message){
    $o=fopen('contact.txt','ab');
    if(!is_resource($o)){return false;}
    $time=date('Y-m-d H:i:s');
    $w=fwrite($o,"\r\nTime: {$time}\r\nName: {$name}\r\nEmail: {$email}\r\nMessage: {$message}\r\n\r\n\r\n"
       ."------------------------------------------------\r\n\r\n");
    fclose($o);
    return $w?true:false;
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
})->serverStart();
