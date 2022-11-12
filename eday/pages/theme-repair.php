<div class="index">
<?php
global $files,$error,$namespace;
if(!is_array($files)||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfThemeRepair;
}
$left=strlen(EDAY_THEME_DIR.$namespace.'/');
$tfiles=[];$selector=['<option value="">[No File Selected]</option>'];
foreach($files as $file){
  $tfile=substr($file,$left);
  $tfiles[]=$tfile;
  $selector[]='<option value="'.$tfile.'">'.$tfile.'</option>';
}
echo '<div class="post-row">Total files: '.count($files).'</div>'
  .'<select onchange="themeSelectFile(this.value);" class="select">'.implode($selector).'</select>';
?>
<div id="file-selected"></div>
<?php
//echo '<pre>'.print_r($tfiles,true).'</pre>';
?>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/theme.js')?>"></script>
<script type="text/javascript">
var THEME_ACTION_URL=SITE_URL+'?admin=theme/ajax';
var THEME_NAMESPACE='<?=$namespace?>';
</script>
<?php
endOfThemeRepair:


