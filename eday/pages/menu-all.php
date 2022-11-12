<div class="index">
<?php
global $menus,$error;
if(!is_array($menus)||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfMenuAll;
}
$menu_parent='<option value="">[Blank Parent]</option>';
$prs=get::menuParents();
$parents=[];
foreach($prs as $pr){
  $parents[$pr['id']]=$pr;
}
?>
<div class="post-row">Total Types: <?=count($menus)?>
  <button onclick="menuAddForm()" class="submit-green menu-add-button" id="menu-add-button" data-status="close"><i class="fa fa-plus"></i></button>
</div>
<div id="menu-add-form" style="height:0px;"><div class="menu-add-form">
<table width="100%" class="form-table"><tbody>
  <tr><td class="form-key">Menu Name</td><td>
    <input type="text" class="input" name="name" placeholder="Insert Menu Name" />
  </td></tr>
  <tr><td class="form-key">Menu URI</td><td>
    <input type="text" class="input" name="uri" placeholder="Insert Menu URI" />
  </td></tr>
  <tr><td class="form-key">Menu Type</td><td>
    <input type="text" class="input" name="type" placeholder="Insert Menu Type" onchange="menuChangetype()" />
  </td></tr>
  <tr><td class="form-key">Menu Parent</td><td>
    <select name="parent" class="select"><?=$menu_parent?></select>
  </td></tr>
  <tr><td class="form-key"></td><td style="padding:20px 10px;">
    <a href="javascript:menuSubmitAdd()" class="submit submit-blue"><i class="fa fa-save"></i> Save Menu</a>
  </td></tr>
</tbody></table>
</div></div>
<div class="menu-index" id="menu-index">Loading...</div>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/menu.js')?>" async="true"></script>
<script type="text/javascript">
var MENU_ACTION_URL=SITE_URL+'?admin=menu/ajax';
var MENU_DATA=<?=json_encode($menus)?>;
var MENU_PARENT=<?=json_encode($parents)?>;
</script>
<?php
endOfMenuAll:


