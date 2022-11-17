<?php
use eday\site;
class webContent extends webPlugin{
  const version='1.0.1';
  public function __construct(){
    /* extends from webPlugin to get additional methods:
     * - path    = string of directory path
     * - url     = string of url path
     * - option  = mixed of option value; <plugin_name>/options.ini
     *             --> per section; default: config
     * - website = object of global website
     * - post    = object of global data post
     */
    parent::__construct(__CLASS__);
  }
  /* generate web content massive/bulk
   * input and output must be array
   */
  public function bulkContent(array $data){
    $ndata=[];
    foreach($data as $pkey=>$pdata){
      $ndata[$pkey]=$pdata;
      if($pdata['type']=='page'){
        continue;
      }$ndata[$pkey]['bulkContent']=$this->bulkContentOne(new dataObject($pdata));
    }return $ndata;
  }
  /* generate web bulk content one post */
  public function bulkContentOne(object $post){
    $stripped=strip_tags($post->content);
    $content=substr($stripped,0,300);
    $content.=strlen($stripped)>300?'...':'';
    $image='';
    if(!empty($post->picture)){
      $image='<img class="page-thm-bulk" src="'.$post->picture.'" />';
    }
    $postTime='Published on '.date('F, jS Y',$post->time);
    if($post->type=='training'){
      $start=$this->getTime($post->start);
      $until=$this->getTime($post->end);
      $timeUntil=$start!=$until&&$until>time()
        ?' Until '.date('F, jS Y',$until):'';
      $startWord=$start>time()?'Start on ':'Held on ';
      $postTime=$startWord.date('F, jS Y',$start).$timeUntil;
    }
    $result='<div class="bulk-post">'
      .'<div class="bulk-post-head">'
        .'<a href="'.site::url.$post->url.'.html" title="'.$post->title.'">'
        .$post->title.'</a></div>'
      .'<div class="bulk-post-time">'.$postTime.'</div>'
      .'<div class="bulk-post-body">'.$image.$content.'</div>'
      .'</div>';
    return $result;
  }
  /* generate web content */
  public function content(string $content){
    /* get post object data */
    $post=$this->post();
    /* type: page */
    if($post->type=='page'){
      $result='<div class="post-title">'.$post->title.'</div>';
      $picture='';
      if($post->picture){
        $picture='<img class="page-thm" src="'.$post->picture.'" />';
      }$result.='<div class="post-content">'.$picture.$content.'</div>';
      return $result;
    }
    /* type: post and article */
    if($post->type=='post'
      ||$post->type=='article'){
      $author=$this->getUser($post->author);
      $authorName=is_object($author)?$author->name:$post->author;
      $result='<div class="post-title">'.$post->title.'</div>'
        .'<div class="post-detail">'
        .'<div class="post-detail-time">Published on '.date('F, jS Y',$post->time).'</div>'
        .'<div class="post-detail-author">Authored by <strong>'.$authorName.'</strong></div>'
        .'</div>';
      if($post->picture){
        $result.='<img class="page-thm" src="'.$post->picture.'" />';
      }$result.='<div class="post-content">'.$content.'</div>';
      return $result;
    }
    /* type: training */
    if($post->type=='training'){
      $start=$this->getTime($post->start);
      $until=$this->getTime($post->end);
      $timeUntil=$start!=$until&&$until>time()
        ?'<div class="post-detail-time">Until '.date('F, jS Y',$until).'</div>':'';
      $startWord=$start>time()?'Start on ':'Held on ';
      $passedClass=$start<time()?'post-detail-passed':'post-detail-next';
      $result='<div class="post-title">'.$post->title.'</div>'
        .'<div class="post-detail">'
        .'<div class="post-detail-time '.$passedClass.'">'
          .$startWord.' '.date('F, jS Y',$start).'</div>'
        .$timeUntil
        .'<div class="post-detail-author">Trainer: <strong>'.$post->trainer.'</strong></div>'
        .'</div>';
      if($post->picture){
        $result.='<img class="page-thm" src="'.$post->picture.'" />';
      }$result.='<div class="post-content">'.$content.'</div>';
      return $result;
    }
    /* return as it is */
    return $content;
  }
  /* get user data */
  public function getUser(string $username){
    $db=$this->website()->db();
    $select=$db->select('users','username='.$username);
    if(!isset($select[0])){return false;}
    return new dataObject($select[0]);
  }
  /* get time from string */
  public function getTime($str=''){
    return @strtotime($str);
  }
}


