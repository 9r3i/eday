<?php
/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain',true,401);
  exit('Error: 401 Unauthorized.');
}
/* use namespace eday site */
use eday\site;

/* prepare site name and year */
$name=$this->site->name;
$year=date('Y');
$footer="Copyright &copy; {$year}, {$name}, All Right Reserved.";
$menus=isset($this->site->menu->sidebar)?$this->site->menu->sidebar:[];
?>

</div><div class="body-sidebar" id="website-sidebar">
  <div class="menu-sidebar">
    <?php foreach($menus as $menu){ ?>
      <a href="<?=site::url.$menu->slug?>" title="<?=$menu->name?>"><div class="menu-sidebar-each"><?=$menu->name?></div></a>
    <?php } ?>
  </div>
  <div class="sidebar-content">
    <?=$this->plugin->load('sidebar','@[hit_counter]')?>
  </div>
</div>
<div style="clear:both;"></div>
</div>
<div class="foot-content">
<?php $this->loadPage('foot-content'); ?>
</div>
<div class="footer" data-footer="<?=$footer?>" title="<?=$name?>"></div>
<div class="pre-load" id="pre-load"></div>
<?=$this->loadJS('website.min')?>
<?=$this->plugin()->load('footer','');?>
</body></html>
