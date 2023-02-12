<?php
/**
 * authored by 9r3i
 * https://github.com/9r3i
 * started at february 1st 2023
 */
class social{
  function footer(string $res){
    $ini=WEBSITE_PLUGINS_DIRECTORY.'social/about.ini';
    $css=WEBSITE_PLUGINS_DIRECTORY.'social/social.css';
    $js=WEBSITE_PLUGINS_DIRECTORY.'social/social.js';
    $jsq=WEBSITE_PLUGINS_DIRECTORY.'social/qrcode.min.js';
    $data=@parse_ini_file($ini,true);
    $style=@file_get_contents($css);
    $script=@file_get_contents($js);
    $scriptq=@file_get_contents($jsq);
    $sharer=$data['option']['sharer']=='1';
    $like=$data['option']['like']=='1';
    $qrcode=$data['option']['qrcode']=='1';
    $option=[
      'like'=>$like,
      'sharer'=>$sharer,
      'qrcode'=>$qrcode,
    ];
    $net='<script>const SOCIAL_OPTION='
      .@json_encode($option).';</script>'
      .'<style type="text/css">'.$style.'</style>'
      .'<script>'.$scriptq.'</script>'
      .'<script>'.$script.'</script>';
    return $res.$net;
  }
}
