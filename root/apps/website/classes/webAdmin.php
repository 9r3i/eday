<?php
/* webAdmin
 * ~ website admin page
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 12th 2019
 * @require:
 *   - dataObject
 */
#[AllowDynamicProperties]
class webAdmin{
  const version='1.0.0';
  protected $path;
  protected $config;
  protected $db;
  protected $site;
  protected $plugin;
  protected $theme;
  protected $webDir;
  protected $webPath;
  protected $trashDir;
  protected $methods;
  protected $ajaxError=false;
  protected $cookie;
  protected $user;
  protected $userLevels;
  protected $pages;
  public function __construct(website $website){
    /* preapre website object properties */
    $data=$website->property();
    $this->config=$data->config;
    $this->db=$data->db;
    $this->site=$data->site;
    $this->plugin=$data->plugin;
    $this->theme=$data->theme;
    $this->webDir="{$data->dir}themes/{$data->theme}/";
    $this->webPath="{$data->path}themes/{$data->theme}/";
    $this->trashDir="{$data->dir}trash/";
    $this->appDir=$data->dir;
    $this->appPath=$data->path;
    /* prepare admin path */
    $this->path=isset($_GET[$this->theme])
      ?$_GET[$this->theme]:'';
    /* set cookie */
    $this->cookie=new dataObject($_COOKIE);
    /* prepare user is login or not */
    $this->user=$this->isLogin();
    /* prepare user level */
    $this->userLevels=[
      'visitor'=>1,
      'member'=>2,
      'author'=>4,
      'editor'=>8,
      'admin'=>16,
      'master'=>32,
    ];
    /* define user level */
    $userLevel=$this->user&&isset($this->user->privilege)
      &&isset($this->userLevels[$this->user->privilege])
      ?$this->userLevels[$this->user->privilege]:1;
    defined('USER_LEVEL') or define('USER_LEVEL',$userLevel);
    /* add user level */
    if($this->user){
      $this->user->add('level',USER_LEVEL);
    }
    /* define all user levels */
    foreach($this->userLevels as $k=>$level){
      $key=strtoupper('user_level_'.$k);
      defined($key) or define($key,$level);
    }
    /* register ajax methods and minimum user level */
    $this->methods=[
      'login'=>1,
      'logout'=>2,        // --> token must be equal to $this->user->token
      'saveAccount'=>2,   // --> userID must be equal to $this->user->id
      'upload'=>4,        // --> post author must be equal to $this->user->username
      'savePost'=>4,      // --> post author must be equal to $this->user->username
      'trashPost'=>4,     // --> post author must be equal to $this->user->username
      'deletePost'=>4,    // --> post author must be equal to $this->user->username
      'saveMenu'=>8,
      'addMenu'=>8,
      'deleteMenu'=>8,
      'saveSettings'=>16,
      'saveUser'=>16,
      'addUser'=>16,
      'deleteUser'=>16,
      'deletePlugin'=>16,
      'pluginFiles'=>16,
      'pluginLoadFile'=>16,
      'pluginSaveFile'=>16,
    ];
    /* page and minimum level */
    $this->pages=[
      'dashboard'=>2,
      'account'=>2,      // --> userID must be equal to $this->user->id
      'posts'=>4,        // --> post author must be equal to $this->user->username
      'edit'=>4,         // --> post author must be equal to $this->user->username
      'new'=>4,
      'menus'=>8,
      'menu'=>8,
      'menuNew'=>8,
      'pluginEdit'=>16,
      'plugins'=>16,
      'settings'=>16,
      'user'=>16,
      'userNew'=>16,
      'users'=>16,
    ];
    /* prepare menu */
    $this->site->menu=$this->menu();
    /* set admin site */
    $this->site->title='webAdmin &#8213; e-Day';
    $this->site->name='webAdmin &#8213; e-Day';
    $this->site->description='webAdmin &#8213; e-Day';
    $this->site->keywords='web, admin, e-Day';
    $this->site->robots='no index, no follow';
    $this->site->webPath=$this->webPath;
    $this->site->adminKey=$this->config->core->admin;
    /* ----- testing script ----- */
    $this->testingScript();
    /* start admin page */
    return $this->start($website);
  }
  /* ----- testing script ----- */
  protected function testingScript(){
    //$this->updateTablePosts();
    /* ----- testing script ----- *
    header('content-type:text/plain');
    $sel=$this->db->select('menu');
    print_r($sel);
    exit;
    // logs
    // menu,options,posts,users
    // request,sidebar,visitor,language_option,category
    // hit_counter,web_contact,custom_menu,daftar_training
    //*/
  }
  /* deletePlugin */
  protected function deletePlugin(dataObject $post){
    /* check plugin namespace */
    if(!isset($post->namespace)){
      return $this->error('Require plugin namespace.');
    }
    /* check plugin directory */
    $dir=$this->appDir.'plugins/'.$post->namespace;
    if(!is_dir($dir)){
      return $this->error('Invalid plugin namespace.'.$dir);
    }$dir.=substr($dir,-1)!='/'?'/':'';
    /* move to trash */
    $rename=@rename($dir,$this->trashDir.'plugins/'.$post->namespace);
    if(!$rename){
      return $this->error('Failed to delete the plugin.');
    }return 'OK';
  }
  /* pluginSaveFile */
  protected function pluginSaveFile(dataObject $post){
    /* check plugin namespace */
    if(!isset($post->namespace)){
      return $this->error('Require plugin namespace.');
    }
    /* check plugin directory */
    $dir=$this->appDir.'plugins/'.$post->namespace;
    if(!is_dir($dir)){
      return $this->error('Invalid plugin namespace.'.$dir);
    }$dir.=substr($dir,-1)!='/'?'/':'';
    /* check plugin filename */
    if(!isset($post->filename)){
      return $this->error('Require plugin filename.');
    }
    /* check plugin the file */
    if(!is_file($dir.$post->filename)
      ||!is_writable($dir.$post->filename)){
      return $this->error('File is not writable.');
    }
    /* check plugin content */
    if(!isset($post->content)){
      return $this->error('Require file content.');
    }
    /* save content */
    $put=@file_put_contents($dir.$post->filename,$post->content);
    if(!$put){
       return $this->error('Failed to save the file.');
    }return 'OK';
  }
  /* pluginLoadFile */
  protected function pluginLoadFile(dataObject $post){
    /* check plugin namespace */
    if(!isset($post->namespace)){
      return $this->error('Require plugin namespace.');
    }
    /* check plugin directory */
    $dir=$this->appDir.'plugins/'.$post->namespace;
    if(!is_dir($dir)){
      return $this->error('Invalid plugin namespace.'.$dir);
    }$dir.=substr($dir,-1)!='/'?'/':'';
    /* check plugin filename */
    if(!isset($post->filename)){
      return $this->error('Require plugin filename.');
    }
    /* check plugin the file */
    if(!is_file($dir.$post->filename)
      ||!is_readable($dir.$post->filename)){
      return $this->error('File is not readable.');
    }
    /* get content */
    $content=@file_get_contents($dir.$post->filename);
    return [
      'namespace'=>$post->namespace,
      'filename'=>$post->filename,
      'content'=>$content,
    ];
  }
  /* pluginFiles */
  protected function pluginFiles(dataObject $post){
    /* check plugin namespace */
    if(!isset($post->namespace)){
      return $this->error('Require plugin namespace.');
    }
    /* check plugin directory */
    $dir=$this->appDir.'plugins/'.$post->namespace;
    if(!is_dir($dir)){
      return $this->error('Invalid plugin namespace.'.$dir);
    }
    $dir.=substr($dir,-1)!='/'?'/':'';
    $scan=$this->explore($dir);
    $files=[];
    foreach($scan as $file){
      $files[]=substr($file,strlen($dir));
    }
    $r=[
      'namespace'=>$post->namespace,
      'files'=>$files,
    ];
    return $r;
  }
  /* addMenu */
  protected function addMenu(dataObject $post){
    /* legal privilege */
    $legPriv=['member','author','editor','admin'];
    /* prepare names */
    $setNames=['type','slug','name','order'];
    $setData=[];
    $error=false;
    /* check arguments */
    foreach($setNames as $name){
      /* check field */
      if(!isset($post->{$name})){
        $error='Require config "'.$name.'" in the field.';
        break;
      }
      /* check emptiness */
      if(empty(trim($post->{$name}))){
        $error='Require new "'.$name.'".';
        break;
      }
      /* set to data */
      $setData[$name]=$post->{$name};
    }
    /* check error */
    if($error){return $this->error($error);}
    /* insert data into table */
    $insert=$this->db->insert('menu',$setData);
    if(!$insert){
      return $this->error('Failed to add menu.');
    }
    /* return as OK */
    return 'OK';
  }
  /* saveMenu*/
  protected function saveMenu(dataObject $post){
    /* legal privilege */
    $legPriv=['member','author','editor','admin'];
    /* prepare names */
    $setNames=['id','type','order','slug','name'];
    $setData=[];
    $error=false;
    /* check arguments */
    foreach($setNames as $name){
      /* check field */
      if(!isset($post->{$name})){
        $error='Require config "'.$name.'" in the field.';
        break;
      }
      /* check emptiness */
      if(empty(trim($post->{$name}))&&$name!='password'){
        $error='Require "'.$name.'".';
        break;
      }
      /* set to data */
      $setData[$name]=$post->{$name};
    }
    /* check error */
    if($error){return $this->error($error);}
    /* prepare id */
    $menuID=$post->id;
    unset($setData['id']);
    /* check data */
    $menus=$this->db->select('menu','aid='.$menuID);
    if(!isset($menus[0])){
      return $this->error('Invalid menu ID.');
    }$menu=new dataObject($menus[0]);
    /* update data */
    $update=$this->db->update('menu','aid='.$menuID,$setData);
    if(!$update){
      return $this->error('Failed to update menu.');
    }
    /* return as OK */
    return 'OK';
  }
  /* deleteMenu */
  protected function deleteMenu(dataObject $post){
    /* check menu id */
    if(!isset($post->menuid)){
      return $this->error('Require menu ID.');
    }
    /* check menu */
    $select=$this->db->select('menu','aid='.$post->menuid);
    if(!isset($select[0])){
      return $this->error('Menu does not exist.');
    }$menu=new dataObject($select[0]);
    /* starting delete */
    $delete=$this->db->delete('menu','aid='.$post->menuid);
    if(!$delete){
      return $this->error('Failed to delete menu.');
    }return 'OK';
  }
  /* update table */
  protected function updateTablePosts(){
    header('content-type:text/plain');
    $select=$this->db->select('posts');
    array_walk($select,function(&$v){
      if($v['training_date']){
        $exp=explode('-',$v['training_date']);
        $v['start']=date('m/d/Y',strtotime($exp[0]));
        $v['end']=isset($exp[1])?date('m/d/Y',strtotime($exp[1])):$start;
      }
      unset($v['training_date']);
      unset($v['expires']);
      unset($v['note']);
      unset($v['barcode']);
      unset($v['account']);
      unset($v['aid']);
      unset($v['cid']);
      unset($v['time']);
    });
    $this->db->create_table('posts2');
    $count=0;
    foreach($select as $postData){
      $count++;
      $postData['datetime']=date('Y-m-d H:i:s',strtotime($postData['datetime']));
      $insert=$this->db->insert('posts2',$postData);
      $status=$insert?'1':'0';
      echo "  --> {$count} --> {$status} --> {$postData['title']} \r\n";
    }exit;
  }
  /* starting page */
  protected function start(website $website){
    /* prepare table log */
    $table='logs';
    /* get tables */
    $tables=$this->db->show_tables();
    /* check table */
    if(!in_array($table,$tables)){
      /* create a new one */
      $this->db->create_table($table);
    }
    /* check ajax request */
    if(preg_match('/^ajax\/([a-zA-Z0-9_]+)$/',$this->path,$akur)){
      $method=$akur[1];
      if(!array_key_exists($method,$this->methods)
        ||USER_LEVEL<$this->methods[$method]){
        return $this->errorResult('Unauthorized.');
      }return $this->ajax($method,$_POST);
    }
    /* check page request */
    if(preg_match('/^page\/(.*)$/',$this->path,$akur)){
      $epath=explode('/',$akur[1]);
      if(!array_key_exists($epath[0],$this->pages)
        ||USER_LEVEL<$this->pages[$epath[0]]){
        return $this->errorResult('Unauthorized.');
      }return $this->page($akur[1]);
    }
    /* not login user */
    if(!$this->user){
      return $website->loadPage('login');
    }
    /* request plugin page shut */
    if(preg_match('/^plugin\/([a-zA-Z_][a-zA-Z0-9_]+)(\/.*)?/',$this->path,$akur)){
      $arg=isset($akur[2])?$akur[2]:null;
      return $this->plugin->adminPage($akur[1],$arg);
    }
    /* load plugin admin head */
    $top=$this->plugin->load('adminMenu',[]);
    $this->site->menu->top=array_merge($this->site->menu->top,$this->generateMenu($top,true));
    /* load main page as logged in user */
    return $website->loadPage('main');
  }
  /* menu */
  protected function menu(){
    /* prepare menu */
    $menu=new dataObject([
      'top'=>[],
      'sidebar'=>[],
    ]);
    /* prepare menu top */
    $top=[
      /* path, name, icon, level */
      ['dashboard','Dashboard','dashboard',2],
      ['posts/posts','All Posts','file',4],
      ['menus/menu','Menus','list-alt',8],
      ['plugins','Plugins','puzzle-piece',16],
      ['account','My Account','lock',2],
      ['users/users','Users','user',16],
      ['settings','Settings','gear',16],
      ['logout','Logout','power-off',2],
    ];
    $menu->top=$this->generateMenu($top);
    /* prepare menu sidebar */
    $sidebar=[];
    $menu->sidebar=$this->generateMenu($sidebar);
    /* return as menu data object */
    return $menu;
  }
  /* menu generator */
  protected function generateMenu(array $menu,bool $external=false){
    $res=[];$icons=$this->faList();
    foreach($menu as $value){
      if(!isset($value[0],$value[1],$value[2],$value[3])
        ||USER_LEVEL<$value[3]){continue;}
      $res[]=new dataObject([
        'name'=>$value[1],
        'slug'=>'?admin='.$value[0],
        'icon'=>in_array($value[2],$icons)?$value[2]:'plug',
        'external'=>$external,
      ]);
    }return $res;
  }
  /* page */
  protected function page(string $path=''){
    /* prepare html file */
    $epath=explode('/',$path);
    $file=$this->webDir.'html/'.$epath[0].'.html';
    if(!is_file($file)||!is_readable($file)){
      return $this->errorResult('Page is not found.');
    }$content=@file_get_contents($file);
    /* prepare script file */
    $jsFile=$this->webDir.'html/js/'.$epath[0].'.js';
    if(is_file($jsFile)&&is_readable($jsFile)){
      $script=@file_get_contents($jsFile);
      /* append to content */
      $content.='<script type="text/javascript">'
        .$script.'</script>';
    }
    /* prepare page */
    $page=[
      'url'=>'?admin='.$path,
      'title'=>ucwords($epath[0]),
      'content'=>$content,
    ];
    /* prepare page data table access */
    if(isset($epath[1])&&preg_match('/^[a-z]+$/i',$epath[1])){
      $fpath=$epath[0];
      $table=$epath[1];
      $aid=isset($epath[2])
        &&preg_match('/^\d+$/',$epath[2])
        ?$epath[2]:null;
      if($fpath){
        if($aid){
          $where=$table=='posts'&&USER_LEVEL<USER_LEVEL_EDITOR
            ?'&author='.$this->user->username:'';
          $sel=$this->db->select($table,'aid='.$aid.$where);
          if(isset($sel[0])){
            unset($sel[0]['password']);
            if($table=='users'&&USER_LEVEL<USER_LEVEL_MASTER
              &&$sel[0]['privilege']=='master'){
              
            }else{
              $page['data']=$sel[0];
            }
          }
        }else{
          $where=$table=='posts'&&USER_LEVEL<USER_LEVEL_EDITOR
            ?'author='.$this->user->username:false;
          $sel=$this->db->select($table,$where);
          if(is_array($sel)){
            array_walk($sel,function(&$v){
              unset($v['password']);
              unset($v['content']);
            });
            if($table=='users'&&USER_LEVEL<USER_LEVEL_MASTER){
              $sel=array_filter($sel,function($v){
                return $v['privilege']!='master';
              });$sel=array_values($sel);
            }$page['data']=$sel;
          }
        }
      }
    }
    /* prepare page data settings */
    elseif($epath[0]=='settings'){
      $iniFile=$this->appDir.'config.ini';
      $ini=is_file($iniFile)&&is_readable($iniFile)
        ?@parse_ini_file($iniFile,true):[];
      $ini=is_array($ini)?$ini:[];
      $page['data']=$ini;
    }
    /* prepare page data account */
    elseif($epath[0]=='account'){
      $sel=$this->db->select('users','aid='.$this->user->id);
      if(isset($sel[0])){
        unset($sel[0]['password']);
        $page['data']=$sel[0];
      }
    }
    /* prepare for dashboard */
    elseif($epath[0]=='dashboard'){
      $data=[];
      $wherePost=$this->user->level<USER_LEVEL_EDITOR
        ?'author='.$this->user->username:false;
      $sel=$this->db->select('posts',$wherePost);
      $data['posts']=is_array($sel)?$sel:[];
      array_walk($data['posts'],function(&$v){
        unset($v['content']);
      });
      $page['data']=$data;
    }
    /* prepare for plugins */
    elseif($epath[0]=='plugins'){
      $page['data']=$this->plugin->listed();
    }
    /* return as json string */
    return $this->result(@json_encode($page));
  }
  /* is login */
  protected function isLogin(){
    /* check cookie */
    if(!isset($this->cookie->webAdmin)){
      return false;
    }
    /* select table */
    $sel=$this->db->select('logs','token='.$this->cookie->webAdmin);
    /* check selection */
    if(!isset($sel[0])){return false;}
    /* return as true */
    return new dataObject($sel[0]);
  }
  /* upload */
  protected function upload(dataObject $post){
    /* check file */
    if(!isset($_FILES['file'])){
      return $this->error('Require file.');
    }$file=$_FILES['file'];
    /* check file error */
    if($file['error']>0){
      return $this->error('File is error ('.$file['error'].').');
    }
    /* move uploaded file */
    $dir='files/upload/';
    $dest=$dir.$file['name'];
    if(!is_dir($dir)){
      @mkdir($dir,0755,true);
    }
    if(!@move_uploaded_file($file['tmp_name'],$dest)){
      return $this->error('Failed to move uploaded file.');
    }
    /* return destination file path */
    return @json_encode([
      'status'=>'OK',
      'path'=>$dest,
    ]);
  }
  /* savePost */
  protected function savePost(dataObject $post){
    /* prepare save type */
    $pid=isset($post->aid)?$post->aid:false;
    /* prepare data */
    $odata=$post->toArray();
    unset($odata['aid']);
    /* update type */
    if($pid){
      /* prepare wherance */
      $where='aid='.$pid;
      $where.=USER_LEVEL<USER_LEVEL_EDITOR
        ?'&author='.$this->user->username:'';
      $update=$this->db->update('posts',$where,$odata);
      if(!$update){
        return $this->error('Failed to save an update post.');
      }return 'OK';
    }
    /* prepare data */
    $def=explode(',','title,content,type,status,access,template,picture,'
      .'description,keywords,trainer,host,start,end,place,price,stock');
    $data=[];
    foreach($def as $key){
      $data[$key]=isset($odata[$key])?$odata[$key]:'';
    }
    /* prepare another value */
    $data['datetime']=date('Y-m-d H:i:s');
    $data['author']=$this->user->username;
    $rawURL=preg_replace('/[^a-z0-9]+/',' ',strtolower(trim($data['title'])));
    $data['url']=preg_replace('/[^a-z0-9]+/','-',$rawURL);
    /* insert data into table */
    $insert=$this->db->insert('posts',$data);
    if(!$insert){
      return $this->error('Failed to save a new post.');
    }return 'OK';
  }
  /* deletePost */
  protected function deletePost(dataObject $post){
    if(!isset($post->id)){return $this->error('Require post ID.');}
    $where=USER_LEVEL<USER_LEVEL_EDITOR
      ?'&author='.$this->user->username:'';
    $delete=$this->db->delete('posts','aid='.$post->id.$where);
    if(!$delete){
      return $this->error('Failed to delete post.');
    }return 'OK';
  }
  /* trashPost */
  protected function trashPost(dataObject $post){
    if(!isset($post->id)){return $this->error('Require post ID.');}
    $where=USER_LEVEL<USER_LEVEL_EDITOR
      ?'&author='.$this->user->username:'';
    $update=$this->db->update('posts','aid='.$post->id.$where,[
      'status'=>'trash',
    ]);
    if(!$update){
      return $this->error('Failed to trash post.');
    }return 'OK';
  }
  /* saveAccount */
  protected function saveAccount(dataObject $post){
    /* prepare names */
    $setNames=['name','email','password','id'];
    $setData=[];
    $error=false;
    /* check arguments */
    foreach($setNames as $name){
      if(!isset($post->{$name})){
        $error='Require config "'.$name.'" in the field.';
        break;
      }$setData[$name]=$post->{$name};
    }
    /* check error */
    if($error){return $this->error($error);}
    /* check privilege */
    if($post->id!=$this->user->id){
      return $this->error('Unauthorized user.');
    }
    /* check password data */
    if(empty($post->password)){
      unset($setData['password']);
    }
    /* prepare id */
    $userID=$post->id;
    unset($setData['id']);
    /* update data */
    $update=$this->db->update('users','aid='.$userID,$setData);
    if(!$update){
      return $this->error('Failed to update user.');
    }
    /* return as OK */
    return 'OK';
  }
  /* deleteUser */
  protected function deleteUser(dataObject $post){
    /* check user id */
    if(!isset($post->uid)){
      return $this->error('Require user ID.');
    }
    /* check user */
    $select=$this->db->select('users','aid='.$post->uid);
    if(!isset($select[0])){
      return $this->error('User does not exist.');
    }$user=new dataObject($select[0]);
    /* check privilege and own id */
    if($user->privilege=='master'){
      return $this->error('Cannot delete this user.');
    }
    if($user->aid==$this->user->id){
      return $this->error('Cannot delete yourself.');
    }
    /* starting delete */
    $delete=$this->db->delete('users','aid='.$post->uid);
    if(!$delete){
      return $this->error('Failed to delete user.');
    }return 'OK';
  }
  /* addUser */
  protected function addUser(dataObject $post){
    /* legal privilege */
    $legPriv=['member','author','editor','admin'];
    /* prepare names */
    $setNames=['username','privilege','password','name','email'];
    $setData=[];
    $error=false;
    /* check arguments */
    foreach($setNames as $name){
      /* check field */
      if(!isset($post->{$name})){
        $error='Require config "'.$name.'" in the field.';
        break;
      }
      /* check emptiness */
      if(empty(trim($post->{$name}))){
        $error='Require new "'.$name.'".';
        break;
      }
      /* set to data */
      $setData[$name]=$post->{$name};
    }
    /* check error */
    if($error){return $this->error($error);}
    /* check privilege */
    if(!in_array($post->privilege,$legPriv)){
      if(USER_LEVEL!==USER_LEVEL_MASTER){
        return $this->error('Invalid privilege.');
      }
    }
    /* check username */
    if(!preg_match('/^[a-z0-9]+$/',$post->username)){
      return $this->error('Data "username" must be lower-case of alpha-numeric.');
    }
    /* check username is being taken */
    $nusers=$this->db->select('users','username='.$post->username);
    if(isset($nusers[0])){
      return $this->error('Data "username" has been taken.');
    }
    /* check email */
    if(!$this->isValidEmail($post->email)){
      return $this->error('Require a valid email.');
    }
    /* insert data into table */
    $insert=$this->db->insert('users',$setData);
    if(!$insert){
      return $this->error('Failed to add user.');
    }
    /* return as OK */
    return 'OK';
  }
  /* saveUser */
  protected function saveUser(dataObject $post){
    /* legal privilege */
    $legPriv=['member','author','editor','admin'];
    /* prepare names */
    $setNames=['id','username','privilege','password','name','email'];
    $setData=[];
    $error=false;
    /* check arguments */
    foreach($setNames as $name){
      /* check field */
      if(!isset($post->{$name})){
        $error='Require config "'.$name.'" in the field.';
        break;
      }
      /* check emptiness */
      if(empty(trim($post->{$name}))&&$name!='password'){
        $error='Require "'.$name.'".';
        break;
      }
      /* set to data */
      $setData[$name]=$post->{$name};
    }
    /* check error */
    if($error){return $this->error($error);}
    /* check privilege */
    if(!in_array($post->privilege,$legPriv)){
      if(USER_LEVEL!==USER_LEVEL_MASTER){
        return $this->error('Invalid privilege.');
      }
    }
    /* check username */
    if(!preg_match('/^[a-z0-9]+$/',$post->username)){
      return $this->error('Username must be lower-case of alpha-numeric.');
    }
    /* check email */
    if(!$this->isValidEmail($post->email)){
      return $this->error('Require a valid email.');
    }
    /* check password data */
    if(empty($post->password)){
      unset($setData['password']);
    }
    /* prepare id */
    $userID=$post->id;
    unset($setData['id']);
    /* check data */
    $users=$this->db->select('users','aid='.$userID);
    if(!isset($users[0])){
      return $this->error('Invalid user ID.');
    }$user=new dataObject($users[0]);
    /* check username */
    if($user->username!=$post->username){
      $nusers=$this->db->select('users','username='.$post->username);
      if(isset($nusers[0])&&$nusers[0]['aid']!=$user->aid){
        return $this->error('Data "username" has been taken.');
      }
    }
    /* check master privilege */
    if($user->privilege=='master'
      &&$this->user->privilege!='master'){
      return $this->error('Require master user to update this user.');
    }
    /* update data */
    $update=$this->db->update('users','aid='.$userID,$setData);
    if(!$update){
      return $this->error('Failed to update user.');
    }
    /* return as OK */
    return 'OK';
  }
  /* saveSettings */
  protected function saveSettings(dataObject $post){
    /* prepare section and names */
    $cNames=[
      'core'=>[
        'allowDatabaseAPI','mainPage','mainPageID',
        'admin','theme','feedLimit',
      ],
      'database'=>['driver','dbhost','dbname','dbuser','dbpass'],
      'website'=>['name','title','description','keywords','robots'],
      'load'=>['page01','page02','page03'],
    ];
    $cData=[];
    $error=false;
    /* prepare config file */
    $iniFile=$this->appDir.'config.ini';
    if(!is_file($iniFile)||!is_readable($iniFile)){
      return $this->error('Failed to prepare configuration file.');
    }$ini=@parse_ini_file($iniFile,true);
    /* check arguments */
    foreach($cNames as $section=>$names){
      if(!isset($ini[$section])){
        $error='No section "'.$section.'" in config file.';
        break;
      }
      if(!isset($post->{$section})){
        $error='Require section "'.$section.'" in the settings.';
        break;
      }
      foreach($names as $name){
        if(!isset($ini[$section][$name])){
          $error='No config "'.$name.'" in section "'.$section.'".';
          break 2;
        }
        if(!isset($post->{$section}->{$name})){
          $error='Require config "'.$name.'" in section "'.$section.'".';
          break 2;
        }
        $cData[$section][$name]=$post->{$section}->{$name};
      }
    }
    /* check error */
    if($error){return $this->error($error);}
    /* prepare output */
    $out=['; generated by e-Day at '.date('Y-m-d H:i:s'),''];
    foreach($ini as $section=>$data){
      $out[]='['.$section.']';
      foreach($data as $key=>$value){
        if(preg_match('/^\d+$/',$value)){
          $out[]="{$key}={$value}";
        }else{
          $out[]="{$key}=\"{$value}\"";
        }
      }$out[]='';
    }$out[]='';
    /* backup copy */
    $tfile=$iniFile.'.'.time().'.tmp';
    @copy($iniFile,$tfile);
    /* save new configuration */
    if(!@file_put_contents($iniFile,implode("\r\n",$out))){
      @copy($tfile,$iniFile);
      return $this->error('Failed to save settings.');
    }@unlink($tfile);
    /* return as OK */
    return 'OK';
  }
  /* logout */
  protected function logout(dataObject $post){
    /* check token */
    if(!isset($post->token)){
      return $this->error('Require access token.');
    }
    /* check user token */
    if($post->token!=$this->user->token){
      return $this->error('Invalid access token.');
    }
    /* delete from table */
    $delete=$this->db->delete('logs','token='.$post->token);
    if(!$delete){
      return $this->error('Failed to logout.');
    }return 'OK';
  }
  /* login */
  protected function login(dataObject $post){
    /* check post requirement */
    if(!isset($post->username,$post->password)){
      return $this->error('Require username and password');
    }
    /* select database table */
    $sel=$this->db->select('users','username='.$post->username);
    if(!isset($sel[0])){
      return $this->error('Invalid username or password.');
    }
    /* set user as object */
    $user=new dataObject($sel[0]);
    /* check password */
    if($this->db->hash($post->password)!==$user->password){
      return $this->error('Invalid username or password.');
    }
    /* generate new token */
    $token='webAdmin-'.preg_replace('/[^a-z0-9]+/i','',base64_encode(md5(mt_rand(),true)));
    /* prepare output data */
    $data=[
      'token'=>$token,
      'username'=>$user->username,
      'name'=>$user->name,
      'id'=>$user->aid,
      'email'=>$user->email,
      'privilege'=>$user->privilege,
      'level'=>$this->userLevels[$user->privilege],
    ];
    /* prepare table and insert */
    $table='logs';
    $this->db->error=false;
    $ins=$this->db->insert($table,$data);
    /* return failed if error */
    if($this->db->error){
      return $this->error('Failed to save data login.');
    }
    /* return as json */
    return @json_encode($data);
  }
  /* ajax */
  protected function ajax(string $method,array $data=[]){
    $out=@\call_user_func_array([$this,$method],[new dataObject($data)]);
    if(!$out){
      return $this->errorResult($this->ajaxError);
    }return $this->result($out);
  }
  /* error set to ajaxError - for ajax */
  protected function error($s=null){
    $this->ajaxError=$s;
    return false;
  }
  /* result - for ajax */
  protected function result($s=null){
    $s=is_string($s)?$s:@json_encode($s);
    header('Content-Type: text/plain');
    header('Content-Length: '.strlen($s));
    exit($s);
  }
  /* error result - for ajax */
  protected function errorResult($s=null){
    $s=is_string($s)?$s:'Unknown.';
    return $this->result('Error: '.$s);
  }
  /* explore directory */
  protected function explore(string $d,bool $id=false){
    if(!is_dir($d)){return [];}
    $d.=substr($d,-1)!='/'?'/':'';
    $s=@scandir($d);
    $s=is_array($s)?$s:[];
    $s=@array_diff($s,['.','..']);
    $r=[];
    foreach($s as $f){
      if(is_link($d.$f)){continue;}
      elseif(is_dir($d.$f)){
        $n=$this->explore($d.$f);
        $r=array_merge($r,$n);
        if($id){$r[]=$d.$f;}
      }elseif(is_file($d.$f)){
        $r[]=$d.$f;
      }
    }return $r;
  }
  /* check valid email */
  protected function isValidEmail($email){
    $pattern='/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|'
      .'(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|'
      .'(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
    return preg_match($pattern,$email)?true:false;
  }
  /* font-awesome list */
  protected function faList(){
    return ["medium","subway","train","viacoin","bed","hotel","user-times","user-plus",
      "server","whatsapp","pinterest-p","facebook-official","neuter","mars-stroke-h",
      "mars-stroke-v","mars-stroke","venus-mars","mars-double","venus-double",
      "transgender-alt","transgender","mercury","mars","venus","heartbeat",
      "street-view","motorcycle","user-secret","ship","diamond","cart-arrow-down",
      "cart-plus","skyatlas","simplybuilt","shirtsinbulk","sellsy","leanpub","forumbee",
      "dashcube","connectdevelop","buysellads","meanpath","ils","sheqel","shekel","cc",
      "angellist","ioxhost","bus","bicycle","toggle-on","toggle-off","lastfm-square",
      "lastfm","line-chart","pie-chart","area-chart","birthday-cake","paint-brush",
      "eyedropper","at","copyright","trash","bell-slash-o","bell-slash","cc-stripe",
      "cc-paypal","cc-amex","cc-discover","cc-mastercard","cc-visa","google-wallet",
      "paypal","calculator","wifi","newspaper-o","yelp","twitch","slideshare",
      "plug","binoculars","tty","futbol-o","soccer-ball-o","bomb","share-alt-square",
      "share-alt","sliders","paragraph","header","circle-thin","genderless","history",
      "paper-plane-o","send-o","paper-plane","send","weixin","wechat","qq","tencent-weibo",
      "hacker-news","git","git-square","empire","ge","rebel","ra","circle-o-notch",
      "life-ring","support","life-saver","life-buoy","life-bouy","jsfiddle","codepen",
      "vine","file-code-o","file-video-o","file-movie-o","file-audio-o","file-sound-o",
      "file-archive-o","file-zip-o","file-image-o","file-picture-o","file-photo-o",
      "file-powerpoint-o","file-excel-o","file-word-o","file-pdf-o","database","soundcloud",
      "deviantart","spotify","tree","taxi","cab","car","automobile","recycle","steam-square",
      "steam","behance-square","behance","cubes","cube","spoon","paw","child","building","fax",
      "language","joomla","drupal","pied-piper-alt","pied-piper","digg","delicious",
      "stumbleupon","stumbleupon-circle","reddit-square","reddit","google","yahoo",
      "graduation-cap","mortar-board","university","bank","institution","openid","wordpress",
      "envelope-square","slack","space-shuttle","plus-square-o","try","turkish-lira",
      "vimeo-square","wheelchair","dot-circle-o","caret-square-o-left","toggle-left",
      "arrow-circle-o-left","arrow-circle-o-right","stack-exchange","pagelines",
      "renren","weibo","vk","bug","archive","moon-o","sun-o","gratipay","gittip",
      "male","female","trello","foursquare","skype","dribbble","linux","android",
      "windows","apple","long-arrow-right","long-arrow-left","long-arrow-up",
      "long-arrow-down","tumblr-square","tumblr","bitbucket-square","bitbucket","adn",
      "flickr","instagram","stack-overflow","dropbox","youtube-play","xing-square","xing",
      "youtube","youtube-square","thumbs-down","thumbs-up","sort-numeric-desc",
      "sort-numeric-asc","sort-amount-desc","sort-amount-asc","sort-alpha-desc",
      "sort-alpha-asc","file-text","file","btc","bitcoin","krw","won","rub","rouble",
      "ruble","jpy","yen","rmb","cny","inr","rupee","usd","dollar","gbp","eur","euro",
      "caret-square-o-right","toggle-right","caret-square-o-up","toggle-up",
      "caret-square-o-down","toggle-down","compass","share-square","external-link-square",
      "pencil-square","check-square","level-down","level-up","minus-square-o","minus-square",
      "ticket","play-circle","rss-square","ellipsis-v","ellipsis-h","bullseye","unlock-alt",
      "anchor","css3","html5","chevron-circle-down","chevron-circle-up","chevron-circle-right",
      "chevron-circle-left","maxcdn","rocket","fire-extinguisher","calendar-o","shield",
      "microphone-slash","microphone","puzzle-piece","eraser","subscript","superscript",
      "exclamation","info","question","chain-broken","unlink","code-fork","crop",
      "location-arrow","star-half-o","star-half-full","star-half-empty","reply-all",
      "mail-reply-all","code","terminal","flag-checkered","flag-o","keyboard-o","gamepad",
      "meh-o","frown-o","smile-o","folder-open-o","folder-o","github-alt","reply","mail-reply",
      "circle","spinner","quote-right","quote-left","circle-o","mobile","mobile-phone",
      "tablet","laptop","desktop","angle-down","angle-up","angle-right","angle-left",
      "angle-double-down","angle-double-up","angle-double-right","angle-double-left",
      "plus-square","h-square","beer","fighter-jet","medkit","ambulance","hospital-o",
      "building-o","file-text-o","cutlery","coffee","bell-o","suitcase","stethoscope",
      "user-md","cloud-upload","cloud-download","exchange","lightbulb-o","clipboard",
      "paste","umbrella","sitemap","bolt","flash","comments-o","comment-o",
      "tachometer","dashboard","gavel","legal","undo","rotate-left","linkedin",
      "envelope","sort-asc","sort-up","sort-desc","sort-down","sort","unsorted",
      "columns","caret-right","caret-left","caret-up","caret-down","money","google-plus",
      "google-plus-square","pinterest-square","pinterest","truck","magic","table",
      "underline","strikethrough","list-ol","list-ul","bars","reorder","navicon","square",
      "floppy-o","save","paperclip","files-o","copy","scissors","cut","flask","cloud","link",
      "chain","users","group","arrows-alt","briefcase","filter","tasks","wrench","globe",
      "arrow-circle-down","arrow-circle-up","arrow-circle-right","arrow-circle-left",
      "hand-o-down","hand-o-up","hand-o-left","hand-o-right","certificate","bell","bullhorn",
      "hdd-o","rss","credit-card","unlock","github","facebook","facebook-f","twitter",
      "phone-square","bookmark-o","square-o","phone","lemon-o","upload","github-square",
      "trophy","sign-in","external-link","thumb-tack","linkedin-square","sign-out","heart-o",
      "star-half","thumbs-o-down","thumbs-o-up","comments","cogs","gears","key","camera-retro",
      "facebook-square","twitter-square","bar-chart","bar-chart-o","arrows-h","arrows-v",
      "folder-open","folder","shopping-cart","retweet","chevron-down","chevron-up","magnet",
      "comment","random","calendar","plane","exclamation-triangle","warning","eye-slash","eye",
      "fire","leaf","gift","exclamation-circle","asterisk","minus","plus","compress","expand",
      "share","mail-forward","arrow-down","arrow-up","arrow-right","arrow-left","ban",
      "check-circle-o","times-circle-o","crosshairs","info-circle","question-circle",
      "check-circle","times-circle","minus-circle","plus-circle","chevron-right",
      "chevron-left","eject","step-forward","fast-forward","forward","stop","pause","play",
      "backward","fast-backward","step-backward","arrows","check-square-o","share-square-o",
      "pencil-square-o","edit","tint","adjust","map-marker","pencil","picture-o","image",
      "photo","video-camera","indent","outdent","dedent","list","align-justify",
      "align-right","align-center","align-left","text-width","text-height","italic","bold",
      "font","camera","print","bookmark","book","tags","tag","barcode","qrcode","volume-up",
      "volume-down","volume-off","headphones","flag","lock","list-alt","refresh","repeat",
      "rotate-right","play-circle-o","inbox","arrow-circle-o-up","arrow-circle-o-down",
      "download","road","clock-o","file-o","home","trash-o","cog","gear","signal",
      "power-off","search-minus","search-plus","times","close","remove","check","th-list",
      "th","th-large","film","user","star-o","star","heart","envelope-o","search",
      "music","glass"
    ];
  }
}


