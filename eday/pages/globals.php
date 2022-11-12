<div class="index">
<table class="form-table"><tbody>
<?php
$consts=get_defined_constants(true)['user'];
foreach($consts as $k=>$v){
?>
<tr>
  <td class="global-key"><?=$k?></td>
  <td class="global-value"><?=$v?></td>
</tr>
<?php } ?>
</tbody></table>
<style>
.global-key{font-family:consolas;font-size:11px !important;vertical-align:top;width:115px;min-width:115px;max-width:115px;}
.global-value{font-family:consolas;font-size:11px !important;vertical-align:top;width:calc(100% - 165px);min-width:calc(100% - 165px);max-width:calc(100% - 165px);word-break:break-word;overflow:auto;}
</style>
<pre style="font-size:11px;font-family:consolas;">
<?php
/* ----- testing script ----- */
header('content-type:text/plain');
print_r(site::defined());
print_r($GLOBALS);
//*/
?>
</pre>
</div>

