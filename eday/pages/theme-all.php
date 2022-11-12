<div class="index">
<?php
global $themes,$error;
if(!$themes||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfThemeAll;
}
$hasAccess=EDAY_ADMIN_TYPE=='master'||EDAY_ADMIN_TYPE=='admin'?true:false;
echo '<div class="post-row">Total themes: '.count($themes).'</div>';
foreach($themes as $theme){
  $isActive=site::config('theme')==$theme->namespace?1:0;
  $author_link=' &#8213; <a href="'.$theme->author_uri.'" target="_blank" '
    .'title="Author: '.htmlspecialchars($theme->author_name).'">'
    .htmlspecialchars($theme->author_name).'</a> ';
  $activate=!$isActive?'<a href="javascript:themeActivate(\''.$theme->namespace.'\')" class="submit-green">'
    .'<i class="fa fa-puzzle-piece"></i> Activate</a> ':'';
  $configuration=$isActive?'<a href="'.site::url.'?admin=theme/config/'
    .$theme->namespace.'" class="submit-blue"><i class="fa fa-gear"></i> Configuration</a> ':'';
  $repair=$hasAccess?'<a href="'.site::url.'?admin=theme/repair/'
    .$theme->namespace.'" class="submit-red"><i class="fa fa-wrench"></i> Repair</a> ':'';
  echo '<div class="post-row">'
    .'<div class="post-row-title"><a href="'.$theme->uri.'" target="_blank">'
      .htmlspecialchars($theme->name).'</a>'.($isActive?' <span>[Active]</span>':'').'</div>'
    .'<div class="post-row-detail">'.htmlspecialchars($theme->version)
      .$author_link
      .($theme->description?' &#8213; '.htmlspecialchars($theme->description):'')
      .'</div>'
    .'<div class="post-row-option">'
      .$activate.$configuration.$repair
      .'</div>'
    .'</div>';
}
//echo '<pre>'.print_r($themes,true).'</pre>';
?>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/theme.js')?>"></script>
<script type="text/javascript">
var THEME_ACTION_URL=SITE_URL+'?admin=theme/ajax';
</script>
<?php
endOfThemeAll:


