<?php
/**
 * authored by 9r3i
 * https://github.com/9r3i
 * started at february 1st 2023
 */
class wafloat{
  function footer(string $res){
    $ini=WEBSITE_PLUGINS_DIRECTORY.'wafloat/about.ini';
    $css=WEBSITE_PLUGINS_DIRECTORY.'wafloat/style.css';
    $js=WEBSITE_PLUGINS_DIRECTORY.'wafloat/script.js';
    $data=@parse_ini_file($ini,true);
    $style=@file_get_contents($css);
    $script=@file_get_contents($js);
    $number=$data['whatsapp']['number'];
    $text=$data['whatsapp']['text'];
    $anchor=$data['whatsapp']['anchor'];
    $url='https://api.whatsapp.com/send/?phone='.$number
      .'&type=phone_number&app_absent=0';
    $image=WEBSITE_PLUGINS_PATH.'wafloat/whatsapp.png';
    $net='<div class="wafloat" id="wafloat" title="'
      .$anchor.'" data-url="'.$url.'" data-text="'.$text.'">'
      .'<span>'.$anchor.'</span>'
      .'</div>'
      .'<style type="text/css">'.$style
      .'.wafloat{background-image:url(\''.$image.'\');}'
      .'</style>'
      .'<script>'.$script.'</script>';
    return $res.$net;
  }
}

