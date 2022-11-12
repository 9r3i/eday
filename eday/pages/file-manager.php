<div class="index">
<?php
global $files,$error,$path;
if(!is_array($files)||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfFileManager;
}
$eps=explode('/',$path);$xpp=[];
$xp=['<a href="'.site::url.'?admin=file/manager">www</a>'];
foreach($eps as $ep){
  $xpp[]=$ep;
  $xp[]='<a href="'.site::url.'?admin=file/manager/'
    .urlencode(base64_encode(implode('/',$xpp))).'">'.$ep.'</a>';
}
?>
<div class="file-manager">
<table class="file-manager-table"><tbody>
<tr><td colspan="6"><?=implode('/',$xp)?></td></tr>
<?php
$filelist=[];$disallowed=['editors','kitchen'];
foreach($files as $file){
$f=(object)$file;
$filelist[]=$f->name;
if($path=='files'&&in_array($f->name,$disallowed)){continue;}
$filename=$f->name;
$icon='<i class="fa fa-question"></i>';
if($f->type=='directory'){
  $icon='<i class="fa fa-folder"></i>';
  $filename='<a href="'.site::url.'?admin=file/manager/'
    .urlencode(base64_encode($path.($path==''?'':'/').$f->name)).'">'
    .$f->name.'</a>';
}elseif($f->type=='file'){
  $icon='<i class="fa fa-file-o"></i>';
  $filename='<a href="'.site::url.$path.'/'.$f->name
    .'" target="_blank">'.$f->name.'</a>';
}
$action='';
if($f->type!='directory'){
  $action.='<a href="javascript:fileDelete(\''.$f->name.'\')" '
    .'class="submit-red"><i class="fa fa-trash"></i></a> ';
  $action.='<a href="javascript:fileRename(\''.$f->name.'\')" '
    .'class="submit-blue"><i class="fa fa-edit"></i></a> ';
}
?>
<tr>
<td class="file-manager-icon"><?=$icon?></td>
<td class="file-manager-name" data-filename="<?=$f->name?>"><?=$filename?></td>
<td class="file-manager-size"><?=number_format(ceil($f->size/1024),0)?> KB</td>
<td class="file-manager-modified"><?=base::timeAgo($f->modified)?></td>
<td class="file-manager-type"><?=$f->type?></td>
<td class="file-manager-action"><?=$action?></td>
</tr>
<?php } if($path!==''){ ?>
<tr><td colspan="6"><input type="file" class="input" id="file-input" onchange="fileUpload(this)" /></td></tr>
<?php } ?>
</tbody></table>
</div>
<?php
//echo '<pre>'.print_r($files,true).'</pre>';
?>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/file.js')?>"></script>
<script type="text/javascript">
var FILE_CURRENT_PATH='<?=$path?>';
var FILE_LIST=<?=json_encode($filelist)?>;
</script>
<?php
endOfFileManager:


