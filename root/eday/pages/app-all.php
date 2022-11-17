<div class="index">
<?php
/* use namespace eday */
use eday\admin;
use eday\site;
global $apps,$error;
if(!$apps||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfAppAll;
}
$hasAccess=EDAY_ADMIN_TYPE=='master'||EDAY_ADMIN_TYPE=='admin'?true:false;
echo '<div class="post-row">Total applications: '.count($apps).'</div>';
foreach($apps as $app){
  $isActive=site::config('app')==$app->namespace?1:0;
  $author_link=' &#8213; <a href="'.$app->author_uri.'" target="_blank" '
    .'title="Author: '.htmlspecialchars($app->author_name).'">'
    .htmlspecialchars($app->author_name).'</a> ';
  $activate=!$isActive?'<a href="javascript:appActivate(\''.$app->namespace.'\')" class="submit-green">'
    .'<i class="fa fa-puzzle-piece"></i> Activate</a> ':'';
  $configuration=$isActive?'<a href="'.site::url.'?'.EDAY_ADMIN_KEY.'=app/config/'
    .$app->namespace.'" class="submit-blue"><i class="fa fa-gear"></i> Configuration</a> ':'';
  $repair=$hasAccess?'<a href="'.site::url.'?'.EDAY_ADMIN_KEY.'=app/repair/'
    .$app->namespace.'" class="submit-red"><i class="fa fa-wrench"></i> Repair</a> ':'';
  echo '<div class="post-row">'
    .'<div class="post-row-title"><a href="'.$app->uri.'" target="_blank">'
      .htmlspecialchars($app->name).'</a>'.($isActive?' <span>[Active]</span>':'').'</div>'
    .'<div class="post-row-detail">'.htmlspecialchars($app->version)
      .$author_link
      .($app->description?' &#8213; '.htmlspecialchars($app->description):'')
      .'</div>'
    .'<div class="post-row-option">'
      .$activate.$configuration.$repair
      .'</div>'
    .'</div>';
}
//echo '<pre>'.print_r($apps,true).'</pre>';
?>
</div>
<script type="text/javascript" src="<?=admin::appURL('js/app.js')?>"></script>
<script type="text/javascript">
var APP_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=app/ajax';
</script>
<?php
endOfAppAll:


