<?php
/* website
 * ~ website builder
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 5th 2019
 * @require:
 *   - dataObject
 *   - Ldb2x
 *   - apix
 *   - site
 *   - webAPI
 *   - webPlugins
 *   - webPlugin
 *   - webHelper
 */
use eday\site;
#[AllowDynamicProperties]
class website{
  const version='1.0.1';
  protected $app=null;
  protected $dir=null;
  protected $path=null;
  protected $configFile=null;
  protected $config=null;
  protected $db=null;
  protected $site=null;
  protected $content=null;
  protected $plugin=null;
  protected $theme=null;
  /* constructor */
  public function __construct(){
    /* change directory */
    @chdir(EDAY_INDEX_DIR);
    /* set application name */
    $this->app=site::config('app');
    /* set directory path */
    $this->dir=EDAY_APP_DIR.$this->app.'/';
    /* set configuration file */
    $this->configFile=$this->dir.'config.ini';
    /* set app path */
    $this->path=EDAY_APP_PATH.$this->app.'/';
    /* parse configuration file */
    $ini=@parse_ini_file($this->configFile,true);
    $ini=is_array($ini)?$ini:[];
    /* set configuration as data object */
    $this->config=new dataObject($ini);
    /* set site data */
    $this->site=$this->config->website;
    /* set database */
    $dbname=isset($this->config->database->dbname)
      &&is_string($this->config->database->dbname)
      ?$this->config->database->dbname:'root';
    $this->db=new Ldb2x($dbname);
    /* set timezone */
    @date_default_timezone_set(site::config('timezone'));
    /* start website */
    return $this->start();
  }
  /* start website */
  public function start(){
    /* check started website */
    if(defined('EDAY_WEBSITE_STARTED')){return false;}
    define('EDAY_WEBSITE_STARTED',true);
    /* globalize $this object as $website
     * and $post for helping plugin works
     */
    global $website,$post;
    $website=$this;
    /* prepare plugins
     * --> initialize
     * --> WEBSITE_PLUGINS_PATH
     * --> WEBSITE_PLUGINS_DIRECTORY
     */
    $this->plugin=new webPlugins($this->dir.'plugins',$this->path.'plugins');
    /* prepare website api */
    if(isset($_POST['eday-website-api'])){
      if(!site::config('api')){
        header('content-type:text/plain',true,401);
        exit('Error: 401 Unauthorized.');
      }return new webAPI;
    }
    /* prepare admin request */
    if(isset($this->config->core->admin)
      &&is_string($this->config->core->admin)
      &&isset($_GET[$this->config->core->admin])){
      $this->site=webHelper::siteData([]);
      $this->theme=$this->config->core->admin;
      return new webAdmin($this);
    }
    /* prepare for request website-data */
    if(isset($_GET['website-data'])){
      $where='access=public&status=publish&type=admin&author=admin';
      $select=$this->db->select('posts',$where);
      $posts=[];
      foreach($select as $each){
        $post=new dataObject($each);
        $post->content=$this->plugin->load('content',$post->content);
        $posts[$post->url]=$post;
      }$json=@json_encode($posts);
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($json));
      exit($json);
    }
    /* prepare get and select */
    $isBulk=false;
    $select=[];
    if(isset($_GET['slug'])){
      $select=$this->db->select('posts','url='.$_GET['slug'].'&access=public&status=publish');
      $canonical=$_GET['slug'];
    }elseif(isset($_GET['type'])){
      $select=$this->db->select('posts','type='.$_GET['type'].'&access=public&status=publish');
      $isBulk=true;
    }elseif(isset($_GET['feed'])){
      return $this->feed($_GET['feed']);
    }elseif(empty(site::ruri)||isset($_GET['home'])){
      $aid=$this->config->core->mainPageID;
      $select=$this->db->select('posts',"aid=$aid");
    }$select=is_array($select)?$select:[];
    /* prepare post */
    $post=isset($select[0])
      ?new dataObject($select[0])
      :webHelper::postDefaultData();
    /* prepare for ajax request */
    if(isset($_GET['website-ajax-request'])){
      if($isBulk){
        $bulks=$this->plugin->load('bulkContent',$select);
        $result=[];
        foreach($bulks as $bulk){
          if(isset($bulk['bulkContent'])){
            $result[]=$bulk['bulkContent'];
          }
        }
        $npost=[
          'title'=>ucfirst($_GET['type']),
          'url'=>$_GET['type'],
          'type'=>'bulk',
          'content'=>implode("\r\n",array_reverse($result)),
        ];
        $json=@json_encode($npost);
      }else{
        $post->content=$this->plugin->load('content',$post->content);
        $json=@json_encode($post);
      }
      header('Content-Type: text/plain');
      header('Content-Length: '.strlen($json));
      exit($json);
    }
    /* execute action plugins */
    $this->plugin->load('action');
    /* prepare menu */
    $menus=$this->db->select('menu');
    $menus=is_array($menus)?$menus:[];
    /* generate site data */
    $this->site=webHelper::siteData($post->toArray());
    /* add site menu */
    $this->site->menu=webHelper::parseSiteMenu($menus);
    /* prepare site canonical */
    if(isset($canonical)){
      $this->site->canonical=site::url.$canonical.'.html';
      $this->site->pingback=site::url.$canonical.'.html';
      $this->site->alternate=site::url.$canonical.'.xml';
    }elseif($isBulk&&isset($_GET['type'])){
      $this->site->canonical=site::url.$_GET['type'];
      $this->site->pingback=site::url.$_GET['type'];
      $this->site->alternate=site::url.$_GET['type'].'.xml';
    }
    /* body -- content --> generate by plugins content */
    if($isBulk){
      $bulks=$this->plugin->load('bulkContent',$select);
      $result=[];
      foreach($bulks as $bulk){
        if(isset($bulk['bulkContent'])){
          $result[]=$bulk['bulkContent'];
        }
      }$this->content=implode("\r\n",array_reverse($result));
    }else{
      $this->content=$this->plugin->load('content',$post->content);
    }
    /* prepare main page */
    $mainPage=isset($this->config->core->mainPage)
      &&is_string($this->config->core->mainPage)
      ?$this->config->core->mainPage:'main';
    /* preapre theme */
    $this->theme=isset($this->config->core->theme)
      &&is_string($this->config->core->theme)
      ?$this->config->core->theme:'default';
    /* preapre and check file */
    $file=$this->dir.'themes/'.$this->theme.'/pages/'.$mainPage.'.php';
    if(!is_file($file)||!is_readable($file)){
      header('Content-Type: text/plain; charset=UTF-8');
      exit('Error: Failed to load theme.');
    }
    /* preapre header */
    header('Content-Type: text/html; charset=UTF-8');
    /* load theme page -- html */
    return $this->loadPage($mainPage);
  }
  /* feed */
  public function feed(string $path){
    /* prepare feed css */
    $cssPath=site::appURL('css/feed.css');
    /* prepare post */
    if($path=='feed'){
      $posts=$this->db->select('posts');
    }else{
      $posts=$this->db->select('posts','url='.$path);
    }
    /* prepare started time */
    $startedTime=microtime(true);
    /* prepare content */
    $content='<?xml version="1.0" encoding="UTF-8"?>'
      .'<?xml-stylesheet type="text/css" href="'.$cssPath.'" media="print, screen" ?>'
      .'<rss version="2.0" '
        .'xmlns:content="http://purl.org/rss/1.0/modules/content/" '
        .'xmlns:wfw="http://wellformedweb.org/CommentAPI/" '
        .'xmlns:dc="http://purl.org/dc/elements/1.1/" '
        .'xmlns:atom="http://www.w3.org/2005/Atom" '
        .'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" '
        .'xmlns:slash="http://purl.org/rss/1.0/modules/slash/">'
      .'<channel>'
      .'<title><![CDATA['.$this->site->name.']]></title>'
      .'<link>'.EDAY_ADDR.$path.'.xml</link>'
      .'<description><![CDATA['.$this->site->description.']]></description>'
      .'<language>ID-id</language>'
      .'<pubDate>'.date('r').'</pubDate>'
      .'<atom:link href="'.EDAY_ADDR.$path.'.xml" rel="self" type="application/rss+xml" />'
      .'<author>https://gihub.com/9r3i</author>';
    /* prepare next page and stop */
    $limit=$this->config->core->feedLimit;
    $counter=0;
    /* parse posts */
    foreach(array_reverse($posts) as $pdata){
      $post=new dataObject($pdata);
      $counter++;
      if($counter>$limit){break;}
      $content.='<item>'
        .'<title><![CDATA['.mb_convert_encoding($post->title,'ASCII','auto').']]></title>'
        .'<link>'.EDAY_ADDR.$post->url.'.html</link>';
      $tcontent=strip_tags($post->content);
      $pcontent=substr($tcontent,0,300);
      $pcontent.=strlen($tcontent)>300?'...':'';
      $content.='<description><![CDATA['.mb_convert_encoding($pcontent,'ASCII','auto').']]></description>'
        .'<pubDate>'.date('r',$post->time).'</pubDate>'
        .'<guid>'.EDAY_ADDR.$post->url.'.html</guid>'
        .'</item>';
    }$content.='</channel></rss>';
    $content.='<!-- Generated by e-Day::webFeed in '
      .number_format(microtime(true)-$startedTime,3).' seconds, on '
      .date('Y-m-d H:i:s').' -->';
    /* set header content */
    header('Content-Type: application/rss+xml');
    header('Content-Length: '.strlen($content));
    /* print content as exit */
    exit($content);
  }
  /* database */
  public function db(){
    return $this->db;
  }
  /* site */
  public function site(){
    return $this->site;
  }
  /* theme */
  public function theme(){
    return $this->theme;
  }
  /* plugin */
  public function plugin(){
    return $this->plugin;
  }
  /* config */
  public function config(){
    return $this->config;
  }
  /* property */
  public function property(){
    return new dataObject(get_object_vars($this));
  }
  /* load a page -- directory: pages */
  public function loadPage(string $name){
    $file=$this->dir.'themes/'.$this->theme.'/pages/'.$name.'.php';
    if(!is_file($file)||!is_readable($file)){
      return false;
    }return require($file);
  }
  /* load a javascript -- directory: js */
  public function loadJS(string $name,bool $inline=false){
    $file=$this->dir.'themes/'.$this->theme.'/js/'.$name.'.js';
    if(!is_file($file)||!is_readable($file)){
      return false;
    }$format='<script %s type="text/javascript">%s</script>';
    if($inline){
      return sprintf($format."\r\n",'',@file_get_contents($file));
    }$path=$this->path.'themes/'.$this->theme.'/js/'.$name.'.js';
    return sprintf($format."\r\n",'src="'.$path.'"','');
  }
  /* load a css file -- directory: css */
  public function loadCSS(string $name,bool $inline=false){
    $file=$this->dir.'themes/'.$this->theme.'/css/'.$name.'.css';
    if(!is_file($file)||!is_readable($file)){
      return false;
    }$format='<style type="text/css" media="print,screen">%s</style>';
    if($inline){
      return sprintf($format."\r\n",@file_get_contents($file));
    }$format='<link rel="stylesheet" href="%s" type="text/css" media="print,screen">';
    $path=$this->path.'themes/'.$this->theme.'/css/'.$name.'.css';
    return sprintf($format."\r\n",$path);
  }
}


