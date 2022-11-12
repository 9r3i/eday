<div class="index">

<style type="text/css" media="screen,print">
.field{padding:10px 10px 20px;margin-bottom:10px;background-color:#fff;}
.field legend{padding:1px 1px;}
</style>

<fieldset class="field"><legend>Welcome</legend>
Hello, <strong><?=EDAY_ADMIN_USERNAME?></strong>. Welcome to e-Day admin's dashboard.
<br /><a href="<?=site::url?>" target="_blank" title="View Website">View Website</a>.
</fieldset>

<fieldset class="field"><legend>Products</legend>
You have <a href="<?=site::url?>?admin=product/all" title="Show All Products"><?=site::productRow()?> products</a>.
</fieldset>

<fieldset class="field"><legend>Posts</legend>
You have <a href="<?=site::url?>?admin=post/all" title="Show All Posts"><?=site::postRow()?> posts</a>.
</fieldset>

<?php
if(EDAY_ADMIN_TYPE!=='master'&&EDAY_ADMIN_TYPE!=='admin'){
  goto endOfDashboard;
}
?>

<fieldset class="field"><legend>Users</legend>
You have <a href="<?=site::url?>?admin=user/all" title="Show All Users"><?=site::userRow()?> active users</a>.
</fieldset>

<fieldset class="field"><legend>Theme</legend>
<?php
$themes=@unserialize(EDAY_THEMES);
$theme_count=count($themes);
$theme=site::config('theme');
$current=$themes[$theme];
?>
You have <a href="<?=site::url?>?admin=theme/all" title="Show All Themes"><?=$theme_count?> themes</a>.
<br />Current active theme is <strong><?=$current->name?></strong><?=$current->description?' ('.$current->description.')':''?>.
<br />Authored by <strong><?=$current->author_name?></strong>.
</fieldset>

</div>
<?php
endOfDashboard:


