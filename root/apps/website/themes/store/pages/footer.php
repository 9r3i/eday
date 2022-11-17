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

</div><!-- end of website-content -->
<div style="clear:both;"></div>
</div><!-- end of website-body -->
<div style="clear:both;"></div>
<div class="foot-content" id="website-footer">
<?php $this->loadPage('foot-content'); ?>
</div>
<div class="footer" data-footer="<?=$footer?>" title="<?=$name?>"></div>
<div class="pre-load" id="pre-load"></div>
<?=$this->loadJS('menu.replace.min')?>
<?=$this->plugin()->load('footer','');?>
</body></html>
