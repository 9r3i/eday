<?php
/* web Plugins
 * ~ website plugins management
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 6th 2019
 */
class webPlugins{
  const version='1.0.0';
  private $dir=null;
  private $path=null;
  private $plugged=null;
  private $listed=null;
  public function __construct(string $dir='',string $path=''){
    /* prepare plugins directory */
    $dir=str_replace('\\','/',$dir);
    $dir.=substr($dir,-1)!='/'?'/':'';
    $this->dir=$dir;
    /* prepare plugins path */
    $path.=substr($path,-1)!='/'?'/':'';
    $this->path=$path;
    /* defined website plugin directory */
    defined('WEBSITE_PLUGINS_DIRECTORY') 
      or define('WEBSITE_PLUGINS_DIRECTORY',$dir);
    defined('WEBSITE_PLUGINS_PATH') 
      or define('WEBSITE_PLUGINS_PATH',$path);
    /* scan directory */
    $this->plugged=[];
    $this->listed=[];
    $scan=@array_diff(@scandir($this->dir),['..','.']);
    $scan=is_array($scan)?$scan:[];
    foreach($scan as $file){
      $cfile=$this->dir."{$file}/{$file}.php";
      if(is_file($cfile)){
        $this->plugged[]=$file;
        require_once($cfile);
      }$this->listed[$file]=$this->about($file);
    }return $this;
  }
  /* get plugin listed */
  public function listed(){
    return $this->listed;
  }
  /* get plugin about */
  public function about(string $ns){
    $iniFile=$this->dir."{$ns}/about.ini";
    $info=[
      'namespace'=>$ns,
      'name'=>$ns,
      'author'=>'',
      'author-uri'=>'',
      'version'=>'1.0.0',
      'description'=>'',
    ];
    $def=new dataObject([
      'info'=>$info,
      'config'=>[]
    ]);
    if(is_file($iniFile)
      &&is_readable($iniFile)
      &&($ini=@parse_ini_file($iniFile,true))
      &&is_array($ini)){
      if($ini['info']){
        foreach($ini['info'] as $key=>$value){
          if(isset($info[$key])
            &&$key!='namespace'){
            $info[$key]=$value;
          }
        }$def->info=new dataObject($info);
      }
      if(isset($ini['config'])){
        $def->config=new dataObject($ini['config']);
      }
    }return $def;
  }
  /* load all plugins by method */
  public function load(string $method='',$arg=null){
    /* load every object match to method name */
    foreach($this->plugged as $plug){
      $obj=new $plug;
      if(!is_object($obj)){continue;}
      /* check method is public */
      if(!method_exists($obj,$method)
        ||!(new ReflectionMethod($obj,$method))->isPublic()){
        continue;
      }
      /* execute requested method */
      $arg=\call_user_func_array([$obj,$method],[$arg]);
    }return $arg;
  }
  /* get admin page */
  public function adminPage(string $ns,$arg=null){
    if(!in_array($ns,$this->plugged)
      ||!class_exists($ns,false)){
      $error='Error: Plugin admin page for "'.$ns.'" is not available.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }
    $obj=new $ns;
    if(!is_object($obj)){
      $error='Error: Plugin admin page for "'.$ns.'" is not working.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }
    /* check method is public */
    $method='adminPage';
    if(!method_exists($obj,$method)
      ||!(new ReflectionMethod($obj,$method))->isPublic()){
      $error='Error: Plugin admin page METHOD for "'.$ns.'" is not public.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }
    /* execute requested method */
    $result=\call_user_func_array([$obj,$method],[$arg]);
    /* check result */
    if(is_null($result)){
      $error='Error: Plugin admin page for "'.$ns.'" is return NULL.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }elseif(is_bool($result)){
      $error='Error: Plugin admin page for "'.$ns.'" is return '
        .($result?'TRUE':'FALSE').'.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }elseif(!is_string($result)){
      $error=print_r($result,true);
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }exit($result);
  }
}


