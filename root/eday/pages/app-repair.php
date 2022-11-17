<div class="index">
<?php
/* use namespace eday */
use eday\admin;
global $files,$error,$namespace;
if(!is_array($files)||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfAppRepair;
}
$left=strlen(EDAY_APP_DIR.$namespace.'/');
$tfiles=[];$selector=['<option value="">[No File Selected]</option>'];
foreach($files as $file){
  $tfile=substr($file,$left);
  $tfiles[]=$tfile;
  $selector[]='<option value="'.$tfile.'">'.$tfile.'</option>';
}
echo '<div class="post-row">Total files: '.count($files).'</div>'
  .'<select onchange="appSelectFile(this.value);" class="select">'.implode($selector).'</select>';
?>
<div id="file-selected"></div>
<?php
//echo '<pre>'.print_r($tfiles,true).'</pre>';
?>
</div>
<script type="text/javascript" src="<?=admin::appURL('js/app.js')?>"></script>
<script type="text/javascript">
var APP_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=app/ajax';
var APP_NAMESPACE='<?=$namespace?>';
</script>
<?php
endOfAppRepair:


