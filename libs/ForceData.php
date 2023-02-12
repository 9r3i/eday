<?php
/**
 * ForceData
 * ~ the 6th generation -- project F (foxtrot)
 * ~ data helper for ForceServer
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 13th 2022
 */
class ForceData{
  const version='1.1.1';
  private $dir=null;
  private $diri=null;
  /* construct */
  public function __construct(string $dir){
    /* prepare directory */
    $forceRoot=defined('FORCE_CLI_DIR')
      ?str_replace('\/','/',FORCE_CLI_DIR)
      :str_replace('\/','/',__DIR__);
    $forceRoot.=substr($forceRoot,-1)!='/'?'/':"";
    $dir=preg_replace('/[^0-9a-z_]+/i','',$dir);
    $root=$forceRoot.'/force/data/';
    $this->dir=str_replace('\/','/',$root.$dir.'/data/');
    if(!is_dir($this->dir)){
      @mkdir($this->dir,0755,true);
    }
    $this->diri=str_replace('\/','/',$root.$dir.'/images/');
    if(!is_dir($this->diri)){
      @mkdir($this->diri,0755,true);
    }
    /* prepare first touch */
    $this->firstTouch();
  }
  /* =============== private helpers =============== */
  /* first touch */
  private function firstTouch(){
    $indexArray=[
      '<?php',
      'header("HTTP/1.1 401 Unauthorized");',
      'exit("Error: 401 Unauthorized");',
    ];
    $index=implode("\r\n",$indexArray);
    $ifile=$this->dir.'index.php';
    $ifilei=$this->diri.'index.php';
    if(!is_file($ifile)){@file_put_contents($ifile,$index);}
    if(!is_file($ifilei)){@file_put_contents($ifilei,$index);}
    $users=[
      [
        'uname'=>'9r3i',
        'upass'=>'$2y$10$n86NUzV/XCTtgMMyalIHNO27YUouHlqAfzDdlfbB1mte0wR17maXq',
      ],
      [
        'uname'=>'admin',
        'upass'=>'$2y$10$OCFD3egt81uBiVMszBJoxeEbAkCwquI9aLgYQ8odCenVXbEopdlB.',
      ],
    ];
    $ufile=$this->dir.'__user.json';
    if(!is_file($ufile)){$this->data('user',$users);}
    return true;
  }
  /* =============== helpers =============== */
  /* global variable */
  final public static function globVar(string $key){
    global $FORCEDATA_GLOBVAR;
    $FORCEDATA_GLOBVAR=is_array($FORCEDATA_GLOBVAR)
      ?$FORCEDATA_GLOBVAR:[];
    $args=func_get_args();
    if(isset($args[1])){
      $FORCEDATA_GLOBVAR[$key]=$args[1];
      return true;
    }return isset($FORCEDATA_GLOBVAR[$key])
      ?$FORCEDATA_GLOBVAR[$key]:false;
  }
  /* dir */
  public function dir(){
    return $this->dir;
  }
  /* diri */
  public function diri(){
    return $this->diri;
  }
  /* is a valid pkey */
  public function isValidPkey(string $pkey):bool{
    $parse=$this->pkeyParse($pkey);
    if(!$parse->valid||$parse->expire<time()){
      return false;
    }
    $users=$this->findData('uname',$parse->uname,'user');
    if(!is_array($users)||count($users)<1){
      return false;
    }
    $user=array_values($users)[0];
    return $parse->uname===$user['uname'];
  }
  /* find by id in data array */
  public function findById($id){
    return $this->findData('id',$id);
  }
  /* find by key in data array */
  public function findData($key,$value,string $base='data'){
    $data=$this->data($base);
    ForceData::globVar('key',$key);
    ForceData::globVar('value',$value);
    return array_filter($data,function($v){
      $key=ForceData::globVar('key');
      $value=ForceData::globVar('value');
      return isset($v[$key])&&$v[$key]==$value;
    });
  }
  /* base of data */
  public function data(string $dname='data',$ndata=false){
    $name='__'.$dname.'.json';
    if(is_array($ndata)){
      return $this->write($name,@json_encode($ndata));
    }
    if(!is_file($this->dir.$name)){
      $this->write($name,'[]');
    }
    $raw=$this->read($name);
    $data=@json_decode($raw,true);
    return is_array($data)?$data:[];
  }
  /* =============== stand-alone =============== */
  /* read */
  public function read(string $name):string{
    $file=$this->dir.$name;
    $o=fopen($file,'rb');
    $s=filesize($file);
    if(!is_resource($o)||$s==0){return '';}
    $r=fread($o,filesize($file));
    fclose($o);
    return $r;
  }
  /* write */
  public function write(string $name,string $content=''){
    $file=$this->dir.$name;
    $o=fopen($file,'wb');
    if(!is_resource($o)){return 0;}
    $w=fwrite($o,$content);
    fclose($o);
    return $w;
  }
  /* convert title to slug */
  public function toSlug(string $name):string{
    return preg_replace('/[^0-9a-z]+/','-',strtolower(trim($name)));
  }
  /* create a pkey */
  public function pkeyCreate(string $uname,int $expire):string{
    $ex=base_convert($expire,10,36);
    $hash=preg_replace('/[^0-9a-z]+/','',strtolower(base64_encode(md5($uname.$expire,true))));
    return implode('.',[
      $uname,
      $ex,
      $hash,
    ]);
  }
  /* =============== private stand-alone =============== */
  /* parse a pkey */
  private function pkeyParse(string $pkey){
    $raw=explode('.',$pkey);
    if(count($raw)!=3){return false;}
    $uname=$raw[0];
    $expire=base_convert($raw[1],36,10);
    $hash=preg_replace('/[^0-9a-z]+/','',strtolower(base64_encode(md5($uname.$expire,true))));
    return (object)[
      'uname'=>$uname,
      'expire'=>$expire,
      'key'=>$raw[2],
      'hash'=>$hash,
      'valid'=>$hash===$raw[2],
    ];
  }
}
