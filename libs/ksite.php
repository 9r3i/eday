<?php
/* ksite
 * ~ katya site - faster than katya CMS
 * ~ in purpose to build almost-static website
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 19th 2019
 */
class ksite{
  const version='1.0.0';
  protected $dir;
  protected $file;
  protected $core;
  protected $user;
  protected $site;
  protected $post;
  protected $info;
  protected $date;
  protected $time;
  protected $started;
  protected $db;
  protected $adminPath;
  protected $ajaxError;
  protected $error;
  protected $dataDir;
  /* construction */
  public function __construct(){
    /* set started in microtime */
    $this->started=microtime(true);
    /* prepare ksite dir */
    $dir=defined('KSITE_CLI_DIR')?KSITE_CLI_DIR:__DIR__;
    $dir=str_replace('\\','/',$dir);
    $dir.=substr($dir,-1)!='/'?'/':'';
    $this->dir=$dir;
    /* prepare ksite data dir */
    $datadir=defined('KSITE_DATA_DIR')?KSITE_DATA_DIR:__DIR__;
    $datadir=str_replace('\\','/',$datadir);
    $datadir.=substr($datadir,-1)!='/'?'/':'';
    $this->dataDir=$datadir;
    /* prepare configuration */
    $ini=[];
    $this->file=$this->dir.'config.ini';
    if(is_file($this->file)&&is_readable($this->file)){
      $ini=@parse_ini_file($this->file,true);
      $ini=is_array($ini)?$ini:[];
    }
    /* prepare main variable */
    $this->init($ini);
    /* date, time and info */
    $this->date=date('Y-m-d');
    $this->time=time();
    $this->info=$this->info();
    /* prepare admin path */
    $this->adminPath=$this->info->base.'?'.$this->core->adminKey;
    /* prepare admin file */
    if(isset($_GET[$this->core->adminKey])){
      $adminFile=$this->dir.'admin.php';
      if(!is_file($adminFile)){
        $error='Error: Failed to get admin file.';
        header('Content-Type: text/plain');
        header('Content-Length: '.strlen($error));
        exit($error);
      }return require_once($adminFile);
    }
    /* prepare data from ajax request */
    if(isset($_GET[$this->core->blogData])){
      /* prepare search from ajax request */
      if(strpos($_GET[$this->core->blogData],$this->core->blogSearch)===0){
        $keyword=substr($_GET[$this->core->blogData],strlen($this->core->blogSearch));
        $data=$this->blogSearch(preg_replace('/^\//','',$keyword));
      }else{
        $data=$this->getData($_GET[$this->core->blogData]);
      }$data=$data?$data:'Error: Data is not available.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($data));
      exit($data);
    }
    /* prepare like from ajax request */
    if(isset($_GET[$this->core->blogLike])){
      $db=$this->db();
      $insert=$db->query('insert into likes '.http_build_query([
        'pid'=>$_GET[$this->core->blogLike]
      ]));
      $data=$insert?'OK':'Error: Failed to like.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($data));
      exit($data);
    }
    /* prepare main file */
    $mainFile=$this->dir.$this->core->mainFile.'.php';
    if(!is_file($mainFile)){
      $error='Error: Failed to get main file.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }
    /* load main file */
    return require_once($mainFile);
  }
  /* --- admin require --- */
  /* cover request -- execute */
  protected function coverRequest(string $request){
    $get=new ksiteData($_GET);
    $post=new ksiteData($_POST);
    if($request=='ajax'){
      $this->ajax(new ksiteData($_POST));
      exit;
    }elseif($request=='logout/true'){
      setcookie('ksite-admin','',time()-10);
      header("Location: {$this->adminPath}");
      exit;
    }elseif($request=='setting/true'){
      if(!isset($post->content)){
        header('Content-Type: text/plain');
        exit('Error: Require setting content.');
      }$tfile=$this->file.'.tmp';
      @copy($this->file,$tfile);
      $put=@file_put_contents($this->file,$post->content);
      if(!$put){
        @rename($tfile,$this->file);
        header('Content-Type: text/plain');
        exit('Error: Failed to save content.');
      }@unlink($tfile);
      header("Location: {$this->adminPath}=setting");
      exit;
    }elseif($request=='delete/true'){
      if(!isset($get->pid)){
        header('Content-Type: text/plain');
        exit('Error: Require post ID.');
      }$db=$this->db();
      $delete=$db->query('delete from posts where id='.$get->pid);
      if(!$delete){
        header('Content-Type: text/plain');
        exit('Error: Failed to delete.');
      }header("Location: {$this->adminPath}=posts");
      exit;
    }elseif($request=='edit/true'){
      if(!isset($post->id)){
        header('Content-Type: text/plain');
        exit('Error: Require post ID.');
      }$db=$this->db();
      $update=$db->query('update into posts where id='.$post->id
        .'/'.http_build_query([
          'title'=>$post->title,
          'content'=>$post->content,
          'time'=>$post->time,
          'status'=>$post->status,
        ]));
      if(!$update){
        header('Content-Type: text/plain');
        exit('Error: Failed to update.');
      }header("Location: {$this->adminPath}=edit&pid={$post->id}");
      exit;
    }elseif($request=='new/true'){
      $db=$this->db();
      $insert=$db->query('insert into posts '
        .http_build_query([
          'title'=>$post->title,
          'content'=>$post->content,
          'status'=>$post->status,
          'time'=>time(),
          'author'=>$this->user->id,
          'hit'=>0,
        ]));
      if(!$insert){
        header('Content-Type: text/plain');
        exit('Error: Failed to insert.');
      }header("Location: {$this->adminPath}=posts");
      exit;
    }elseif($request=='visitor/clear'){
      $file=INDEX_SERVER.'visitor.log';
      if(is_file($file)){@unlink($file);}
      header("Location: {$this->adminPath}=visitor");
      exit;
    }elseif($request=='dashboard/mserver/log'){
      $file=INDEX_SERVER.'mserver.log';
      if(is_file($file)&&is_readable($file)){
        $data=@file_get_contents($file);
      }else{
        $data='Error: File is not available.';
      }
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($data));
      exit($data);
    }elseif($request=='upload/true'){
      return $this->uploadFile(new ksiteData($_FILES));
    }
  }
  /* --- pages --- */
  /* page upload */
  protected function pageUpload(){
    return '<div class="admin-upload" id="admin-upload">'
      .'<input type="file" name="file" data-upload="file" class="admin-upload-input" />'
      .'</div>';
  }
  /* page generate --- execute */
  protected function pageGenerateExec(){
    /* prepare kdb and output */
    $kdb=$this->db();
    $result=[
      'error'=>0,      // [number] error count
      'list'=>0,       // [json] blog list
      'likes'=>0,      // [json] list of likes
      'tags'=>0,       // [json] total write
      'home'=>0,       // [json] index content --> content
      'author'=>0,     // [json] index content --> content
      'allposts'=>0,   // [json] total write
      'rss'=>0,        // [xml] total write
      'sitemap'=>0,    // [xml] total write
      'totalID'=>0,    // [number] total write
      'totalTag'=>0,   // [number] total write
      'id'=>[],        // [json] each post in data/id  --> content
      'tag'=>[],       // [json] each tag in data/tag --> content
    ];
    /* prepare posts index and list of menu -- id,title,content,status,time,hit,author */
    $listed=$kdb->query('select from "posts" where status=publish'
      .'/sort=DESC&output=id,title,content,time,status');
    $result['allposts']=$this->putData('allposts',json_encode($listed));
    $list=[];$posts=[];
    foreach($listed as $sel){
      $list[]=['id'=>$sel['id'],'title'=>$sel['title']];
      unset($sel['status']);
      $posts[$sel['id']]=$sel;
      $posts[$sel['id']]['description']=$this->contentDescription($sel['content']);
      $result['id'][$sel['id']]=$this->putData('id/'
        .$sel['id'],@json_encode($posts[$sel['id']]));
      if(!$result['id'][$sel['id']]){$result['error']++;}
    }$result['list']=$this->putData('list',@json_encode($list));
    $result['totalID']=array_sum($result['id']).' -- '.count($result['id']);
    if(!$result['list']){$result['error']++;}
    if(!$result['totalID']){$result['error']++;}
    /* prepare tags and content */
    $tags=[];
    $tagsPosts=$kdb->query('select from "posts" like content=#&status=publish'
      .'/limit=10000&start=0&output=id,content,status&sort=DESC');
    foreach($tagsPosts as $v){
      preg_match_all('/#[a-z]+/i',$v['content'],$akur);
      foreach($akur[0] as $ak){
        if(!isset($tags[strtolower($ak)])){
          $tags[strtolower($ak)]=[];
        }
        if(!in_array($v['id'],$tags[strtolower($ak)])){
          $tags[strtolower($ak)][]=$v['id'];
        }
      }
    }ksort($tags,SORT_NATURAL);
    $result['tags']=$this->putData('tags',@json_encode($tags));
    if(!$result['tags']){$result['error']++;}
    /* prepare index tags and tag page */
    $tagsContent=[
      '<div class="tags-total">Total: '.count($tags).' tags</div>',
      '<div class="tags-content">',
    ];
    foreach($tags as $tag=>$tdata){
      $count=count($tdata);
      $tname=substr($tag,1);
      $tagsContent[]='<a href="/?tag='.$tname
        .'" class="tag-count-'.$this->tagClass($count)
        .' tags-each" title="'.$tag.'">'.$tname.'('.$count.')</a> ';
      $tagsPageContent=[
        '<div class="bulk-detail">Total: '.$count.'</div>',
        '<div class="bulk-parent">',
      ];
      foreach($tdata as $tid){
        if(!isset($posts[$tid])){continue;}
        $post=$posts[$tid];
        $pcontent=$this->contentDescription($post['content']);
        $tagsPageContent[]='<div class="bulk-row">'
          .'<div class="bulk-title">'
            .'<a href="/?id='.$post['id'].'" title="'.$post['title'].'">'
            .$post['title'].'</a></div>'
          .'<div class="bulk-time">'.date('l, F jS Y',$post['time']).'</div>'
          .'<div class="bulk-content">'.$pcontent.'</div>'
          .'</div>';
      }$tagsPageContent[]='</div>';
      $result['tag'][$tname]=$this->putData('tag/'.$tname,@json_encode([
        'id'=>0,
        'title'=>$tag,
        'content'=>implode($tagsPageContent),
        'time'=>time(),
        'description'=>'Labeled by tag '.$tag,
      ]));
      if(!$result['tag'][$tname]){$result['error']++;}
    }$tagsContent[]='</div>';
    $result['totalTag']=array_sum($result['tag']).' -- '.count($result['tag']);
    if(!$result['totalTag']){$result['error']++;}
    $result['home']=$this->putData('home',@json_encode([
      'id'=>0,
      'title'=>$this->site->description,
      'content'=>implode($tagsContent),
      'time'=>time(),
      'description'=>$this->site->description,
    ]));
    if(!$result['home']){$result['error']++;}
    /* prepare like */
    $likesData=[];
    $likes=$kdb->query('select from "likes"');
    foreach($likes as $like){
      if(isset($likesData[$like['pid']])){
        $likesData[$like['pid']]++;
      }else{
        $likesData[$like['pid']]=1;
      }
    }$result['likes']=$this->putData('likes',@json_encode($likesData));
    if(!$result['likes']){$result['error']++;}
    /* prepare author cover */
    $imagePath=$this->info->base.'ksite/images/';
    $about='<div class="author-about">'.$this->user->about.'</div>';
    $email='<div class="author-email">Email: <a href="mailto:'
      .$this->user->email.'" target="_blank">'.$this->user->email.'</a></div>';
    $uri='<div class="author-uri">URI: <a href="'.$this->user->uri
      .'" target="_blank">'.$this->user->uri.'</a></div>';
    $cover='<div class="author"><div class="author-cover">'
      .'<img src="'.$imagePath.$this->user->username.'-cover.jpg">'
      .'<div class="author-picture">'
      .'<img src="'.$imagePath.$this->user->username.'-picture.jpg">'
      .'</div></div></div>';
    /* put data author --> \u00e1 or á or &aacute; */
    $result['author']=$this->putData('author',@json_encode([
      'id'=>$this->user->id,
      'title'=>str_replace('&aacute;','á',$this->user->name),
      'content'=>$about,
      'time'=>strtotime('2012-04-01'),
      'description'=>$this->contentDescription($this->user->about),
      'cover'=>$cover,
      'footer'=>$email.$uri,
    ]));
    if(!$result['author']){$result['error']++;}
    /* prepare for rss and sitemap */
    $rssQuery=$kdb->query('select from "posts" where status=publish'
      .'/output=id,title,content,time&limit=20&start=0&sort=DESC&order_by=time');
    $siteMapQuery=$kdb->query('select from "posts" where status=publish'
      .'/output=id,time&limit=1000&start=0&sort=ASC&order_by=id');
    $rssQuery=is_array($rssQuery)?$rssQuery:[];
    $siteMapQuery=is_array($siteMapQuery)?$siteMapQuery:[];
    /* xml write -- REQUIRE --> $tags */
    $result['rss']=$this->generateFeedRSS($rssQuery,$this->site->name,$this->site->description);
    $result['sitemap']=$this->generateSiteMap($tags,$siteMapQuery);
    if(!$result['rss']){$result['error']++;}
    if(!$result['sitemap']){$result['error']++;}
    /* return result */
    return '<pre>'.print_r($result,true).'</pre>';
  }
  /* page generate */
  protected function pageGenerate(){
    return '<div class="admin-confirm">Are you sure?</div>'
      .'<div class="admin-confirm">'
      .'<a class="button" href="'.$this->adminPath
      .'=generateExec">Yes</a></div>';
  }
  /* page visitor */
  protected function pageVisitor(){
    /* prepare file */
    $file=INDEX_SERVER.'visitor.log';
    if(!is_file($file)||!is_readable($file)){
      return '<div class="admin-error">Error: Log file is not available.</div>';
    }
    /* prepare data log */
    $data=@file($file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    $data=is_array($data)?$data:[];
    $key=count($data);
    /* parse lines */
    $content='<div class="visitor-parent">';
    $content.='<div class="visitor-row">'
      .'Total: '.$key.' lines &#8213; <a href="'.$this->adminPath
        .'=visitor/clear" class="button button-red">Clear</a>'
      .'</div>';
    $content.='<div class="visitor-row">'.date('l, F jS Y &#8213; H:i:s').'</div>';
    while($key--){
      $row=@json_decode($data[$key]);
      if(!$row||!is_object($row)){continue;}
      $content.='<div class="visitor-row">';
      foreach($row as $k=>$v){
        $vd=is_string($v)?$v:@json_encode($v,JSON_PRETTY_PRINT);
        if($k=='time'){$vd=date('l, F jS Y &#8213; H:i:s',$v);}
        $content.='<div class="visitor-key">'.$k.'</div>'
          .'<div class="visitor-value">'.$vd.'</div>';
      }$content.='</div>';
    }$content.='</div>';
    /* return the content */
    return $content;
  }
  /* page setting */
  protected function pageSetting(){
    /* get content */
    $raw=@file_get_contents($this->file);
    /* parse html */
    $content='<div class="edit-parent"><form action="'
      .$this->adminPath.'=setting/true" method="post">'
      .'<div class="edit-row"><input type="text" disabled="true" value="'.$this->file.'" /></div>'
      .'<div class="edit-row"><textarea name="content">'
        .htmlspecialchars($raw)
        .'</textarea></div>'
      .'<div class="edit-row">'
        .'<input type="submit" name="save" value="Save" />'
        .'</div>'
      .'<div class="edit-row"></div>'
      .'</form></div>';
    return $content; 
  }
  /* page new */
  protected function pageNew(){
    /* parse html */
    $content='<div class="edit-parent"><form action="'
      .$this->adminPath.'=new/true" method="post">'
      .'<div class="edit-row"><input type="text" name="title" placeholder="Title" /></div>'
      .'<div class="edit-row"><textarea name="content" placeholder="Content"></textarea></div>'
      .'<div class="edit-row">'
        .'<input type="submit" name="save" value="Save" />'
        .'<select class="edit-inline" name="status">'
          .'<option value="publish">Publish</option>'
          .'<option value="draft">Draft</option>'
        .'</select>'
        .'</div>'
      .'<div class="edit-row"></div>'
      .'</form></div>';
    return $content;
  }
  /* page edit */
  protected function pageEdit(ksiteData $get){
    /* check post id */
    if(!isset($get->pid)){
      return '<div class="admin-error">Error: Require post ID.</div>';
    }
    /* prepare post data */
    $db=$this->db();
    $posts=$db->query('select from posts where id='.$get->pid);
    if(!$posts||!is_array($posts)||!isset($posts[0])){
      return '<div class="admin-error">Error: The post is not available.</div>';
    }$post=new ksiteData($posts[0]);
    /* parse html */
    $content='<div class="edit-parent"><form action="'
      .$this->adminPath.'=edit/true" method="post">'
      .'<div class="edit-row"><input type="text" name="title" value="'.$post->title.'" /></div>'
      .'<div class="edit-row"><textarea name="content">'
        .htmlspecialchars($post->content).'</textarea></div>'
      .'<div class="edit-row">'
        .'<input type="submit" name="save" value="Save" />'
        .'<select class="edit-inline" name="status">'
          .'<option value="publish">Publish</option>'
          .'<option value="draft" '
            .($post->status=='draft'?'selected="selected"':'')
            .'>Draft</option>'
        .'</select>'
        .'<input class="edit-inline" type="text" name="time" value="'.$post->time.'" />'
        .'</div>'
      .'<div class="edit-row"></div>'
      .'<input type="hidden" name="id" value="'.$post->id.'" />'
      .'</form></div>';
    return $content;
  }
  /* page delete */
  protected function pageDelete(ksiteData $get){
    if(!isset($get->pid)){
      return '<div class="admin-error">Error: Require post ID.</div>';
    }return '<div class="admin-confirm">Are you sure?</div>'
      .'<div class="admin-confirm">'
      .'<a class="button" href="'.$this->adminPath
      .'=delete/true&pid='.$get->pid.'">Yes</a></div>';
  }
  /* page posts */
  protected function pagePosts(){
    /* connect database */
    $db=$this->db();
    /* select post */
    $posts=$db->query('select from posts where */output=id,title,time,status');
    $posts=is_array($posts)?$posts:[];
    /* parse data into html */
    $k=count($posts);
    $content='';
    while($k--){
      $post=$posts[$k];
      $content.='<div class="post-row">'
        .'<div class="post-title">'.$post['title'].'</div>'
        .'<div class="post-time">'.date('Y-m-d H:i:s',$post['time'])
          .' &#8213; #'.$post['id'].' &#8213; '.$post['status'].'</div>'
        .'<div class="post-option">'
          .'<a href="?id='.$post['id'].'" target="_blank" class="button button-blue">View</a>'
          .'<a href="?'.$this->core->adminKey.'=edit&pid='
            .$post['id'].'" class="button button-green">Edit</a>'
          .'<a href="?'.$this->core->adminKey.'=delete&pid='
            .$post['id'].'" class="button button-red">Delete</a>'
        .'</div>'
        .'</div>';
    }return $content;
  }
  /* page logout */
  protected function pageLogout(){
    return '<div style="margin:10px 0px;">Are you sure?</div>'
      .'<div><a class="button" href="'
      .$this->adminPath.'=logout/true">Yes</a></div>';
  }
  /* page dashboard */
  protected function pageDashboard(){
    $content=''
      .'<pre>ksite::version --> '.ksite::version.'</pre>'
      .'<pre>'.date('Y-m-d H:i:s').' ('.date_default_timezone_get().')</pre>'
      .'<pre><a href="'.$this->info->base.'" target="_blank" class="button button-blue">View Website</a></pre>'
      .'<div id="dashboard-statistic"></div>'
      .'<pre>'.print_r($this,true).'</pre>';
    return $content;
  }
  /* load page */
  protected function loadPage(string $page){
    /* check page */
    $page='page'.ucfirst($page);
    if(!method_exists($this,$page)){
      return 'Error: Page is not available.';
    }
    /* load page */
    return call_user_func_array([$this,$page],[new ksiteData($_GET),new ksiteData($_POST)]);
  }
  /* ajax request */
  protected function ajax(ksiteData $post){
    /* check post */
    if(!isset($post->method)){
      return $this->errorResult('Error: Require ajax method.');
    }
    /* check method */
    /*  */
    /*  */
    /*  */
  }
  /* --- upload --- */
  protected function uploadFile(ksiteData $files){
    /* check _files name file */
    if(!isset($files->file)){
      return $this->errorResult('Require files named "file".');
    }$file=$files->file;
    /* check file error */
    if($file->error){
      return $this->errorResult('File is error.');
    }
    /* prepare file extension */
    $fext='unknown';
    if(preg_match('/\.(a-z0-9)$/i',$file,$akur)){
      $fext=$akur[1];
    }
    /* check file type */
    $ftype='others/'.$fext;
    if(preg_match('/^(image|audio|video|text)\//',$file->type,$akur)){
      $ftype=$akur[1].'s';
    }
    /* prepare directory */
    $ndir='files/'.$ftype.'/';
    $dir=MSERVER_ROOT.$ndir;
    if(!is_dir($dir)){@mkdir($dir,0755,true);}
    /* move uploaded file */
    if(!@move_uploaded_file($file->tmp_name,$dir.$file->name)){
      return $this->errorResult('Failed to move uploaded file.');
    }
    /* return result: the path where the file is */
    return $this->result($ndir.$file->name);
  }
  /* --- ajax only --- */
  /* blog search */
  protected function blogSearch(string $keyword=''){
    /* prepare default data */
    $data=new ksiteData([
      'id'=>0,
      'title'=>'Search',
      'content'=>'<div class="search-form">'
        .'<div class="search-form-column-input">'
          .'<input class="search-form-input" data-search="keyword" '
          .'placeholder="Search..." value="'.htmlspecialchars($keyword).'" />'
          .'</div>'
        .'<div class="search-form-column-button">'
          .'<div class="search-button" data-search="submit"></div>'
          .'</div>'
        .'<div class="search-suggestion" data-search="suggestion"></div>'
        .'</div>',
      'time'=>strtotime('2012-04-01'),
      'description'=>'Search in the blog.',
    ]);
    /* prepare cache file and dir */
    $cfile=$this->dataDir.'data/search/'.md5($keyword).'.txt';
    $cdir=dirname($cfile);
    if(!is_dir($cdir)){@mkdir($cdir,0755,true);}
    /* check cache file and keyword */
    if($keyword&&is_file($cfile)&&is_readable($cfile)){
      $temp=file_get_contents($cfile);
      return is_string($temp)?$temp:false;
    }elseif($keyword){
      /* connect database */
      $db=$this->db();
      /* execute query */
      $posts=$db->query('select from posts like '.http_build_query([
        'content'=>$keyword,
        'status'=>'publish',
      ]).'/sort=DESC&output=id,title,content,time,status');
      $posts=is_array($posts)?$posts:[];
      /* prepare for bulk content */
      $bulkContent=[
        '<div class="bulk-detail">Total: '.count($posts).'</div>',
        '<div class="bulk-parent">',
      ];
      /* parse posts for bulk content */
      foreach($posts as $post){
        $pcontent=$this->contentDescription($post['content']);
        $bulkContent[]='<div class="bulk-row">'
          .'<div class="bulk-title">'
            .'<a href="/?id='.$post['id'].'" title="'.$post['title'].'">'
            .$post['title'].'</a></div>'
          .'<div class="bulk-time">'.date('l, F jS Y',$post['time']).'</div>'
          .'<div class="bulk-content">'.$pcontent.'</div>'
          .'</div>';
      }$bulkContent[]='</div>';
      /* append content with bulk content */
      $data->content='<div class="search-wrap">'
        .$data->content
        .implode($bulkContent)
        .'</div>';
      /* set another value */
      $data->title.=': '.$keyword;
      $data->time=time();
      $data->description='Search result for "'.htmlentities($keyword).'".';
      $put=@file_put_contents($cfile,@json_encode($data->toArray()));
      if(!$put&&is_file($cfile)){@unlink($cfile);}
    }else{
      $data->content='<div class="search-wrap">'
        .$data->content.'</div>';
    }
    /* encode data as json */
    return @json_encode($data->toArray());
  }
  /* error result */
  protected function error(string $out=''){
    $this->ajaxError=$out;
    return false;
  }
  /* error result */
  protected function errorResult(string $out=''){
    return $this->result('Error: '.$out);
  }
  /* default result output */
  protected function result(string $out){
    header('Content-Type: text/plain');
    header('Content-Length: '.strlen($out));
    exit($out);
  }
  /* --- no admin require --- */
  /* put data file */
  protected function putData(string $data,string $content){
    $file=$this->dataDir.'data/'.$data.'.txt';
    $dir=dirname($file);
    if(!is_dir($dir)){
      @mkdir($dir,0755,true);
    }return @file_put_contents($file,$content);
  }
  /* get data file */
  protected function getData(string $data){
    $file=$this->dataDir.'data/'.$data.'.txt';
    if(!is_file($file)||!is_readable($file)){
      return false;
    }$content=@file_get_contents($file);
    return is_string($content)?$content:false;
  }
  /* database connection */
  protected function db(){
    if($this->db){return $this->db;}
    $kdb=new kdb('localhost','master','lagunaseca','luthfie','Asia/Jakarta');
    if(!$kdb||$kdb->error){
      $error='Error: Failed to connect into database.';
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($error));
      exit($error);
    }$this->db=$kdb;
    return $kdb;
  }
  /* get site info */
  protected function info(){
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
    $data=new ksiteData([
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
      'base'=>'',
      'url'=>'',
      'type'=>'',
    ]);
    /* set is bot */
    $isbot=preg_match('/bot|compatible|wow64|\+|crawl/i',$data->ua)?true:false;
    /* set port and url */
    $port=($data->protocol=='http'&&$data->port==80)
      ||($data->protocol=='https'&&$data->port==443)
      ?'':':'.$data->port;
    /* prepare base path */
    $basePath=$this->core->basePath;
    $basePath.=substr($basePath,-1)!='/'?'/':'';
    $basePath=substr($basePath,0,1)!='/'?'/'.$basePath:$basePath;
    /* push url to data */
    $data->base=$data->protocol.'://'.$data->host.$port.$basePath;
    $data->url=$data->protocol.'://'.$data->host.$port.$data->uri;
    $data->type=$isbot?'bot':'human';
    return $data;
  }
  /* get spent time */
  protected function generatedBy(){
    $version=self::version;
    $sec=number_format(microtime(true)-$this->started,4);
    return "<!-- generated by 9r3i\ksite v{$version} in {$sec} sec -->";
  }
  /* get json data of this object */
  protected function getJSON(bool $pretty=true){
    /* prepare array data */
    $user=$this->user;
    unset($user->password);
    $res=[
      'dir'=>$this->dir,
      'file'=>$this->file,
      'core'=>$this->core,
      'user'=>$user,
      'site'=>$this->site,
      'post'=>$this->post,
      'info'=>$this->info,
      'date'=>$this->date,
      'time'=>$this->time,
      'error'=>$this->error,
    ];
    /* return as json */
    if($pretty){
      return @json_encode($res,JSON_PRETTY_PRINT);
    }return @json_encode($res);
  }
  /* apple touch icon -- png
   * @prameters:
   *   $path = string of formated path
   */
  protected function appleTouchIcon(string $path='images/%s.png'){
    /* prepare sizes */
    $icons='16,32,57,60,64,72,76,96,114,120,128,144,152,180,192';
    $icons=explode(',',$icons);
    /* build html string */
    $result=[];
    foreach($icons as $icon){
      $image=sprintf($path,$icon);
      $result[]='<link rel="apple-touch-icon" sizes="'.$icon.'x'.$icon.'" href="'.$image.'" />';
    }$result[]='';
    return implode("\r\n  ",$result);
  }
  /* initialize */
  protected function init(array $ini){
    /* prepare default data */
    $core=[
      'adminKey'=>'ksite',
      'blogData'=>'data',
      'blogLike'=>'like',
      'blogSearch'=>'search',
      'mainFile'=>'main',
      'basePath'=>'/',
    ];
    $user=[
      'id'=>'',
      'username'=>'',
      'email'=>'',
      'uri'=>'',
      'name'=>'',
      'password'=>'',
      'about'=>'',
      'picture'=>'',
      'cover'=>'',
      'token'=>'ksite-'.preg_replace('/[^a-z0-9]+/i','',base64_encode(md5(date('Ymd'),true))),
    ];
    $site=[
      'name'=>'',
      'description'=>'',
      'keywords'=>'',
      'robots'=>'',
      'author'=>'',
      'uri'=>'',
      'canonical'=>'',
      'pingback'=>'',
      'alternate'=>'',
    ];
    $post=[
      'id'=>0,
      'title'=>'',
      'content'=>'',
      'time'=>0,
      'tags'=>[],
      'likes'=>0,
    ];
    /* prepare core, user, site and post */
    $this->core=new ksiteData($core);
    $this->user=new ksiteData($user);
    $this->site=new ksiteData($site);
    $this->post=new ksiteData($post);
    /* get value from ini */
    foreach(['core','user','site','post'] as $type){
      foreach(${$type} as $key=>$value){
        if(isset($ini[$type],$ini[$type][$key])){
          $this->{$type}->{$key}=preg_match('/^\d+$/',$ini[$type][$key])
            ?intval($ini[$type][$key]):$ini[$type][$key];
        }
      }
    }return true;
  }
  /* tag class */
  protected function tagClass(int $count){
    $classType=[
      'ultra-high'=>128,
      'very-high'=>64,
      'high'=>32,
      'moderate-high'=>16,
      'moderate'=>8,
      'moderate-low'=>4,
      'low'=>2,
      'very-low'=>1,
    ];
    $className='very-low';
    foreach($classType as $cname=>$length){
      if($count>=$length){
        $className=$cname;
        break;
      }
    }return $className;
  }
  /* build content for description */
  protected function contentDescription(string $s='',int $m=300){
    if($m>=strlen($s)){return $s;}
    $s=strip_tags($s);
    if($m>=strlen($s)){return $s;}
    $s=str_replace('"','&quot;',$s);
    if($m>=strlen($s)){return $s;}
    $p=preg_split('/(\r\n|\r|\n)+/',$s);
    $n='';$i=0;
    while(strlen($n)<$m){
      $n.=$p[$i].' ';
      $i++;
    }return $n.'...';
  }
  /* generate rss feed */
  protected function generateFeedRSS(array $posts=[],string $title='',string $description=''){
    if(!defined('MSERVER_ROOT')){return false;}
    $o=@fopen(MSERVER_ROOT.'rss.xml','wb');
    if(!is_resource($o)){return false;}
    $start=microtime(true);
    @flock($o,LOCK_EX);
    $w=@fwrite($o,'<?xml version="1.0" encoding="UTF-8"?>');
    $w+=@fwrite($o,'<rss version="2.0"
      xmlns:content="http://purl.org/rss/1.0/modules/content/"
      xmlns:wfw="http://wellformedweb.org/CommentAPI/"
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:atom="http://www.w3.org/2005/Atom"
      xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
      xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    ><channel>'
    .'<title><![CDATA['.html_entity_decode($title,ENT_QUOTES|ENT_HTML5,'UTF-8').']]></title>'
    .'<link>'.$this->info->base.'rss.xml</link>'
    .'<description><![CDATA['.html_entity_decode($description,ENT_QUOTES|ENT_HTML5,'UTF-8')
      .']]></description>'
    .'<language>US-en</language>'
    .'<pubDate>'.date('r',strtotime(date('Y-m-d 00:00:00'))).'</pubDate>'
    .'<atom:link href="'.$this->info->base.'rss.xml" rel="self" type="application/rss+xml" />'
    .'<generator>9r3i\katya\cms</generator>');
    $limit=250;
    foreach($posts as $id=>$post){
      $des=substr($post['content'],0,$limit);
      $en=mb_detect_encoding($des);
      $w+=@fwrite($o,'<item>'
        .'<title><![CDATA['.utf8_decode($post['title']).']]></title>'
        .'<link>'.$this->info->base.'?id='.$post['id'].'</link>'
        .'<description><![CDATA['.mb_convert_encoding($des,'UTF-8',$en)
          .(strlen($post['content'])>$limit
            ?'... <a href="'.$this->info->base.'?id='.$post['id']
              .'" rel="follow" title="Read More...">[Read More]</a>'
            :'')
          .']]></description>'
        .'<pubDate>'.date('r',$post['time']).'</pubDate>'
        .'<guid>'.$this->info->base.'?id='.$post['id'].'</guid>'
        .'</item>');
    }
    $w+=@fwrite($o,'</channel></rss>');
    $w+=@fwrite($o,'<!-- generated by 9r3i\katya\cms\v10 -->');
    @flock($o,LOCK_UN);@fclose($o);
    return $w;
  }
  /* generate sitemap */
  protected function generateSiteMap(array $tags=[],array $posts=[]){
    if(!defined('MSERVER_ROOT')){return false;}
    $sitemap=[
      [
        'loc'=>$this->info->base,
        'lastmod'=>date('r',strtotime(date('Y-m-d 00:00:00'))),
        'changefreq'=>'daily',
        'priority'=>'0.9',
      ],
      [
        'loc'=>$this->info->base.'rss.xml',
        'lastmod'=>date('r',strtotime(date('Y-m-d 00:00:00'))),
        'changefreq'=>'weekly',
        'priority'=>'0.7',
      ],
    ];
    $keywords=[
      'islam','ilmu','kiamat','maksiat',
      'akhlaq','hijab','pacaran','akhir zaman'
    ];
    foreach($keywords as $keyword){
      $sitemap[]=[
        'loc'=>$this->info->base.'?search='.urlencode($keyword),
        'lastmod'=>date('r',strtotime(date('Y-m-d 00:00:00'))),
        'changefreq'=>'daily',
        'priority'=>'0.6',
      ];
    }
    foreach($tags as $tag=>$count){
      $sitemap[]=[
        'loc'=>$this->info->base.'?tag='.substr($tag,1),
        'lastmod'=>date('r',strtotime(date('Y-m-d 00:00:00'))),
        'changefreq'=>'weekly',
        'priority'=>'0.5',
      ];
    }
    foreach($posts as $post){
      $sitemap[]=[
        'loc'=>$this->info->base.'?id='.$post['id'],
        'lastmod'=>date('r',$post['time']),
        'changefreq'=>'weekly',
        'priority'=>'0.5',
      ];
    }
    $o=@fopen(MSERVER_ROOT.'sitemap.xml','wb');
    if(!is_resource($o)){return false;}
    @flock($o,LOCK_EX);
    $w=@fwrite($o,'<?xml version="1.0" encoding="UTF-8"?>');
    $w+=@fwrite($o,'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    foreach($sitemap as $map){
      $w+=@fwrite($o,'<url>');
      foreach($map as $tag=>$value){
        $w+=@fwrite($o,'<'.$tag.'>'.$value.'</'.$tag.'>');
      }$w+=@fwrite($o,'</url>');
    }$w+=@fwrite($o,'</urlset>');
    @flock($o,LOCK_UN);@fclose($o);
    return $w;
  }
}


