<?php
/* ldb class
 * ~ stands for luthfie database
 * ~ custom portable database
 * ~ version 2x - fixed version
 * authored by 9r3i
 * https://github.com/9r3i
 * started at february 12th 2018 - version 2x.1.0
 * continued at february 28th 2018 - version 2x.2.0
 * - using file lock to prevent from another access
 * continued at december 1st 2018 - version 2x.3.0
 * - delete temporary files in method update, delete and convertToLdbX
 * continued at november 5th 209 - version 2x.4.0
 * - class totally renamed to Ldb2x from Ldb
 * - replace setting database directory; default: Ldb2x (this file directory)
 * - add constant LDB2X_CLI_DIR to replace/overwrite database root
 * continued at november 21st 209 - version 2x.4.1
 * - fix error unlink in method update, delete and elses
 * continued at october 4th 2021 - version 2x.4.2
 * - remove get_magic_quotes_gpc in strip_magic method
 * continued at december 25th 2021 - version 2x.4.3
 * - fix error of flock as php8 termux refuses to do so
 */
class Ldb2x{
  const version='2x.4.3';
  protected $database=null;
  protected $tables=null;
  private $dir=null;
  private $db_dir=null;
  private $dump_dir=null;
  private $temp_dir=null;
  private $bid=null;
  private $cid=null;
  private $login=false;
  public $time=null;
  public $microtime=null;
  public $error=false;
  public $aid=0;
  public function __construct($db=null){
    if(!is_string($db)||!preg_match('/^[a-z0-9]+$/i',$db)){
      $this->error='Invalid database';
      return false;
    }
    $this->time=time();
    $this->microtime=microtime(true);
    $this->bid=452373300; /* Luthfie's birthdate in time(); */
    $this->cid=dechex($this->time-$this->bid);
    $this->database=$db;
    $this->setting();
    $this->login=true;
    $this->show_tables();
  }
  /* new public functions */
  public function insert($table,$data=array()){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $filename=$this->db_dir.$table.'.ldb';
    if(!is_file($filename)){
      $this->error='Table '.$table.' does not exist';
      return false;
    }elseif(!is_array($data)){
      $this->error='Require array data';
      return false;
    }
    if(!$this->isLdbX($table)){
      $this->convertToLdbX($table);
      return $this->insert($table,$data);
    }
    $this->tdb($table);
    $this->aid++;
    $this->tdb($table,$this->aid);
    $column=['aid'=>$this->aid];
    foreach($data as $k=>$v){
      $column[$k]=$k=='password'?$this->hash((string)$v):(string)$v;
    }
    $column['time']=$this->time;
    $this->cid=dechex(time());
    $column['cid']=$this->cid;
    $o=fopen($filename,'rb+');
    if(!$o){
      $this->error='Failed to open the table';
      return false;
    }@flock($o,LOCK_EX);
    if(fseek($o,0,SEEK_END)===0){
      $w=fwrite($o,base64_encode(serialize($column))."\n");
    }@flock($o,LOCK_UN);fclose($o);
    return isset($w)&&$w?true:false;
  }
  public function select($table,$where=false){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $filename=$this->db_dir.$table.'.ldb';
    if(!is_file($filename)){
      $this->error='Table '.$table.' does not exist';
      return false;
    }
    if(!$this->isLdbX($table)){
      $this->convertToLdbX($table);
      return $this->select($table,$where);
    }
    parse_str(is_string($where)?$where:'',$index);
    if(!is_array($index)){
      $this->error='Cannot find index table';
      return false;
    }
    $hasil=array();
    $o=fopen($filename,'rb');
    while(!feof($o)){
      $g=fgets($o);
      $d=unserialize(base64_decode(trim($g)));
      if(!is_array($d)){continue;}
      if(0==count($index)){
        $hasil[]=$d;
        continue;
      }$store=0;
      foreach($index as $k=>$v){
        if(isset($d[$k])&&$d[$k]==$v){
          $store+=1;
        }
      }if($store==count($index)){$hasil[]=$d;}
    }fclose($o);
    return $hasil;
  }
  public function update($table,$where=false,$data=array()){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $filename=$this->db_dir.$table.'.ldb';
    if(!is_file($filename)){
      $this->error='Table '.$table.' does not exist';
      return false;
    }elseif(!is_string($where)){
      $this->error='Require string wherance';
      return false;
    }elseif(!is_array($data)){
      $this->error='Require array data';
      return false;
    }
    if(!$this->isLdbX($table)){
      $this->convertToLdbX($table);
      return $this->update($table,$where,$data);
    }
    $index=explode('=',$where);
    if(!is_array($index)||!isset($index[1])){
      $this->error = 'Cannot find index table';
      return false;
    }
    $temp=$this->temp_dir.$table.'-'.$this->cid.'.tmp';
    $o=fopen($filename,'rb');
    $t=fopen($temp,'wb');
    if(!$o){
      $this->error='Failed to open the table';
      return false;
    }$res=0;@flock($o,LOCK_EX);
    while(!feof($o)){
      $g=fgets($o);
      $d=unserialize(base64_decode(trim($g)));
      if(isset($d[$index[0]])&&$d[$index[0]]==$index[1]){
        foreach($d as $k=>$v){
          if(isset($data[$k])){
            $d[$k]=$k=='password'?$this->hash($data[$k]):$data[$k];
          }
        }$g=base64_encode(serialize($d))."\n";
      }
      fwrite($t,$g);
      $res+=1;
    }@flock($o,LOCK_UN);
    fclose($o);
    fclose($t);
    $copy=@copy($temp,$filename);
    if(is_file($temp)){@unlink($temp);}
    return $copy?$res:false;
  }
  public function delete($table,$where=false){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $filename=$this->db_dir.$table.'.ldb';
    if(!is_file($filename)){
      $this->error='Table '.$table.' does not exist';
      return false;
    }elseif(!is_string($where)){
      $this->error='Require wherance';
      return false;
    }
    if(!$this->isLdbX($table)){
      $this->convertToLdbX($table);
      return $this->delete($table,$where);
    }
    $index=explode('=',$where);
    if(!is_array($index)||!isset($index[1])){
      $this->error = 'Cannot find index table';
      return false;
    }
    $temp=$this->temp_dir.$table.'-'.$this->cid.'.tmp';
    $o=fopen($filename,'rb');
    $t=fopen($temp,'wb');
    if(!$o){
      $this->error='Failed to delete';
      return false;
    }$res=0;@flock($o,LOCK_EX);
    while(!feof($o)){
      $g=fgets($o);
      $d=unserialize(base64_decode(trim($g)));
      if(isset($d[$index[0]])&&$d[$index[0]]==$index[1]){continue;}
      fwrite($t,$g);
      $res+=1;
    }@flock($o,LOCK_UN);
    fclose($o);
    fclose($t);
    $copy=@copy($temp,$filename);
    if(is_file($temp)){@unlink($temp);}
    return $copy?$res:false;
  }
  public function search($table,$key=false){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $filename=$this->db_dir.$table.'.ldb';
    if(!is_file($filename)){
      $this->error='Table '.$table.' does not exist';
      return false;
    }elseif(!isset($key)){
      $this->error='Require keywords';
      return false;
    }
    if(!$this->isLdbX($table)){
      $this->convertToLdbX($table);
      return $this->search($table,$key);
    }
    $index=@explode('=',$key);
    if(!is_array($index)||!isset($index[1])){
      $this->error='Cannot find index table';
      return false;
    }
    $hasil=array();
    $o=fopen($filename,'rb');
    while(!feof($o)){
      $g=fgets($o);
      $d=unserialize(base64_decode(trim($g)));
      if(!isset($d[$index[0]])){continue;}
      $c=stripos($d[$index[0]],$index[1]);
      if($c===false){continue;}
      $hasil[]=$d;
    }fclose($o);
    return $hasil;
  }
  public function create_table($name=null){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $name=isset($name)?$name:'+';
    if(preg_match('/^[a-zA-Z0-9_]+$/i',$name,$akur)){
      $filename=$this->db_dir.$akur[0].'.ldb';
      if(is_file($filename)){
        $this->error='Table '.$akur[0].' has been existed';
        return false;
      }
      if(!$this->write($filename)){
        $this->error='Failed to create table '.$akur[0].'';
        return false;
      }
      $this->error=false;
      $this->show_tables();
      return true;
    }else{
      $this->error='Character name is not available';
      return false;
    }
  }
  public function delete_table($name=null){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $name=isset($name)?$name:'+';
    $filename=$this->db_dir.$name.'.ldb';
    if(is_file($filename)){
      @copy($filename,$this->dump_dir.$name.'_'.$this->cid.'.ldb');
      $this->tdb($name,'0');
      if($filename){@unlink($filename);}
      $this->show_tables();
      return true;
    }else{
      $this->error='Table '.$name.' does not exist';
      return false;
    }
  }
  public function show_tables(){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $sdir=@scandir($this->db_dir);
    $hasil=array();
    foreach($sdir as $sd){
      if(preg_match('/^[a-zA-Z0-9_]+\.ldb$/i',$sd)){
        $hasil[]=str_replace('.ldb','',$sd);
      }
    }return $this->tables = $hasil;
  }
  public function show_database(){
    if(!$this->login){
      $this->error='Require database access';
      return false;
    }$this->error=false;
    $sdir=@scandir($this->dir);
    $hasil=array();
    foreach($sdir as $sd){
      if(preg_match('/^[a-zA-Z0-9_]+$/i',$sd)&&$sd!=='.htaccess'&&is_dir($this->dir.$sd)){
        $hasil[]=$sd;
      }
    }return $hasil;
  }
  public function valid_password($table,$where,$password){
    $select=$this->select($table,$where);
    if(isset($select[0],$select[0]['password'])&&$select[0]['password']==$this->hash($password)){
      return true;
    }return false;
  }
  public function hash($password=null,$algo='sha256'){
    $algo=in_array($algo,hash_algos())?$algo:'sha256';
    $hash=hash($algo,$password,false);
    return $hash;
  }
  public function strip_magic($str){
    return $str;
  }
  public function spend_time(){
    return @number_format(@microtime(true)-$this->microtime,4,'.','');
  }
  /* new private functions */
  private function isLdbX($table=null){
    $filename=$this->db_dir.$table.'.ldb';
    if(!is_file($filename)){
      $this->error='Table '.$table.' does not exist';
      return false;
    }
    $o=fopen($filename,'rb');
    $g=fgets($o);
    if($g==''){return true;}
    $d=@unserialize(base64_decode(trim($g)));
    return is_array($d)?true:false;
  }
  private function convertToLdbX($table=null){
    $filename=$this->db_dir.$table.'.ldb';
    if(!is_file($filename)){
      $this->error='Table '.$table.' does not exist';
      return false;
    }
    $content=@file_get_contents($filename);
    $parse=$this->parse_content($content);
    $temp=$this->temp_dir.$table.'-'.$this->cid.'.tmp';
    $t=fopen($temp,'wb');$w=0;
    foreach($parse as $data){
      $w+=fwrite($t,base64_encode(serialize($data))."\n");
    }fclose($t);
    @copy($filename,$this->dump_dir.$table.'.old');
    @copy($temp,$filename);
    if(is_file($temp)){@unlink($temp);}
    return $w;
  }
  /* old private functions */
  private function tdb($table,$key=false){
    $tdb=$this->db_dir.'~tdb_'.$table.'.tdb';
    if($key){
      return $this->write($tdb,$key);
    }elseif(!is_file($tdb)){
      $this->write($tdb,'0');
      return '0';
    }else{
      $content=@file_get_contents($tdb);
      $this->aid=$content;
      return $content;
    }
  }
  private function write($filename=null,$content='',$type='wb'){
    $filename=isset($filename)?$filename:'error-'.time().'.txt';
    $fp=fopen($filename,$type);
    if($fp){
      @flock($fp,LOCK_EX);
      $write=fwrite($fp,$content);
      @flock($fp,LOCK_UN);
      fclose($fp);
      return $write?true:false;
    }fclose($fp);
    return false;
  }
  private function setting(){
    /* prepare Ldb directory */
    $Ldb_dir=str_replace('\\','/',__DIR__).'/Ldb2x/';
    if(defined('LDB2X_CLI_DIR')&&is_string(LDB2X_CLI_DIR)){
      $Ldb_dir=str_replace('\\','/',LDB2X_CLI_DIR);
      $Ldb_dir.=substr($Ldb_dir,-1)!='/'?'/':'';
    }
    /* Ldb directory */
    $this->dir=$Ldb_dir;
    if(!is_dir($this->dir)){
      @mkdir($this->dir,0700);
      @chmod($this->dir,0700);
    }
    if(!is_file($this->dir.'.htaccess')){
      $this->write($this->dir.'.htaccess','Options -Indexes'."\r\n".'deny from all');
    }
    /* database directory */
    $this->db_dir=$this->dir.$this->database.'/';
    if(!is_dir($this->db_dir)){
      @mkdir($this->db_dir,0700);
      @chmod($this->db_dir,0700);
    }
    if(!is_file($this->db_dir.'.htaccess')){
      $this->write($this->db_dir.'.htaccess','Options -Indexes'."\r\n".'deny from all');
    }
    /* dump directory */
    $this->dump_dir=$this->db_dir.'_dump/';
    if(!is_dir($this->dump_dir)){
      @mkdir($this->dump_dir,0700);
      @chmod($this->dump_dir,0700);
    }
    if(!is_file($this->dump_dir.'.htaccess')){
      $this->write($this->dump_dir.'.htaccess','Options -Indexes'."\r\n".'deny from all');
    }
    /* temp directory */
    $this->temp_dir=$this->db_dir.'_temp/';
    if(!is_dir($this->temp_dir)){
      @mkdir($this->temp_dir,0700);
      @chmod($this->temp_dir,0700);
    }
    if(!is_file($this->temp_dir.'.htaccess')){
      $this->write($this->temp_dir.'.htaccess','Options -Indexes'."\r\n".'deny from all');
    }
  }
  /* old methods - to help convert into x version */
  private function parse_content($content,$access=4){
    $batas_row = $this->batas('row');
    $batas_column = $this->batas('column');
    $batas_equal = $this->batas('equal');
    $hasil = array();
    $rows = @explode($batas_row,$content);
    if(is_array($rows)&&count($rows)>0){
      $r=0;
      foreach($rows as $row){
        if(!empty($row)){
          $columns = @explode($batas_column,$row);
          if(is_array($columns)&&count($columns)>0){
            foreach($columns as $column){
              $equal = @explode($batas_equal,$column);
              if(isset($equal[0])&&isset($equal[1])){
                $hasil[$r][$equal[0]] = $equal[1];
              }
            }
          }
          $r++;
        }
      }
      if($access==16){
        $full = array();
        foreach($hasil as $has){
          if(is_array($has)&&count($has)>0){
            foreach($has as $key=>$val){
              $full[$key][$val][] = $has;
            }
          }
        }
        $hasil = $full;
      }
    }return $hasil;
  }
  private function batas($key=''){
    $batas = array(
      'table'=>'+++++++++'.md5('batas_table').'+++++++++',
      'row'=>'-----'.md5('batas_row').'-----',
      'column'=>'||'.md5('batas_column').'||',
      'equal'=>'==='.md5('batas_equal').'==='
    );
    return array_key_exists($key,$batas)?$batas[$key]:false;
  }
}


