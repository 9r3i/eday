<?php
class serverHelper{
  const version='1.0.1';
  public static function generatePublicToken(){
    return md5(strtotime(date('Y-m-d H:i')));
  }
  public static function visitorLog($dir=null){
    /* prepare visitor directory */
    if(!is_string($dir)||!is_dir($dir)){return false;}
    /* prepare file */
    $file=$dir.'/visitor.log';
    /* check visitor login cookie */
    $ptrn='/katya\d+_user_[a-z0-9]+|ksite\-[a-zA-Z0-9]+/';
    if(preg_match($ptrn,implode(';',array_keys($_COOKIE)),$a)){
      return false;
    }
    /* prepare visitor data */
    $data=self::visitorData();
    /* check data ua */
    if(preg_match('/9r3i/',$data->ua)){return false;}
    /* check data post limit */
    if(strlen(print_r($data->post,true))>0xffff){
      $data->post=array_merge(
        ['status'=>'[OVERLOAD]'],
        ['keys'=>array_keys($data->post)]
      );
    }
    if(count($data->ips)==1&&in_array($data->ip,$data->ips)){
      $data->ips=[];
    }
    /* prepare string visitor data */
    $json=@json_encode($data,true);
    if(!is_string($json)){
      $json=@json_encode(['error'=>true]);
      $json=is_string($json)?$json:'-';
    }
    /* prepare file resource */
    $o=@fopen($file,'ab');
    if(!is_resource($o)){return false;}
    /* write visitor log */
    $w=@fwrite($o,"{$json}\r\n");
    @fclose($o);
    return $w;
  }
  public static function visitorLogKDB(){
    /* check visitor login cookie */
    if(preg_match('/katya\d+_user_[a-z0-9]+/',implode(';',array_keys($_COOKIE)),$a)){
      return false;
    }
    /* prepare visitor data */
    $data=self::visitorData();
    /* prepare json data */
    $rData=[];
    if($data->method=='POST'&&!empty($data->post)){
      $rData['post']=$data->post;
    }
    if(count($data->ips)>1){
      $rData['ips']=$data->ips;
    }
    $vData=!empty($rData)?print_r($rData,true):'-';
    /* set query visitor */
    $query='insert into "visitors" '.http_build_query(array(
      'ip'=>$data->ip,
      'ua'=>$data->ua,
      'uri'=>$data->url,
      'ref'=>$data->ref,
      'method'=>$data->method,
      'type'=>$data->type,
      'data'=>$vData,
    ));
    /* connect into database */
    $kdb=new kdb('localhost','master','lagunaseca','luthfie','Asia/Jakarta');
    /* save query into database */
    if(!$kdb->error){
      $kdb->query($query);
    }return true;
  }
  public static function visitorData(){
    /* get all ips */
    $skey=['HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED',
      'HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR'];
    $ips=[];
    foreach($skey as $key){
      if(isset($_SERVER[$key])){
        $ips[]=$_SERVER[$key];
      }
    }
    /* set object data visitor */
    $data=(object)[
      'time'=>time(),
      'method'=>isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'GET',
      'protocol'=>isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']
        :'http'.(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on'?'s':''),
      'host'=>isset($_SERVER['SERVER_NAME'])&&$_SERVER['SERVER_NAME']!='0.0.0.0'
        ?$_SERVER['SERVER_NAME']:'127.0.0.1',
      'port'=>isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:80,
      'uri'=>isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'/',
      'ref'=>isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'',
      'post'=>$_POST,
      'ip'=>isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1',
      'ua'=>isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Unknown',
      'ips'=>$ips,
      'url'=>'',
      'type'=>'',
    ];
    /* set is bot */
    $isbot=preg_match('/bot|compatible|wow64|\+|crawl/i',$data->ua)?true:false;
    /* set port and url */
    $port=($data->protocol=='http'&&$data->port==80)
      ||($data->protocol=='https'&&$data->port==443)
      ?'':':'.$data->port;
    $url=$data->protocol.'://'.$data->host.$port.$data->uri;
    /* push url to data */
    $data->url=$url;
    $data->type=$isbot?'bot':'human';
    return $data;
  }
}


