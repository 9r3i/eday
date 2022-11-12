<?php
/* sdb
 * ~ customized SQLite3 database
 * authored by 9r3i
 * https://github.com/9r3i
 * started at august 23rd 2018
 * continue at september 2nd 2018 - v1.1.0 - complete alter table
 * continue at september 3rd 2018 - v2.0.0 - full hosting connection
 * require: SQLite3 extension
 */
class sdb{
  const version='2.0.3';
  protected $host=null;
  protected $database=null;
  protected $connected=false;
  protected $db=null;
  protected $user=null;
  protected $session=null;
  protected $scope=[];
  protected $access=[];
  protected $errors=[];
  protected $directory=null;
  protected $serverInformation=null;
  public $error=false;
  public function __construct($host=null,$user=null,$pass=null,$db=null,$timezone=null){
    /* set public stating time */
    $this->start=microtime(true);
    /* check SQLite3 class */
    if(!class_exists('SQLite3',false)){
      return $this->error('this class require SQLite3 extension');
    }
    /* database host */
    $this->host=is_string($host)?$host:'localhost';
    /* check database name */
    if(!is_string($db)||!preg_match('/^[a-zA-Z0-9_]+$/',$db)){
      return $this->error('invalid database name');
    }$this->database=$db;
    /* set default timezone */
    if(!@date_default_timezone_set($timezone)){@date_default_timezone_set('Asia/Jakarta');}
    /* setup database root directory */
    $dir=defined('SDB_CLI_DIR')?SDB_CLI_DIR:__DIR__;
    $dir=str_replace('\\','/',$dir);
    $dir.=substr($dir,-1)!='/'?'/':'';
    if(!is_dir($dir)){@mkdir($dir,0755,true);}
    $this->directory=$dir;
    /* constants customizing */
    $sqlite_constants=[
      'SQLITE3_ASSOC','SQLITE3_NUM','SQLITE3_BOTH', // 1,2,3
      'SQLITE3_INTEGER','SQLITE3_FLOAT','SQLITE3_TEXT','SQLITE3_BLOB','SQLITE3_NULL' // 1,2,3,4,5
    ];
    foreach($sqlite_constants as $const){
      $nconst=preg_replace('/^SQLITE3_/','SDB_',$const);
      defined($nconst) or define($nconst,constant($const));
    }
    /* return self object as localhost */
    if($this->host=='localhost'){
      return $this->preload($user,$pass)?$this:false;
    }
    /* stream mode */
    $this->serverUserSet($user,$pass,isset($timezone)?$timezone:'Asia/Jakarta');
    return $this->serverAccess()?$this:false;
  }
  /* ----- public methods ----- */
  public function close($n=null){
    if($this->host==='localhost'){
      @$this->db->close();
    }
    $this->host=null;
    $this->database=null;
    $this->connected=false;
    $this->db=null;
    $this->user=null;
    $this->session=null;
    $this->scope=null;
    $this->access=null;
    $this->directory=null;
    $this->serverInformation=null;
    return true;
  }
  public function serverInformation(){
    return $this->serverInformation;
  }
  public function connected(){
    return $this->connected;
  }
  public function session(){
    return $this->session;
  }
  public function scope(){
    return $this->scope;
  }
  public function access(){
    return $this->access;
  }
  public function errors(){
    return $this->errors;
  }
  /* ----- public methods - require connection ----- */
  public function queries($q=null){
    if(!$this->connected){
      return $this->error('require database connection');
    }
    if($this->host!=='localhost'){
      return $this->serverAccess($q,'queries');
    }
    $qs=explode(';',$q);$r=[];
    foreach($qs as $s){
      $s=trim($s);
      if($s==''){continue;}
      $n=$this->query($s);
      if($this->error){
        $r[]='error: '.$this->error;
        $this->error=false;
        continue;
      }$r[]=$n;
    }return $r;
  }
  public function query($q=null){
    if(!$this->connected){
      return $this->error('require database connection');
    }
    if(!is_string($q)){return $this->error('invalid query');}
    $q=trim($q);
    $stats=implode('|',array_keys($this->statements()));
    if(!preg_match('/^('.$stats.')\b(.*)$/i',$q,$a)){
      return $this->error('invalid statement');
    }
    $statement=strtolower($a[1]);
    $query=trim($a[2]);
    if(!array_key_exists($statement,$this->statements())){
      return $this->error('unknown statement "'.$statement.'"');
    }
    $fn=$this->statements()[$statement];
    if(!method_exists($this,$fn)){
      return $this->error('method statement does not exist');
    }
    if(!in_array(strtoupper($statement),$this->scope)){
      return $this->error('user has no scope to this statement');
    }
    if($this->host!=='localhost'){
      return $this->serverAccess($q,'query');
    }
    $call=@\call_user_func_array([$this,$fn],[$query]);
    if(!$call){
      if($this->error){return false;}
      return $this->error('failed to execute query');
    }
    return $call;
  }
  public function server($l=0){
    @set_time_limit($l);
    $this->serverHeader();
    if(isset($_POST['sdb'])){
      return $this->serverLoad();
    }return false;
  }
  /* ----- private methods ----- */
  private function real($fq=null,$r=null){
    $ptrn='/^(db|query|exec|single|lastid|version|function)(.*)?$/i';
    if(!is_string($fq)||!preg_match($ptrn,$fq,$a)){
      return $this->error('invalid function');
    }$f=strtolower($a[1]);
    $q=isset($a[2])?trim($a[2]):'';
    if(in_array($f,['db','lastid','version'])&&$q!==''){
      return $this->error('invalid query near: '.$q);
    }elseif($f=='db'){return $this->db;}
    elseif($f=='lastid'){return @$this->db->lastInsertRowID();}
    elseif($f=='version'){return SQLite3::version();}
    elseif($f=='function'){return @$this->db->createFunction($q,$r);}
    elseif($f=='query'){$c=@$this->db->query($q);}
    elseif($f=='single'){$c=@$this->db->querySingle($q,true);}
    elseif($f=='exec'){$c=@$this->db->exec($q);}
    else{return $this->error('unknown function "'.$f.'"');}
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }
    if(is_object($c)&&method_exists($c,'fetchArray')){
      $r=[];
      while($t=$c->fetchArray(SDB_ASSOC)){
        $r[]=$t;
      }return $r;
    }return $c;
  }
  private function select($q=null){
    if(!is_string($q)){return $this->error('invalid query');}
    $pattern='/^(.*)\s+from\s+([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)(.*)$/i';
    if(!preg_match($pattern,$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[2]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    if(!in_array($table,$this->access)){
      return $this->error('has no access to this table');
    }
    $query='select '.$a[1].' from '.$table.' '.$a[3];
    $prep=$this->db->prepare($query);
    if(!$prep){return $this->error('failed to prepare query');}
    $s=$this->db->query($query);
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }$r=[];
    while($t=$s->fetchArray(SDB_ASSOC)){
      $r[]=$t;
    }return $r;
  }
  private function delete($q=null){
    if(!is_string($q)){return $this->error('invalid query');}
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)\s+where(.*)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    if(!in_array($table,$this->access)){
      return $this->error('has no access to this table');
    }
    $where=trim($a[2]);
    $query='delete from '.$table.' where '.$where;
    $prep=$this->db->prepare($query);
    if(!$prep){return $this->error('failed to prepare query');}
    if(!$prep->execute()||$prep->errno){
      return $this->error($prep->error);
    }
    $prep->close();
    return $this->db->changes();
  }
  private function update($q=null){
    if(!is_string($q)){return $this->error('invalid query');}
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)(.*)where(.*)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    if(!in_array($table,$this->access)){
      return $this->error('has no access to this table');
    }
    if(!mb_parse_str(trim($a[2]),$parse)||count($parse)==0){
      return $this->error('require column parameter');
    }$where=trim($a[3]);
    $column=[];$data=[];$set=[];$error=false;
    foreach($parse as $k=>$v){
      if(!preg_match('/^[a-z0-9_]+$/i',$k)){
        $error='invalid column name format "'.$k.'"';break;
      }
      if(!preg_match('/^(int|string|blob|float)\((.*)\)$/i',$v,$b)){
        if(!preg_match('/^(int|string|blob|float):/i',$v,$b)){
          $error='invalid column value "'.$v.'"';break;
        }$b=[$v,$b[1],substr($v,strlen($b[1])+1)];
      }
      $set[$k]=$k.'=:'.$k;
      if(strtolower($b[1])=='int'){
        $column[$k]=SDB_INTEGER;
        $data[$k]=intval($b[2]);
      }elseif(strtolower($b[1])=='float'){
        $column[$k]=SDB_FLOAT;
        $data[$k]=floatval($b[2]);
      }elseif(strtolower($b[1])=='string'){
        $column[$k]=SDB_TEXT;
        $data[$k]=(string)$b[2];
      }elseif(strtolower($b[1])=='blob'){
        $column[$k]=SDB_BLOB;
        $data[$k]=$b[2];
      }else{
        $error='unknown error in column name "'.$k.'"';break;
      }
    }
    if($error){return $this->error($error);}
    $query='update '.$table.' set '.implode(',',$set).' where '.$where;
    $prep=$this->db->prepare($query);
    if(!$prep){return $this->error('failed to prepare query');}
    foreach($column as $k=>$v){
      $prep->bindValue(':'.$k,$data[$k],$v);
    }
    if(!$prep->execute()||$prep->errno){
      return $this->error($prep->error);
    }
    $prep->close();
    return $this->db->changes();
  }
  private function insert($q=null){
    if(!is_string($q)){return $this->error('invalid query');}
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)(.*)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    if(!in_array($table,$this->access)){
      return $this->error('has no access to this table');
    }
    if(!mb_parse_str(trim($a[2]),$parse)||count($parse)==0){
      return $this->error('require column parameter');
    }
    $column=[];$data=[];$error=false;
    foreach($parse as $k=>$v){
      if(!preg_match('/^[a-z0-9_]+$/i',$k)){
        $error='invalid column name format "'.$k.'"';break;
      }
      if(!preg_match('/^(int|string|blob|float)\((.*)\)$/i',$v,$b)){
        if(!preg_match('/^(int|string|blob|float):/i',$v,$b)){
          $error='invalid column value "'.$v.'"';break;
        }$b=[$v,$b[1],substr($v,strlen($b[1])+1)];
      }
      if(strtolower($b[1])=='int'){
        $column[$k]=SDB_INTEGER;
        $data[$k]=intval($b[2]);
      }elseif(strtolower($b[1])=='float'){
        $column[$k]=SDB_FLOAT;
        $data[$k]=floatval($b[2]);
      }elseif(strtolower($b[1])=='string'){
        $column[$k]=SDB_TEXT;
        $data[$k]=(string)$b[2];
      }elseif(strtolower($b[1])=='blob'){
        $column[$k]=SDB_BLOB;
        $data[$k]=$b[2];
      }else{
        $error='unknown error in column name "'.$k.'"';break;
      }
    }
    if($error){return $this->error($error);}
    $query='insert into '.$table.' ('.implode(',',array_keys($column)).') '
      .'values (:'.implode(',:',array_keys($column)).')';
    $prep=$this->db->prepare($query);
    if(!$prep){return $this->error('failed to prepare query');}
    foreach($column as $k=>$v){
      $prep->bindValue(':'.$k,$data[$k],$v);
    }
    if(!$prep->execute()||$prep->errno){
      return $this->error($prep->error);
    }
    $prep->close();
    return $this->db->changes();
  }
  private function columnAdd($table=null,$parse=null){
    if(!is_string($table)||!preg_match('/^[a-z0-9_]+$/i',$table)){
      return $this->error('invalid new table name');
    }
    if(!is_array($parse)||count($parse)==0){
      return $this->error('require column parameter');
    }
    $column=[];$error=false;
    foreach($parse as $k=>$v){
      if(!preg_match('/^[a-z0-9_]+$/i',$k)){
        $error='invalid column name format "'.$k.'"';break;
      }
      if(!preg_match('/^(aid|string|int|varchar)\((\d+)?\)$/i',$v,$b)){
        $error='invalid column value "'.$v.'"';break;
      }
      if(strtolower($b[1])=='aid'){
        $column[]=$k.' INTEGER PRIMARY KEY AUTOINCREMENT';
      }elseif(strtolower($b[1])=='string'){
        $length=isset($b[2])?(int)$b[2]:256;
        $column[]=$k.' STRING('.$length.') NOT NULL DEFAULT ""';
      }elseif(strtolower($b[1])=='int'){
        $length=isset($b[2])&&(int)$b[2]<20?(int)$b[2]:10;
        $column[]=$k.' INTEGER('.$length.') NOT NULL DEFAULT 0';
      }elseif(strtolower($b[1])=='varchar'){
        $length=isset($b[2])?(int)$b[2]:256;
        $column[]=$k.' VARCHAR('.$length.') NOT NULL DEFAULT ""';
      }else{
        $error='unknown error in column name "'.$k.'"';break;
      }
    }if($error){return $this->error($error);}
    $success=0;
    foreach($column as $col){
      $query='alter table '.$table.' ADD '.$col.'';
      $x=@$this->db->exec($query);
      if($this->db->lastErrorCode()>0){$error=$this->db->lastErrorMsg();break;}
      $success++;
    }if($error){return $this->error($error);}
    return $success;
  }
  private function columnEdit($table=null,$parse=null){
    if(!is_string($table)||!preg_match('/^[a-z0-9_]+$/i',$table)){
      return $this->error('invalid new table name');
    }
    if(!is_array($parse)||count($parse)==0){
      return $this->error('require column parameter');
    }$columns=$this->showColumns($table.' raw');
    if(!is_array($columns)||count($columns)==0){
      return $this->error('failed to get columns');
    }$column=[];
    foreach($columns as $col){
      $npk=$col['pk']
        ?'INTEGER PRIMARY KEY AUTOINCREMENT'
        :$col['type'].' NOT NULL DEFAULT '.$col['dflt_value'];
      $column[$col['name']]=$col['name'].' '.$npk;
    }$error=false;
    foreach($parse as $k=>$v){
      if(!preg_match('/^[a-z0-9_]+$/i',$k)){
        $error='invalid column name format "'.$k.'"';break;
      }
      if(!array_key_exists($k,$column)){
        $error='unknown column name "'.$k.'"';break;
      }
      if(!preg_match('/^(aid|string|int|varchar)\((\d+)?\)$/i',$v,$b)){
        $error='invalid column value "'.$v.'"';break;
      }
      if(strtolower($b[1])=='aid'){
        $column[$k]=$k.' INTEGER PRIMARY KEY AUTOINCREMENT';
      }elseif(strtolower($b[1])=='string'){
        $length=isset($b[2])?(int)$b[2]:256;
        $column[$k]=$k.' STRING('.$length.') NOT NULL DEFAULT ""';
      }elseif(strtolower($b[1])=='int'){
        $length=isset($b[2])&&(int)$b[2]<20?(int)$b[2]:10;
        $column[$k]=$k.' INTEGER('.$length.') NOT NULL DEFAULT 0';
      }elseif(strtolower($b[1])=='varchar'){
        $length=isset($b[2])?(int)$b[2]:256;
        $column[$k]=$k.' VARCHAR('.$length.') NOT NULL DEFAULT ""';
      }else{
        $error='unknown error in column name "'.$k.'"';break;
      }
    }if($error){return $this->error($error);}
    $tmp='_________'.$table.'_tmp';
    $query='PRAGMA foreign_keys=off; BEGIN TRANSACTION;
      DROP TABLE IF EXISTS '.$tmp.';
      ALTER TABLE '.$table.' RENAME TO '.$tmp.';
      CREATE TABLE '.$table.'('.implode(',',$column).');
      INSERT INTO '.$table.' ('.implode(',',array_keys($column)).')
        SELECT '.implode(',',array_keys($column)).' FROM '.$tmp.';
      DROP TABLE IF EXISTS '.$tmp.';
      COMMIT; PRAGMA foreign_keys=on;';
    $x=@$this->db->exec($query);
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }return $x;
  }
  private function columnDelete($table=null,$parse=null){
    if(!is_string($table)||!preg_match('/^[a-z0-9_]+$/i',$table)){
      return $this->error('invalid new table name');
    }
    if(!is_array($parse)||count($parse)==0){
      return $this->error('require column parameter');
    }$columns=$this->showColumns($table.' raw');
    if(!is_array($columns)||count($columns)==0){
      return $this->error('failed to get columns');
    }$column=[];
    foreach($columns as $col){
      $npk=$col['pk']
        ?'INTEGER PRIMARY KEY AUTOINCREMENT'
        :$col['type'].' NOT NULL DEFAULT '.$col['dflt_value'];
      $column[$col['name']]=$col['name'].' '.$npk;
    }$error=false;
    foreach($parse as $k=>$v){
      if(!preg_match('/^[a-z0-9_]+$/i',$k)){
        $error='invalid column name format "'.$k.'"';break;
      }
      if(!array_key_exists($k,$column)){
        $error='unknown column name "'.$k.'"';break;
      }
      unset($column[$k]);
    }if($error){return $this->error($error);}
    $tmp='_________'.$table.'_tmp';
    $query='PRAGMA foreign_keys=off; BEGIN TRANSACTION;
      DROP TABLE IF EXISTS '.$tmp.';
      ALTER TABLE '.$table.' RENAME TO '.$tmp.';
      CREATE TABLE '.$table.'('.implode(',',$column).');
      INSERT INTO '.$table.' ('.implode(',',array_keys($column)).')
        SELECT '.implode(',',array_keys($column)).' FROM '.$tmp.';
      DROP TABLE IF EXISTS '.$tmp.';
      COMMIT; PRAGMA foreign_keys=on;';
    $x=@$this->db->exec($query);
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }return $x;
  }
  private function columnRename($table=null,$parse=null){
    if(!is_string($table)||!preg_match('/^[a-z0-9_]+$/i',$table)){
      return $this->error('invalid new table name');
    }
    if(!is_array($parse)||count($parse)==0){
      return $this->error('require column parameter');
    }$columns=$this->showColumns($table.' raw');
    if(!is_array($columns)||count($columns)==0){
      return $this->error('failed to get columns');
    }$column=[];
    foreach($columns as $col){
      $npk=$col['pk']
        ?'INTEGER PRIMARY KEY AUTOINCREMENT'
        :$col['type'].' NOT NULL DEFAULT '.$col['dflt_value'];
      $column[$col['name']]=$col['name'].' '.$npk;
    }$error=false;$new=$column;
    foreach($parse as $k=>$v){
      if(!preg_match('/^[a-z0-9_]+$/i',$k)){
        $error='invalid column name format "'.$k.'"';break;
      }
      if(!preg_match('/^[a-z0-9_]+$/i',$v)){
        $error='invalid new column name format "'.$v.'"';break;
      }
      if(!array_key_exists($k,$column)){
        $error='unknown column name "'.$k.'"';break;
      }
      if(array_key_exists($v,$column)||array_key_exists($v,$new)){
        $error='column name "'.$v.'" has been taken';break;
      }
      unset($new[$k]);
      $new[$v]=preg_replace('/'.$k.'/',$v,$column[$k]);
    }if($error){return $this->error($error);}
    $tmp='_________'.$table.'_tmp';
    $query='PRAGMA foreign_keys=off; BEGIN TRANSACTION;
      DROP TABLE IF EXISTS '.$tmp.';
      ALTER TABLE '.$table.' RENAME TO '.$tmp.';
      CREATE TABLE '.$table.'('.implode(',',$new).');
      INSERT INTO '.$table.' ('.implode(',',array_keys($new)).')
        SELECT '.implode(',',array_keys($column)).' FROM '.$tmp.';
      DROP TABLE IF EXISTS '.$tmp.';
      COMMIT; PRAGMA foreign_keys=on;';
    $x=@$this->db->exec($query);
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }return $x;
  }
  private function tableAlter($q=null){
    if(!is_string($q)){return $this->error('invalid query');}
    $pattern='/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)\s+'
      .'(rename_to|add_column|edit_column|delete_column|rename_column)(.*)$/i';
    if(!preg_match($pattern,$q,$a)){return $this->error('unrecognized query');}
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    if(!in_array($table,$this->access)){
      return $this->error('has no access to this table');
    }
    $option=strtolower($a[2]);
    if($option=='rename_to'){
      if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)$/i',trim($a[3]))){
        return $this->error('invalid new table name');
      }$new=preg_replace('/[^a-z0-9_]/i','',trim($a[3]));
      $query='ALTER TABLE '.$table.' RENAME TO '.$new;
      $x=@$this->db->exec($query);
      if($this->db->lastErrorCode()>0){
        return $this->error($this->db->lastErrorMsg());
      }return $x;
    }
    if(!mb_parse_str(trim($a[3]),$parse)||count($parse)==0){
      return $this->error('require column parameter');
    }
    if($option=='add_column'){
      return $this->columnAdd($table,$parse);
    }
    if($option=='edit_column'){
      return $this->columnEdit($table,$parse);
    }
    if($option=='delete_column'){
      return $this->columnDelete($table,$parse);
    }
    if($option=='rename_column'){
      return $this->columnRename($table,$parse);
    }
    return $this->error('unknown alter table option "'.$option.'"');
  }
  private function tableTruncate($q=null){
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    if(!in_array($table,$this->access)){
      return $this->error('has no access to this table');
    }
    $columns=$this->showColumns($table.' raw');
    $column=[];
    foreach($columns as $col){
      $npk=$col['pk']
        ?'INTEGER PRIMARY KEY AUTOINCREMENT'
        :$col['type'].' NOT NULL DEFAULT '.$col['dflt_value'];
      $column[$col['name']]=$col['name'].' '.$npk;
    }
    $query='PRAGMA foreign_keys=off; BEGIN TRANSACTION;
      DROP TABLE IF EXISTS '.$table.';
      CREATE TABLE '.$table.'('.implode(',',$column).');
      COMMIT; PRAGMA foreign_keys=on;';
    $x=@$this->db->exec($query);
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }return $x;
  }
  private function tableDrop($q=null){
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    if(!in_array($table,$this->access)){
      return $this->error('has no access to this table');
    }
    $x=@$this->db->exec('drop table '.$table);
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }
    $k=array_search($table,$this->access);
    if($k!==false){unset($this->access[$k]);}
    return $x;
  }
  private function tableCreate($q=null){
    if(!is_string($q)){return $this->error('invalid query');}
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)\s+(.*)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('invalid table name');
    }
    if(!mb_parse_str(trim($a[2]),$parse)||count($parse)==0){
      return $this->error('require column parameter');
    }
    $column=[];$error=false;
    foreach($parse as $k=>$v){
      if(!preg_match('/^[a-z0-9_]+$/i',$k)){
        $error='invalid column name format "'.$k.'"';break;
      }
      if(!preg_match('/^(aid|string|int|varchar)\((\d+)?\)$/i',$v,$b)){
        $error='invalid column value "'.$v.'"';break;
      }
      if(strtolower($b[1])=='aid'){
        $column[]=$k.' INTEGER PRIMARY KEY AUTOINCREMENT';
      }elseif(strtolower($b[1])=='string'){
        $length=isset($b[2])?(int)$b[2]:256;
        $column[]=$k.' STRING('.$length.') NOT NULL DEFAULT ""';
      }elseif(strtolower($b[1])=='int'){
        $length=isset($b[2])&&(int)$b[2]<20?(int)$b[2]:10;
        $column[]=$k.' INTEGER('.$length.') NOT NULL DEFAULT 0';
      }elseif(strtolower($b[1])=='varchar'){
        $length=isset($b[2])?(int)$b[2]:256;
        $column[]=$k.' VARCHAR('.$length.') NOT NULL DEFAULT ""';
      }else{
        $error='unknown error in column name "'.$k.'"';break;
      }
    }
    if($error){return $this->error($error);}
    $query='create table '.$table.'('.implode(',',$column).')';
    $x=@$this->db->exec($query);
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }
    if($this->user('access')=='*'){
      $this->access[]=$table;
    }return $x;
  }
  private function showColumns($q=null){
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)(.*)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }
    $table=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($table==$this->rootName($this->database)){
      return $this->error('table does not exist');
    }
    $option=strtolower(trim($a[2]));
    if(!preg_match('/^(raw|format)$/',$option)&&$option!==''){
      return $this->error('invalid option "'.trim($a[2]).'"');
    }
    $column=$this->db->query('PRAGMA table_info('.$table.')');
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }$r=[];
    while($t=$column->fetchArray(SDB_ASSOC)){
      if($option=='raw'){$r[]=$t;}
      elseif($option=='format'){$r[]=$t['name'].'['.$t['type'].']'.($t['pk']?' PRIMARY':'');}
      else{$r[]=$t['name'];}
    }return $r;
  }
  private function showTables($q=null){
    if($q!==''){return $this->error('invalid query near "'.$q.'"');}
    $s=$this->db->query('SELECT * FROM sqlite_master');
    if($this->db->lastErrorCode()>0){
      return $this->error($this->db->lastErrorMsg());
    }$r=[];
    $tn=$this->rootName($this->database);
    while($t=$s->fetchArray(SDB_ASSOC)){
      if(!in_array($t['name'],['sqlite_sequence',$tn])){
        $r[]=$t['name'];
      }
    }return $r;
  }
  private function showDatabases($q=null){
    if($q!==''){return $this->error('invalid query near "'.$q.'"');}
    $s=@scandir($this->directory);
    $r=[];
    foreach($s as $f){
      if(is_file($this->directory.$f)&&preg_match('/^[a-z0-9_]+\.sdb$/i',$f)){
        $r[]=preg_replace('/\.sdb$/i','',$f);
      }
    }return $r;
  }
  private function databaseCreate($q=null){
    if(!mb_parse_str(trim($q),$p)||count($p)==0){
      return $this->error('require at least 2 parameters, db and user');
    }$error=false;
    $def=['db','user','pass','scope'];
    foreach($p as $k=>$v){
      if(!in_array($k,$def)){$error='unknown parameter "'.$k.'"';break;}
    }if($error){return $this->error($error);}
    if(!isset($p['user'],$p['db'])){
      return $this->error('require db and user parameter');
    }
    if(!preg_match('/^[a-z0-9]+$/i',$p['db'])){
      return $this->error('invalid database name');
    }$df=$this->directory.$p['db'].'.sdb';
    if(is_file($df)){
      return $this->error('database name has been taken');
    }
    if(!preg_match('/^[a-z0-9]+$/',$p['user'])){
      return $this->error('invalid database username');
    }$scope='*';
    if(isset($p['scope'])&&$p['scope']!='*'){
      $ex=explode(',',$p['scope']);
      $scope=[];
      foreach($ex as $v){
        $sv=strtolower(trim($v));
        if(!array_key_exists($sv,$this->statements())){
          $error='unknown scope "'.$v.'"';break;
        }$scope[]=$sv;
      }if($error){return $this->error($error);}
      $scope=implode(',',$scope);
    }
    $db=new SQLite3($df);
    $tn=$this->rootName($p['db']);
    $col=[
      'id INTEGER PRIMARY KEY AUTOINCREMENT',
      'username STRING(256) NOT NULL DEFAULT ""',
      'password STRING(256) NOT NULL DEFAULT ""',
      'type STRING(256) NOT NULL DEFAULT ""',
      'scope STRING(256) NOT NULL DEFAULT ""',
      'access STRING(256) NOT NULL DEFAULT ""',
    ];
    $q='create table '.$tn.' ('.implode(',',$col).')';
    if(!$db->exec($q)){
      $db->close();
      @unlink($df);
      return $this->error('failed to create database header');
    }
    $pass=password_hash(isset($p['pass'])?$p['pass']:'',PASSWORD_BCRYPT);
    $q='insert into '.$tn.' (username,password,type,scope,access) '
      .'values (:user,:pass,:type,:scope,:access)';
    $prep=$db->prepare($q);
    if(!$prep){
      $db->close();
      @unlink($df);
      return $this->error('failed to prepare query');
    }
    $prep->bindValue(':user',$p['user'],SDB_TEXT);
    $prep->bindValue(':pass',$pass,SDB_TEXT);
    $prep->bindValue(':type','root',SDB_TEXT);
    $prep->bindValue(':scope',$scope,SDB_TEXT);
    $prep->bindValue(':access','*',SDB_TEXT);
    if(!$prep->execute()||$prep->errno){
      return $this->error($prep->error);
    }
    $prep->close();
    return true;
  }
  private function databaseDrop($q=null){
    if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)$/i',$q,$a)){
      return $this->error('unrecognized query');
    }$dbname=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($dbname!==$this->database){
      return $this->error('have no authorized to this database');
    }$df=$this->directory.$dbname.'.sdb';
    if(!is_file($df)){
      return $this->error('database does not exist');
    }$this->db->close();
    if(!@unlink($df)){
      $this->db->open($df);
      $this->error('failed to drop database');
    }return $this->close();
  }
  private function databaseAlter($q=null){
    if(!is_string($q)){return $this->error('invalid query');}
    $pattern='/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)\s+'
      .'(rename_to|add_user|edit_user|delete_user)(.*)$/i';
    if(!preg_match($pattern,$q,$a)){return $this->error('unrecognized query');}
    $dbname=preg_replace('/[^a-z0-9_]/i','',$a[1]);
    if($dbname!==$this->database){
      return $this->error('have no authorized to this database');
    }$option=strtolower($a[2]);
    if($option=='rename_to'){
      if(!preg_match('/^([a-z0-9_]+|"[a-z0-9_]+"|\'[a-z0-9_]+\'|`[a-z0-9_]+`)$/i',trim($a[3]))){
        return $this->error('invalid new database name');
      }$new=preg_replace('/[^a-z0-9_]/i','',trim($a[3]));
      $df=$this->directory.$dbname.'.sdb';
      $nf=$this->directory.$new.'.sdb';
      if(is_file($nf)){
        return $this->error('database name has been taken');
      }$this->db->close();
      if(!@rename($df,$nf)){
        $this->db->open($df);
        return $this->error('failed to rename database');
      }$tn=$this->rootName($this->database);
      $nn=$this->rootName($new);
      $this->database=$new;
      $this->db->open($nf);
      $this->db->exec('ALTER TABLE '.$tn.' RENAME TO '.$nn);
      return true;
    }
    if(!mb_parse_str(trim($a[3]),$parse)||count($parse)==0){
      return $this->error('require column parameter');
    }
    if($option=='add_user'){
      return $this->userAdd($parse);
    }
    if($option=='edit_user'){
      return $this->userEdit($parse);
    }
    if($option=='delete_user'){
      return $this->userDelete($parse);
    }
    return $this->error('unknown alter database option "'.$option.'"');
  }
  private function userAdd($p=null){
    if(count($p)==0){
      return $this->error('require at least 2 parameters, user and pass');
    }$error=false;
    $def=['user','pass','scope','access'];
    foreach($p as $k=>$v){
      if(!in_array($k,$def)){$error='unknown parameter "'.$k.'"';break;}
    }if($error){return $this->error($error);}
    if(!isset($p['user'],$p['pass'])){
      return $this->error('require user and pass parameter');
    }
    if(!preg_match('/^[a-z0-9]+$/',$p['user'])){
      return $this->error('invalid database username');
    }$scope='*';
    if(isset($p['scope'])&&$p['scope']!='*'){
      $ex=explode(',',$p['scope']);
      $scope=[];
      foreach($ex as $v){
        $sv=strtolower(trim($v));
        if(!array_key_exists($sv,$this->statements())){
          $error='unknown scope "'.$v.'"';break;
        }$scope[]=$sv;
      }if($error){return $this->error($error);}
      $scope=implode(',',$scope);
    }$access='*';$tables=$this->showTables('');
    if(isset($p['access'])&&$p['access']!='*'){
      $ex=explode(',',$p['access']);
      $access=[];
      foreach($ex as $v){
        $sv=trim($v);
        if(!in_array($sv,$tables)){
          $error='unknown table "'.$sv.'"';break;
        }$access[]=$sv;
      }if($error){return $this->error($error);}
      $access=implode(',',$access);
    }
    $tn=$this->rootName($this->database);
    $db=$this->db;
    $s=@$db->querySingle('select * from '.$tn.' where username="'.$p['user'].'"',true);
    if($s&&isset($s['username'])){
      return $this->error('username has been taken');
    }
    $pass=password_hash(isset($p['pass'])?$p['pass']:'',PASSWORD_BCRYPT);
    $q='insert into '.$tn.' (username,password,type,scope,access) '
      .'values (:user,:pass,:type,:scope,:access)';
    $prep=$this->db->prepare($q);
    if(!$prep){return $this->error('failed to prepare query');}
    $prep->bindValue(':user',$p['user'],SDB_TEXT);
    $prep->bindValue(':pass',$pass,SDB_TEXT);
    $prep->bindValue(':type','user',SDB_TEXT);
    $prep->bindValue(':scope',$scope,SDB_TEXT);
    $prep->bindValue(':access',$access,SDB_TEXT);
    if(!$prep->execute()||$prep->errno){
      return $this->error($prep->error);
    }
    $prep->close();
    return true;
  }
  private function userEdit($p=null){
    if(count($p)==0){
      return $this->error('require at least 2 parameters, user is a key');
    }$error=false;
    $def=['user','pass','scope','access'];
    foreach($p as $k=>$v){
      if(!in_array($k,$def)){$error='unknown parameter "'.$k.'"';break;}
    }if($error){return $this->error($error);}
    if(!isset($p['user'])){
      return $this->error('require user parameter');
    }
    if(!preg_match('/^[a-z0-9]+$/',$p['user'])){
      return $this->error('invalid database username');
    }
    $tn=$this->rootName($this->database);
    $db=$this->db;
    $s=@$db->querySingle('select * from '.$tn.' where username="'.$p['user'].'"',true);
    if(!$s||!isset($s['password'],$s['type'],$s['scope'],$s['access'])){
      return $this->error('database user does not exist');
    }$scope=isset($p['scope'])&&$p['scope']=='*'?'*':$s['scope'];
    if(isset($p['scope'])&&$p['scope']!='*'){
      $ex=explode(',',$p['scope']);
      $scope=[];
      foreach($ex as $v){
        $sv=strtolower(trim($v));
        if(!array_key_exists($sv,$this->statements())){
          $error='unknown scope "'.$v.'"';break;
        }$scope[]=$sv;
      }if($error){return $this->error($error);}
      $scope=implode(',',$scope);
    }$access=isset($p['access'])&&$p['access']=='*'?'*':$s['access'];
    $tables=$this->showTables('');
    if(isset($p['access'])&&$p['access']!='*'){
      $ex=explode(',',$p['access']);
      $access=[];
      foreach($ex as $v){
        $sv=trim($v);
        if(!in_array($sv,$tables)){
          $error='unknown table "'.$sv.'"';break;
        }$access[]=$sv;
      }if($error){return $this->error($error);}
      $access=implode(',',$access);
    }
    $pass=password_hash(isset($p['pass'])?$p['pass']:'',PASSWORD_BCRYPT);
    $q='update '.$tn.' set password=:pass,scope=:scope,access=:access '
      .' where username="'.$s['username'].'"';
    $prep=$this->db->prepare($q);
    if(!$prep){return $this->error('failed to prepare query');}
    $prep->bindValue(':pass',$pass,SDB_TEXT);
    $prep->bindValue(':scope',$scope,SDB_TEXT);
    $prep->bindValue(':access',$access,SDB_TEXT);
    if(!$prep->execute()||$prep->errno){
      return $this->error($prep->error);
    }
    $prep->close();
    return true;
  }
  private function userDelete($p=null){
    if(count($p)==0){
      return $this->error('require user as a key');
    }$error=false;
    $def=['user'];
    foreach($p as $k=>$v){
      if(!in_array($k,$def)){$error='unknown parameter "'.$k.'"';break;}
    }if($error){return $this->error($error);}
    if(!isset($p['user'])){
      return $this->error('require user parameter');
    }
    if(!preg_match('/^[a-z0-9]+$/',$p['user'])){
      return $this->error('invalid database username');
    }
    $tn=$this->rootName($this->database);
    $db=$this->db;
    $s=@$db->querySingle('select * from '.$tn.' where username="'.$p['user'].'"',true);
    if(!$s||!isset($s['password'],$s['type'],$s['scope'],$s['access'])){
      return $this->error('database user does not exist');
    }
    if($s['type']=='root'){
      return $this->error('cannot delete this user');
    }
    $q='delete from '.$tn.' where username=:user';
    $prep=$this->db->prepare($q);
    if(!$prep){return $this->error('failed to prepare query');}
    $prep->bindValue(':user',$s['username'],SDB_TEXT);
    if(!$prep->execute()||$prep->errno){
      return $this->error($prep->error);
    }
    $prep->close();
    return true;
  }
  /* ----- private pre-load methods ----- */
  private function preload($user=null,$pass=null){
    if(!$this->root()){
      $this->db=null;
      return $this->error('root database is corrupted');
    }$this->db=null;
    $df=$this->directory.$this->database.'.sdb';
    if(!is_file($df)){
      return $this->error('database does not exist');
    }
    $this->db=new SQLite3($df);
    if(!$this->userVerify($user,$pass)){
      return $this->error('invalid username or password');
    }
    $ht=$this->directory.'.htaccess';
    if(!is_file($ht)){@file_put_contents($ht,'deny from all');}
    $this->connected=true;
    $this->serverInformation=$this->serverInfo();
    return true;
  }
  private function userVerify($u=null,$p=null){
    if(!is_string($u)||!is_string($p)||!preg_match('/^[a-z0-9]+$/',$u)){return false;}
    $db=$this->db;
    $tn=$this->rootName($this->database);
    $s=@$db->querySingle('select * from '.$tn.' where username="'.$u.'"',true);
    if(!$s||!isset($s['password'],$s['type'],$s['scope'],$s['access'])){return false;}
    if(!password_verify($p,$s['password'])){return false;}
    $this->user=fopen('php://temp','wb+');
    $write=fwrite($this->user,serialize($s));
    $scope=$s['scope']=='*'?array_keys($this->statements()):explode(',',$s['scope']);
    array_walk($scope,function(&$v){$v=strtoupper(trim($v));});
    if($s['type']!=='root'){
       $key=array_search('REAL',$scope);
       if($key!==false){unset($scope[$key]);}
    }
    $this->scope=$scope;
    $tables=$this->showTables('');
    $access=$s['access']=='*'?$tables:explode(',',$s['access']);
    array_walk($access,function(&$v){$v=trim($v);});
    foreach($access as $k=>$v){
      if(!in_array($v,$tables)){
        unset($access[$k]);
      }
    }$this->access=$access;
    $this->session='sdb-'.$this->token(uniqid());
    return true;
  }
  private function root(){
    $df=$this->directory.'root.sdb';
    $tn=$this->rootName('root');
    if(is_file($df)){
      $db=new SQLite3($df);
      $s=@$db->querySingle('select * from '.$tn.' where username="root"',true);
      if(!$s||!isset($s['password'],$s['type'],$s['scope'],$s['access'])){return false;}
      return true;
    }
    $db=new SQLite3($df);
    $col=[
      'id INTEGER PRIMARY KEY AUTOINCREMENT',
      'username STRING(256) NOT NULL DEFAULT ""',
      'password STRING(256) NOT NULL DEFAULT ""',
      'type STRING(256) NOT NULL DEFAULT ""',
      'scope STRING(256) NOT NULL DEFAULT ""',
      'access STRING(256) NOT NULL DEFAULT ""',
    ];
    $q='create table '.$tn.' ('.implode(',',$col).')';
    if(!@$db->exec($q)){return false;}
    $pass=password_hash('',PASSWORD_BCRYPT);
    $q='insert into '.$tn.' (username,password,type,scope) '
      .'values ("root","'.$pass.'","root","create database")';
    if(!@$db->exec($q)){return false;}
    $s=@$db->querySingle('select * from '.$tn.' where username="root"',true);
    if(!$s||!isset($s['password'],$s['type'],$s['scope'],$s['access'])){return false;}
    return true;
  }
  private function user($k=null){
    if(!is_resource($this->user)){return false;}
    rewind($this->user);
    $r=@fgets($this->user);
    $o=(object)@unserialize($r);
    return is_string($k)&&isset($o->$k)?$o->$k:$o;
  }
  private function rootName($n=null){
    $h=preg_replace('/[^a-z0-9]/i','',base64_encode(hash('sha512',$n,true)));
    return str_repeat('_',strlen($h)).$h;
  }
  private function statements(){
    return [
      'create database'=>'databaseCreate',
      'drop database'=>'databaseDrop',
      'alter database'=>'databaseAlter',
      'show databases'=>'showDatabases',
      'show tables'=>'showTables',
      'show columns'=>'showColumns',
      'create table'=>'tableCreate',
      'drop table'=>'tableDrop',
      'truncate table'=>'tableTruncate',
      'alter table'=>'tableAlter',
      'insert into'=>'insert',
      'delete from'=>'delete',
      'update'=>'update',
      'select'=>'select',
      'real'=>'real',
    ];
  }
  private function stream($u=null,$d=[],$c=null,$m=null){
    if(!is_string($u)){return false;}
    $c=is_string($c)?$c:'';
    $m=is_string($m)&&in_array($m,['GET','POST','OPTIONS','PUT'])?$m:'POST';
    $o=[
      'http'=>[
        'method'=>$m,
        'header'=>"content-type:application/x-www-form-urlencoded;charset=utf-8;\r\ncookie:".$c,
        'content'=>@http_build_query($d,'','&') // 4th parameter: PHP_QUERY_RFC1738 for PHP >= 5.4
      ],
      'ssl'=>[
        'verify_peer'=>false,
        'verify_peer_name'=>false,
        'capture_session_meta'=>true,
        'crypto_method'=>STREAM_CRYPTO_METHOD_TLS_CLIENT,
      ]
    ];
    return @file_get_contents($u,false,@stream_context_create($o));
  }
  private function token($n=null){
    return preg_replace('/[^a-z0-9]/i','',base64_encode(hash('sha1',$n,true)));
  }
  /* ----- protected methods ----- */
  protected function error($s=null){
    $s=is_string($s)?$s:'unknown error';
    $this->errors[]=$s;
    $this->error=$s;
    return false;
  }
  /* ----- private server methods ----- */
  private function serverUserSet($u=null,$p=null,$t=null){
    $o=fopen('php://temp','wb+');
    $w=fwrite($o,serialize([
      'username'=>$u,
      'password'=>$p,
      'timezone'=>$t,
    ]));
    $this->db=$o;
    rewind($this->db);
    return true;
  }
  private function serverUserGet(){
    if(!is_resource($this->db)){return false;}
    $r=@fgets($this->db);
    rewind($this->db);
    return (object)@unserialize($r);
  }
  private function serverAccess($q=null,$t='connect'){
    $ts=['connect','query','queries'];
    if(!is_string($t)||!in_array($t,$ts)){
      return $this->error('invalid connection type');
    }
    $user=$this->serverUserGet();
    if(!$user||!is_object($user)||!isset($user->username)){
      return $this->error('failed to get user resource');
    }
    $q=$t=='connect'?true:(string)$q;
    $data=$this->serverEncode([
      'db'=>$this->database,
      'username'=>$user->username,
      'password'=>$user->password,
      'timezone'=>$user->timezone,
      $t=>$q,
    ]);
    $push=$this->stream($this->host,['sdb'=>$data]);
    if(!$push){return $this->error('failed to connect into sdb database server');}
    $res=$this->serverDecode($push);
    if(!$res){return $this->error('failed to parse result');}
    if(!isset($res['status'])){return $this->error('invalid result');}
    if($res['status']=='error'){return $this->error($res['message']);}
    $this->session=$res['session'];
    $this->scope=$res['scope'];
    $this->access=$res['access'];
    $this->errors=$res['errors'];
    if($t=='connect'){
      $this->connected=true;
    }
    $this->serverInformation=$res['info'];
    return $res['result'];
  }
  private function serverLoad(){
    $res=array('status'=>'error','message'=>'invalid request');
    $ax=isset($_GET['client'])&&$_GET['client']=='ajax'?true:false;
    if($this->error){
      $res['message']=$this->error;
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    if(!$this->connected){
      $res['message']='require local database connection';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    if($this->host!=='localhost'){
      $res['message']='invalid database host connection';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    $get=$this->serverDecode($_POST['sdb'],$ax);
    if(!$get){
      $res['message']='failed to decode request';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    if(!isset($get['db'])){
      $res['message']='require database name';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    if(!isset($get['username'],$get['password'])){
      $res['message']='require username and password';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    if(isset($get['timezone'])){
      if(!@date_default_timezone_set($get['timezone'])){
        $res['message']='invalid timezone "'.$get['timezone'].'"';
        return $this->serverResult($this->serverEncode($res,$ax));
      }
    }
    /* start connect into database */
    $sdb=new sdb('localhost',$get['username'],$get['password'],$get['db'],$get['timezone']);
    if($sdb->error){
      $res['message']=$sdb->error;
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    $mess='connected';
    if(isset($get['query'])){
      $exec=$sdb->query($get['query']);
      $mess='query is executed';
    }elseif(isset($get['queries'])){
      $exec=$sdb->queries($get['queries']);
      $mess='queries are executed';
    }elseif(isset($get['connect'])){
      $exec=$sdb->connected();
    }else{
      $res['message']='unknown request';
      return $this->serverResult($this->serverEncode($res,$ax));
    }
    /* setup result */
    $res['status']=$sdb->error?'error':'OK';
    $res['message']=$sdb->error?$sdb->error:$mess;
    $res['result']=$exec;
    $res['errors']=$sdb->errors();
    $res['error']=$sdb->error;
    $res['info']=$this->serverInfo();
    $res['session']=$sdb->session();
    $res['scope']=$sdb->scope();
    $res['access']=$sdb->access();
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
      'sdb::version'=>$this::version,
      'SQLite3::version'=>SQLite3::version(),
      'php::version'=>PHP_VERSION,
      'request_length'=>strlen(isset($_POST['sdb'])?$_POST['sdb']:''),
      'memory_usage'=>number_format(memory_get_usage()/1024,2,'.',''),
      'memory_peak_usage'=>number_format(memory_get_peak_usage()/1024,2,'.',''),
      'process_time'=>number_format(microtime(true)-$_SERVER['REQUEST_TIME_FLOAT'],3,'.',''),
      'remote_addr'=>isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'0.0.0.0',
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


