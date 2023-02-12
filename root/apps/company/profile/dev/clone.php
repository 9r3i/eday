<?php
header('content-type:text/plain');
$file='landing.html';
$filecss='assets/css/vendor.min.css';
$aisFile='landing.ais';
$baseurl='https://landing.zytheme.com/ladidapp/assets/css/';
$path='assets/css';
$content=file_get_contents($filecss);
$ptrn=(object)[
  'hrefa'=>'/href="([^"]+)"/i',
  'hrefb'=>'/href=\'([^\']+)\'/i',
  'srca'=>'/src="([^"]+)"/i',
  'srcb'=>'/src=\'([^\']+)\'/i',
  'srcurl'=>'/url\(([^\)]+)\)/i',
  ''=>'',
];



preg_match_all($ptrn->srcurl,$content,$akur);

$res=[];
foreach($akur[1] as $ak){
  $temp=null;
  $url=trim($ak);
  /**
  if(preg_match('/\.(css|js)/i',$url)){
    if(!preg_match('/^http/i',$url)){
      $dir=dirname($url);
      $name=basename(explode('?',$url)[0]);
      @mkdir($dir,0755,true);
      $temp="ai tool phpin rename \"$name\" \"$dir/$name\"";
    }
    $url=preg_match('/^http/i',$url)?$url:$baseurl.$url;
    $res[]="ai tool out \"$url\"";
    $res[]="ai tool dl \"$url\"";
    if($temp){$res[]=$temp;}
  }//*/
  /**
  if(preg_match('/\.(jpg|jpeg|png|gif|ico|webp|svg)/i',$url)){
    if(!preg_match('/^http/i',$url)){
      $dir=dirname($url);
      $name=basename(explode('?',$url)[0]);
      @mkdir($dir,0755,true);
      $temp="ai tool phpin rename \"$name\" \"$dir/$name\"";
    }
    $url=preg_match('/^http/i',$url)?$url:$baseurl.$url;
    $res[]="ai tool out \"$url\"";
    $res[]="ai tool dl \"$url\"";
    if($temp){$res[]=$temp;}
  }//*/
  /* fonts */
  if(preg_match('/\.(svg|eot|ttf|woff|woff2)/i',$url)){
    if(!preg_match('/^http/i',$url)){
      $dir=dirname($url);
      $name=basename(explode('?',$url)[0]);
      @mkdir("$path/$dir",0755,true);
      if(is_file("$path/$dir/$name")){continue;}
      $temp="ai tool phpin rename \"$name\" \"$path/$dir/$name\"";
    }
    $url=preg_match('/^http/i',$url)?$url:$baseurl.$url;
    $res[]="ai tool out \"$url\"";
    $res[]="ai tool dl \"$url\"";
    if($temp){$res[]=$temp;}
  }//*/
  /**
  if(preg_match('/\.(jpg|jpeg|png|gif|ico|webp)/i',$url)){
    $res[$url]='images/'.basename($url);
  }//*/
}


print_r($res);
echo file_put_contents($aisFile,implode("\r\n",$res))."\n";

//print_r(strtr($content,$res));
//echo file_put_contents('landing.2.html',strtr($content,$res));



//https://www.bengkelbos.co.id/assets/



