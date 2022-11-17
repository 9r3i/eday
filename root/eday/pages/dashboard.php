<?php
use eday\site;
?><div class="index">

<style type="text/css" media="screen,print">
.field{padding:10px 10px 20px;margin-bottom:10px;background-color:#fff;}
.field legend{padding:1px 1px;}
</style>

<fieldset class="field"><legend>Welcome</legend>
Hello, <strong><?=EDAY_ADMIN_USERNAME?></strong>. Welcome to e-Day admin's dashboard.
<br /><a href="<?=site::url?>" target="_blank" title="View Website">View Website</a>.
</fieldset>

<fieldset class="field"><legend>Products</legend>
You have <a href="<?=site::url?>?<?=EDAY_ADMIN_KEY?>=product/all" title="Show All Products"><?=site::productRow()?> products</a>.
</fieldset>

<fieldset class="field"><legend>Posts</legend>
You have <a href="<?=site::url?>?<?=EDAY_ADMIN_KEY?>=post/all" title="Show All Posts"><?=site::postRow()?> posts</a>.
</fieldset>

<?php
if(EDAY_ADMIN_TYPE!=='master'&&EDAY_ADMIN_TYPE!=='admin'){
  goto endOfDashboard;
}
?>

<fieldset class="field"><legend>Users</legend>
You have <a href="<?=site::url?>?<?=EDAY_ADMIN_KEY?>=user/all" title="Show All Users"><?=site::userRow()?> active users</a>.
</fieldset>

<fieldset class="field"><legend>Application</legend>
<?php
$apps=@unserialize(EDAY_APPS);
$app_count=count($apps);
$app=site::config('app');
$current=$apps[$app];
?>
You have <a href="<?=site::url?>?<?=EDAY_ADMIN_KEY?>=app/all" title="Show All Application"><?=$app_count?> applications</a>.
<br />Current active application is <strong><?=$current->name?></strong><?=$current->description?' ('.$current->description.')':''?>.
<br />Authored by <strong><?=$current->author_name?></strong>.
<br />Go to <a href="<?=site::url?>?<?=EDAY_ADMIN_KEY?>=app/config/<?=$app?>">Application Config</a>, to setup performance.
</fieldset>

</div>
<?php
endOfDashboard:


