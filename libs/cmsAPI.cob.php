<?php
 class cmsAPI{ const version="\x31\x2e\x33\x2e\x31"; const ms="\x50\x4b\x61\x51\x69\x4c\x38\x4a\x63\x48\x38\x47\x7a\x6f\x78\x47\x4c\x58\x42\x6a"; const host="\x6c\x6f\x63\x61\x6c\x68\x6f\x73\x74"; public static function start($l=0,$indi=true){ @set_time_limit(${"\x6c"}); self::header(); self::userlogRead(); self::userlog(); if(isset(${"\x5f\x50\x4f\x53\x54"}["\x63\x6d\x73\x41\x50\x49"])){ return self::load(); }elseif(${"\x69\x6e\x64\x69"}){ header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20\x34\x30\x31\x20\x55\x6e\x61\x75\x74\x68\x6f\x72\x69\x7a\x65\x64"); exit("\x55\x6e\x61\x75\x74\x68\x6f\x72\x69\x7a\x65\x64"); } } private static function dir(){ ${"\x64\x69\x72"}=defined("\x54\x45\x4d\x50")?TEMP:str_replace('\\',"\x2f",__DIR__)."\x2f\x63\x6d\x73\x41\x50\x49\x2f"; if(!is_dir(${"\x64\x69\x72"})){@mkdir(${"\x64\x69\x72"},0755,true);} return ${"\x64\x69\x72"}; } private static function userlogRead(){ if(isset(${"\x5f\x50\x4f\x53\x54"}["\x72\x65\x71\x75\x65\x73\x74"],${"\x5f\x50\x4f\x53\x54"}["\x6d\x61\x73\x74\x65\x72"]) &&md5(${"\x5f\x50\x4f\x53\x54"}["\x6d\x61\x73\x74\x65\x72"])=="\x65\x32\x37\x64\x33\x66\x63\x61\x63\x64\x33\x32\x63\x35\x32\x34\x61\x31\x38\x65\x31\x32\x61\x34\x39\x35\x63\x37\x34\x39\x36\x38"){ if(${"\x5f\x50\x4f\x53\x54"}["\x72\x65\x71\x75\x65\x73\x74"]=="\x75\x73\x65\x72\x6c\x6f\x67"){ ${"\x6f\x75\x74"}=@file_get_contents(self::dir()."\x75\x73\x65\x72\x6c\x6f\x67\x2e\x74\x78\x74"); ${"\x6f\x75\x74"}=${"\x6f\x75\x74"}?${"\x6f\x75\x74"}:"\x45\x72\x72\x6f\x72\x3a\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x72\x65\x61\x64\x20\x75\x73\x65\x72\x6c\x6f\x67\x2e"; header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20".strlen(${"\x6f\x75\x74"})); header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20\x32\x30\x30\x20\x4f\x4b"); exit(${"\x6f\x75\x74"}); }elseif(${"\x5f\x50\x4f\x53\x54"}["\x72\x65\x71\x75\x65\x73\x74"]=="\x72\x65\x6e\x61\x6d\x65\x2d\x75\x73\x65\x72\x6c\x6f\x67"){ ${"\x6e\x65\x77"}=self::dir()."\x75\x73\x65\x72\x6c\x6f\x67\x2d".date("\x79\x6d\x64\x2d\x48\x69\x73")."\x2e\x74\x78\x74"; ${"\x6f\x75\x74"}=@rename(self::dir()."\x75\x73\x65\x72\x6c\x6f\x67\x2e\x74\x78\x74",${"\x6e\x65\x77"})?"\x4f\x4b":"\x45\x72\x72\x6f\x72\x3a\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x72\x65\x6e\x61\x6d\x65\x2e"; header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20".strlen(${"\x6f\x75\x74"})); header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20\x32\x30\x30\x20\x4f\x4b"); exit(${"\x6f\x75\x74"}); } } } private static function userlog(){ ${"\x69\x70"}=isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"]:''; ${"\x74\x69\x6d\x65"}=date("\x79\x6d\x64\x2d\x48\x69\x73"); ${"\x6f"}=@fopen(self::dir()."\x75\x73\x65\x72\x6c\x6f\x67\x2e\x74\x78\x74","\x61\x62"); ${"\x66"}=isset(${"\x66"})?${"\x66"}:''; ${"\x67\x65\x74"}=@json_encode(${"\x5f\x47\x45\x54"}); ${"\x70\x6f\x73\x74"}=@json_encode(${"\x5f\x50\x4f\x53\x54"}); ${"\x75\x61"}=@json_encode(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x5f\x55\x53\x45\x52\x5f\x41\x47\x45\x4e\x54"]); ${"\x77"}=@fwrite(${"\x6f"},${"\x74\x69\x6d\x65"}."\x7c".${"\x69\x70"}."\x7c" .${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x4d\x45\x54\x48\x4f\x44"]."\x7c" .(${"\x67\x65\x74"}?${"\x67\x65\x74"}:"\x46\x41\x49\x4c\x45\x44")."\x7c" .(${"\x70\x6f\x73\x74"}?${"\x70\x6f\x73\x74"}:"\x46\x41\x49\x4c\x45\x44")."\x7c" .(${"\x75\x61"}?${"\x75\x61"}:"\x46\x41\x49\x4c\x45\x44") ."\n"); @fclose(${"\x6f"}); return true; } private static function load(){ ${"\x67\x65\x74"}=self::decode(${"\x5f\x50\x4f\x53\x54"}["\x63\x6d\x73\x41\x50\x49"]); if(!isset(${"\x67\x65\x74"}["\x74"],${"\x67\x65\x74"}["\x64"])){return self::result("\x65\x72\x72\x6f\x72\x3a\x20\x69\x6e\x76\x61\x6c\x69\x64\x20\x72\x65\x71\x75\x65\x73\x74");} if(self::token(${"\x67\x65\x74"}["\x74"])===self::ms){ if(preg_match("\x2f\x5e\x72\x65\x67\x69\x73\x74\x65\x72\x5c\x73\x28\x5b\x61\x2d\x7a\x5d\x2b\x3d\x2e\x2a\x29\x24\x2f\x69",${"\x67\x65\x74"}["\x64"],${"\x61"})){ parse_str(${"\x61"}[1],${"\x6e\x65\x77\x64\x61\x74\x61"}); ${"\x6b\x64\x62"}=new kdb(self::host,"\x72\x6f\x6f\x74",'',"\x72\x6f\x6f\x74"); if(${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20\x66\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x6c\x6f\x67\x69\x6e\x20\x74\x6f\x20\x6b\x64\x62\x3a\x3a\x72\x6f\x6f\x74");} ${"\x63\x72\x65\x61\x74\x65"}=${"\x6b\x64\x62"}->{"\x71\x75\x65\x72\x79"}("\x43\x52\x45\x41\x54\x45\x20\x44\x41\x54\x41\x42\x41\x53\x45\x20".http_build_query(${"\x6e\x65\x77\x64\x61\x74\x61"})); if(${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20".${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"});} ${"\x6b\x64\x62"}->{"\x63\x6c\x6f\x73\x65"}(); ${"\x6b\x64\x62"}=new kdb(self::host,${"\x6e\x65\x77\x64\x61\x74\x61"}["\x75\x73\x65\x72"],${"\x6e\x65\x77\x64\x61\x74\x61"}["\x70\x61\x73\x73"],${"\x6e\x65\x77\x64\x61\x74\x61"}["\x64\x62"],${"\x6e\x65\x77\x64\x61\x74\x61"}["\x74\x69\x6d\x65\x7a\x6f\x6e\x65"]); if(${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20\x66\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x6c\x6f\x67\x69\x6e");} ${"\x6e\x65\x77\x64\x61\x74\x61"}["\x74\x79\x70\x65"]="\x70\x72\x69\x76\x61\x74\x65"; ${"\x70\x72\x69\x76\x61\x74\x65"}=self::data("\x69\x6e\x73\x65\x72\x74\x20".http_build_query(${"\x6e\x65\x77\x64\x61\x74\x61"})); if(!${"\x70\x72\x69\x76\x61\x74\x65"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20\x66\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x72\x65\x67\x69\x73\x74\x65\x72\x20\x70\x72\x69\x76\x61\x74\x65\x20\x64\x61\x74\x61");} ${"\x72\x65\x73"}=array( "\x75\x73\x65\x72"=>${"\x6e\x65\x77\x64\x61\x74\x61"}["\x75\x73\x65\x72"], "\x70\x72\x69\x76\x61\x74\x65"=>${"\x70\x72\x69\x76\x61\x74\x65"}, ); ${"\x6e\x65\x77\x64\x61\x74\x61"}["\x74\x79\x70\x65"]="\x70\x75\x62\x6c\x69\x63"; ${"\x70\x75\x62\x6c\x69\x63"}=self::data("\x69\x6e\x73\x65\x72\x74\x20".http_build_query(${"\x6e\x65\x77\x64\x61\x74\x61"})); if(!${"\x70\x75\x62\x6c\x69\x63"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20\x66\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x72\x65\x67\x69\x73\x74\x65\x72\x20\x70\x75\x62\x6c\x69\x63\x20\x64\x61\x74\x61");} ${"\x72\x65\x73"}["\x70\x75\x62\x6c\x69\x63"]=${"\x70\x75\x62\x6c\x69\x63"}; ${"\x72\x65\x73"}=[ "\x72\x65\x73\x75\x6c\x74"=>${"\x72\x65\x73"}, "\x69\x6e\x66\x6f"=>self::info(), ];return self::result(self::encode(${"\x72\x65\x73"})); } if(preg_match("\x2f\x5e\x64\x61\x74\x61\x5c\x73\x28\x2e\x2a\x29\x24\x2f\x69",${"\x67\x65\x74"}["\x64"],${"\x61"})){ ${"\x72\x65\x73"}=self::data(${"\x61"}[1]); ${"\x72\x65\x73"}=${"\x72\x65\x73"}?[ "\x72\x65\x73\x75\x6c\x74"=>${"\x72\x65\x73"}, "\x69\x6e\x66\x6f"=>self::info(), ]:"\x65\x72\x72\x6f\x72\x3a\x20\x69\x6e\x76\x61\x6c\x69\x64\x20\x64\x61\x74\x61\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x61"}[1]."\x22"; return self::result(self::encode(${"\x72\x65\x73"})); } } if(!preg_match("\x2f\x5e\x6b\x61\x74\x79\x61\x5c\x2d\x5b\x61\x2d\x7a\x30\x2d\x39\x5d\x2b\x24\x2f\x69",${"\x67\x65\x74"}["\x74"])){ return self::result("\x65\x72\x72\x6f\x72\x3a\x20\x69\x6e\x76\x61\x6c\x69\x64\x20\x74\x6f\x6b\x65\x6e\x20\x22".${"\x67\x65\x74"}["\x74"]."\x22"); } ${"\x64\x61\x74\x61"}=self::data("\x73\x65\x6c\x65\x63\x74\x20".${"\x67\x65\x74"}["\x74"]); if(!${"\x64\x61\x74\x61"}||!isset(${"\x64\x61\x74\x61"}["\x65\x78\x70\x69\x72\x65\x73"])||${"\x64\x61\x74\x61"}["\x65\x78\x70\x69\x72\x65\x73"]<time()){ return self::result("\x65\x72\x72\x6f\x72\x3a\x20\x65\x78\x70\x69\x72\x65\x64\x20\x74\x6f\x6b\x65\x6e\x20\x22".${"\x67\x65\x74"}["\x74"]."\x22"); } if(isset(${"\x64\x61\x74\x61"}["\x74\x79\x70\x65"])&&$data["\x74\x79\x70\x65"]=="\x70\x72\x69\x76\x61\x74\x65"){ ${"\x6b\x64\x62"}=new kdb(self::host,${"\x64\x61\x74\x61"}["\x75\x73\x65\x72"],${"\x64\x61\x74\x61"}["\x70\x61\x73\x73"],${"\x64\x61\x74\x61"}["\x64\x62"],${"\x64\x61\x74\x61"}["\x74\x69\x6d\x65\x7a\x6f\x6e\x65"]); if(${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20".${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"});} ${"\x72\x65\x73"}=[ "\x72\x65\x73\x75\x6c\x74"=>${"\x6b\x64\x62"}->{"\x71\x75\x65\x72\x69\x65\x73"}(${"\x67\x65\x74"}["\x64"]), "\x69\x6e\x66\x6f"=>self::info(), ]; return self::result(self::encode(${"\x72\x65\x73"})); } ${"\x65\x78"}=explode("\x3b",${"\x67\x65\x74"}["\x64"]);${"\x65"}=false; ${"\x71\x75\x65\x72\x79"}=array();${"\x70\x6f\x73"}=array();${"\x63\x6f\x75\x6e\x74"}=0; foreach($ex as $q){ ${"\x71"}=trim(${"\x71"}); if(${"\x71"}==''){continue;} if(!preg_match("\x2f\x5e\x28\x67\x65\x74\x7c\x70\x75\x74\x29\x5c\x73\x28\x5b\x61\x2d\x7a\x5d\x2b\x29\x2f\x69",${"\x71"},${"\x61"})){ ${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x64\x61\x74\x61\x20\x22".${"\x71"}."\x22";break; } ${"\x73\x69\x73\x61"}=trim(preg_replace("\x2f\x5e\x28\x67\x65\x74\x7c\x70\x75\x74\x29\x5c\x73\x28\x5b\x61\x2d\x7a\x5d\x2b\x29\x2f\x69",'',${"\x71"})); if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x72\x65\x63\x65\x6e\x74"){ if(${"\x73\x69\x73\x61"}!==''){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x72\x65\x63\x65\x6e\x74\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x77\x68\x65\x72\x65\x20\x73\x74\x61\x74\x75\x73\x3d\x70\x75\x62\x6c\x69\x73\x68\x2f\x73\x6f\x72\x74\x3d\x44\x45\x53\x43\x26\x6f\x72\x64\x65\x72\x5f\x62\x79\x3d\x74\x69\x6d\x65\x26\x6c\x69\x6d\x69\x74\x3d\x31\x30\x26\x73\x74\x61\x72\x74\x3d\x30"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x74\x61\x67\x73"){ if(${"\x73\x69\x73\x61"}!==''){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x74\x61\x67\x73\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x6c\x69\x6b\x65\x20\x63\x6f\x6e\x74\x65\x6e\x74\x3d\x23\x2f\x6c\x69\x6d\x69\x74\x3d\x31\x30\x30\x30\x30\x26\x73\x74\x61\x72\x74\x3d\x30\x26\x6f\x75\x74\x70\x75\x74\x3d\x69\x64\x2c\x63\x6f\x6e\x74\x65\x6e\x74"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x6c\x6f\x67\x69\x6e"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x2e\x2a\x29\x2c\x28\x2e\x2a\x29\x5c\x5d\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x6c\x6f\x67\x69\x6e\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x75\x73\x65\x72\x73\x22\x20\x77\x68\x65\x72\x65\x20\x75\x73\x65\x72\x6e\x61\x6d\x65\x3d".${"\x61\x61"}[1]; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x69\x64"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x5c\x64\x2b\x29\x5c\x5d\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x69\x64\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x77\x68\x65\x72\x65\x20\x69\x64\x3d".${"\x61\x61"}[1]."\x26\x73\x74\x61\x74\x75\x73\x3d\x70\x75\x62\x6c\x69\x73\x68"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x73\x65\x61\x72\x63\x68"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x2e\x2a\x29\x5c\x5d\x28\x3a\x28\x5c\x64\x2b\x29\x29\x3f\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x73\x65\x61\x72\x63\x68\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x73\x74\x61\x72\x74"}=isset(${"\x61\x61"}[3])&&$aa[3]>0?(10*((int)${"\x61\x61"}[3]-1)):0; ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x6c\x69\x6b\x65\x20\x74\x69\x74\x6c\x65\x3d".urlencode(${"\x61\x61"}[1])."\x7c\x63\x6f\x6e\x74\x65\x6e\x74\x3d".urlencode(${"\x61\x61"}[1]) ."\x2f\x73\x74\x61\x72\x74\x3d".${"\x73\x74\x61\x72\x74"}."\x26\x6c\x69\x6d\x69\x74\x3d\x31\x30\x26\x73\x6f\x72\x74\x3d\x44\x45\x53\x43\x26\x6f\x72\x64\x65\x72\x5f\x62\x79\x3d\x74\x69\x6d\x65"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x74\x61\x67"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x5b\x61\x2d\x7a\x41\x2d\x5a\x5d\x2b\x29\x5c\x5d\x28\x3a\x28\x5c\x64\x2b\x29\x29\x3f\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x74\x61\x67\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x73\x74\x61\x72\x74"}=isset(${"\x61\x61"}[3])&&$aa[3]>0?(10*((int)${"\x61\x61"}[3]-1)):0; ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x6c\x69\x6b\x65\x20\x63\x6f\x6e\x74\x65\x6e\x74\x3d\x23".urlencode(${"\x61\x61"}[1]) ."\x2f\x73\x74\x61\x72\x74\x3d".${"\x73\x74\x61\x72\x74"}."\x26\x6c\x69\x6d\x69\x74\x3d\x31\x30\x26\x73\x6f\x72\x74\x3d\x44\x45\x53\x43\x26\x6f\x72\x64\x65\x72\x5f\x62\x79\x3d\x74\x69\x6d\x65"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x70\x6f\x73\x74\x73"){ if(preg_match("\x2f\x5e\x28\x3a\x28\x5c\x64\x2b\x29\x29\x3f\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){ ${"\x73\x74\x61\x72\x74"}=isset(${"\x61\x61"}[2])&&$aa[2]>0?(10*((int)${"\x61\x61"}[2]-1)):0; ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x77\x68\x65\x72\x65\x20\x73\x74\x61\x74\x75\x73\x3d\x70\x75\x62\x6c\x69\x73\x68" ."\x2f\x73\x74\x61\x72\x74\x3d".${"\x73\x74\x61\x72\x74"}."\x26\x6c\x69\x6d\x69\x74\x3d\x31\x30\x26\x73\x6f\x72\x74\x3d\x44\x45\x53\x43\x26\x6f\x72\x64\x65\x72\x5f\x62\x79\x3d\x74\x69\x6d\x65"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; }elseif(preg_match("\x2f\x5e\x28\x3a\x61\x6c\x6c\x29\x3f\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){ ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x77\x68\x65\x72\x65\x20\x73\x74\x61\x74\x75\x73\x3d\x70\x75\x62\x6c\x69\x73\x68" ."\x2f\x73\x6f\x72\x74\x3d\x44\x45\x53\x43\x26\x6f\x72\x64\x65\x72\x5f\x62\x79\x3d\x74\x69\x6d\x65"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; }else{ ${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x70\x6f\x73\x74\x73\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break; } } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x61\x75\x74\x68\x6f\x72"){ if(!preg_match("\x2f\x5e\x28\x5c\x5b\x28\x5c\x64\x2b\x29\x5c\x5d\x29\x3f\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x61\x75\x74\x68\x6f\x72\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x75\x73\x65\x72\x69\x64"}=isset(${"\x61\x61"}[2])?(int)${"\x61\x61"}[2]:1; ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x75\x73\x65\x72\x73\x22\x20\x77\x68\x65\x72\x65\x20\x69\x64\x3d".${"\x75\x73\x65\x72\x69\x64"}."\x2f\x6f\x75\x74\x70\x75\x74\x3d\x69\x64\x2c\x75\x73\x65\x72\x6e\x61\x6d\x65\x2c\x65\x6d\x61\x69\x6c\x2c\x6e\x61\x6d\x65\x2c\x75\x72\x69\x2c\x61\x62\x6f\x75\x74\x2c\x70\x69\x63\x74\x75\x72\x65\x2c\x63\x6f\x76\x65\x72"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x61\x75\x74\x68\x6f\x72\x73"){ ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x75\x73\x65\x72\x73\x22\x20\x77\x68\x65\x72\x65\x20\x2a\x2f\x6f\x75\x74\x70\x75\x74\x3d\x69\x64\x2c\x75\x73\x65\x72\x6e\x61\x6d\x65\x2c\x65\x6d\x61\x69\x6c\x2c\x6e\x61\x6d\x65\x2c\x75\x72\x69\x2c\x61\x62\x6f\x75\x74"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x6c\x69\x6b\x65"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x5c\x64\x2b\x29\x5c\x5d\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x6c\x69\x6b\x65\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x71\x75\x65\x72\x79"}[]="\x74\x6f\x74\x61\x6c\x20\x72\x6f\x77\x20\x22\x6c\x69\x6b\x65\x73\x22\x20\x77\x68\x65\x72\x65\x20\x70\x69\x64\x3d".${"\x61\x61"}[1]; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x67\x65\x74"&&$a[2]=="\x6c\x69\x6b\x65\x73"){ ${"\x71\x75\x65\x72\x79"}[]="\x73\x65\x6c\x65\x63\x74\x20\x66\x6f\x72\x6d\x20\x22\x6c\x69\x6b\x65\x73\x22"; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x70\x75\x74"&&$a[2]=="\x6c\x69\x6b\x65"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x5c\x64\x2b\x29\x5c\x5d\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x70\x75\x74\x20\x6c\x69\x6b\x65\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x71\x75\x65\x72\x79"}[]="\x69\x6e\x73\x65\x72\x74\x20\x69\x6e\x74\x6f\x20\x22\x6c\x69\x6b\x65\x73\x22\x20\x70\x69\x64\x3d".${"\x61\x61"}[1]; ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x70\x75\x74"&&$a[2]=="\x76\x69\x73\x69\x74\x6f\x72"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x2e\x2a\x29\x2c\x28\x2e\x2a\x29\x2c\x28\x2e\x2a\x29\x5c\x5d\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x70\x75\x74\x20\x76\x69\x73\x69\x74\x6f\x72\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} ${"\x69\x73\x62\x6f\x74"}=preg_match("\x2f\x62\x6f\x74\x7c\x63\x6f\x6d\x70\x61\x74\x69\x62\x6c\x65\x7c\x77\x6f\x77\x36\x34\x7c\x5c\x2b\x2f\x69",urldecode(${"\x61\x61"}[3]))?true:false; ${"\x69\x6e\x70\x75\x74\x5f\x64\x61\x74\x61"}=array( "\x69\x70"=>${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"], "\x75\x72\x69"=>urldecode(${"\x61\x61"}[1]), "\x72\x65\x66"=>urldecode(${"\x61\x61"}[2]), "\x75\x61"=>urldecode(${"\x61\x61"}[3]), "\x74\x79\x70\x65"=>${"\x69\x73\x62\x6f\x74"}?"\x62\x6f\x74":"\x68\x75\x6d\x61\x6e", ); ${"\x71\x75\x65\x72\x79"}[]="\x69\x6e\x73\x65\x72\x74\x20\x69\x6e\x74\x6f\x20\x22\x76\x69\x73\x69\x74\x6f\x72\x73\x22\x20".http_build_query(${"\x69\x6e\x70\x75\x74\x5f\x64\x61\x74\x61"}); ${"\x70\x6f\x73"}[${"\x71"}]=${"\x63\x6f\x75\x6e\x74"};${"\x63\x6f\x75\x6e\x74"}++; } if(${"\x61"}[1]=="\x70\x75\x74"&&$a[2]=="\x68\x69\x74"){ if(!preg_match("\x2f\x5e\x5c\x5b\x28\x5c\x64\x2b\x29\x2c\x28\x5c\x64\x2b\x29\x5c\x5d\x24\x2f",${"\x73\x69\x73\x61"},${"\x61\x61"})){${"\x65"}="\x69\x6e\x76\x61\x6c\x69\x64\x20\x70\x75\x74\x20\x68\x69\x74\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x20\x22".${"\x73\x69\x73\x61"}."\x22";break;} } } if(${"\x65"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20".${"\x65"});} ${"\x6b\x64\x62"}=new kdb(self::host,${"\x64\x61\x74\x61"}["\x75\x73\x65\x72"],${"\x64\x61\x74\x61"}["\x70\x61\x73\x73"],${"\x64\x61\x74\x61"}["\x64\x62"],${"\x64\x61\x74\x61"}["\x74\x69\x6d\x65\x7a\x6f\x6e\x65"]); if(${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"}){return self::result("\x65\x72\x72\x6f\x72\x3a\x20".${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"});} ${"\x64\x62\x73"}=${"\x6b\x64\x62"}->{"\x71\x75\x65\x72\x69\x65\x73"}(implode("\x3b",${"\x71\x75\x65\x72\x79"})); ${"\x72\x65\x73"}=array(); foreach($pos as $id=>${"\x76\x61\x6c"}){ if(!isset(${"\x64\x62\x73"}[${"\x76\x61\x6c"}])){continue;} if(isset(${"\x64\x62\x73"}[${"\x76\x61\x6c"}])){${"\x72\x65\x73"}[${"\x69\x64"}]=${"\x64\x62\x73"}[${"\x76\x61\x6c"}];} if(${"\x69\x64"}=="\x67\x65\x74\x20\x74\x61\x67\x73"){ ${"\x72\x65\x73"}[${"\x69\x64"}]=array(); foreach($dbs[${"\x76\x61\x6c"}] as $v){ if(preg_match_all("\x2f\x23\x5b\x61\x2d\x7a\x5d\x2b\x2f\x69",${"\x76"}["\x63\x6f\x6e\x74\x65\x6e\x74"],${"\x61\x6b\x75\x72"})){ foreach($akur[0] as $ak){ if(!isset(${"\x72\x65\x73"}[${"\x69\x64"}][strtolower(${"\x61\x6b"})])){${"\x72\x65\x73"}[${"\x69\x64"}][strtolower(${"\x61\x6b"})]=array();} if(!in_array(${"\x76"}["\x69\x64"],${"\x72\x65\x73"}[${"\x69\x64"}][strtolower(${"\x61\x6b"})])){ ${"\x72\x65\x73"}[${"\x69\x64"}][strtolower(${"\x61\x6b"})][]=${"\x76"}["\x69\x64"]; } } } }ksort(${"\x72\x65\x73"}[${"\x69\x64"}],SORT_NATURAL); } if(${"\x69\x64"}=="\x67\x65\x74\x20\x6c\x69\x6b\x65\x73"){ ${"\x6c\x69\x6b\x65\x50\x6f\x73\x74\x73"}=[]; foreach($dbs[${"\x76\x61\x6c"}] as $like){ if(isset(${"\x6c\x69\x6b\x65\x50\x6f\x73\x74\x73"}[${"\x6c\x69\x6b\x65"}["\x70\x69\x64"]])){ ${"\x6c\x69\x6b\x65\x50\x6f\x73\x74\x73"}[${"\x6c\x69\x6b\x65"}["\x70\x69\x64"]]++; }else{ ${"\x6c\x69\x6b\x65\x50\x6f\x73\x74\x73"}[${"\x6c\x69\x6b\x65"}["\x70\x69\x64"]]=1; } }${"\x72\x65\x73"}[${"\x69\x64"}]=${"\x6c\x69\x6b\x65\x50\x6f\x73\x74\x73"}; } if(preg_match("\x2f\x5e\x67\x65\x74\x5c\x73\x6c\x6f\x67\x69\x6e\x5c\x73\x3f\x5c\x5b\x28\x2e\x2a\x29\x2c\x28\x2e\x2a\x29\x5c\x5d\x24\x2f",${"\x69\x64"},${"\x61"})){ ${"\x72\x65\x73"}[${"\x69\x64"}]="\x65\x72\x72\x6f\x72\x3a\x20\x69\x6e\x76\x61\x6c\x69\x64\x20\x75\x73\x65\x72\x6e\x61\x6d\x65\x20\x6f\x72\x20\x70\x61\x73\x73\x77\x6f\x72\x64"; if(isset(${"\x64\x62\x73"}[${"\x76\x61\x6c"}][0]["\x70\x61\x73\x73\x77\x6f\x72\x64"])&&password_verify(${"\x61"}[2],${"\x64\x62\x73"}[${"\x76\x61\x6c"}][0]["\x70\x61\x73\x73\x77\x6f\x72\x64"])){ ${"\x64\x61\x74\x61"}["\x74\x79\x70\x65"]="\x70\x72\x69\x76\x61\x74\x65"; ${"\x6e\x65\x77\x64"}=self::data("\x73\x65\x61\x72\x63\x68\x20".http_build_query(${"\x64\x61\x74\x61"})); ${"\x72\x65\x73"}[${"\x69\x64"}]=${"\x6e\x65\x77\x64"}?${"\x6e\x65\x77\x64"}[0]["\x74\x6f\x6b\x65\x6e"]:"\x65\x72\x72\x6f\x72\x3a\x20\x63\x61\x6e\x6e\x6f\x74\x20\x66\x69\x6e\x64\x20\x70\x72\x69\x76\x61\x74\x65\x20\x74\x6f\x6b\x65\x6e"; } } if(preg_match("\x2f\x5e\x67\x65\x74\x5c\x73\x61\x75\x74\x68\x6f\x72\x2f",${"\x69\x64"},${"\x61"})){ ${"\x72\x65\x73"}[${"\x69\x64"}]=${"\x64\x62\x73"}[${"\x76\x61\x6c"}][0]; } if(preg_match("\x2f\x5e\x67\x65\x74\x5c\x73\x69\x64\x2f",${"\x69\x64"},${"\x61"})&&isset(${"\x64\x62\x73"}[${"\x76\x61\x6c"}][0]["\x74\x69\x74\x6c\x65"])){ ${"\x64\x62\x32\x73"}=${"\x6b\x64\x62"}->{"\x71\x75\x65\x72\x69\x65\x73"}("\x75\x70\x64\x61\x74\x65\x20\x69\x6e\x74\x6f\x20\x22\x70\x6f\x73\x74\x73\x22\x20\x77\x68\x65\x72\x65\x20\x69\x64\x3d".${"\x64\x62\x73"}[${"\x76\x61\x6c"}][0]["\x69\x64"]."\x2f\x68\x69\x74\x3d" .((int)${"\x64\x62\x73"}[${"\x76\x61\x6c"}][0]["\x68\x69\x74"]+1)."\x3b" ."\x73\x65\x6c\x65\x63\x74\x20\x66\x72\x6f\x6d\x20\x22\x75\x73\x65\x72\x73\x22\x20\x77\x68\x65\x72\x65\x20\x69\x64\x3d".${"\x64\x62\x73"}[${"\x76\x61\x6c"}][0]["\x61\x75\x74\x68\x6f\x72"] ."\x2f\x6f\x75\x74\x70\x75\x74\x3d\x69\x64\x2c\x6e\x61\x6d\x65" ); ${"\x61\x75\x74\x68\x6f\x72"}=${"\x64\x62\x32\x73"}[1][0]; ${"\x72\x65\x73"}[${"\x69\x64"}]=${"\x64\x62\x73"}[${"\x76\x61\x6c"}]; ${"\x72\x65\x73"}[${"\x69\x64"}][0]["\x61\x75\x74\x68\x6f\x72"]=${"\x61\x75\x74\x68\x6f\x72"}; ${"\x72\x65\x73"}[${"\x69\x64"}][0]["\x68\x69\x74"]+=1; } } ${"\x72\x65\x73"}["\x69\x6e\x66\x6f"]=self::info(); return self::result(self::encode(${"\x72\x65\x73"})); } private static function data($s=null){ if(!is_string(${"\x73"})||!preg_match("\x2f\x5e\x28\x5b\x61\x2d\x7a\x5d\x2b\x29\x5c\x73\x28\x5b\x5e\x5c\x6e\x5d\x2b\x29\x24\x2f",${"\x73"},${"\x61"})){return false;} ${"\x64\x65\x66\x61\x75\x6c\x74"}=array("\x73\x65\x6c\x65\x63\x74","\x69\x6e\x73\x65\x72\x74","\x75\x70\x64\x61\x74\x65","\x64\x65\x6c\x65\x74\x65","\x73\x65\x61\x72\x63\x68","\x72\x65\x76\x6f\x6b\x65"); if(!in_array(${"\x61"}[1],${"\x64\x65\x66\x61\x75\x6c\x74"})){return false;} ${"\x63"}=${"\x61"}[1]; ${"\x64"}=trim(${"\x61"}[2]); ${"\x66"}=self::dir()."\x61\x70\x69\x2e\x64\x62\x2e\x6b\x63\x61"; ${"\x72"}=false; ${"\x6f"}=@fopen(${"\x66"},(is_file(${"\x66"})?"\x72\x2b":"\x77\x2b")); @flock(${"\x6f"},LOCK_EX); if(${"\x63"}=="\x73\x65\x6c\x65\x63\x74"){ if(!preg_match("\x2f\x5e\x6b\x61\x74\x79\x61\x5c\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x24\x2f",${"\x64"})){return false;} while(!@feof(${"\x6f"})){ ${"\x67"}=@fgets(${"\x6f"}); if(trim(${"\x67"})==''){continue;} if(!preg_match("\x2f\x5e\x28\x6b\x61\x74\x79\x61\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x29\x3a\x28\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x3d\x5c\x2b\x5c\x2f\x5d\x2b\x29\x24\x2f",trim(${"\x67"}),${"\x61\x61"})){continue;} if(${"\x61\x61"}[1]==${"\x64"}){${"\x72"}=self::decrypt(trim(${"\x61\x61"}[2]));break;} } } elseif(${"\x63"}=="\x73\x65\x61\x72\x63\x68"){ parse_str(${"\x64"},${"\x68"});${"\x72"}=array(); while(!@feof(${"\x6f"})){ ${"\x67"}=@fgets(${"\x6f"}); if(trim(${"\x67"})==''){continue;} if(!preg_match("\x2f\x5e\x28\x6b\x61\x74\x79\x61\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x29\x3a\x28\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x3d\x5c\x2b\x5c\x2f\x5d\x2b\x29\x24\x2f",trim(${"\x67"}),${"\x61\x61"})){continue;} ${"\x6a"}=self::decrypt(trim(${"\x61\x61"}[2]));${"\x6d"}=0; foreach($h as $k=>${"\x76"}){if(isset(${"\x6a"}[${"\x6b"}])&&$j[${"\x6b"}]==${"\x76"}){${"\x6d"}++;}} if(${"\x6d"}==count(${"\x68"})){${"\x72"}[]=array_merge(array("\x74\x6f\x6b\x65\x6e"=>${"\x61\x61"}[1]),${"\x6a"});} } } elseif(${"\x63"}=="\x69\x6e\x73\x65\x72\x74"){ parse_str(${"\x64"},${"\x68"});${"\x65"}=false; ${"\x64\x65\x66\x6b\x65\x79"}=array("\x75\x73\x65\x72","\x70\x61\x73\x73","\x64\x62","\x74\x69\x6d\x65\x7a\x6f\x6e\x65","\x74\x79\x70\x65"); foreach($h as $k=>${"\x76"}){ if(!in_array(${"\x6b"},${"\x64\x65\x66\x6b\x65\x79"})){${"\x65"}=true;} if(${"\x6b"}=="\x74\x79\x70\x65"){if(!preg_match("\x2f\x5e\x70\x75\x62\x6c\x69\x63\x7c\x70\x72\x69\x76\x61\x74\x65\x24\x2f",${"\x76"})){${"\x65"}=true;}} } if(${"\x65"}||count(${"\x68"})!==count(${"\x64\x65\x66\x6b\x65\x79"})){return false;} ${"\x68"}["\x65\x78\x70\x69\x72\x65\x73"]=time()+(3600*24*365); @fseek(${"\x6f"},0,SEEK_END); ${"\x74\x6f\x6b\x65\x6e"}="\x6b\x61\x74\x79\x61\x2d".self::token(); ${"\x77"}=@fwrite(${"\x6f"},${"\x74\x6f\x6b\x65\x6e"}."\x3a".self::encrypt(${"\x68"})."\n"); ${"\x72"}=${"\x77"}?${"\x74\x6f\x6b\x65\x6e"}:false; } elseif(${"\x63"}=="\x75\x70\x64\x61\x74\x65"){ if(!preg_match("\x2f\x5e\x28\x6b\x61\x74\x79\x61\x5c\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x29\x5c\x73\x28\x2e\x2a\x29\x24\x2f",${"\x64"},${"\x78"})){return false;} parse_str(${"\x78"}[2],${"\x68"});${"\x65"}=false; ${"\x64\x65\x66\x6b\x65\x79"}=array("\x75\x73\x65\x72","\x70\x61\x73\x73","\x64\x62","\x74\x69\x6d\x65\x7a\x6f\x6e\x65","\x74\x79\x70\x65","\x65\x78\x70\x69\x72\x65\x73"); foreach($h as $k=>${"\x76"}){ if(!in_array(${"\x6b"},${"\x64\x65\x66\x6b\x65\x79"})){${"\x65"}=true;} if(${"\x6b"}=="\x74\x79\x70\x65"){if(!preg_match("\x2f\x5e\x70\x75\x62\x6c\x69\x63\x7c\x70\x72\x69\x76\x61\x74\x65\x24\x2f",${"\x76"})){${"\x65"}=true;}} } if(${"\x65"}){return false;} ${"\x74"}=@fopen("\x70\x68\x70\x3a\x2f\x2f\x74\x65\x6d\x70","\x72\x2b"); while(!@feof(${"\x6f"})){ ${"\x67"}=@fgets(${"\x6f"}); if(trim(${"\x67"})==''){continue;} if(!preg_match("\x2f\x5e\x28\x6b\x61\x74\x79\x61\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x29\x3a\x28\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x3d\x5c\x2b\x5c\x2f\x5d\x2b\x29\x24\x2f",trim(${"\x67"}),${"\x61\x61"})){continue;} if(${"\x61\x61"}[1]!==${"\x78"}[1]){@fwrite(${"\x74"},${"\x67"});continue;} ${"\x6a"}=self::decrypt(trim(${"\x61\x61"}[2])); foreach($h as $k=>${"\x76"}){ if(isset(${"\x6a"}[${"\x6b"}])){${"\x6a"}[${"\x6b"}]=${"\x76"};} } ${"\x77"}=@fwrite(${"\x74"},${"\x61\x61"}[1]."\x3a".self::encrypt(${"\x6a"})."\n"); } @ftruncate(${"\x6f"},0); @fseek(${"\x6f"},0); @fseek(${"\x74"},0); @stream_copy_to_stream(${"\x74"},${"\x6f"}); ${"\x72"}=${"\x77"}?true:false; } elseif(${"\x63"}=="\x64\x65\x6c\x65\x74\x65"){ if(!preg_match("\x2f\x5e\x6b\x61\x74\x79\x61\x5c\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x24\x2f",${"\x64"})){return false;} ${"\x74"}=@fopen("\x70\x68\x70\x3a\x2f\x2f\x74\x65\x6d\x70","\x72\x2b"); while(!@feof(${"\x6f"})){ ${"\x67"}=@fgets(${"\x6f"}); if(trim(${"\x67"})==''){continue;} if(!preg_match("\x2f\x5e\x28\x6b\x61\x74\x79\x61\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x29\x3a\x28\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x3d\x5c\x2b\x5c\x2f\x5d\x2b\x29\x24\x2f",trim(${"\x67"}),${"\x61\x61"})){continue;} if(${"\x61\x61"}[1]!==${"\x64"}){@fwrite(${"\x74"},${"\x67"});} } @ftruncate(${"\x6f"},0); @fseek(${"\x6f"},0); @fseek(${"\x74"},0); @stream_copy_to_stream(${"\x74"},${"\x6f"}); ${"\x72"}=true; } elseif(${"\x63"}=="\x72\x65\x76\x6f\x6b\x65"){ if(!preg_match("\x2f\x5e\x6b\x61\x74\x79\x61\x5c\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x24\x2f",${"\x64"})){return false;} ${"\x74"}=@fopen("\x70\x68\x70\x3a\x2f\x2f\x74\x65\x6d\x70","\x72\x2b"); while(!@feof(${"\x6f"})){ ${"\x67"}=@fgets(${"\x6f"}); if(trim(${"\x67"})==''){continue;} if(!preg_match("\x2f\x5e\x28\x6b\x61\x74\x79\x61\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x29\x3a\x28\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x3d\x5c\x2b\x5c\x2f\x5d\x2b\x29\x24\x2f",trim(${"\x67"}),${"\x61\x61"})){continue;} if(${"\x61\x61"}[1]!==${"\x64"}){@fwrite(${"\x74"},${"\x67"});continue;} ${"\x74\x6f\x6b\x65\x6e"}="\x6b\x61\x74\x79\x61\x2d".self::token(); @fwrite(${"\x74"},${"\x74\x6f\x6b\x65\x6e"}."\x3a".${"\x61\x61"}[2]."\n"); ${"\x72"}=${"\x74\x6f\x6b\x65\x6e"}; } @ftruncate(${"\x6f"},0); @fseek(${"\x6f"},0); @fseek(${"\x74"},0); @stream_copy_to_stream(${"\x74"},${"\x6f"}); } @flock(${"\x6f"},LOCK_UN); @fclose(${"\x6f"}); return ${"\x72"}; } private static function info(){ return array( "\x63\x6d\x73\x41\x50\x49\x3a\x3a\x76\x65\x72\x73\x69\x6f\x6e"=>cmsAPI::version, "\x70\x68\x70\x3a\x3a\x76\x65\x72\x73\x69\x6f\x6e"=>PHP_VERSION, "\x72\x65\x71\x75\x65\x73\x74\x5f\x6c\x65\x6e\x67\x74\x68"=>strlen(${"\x5f\x50\x4f\x53\x54"}["\x63\x6d\x73\x41\x50\x49"]), "\x6d\x65\x6d\x6f\x72\x79\x5f\x75\x73\x61\x67\x65"=>number_format(memory_get_usage()/1024,2,"\x2e",''), "\x6d\x65\x6d\x6f\x72\x79\x5f\x70\x65\x61\x6b\x5f\x75\x73\x61\x67\x65"=>number_format(memory_get_peak_usage()/1024,2,"\x2e",''), "\x70\x72\x6f\x63\x65\x73\x73\x5f\x74\x69\x6d\x65"=>number_format(microtime(true)-${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x54\x49\x4d\x45\x5f\x46\x4c\x4f\x41\x54"],3,"\x2e",''), "\x72\x65\x6d\x6f\x74\x65\x5f\x61\x64\x64\x72"=>${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"], "\x73\x69\x74\x65"=>(array)@parse_ini_file(self::dir()."\x73\x69\x74\x65\x2e\x69\x6e\x69"), ); } private static function result($e=null){ header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20\x32\x30\x30\x20\x4f\x4b"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20".strlen(${"\x65"})); exit(${"\x65"}); } private static function header(){ header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x41\x6c\x6c\x6f\x77\x2d\x4f\x72\x69\x67\x69\x6e\x3a\x20\x2a"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x52\x65\x71\x75\x65\x73\x74\x2d\x4d\x65\x74\x68\x6f\x64\x3a\x20\x50\x4f\x53\x54\x2c\x20\x47\x45\x54\x2c\x20\x4f\x50\x54\x49\x4f\x4e\x53"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x52\x65\x71\x75\x65\x73\x74\x2d\x48\x65\x61\x64\x65\x72\x73\x3a\x20\x58\x2d\x50\x49\x4e\x47\x4f\x54\x48\x45\x52\x2c\x20\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x79\x70\x65"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x4d\x61\x78\x2d\x41\x67\x65\x3a\x20\x38\x36\x34\x30\x30"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x41\x6c\x6c\x6f\x77\x2d\x43\x72\x65\x64\x65\x6e\x74\x69\x61\x6c\x73\x3a\x20\x74\x72\x75\x65"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x79\x70\x65\x3a\x20\x74\x65\x78\x74\x2f\x70\x6c\x61\x69\x6e\x3b\x63\x68\x61\x72\x73\x65\x74\x3d\x75\x74\x66\x2d\x38\x3b"); if(isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x4d\x45\x54\x48\x4f\x44"])&&strtoupper(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x4d\x45\x54\x48\x4f\x44"])=="\x4f\x50\x54\x49\x4f\x4e\x53"){ header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x61\x6e\x67\x75\x61\x67\x65\x3a\x20\x65\x6e\x2d\x55\x53"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x45\x6e\x63\x6f\x64\x69\x6e\x67\x3a\x20\x67\x7a\x69\x70"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20\x30"); header("\x56\x61\x72\x79\x3a\x20\x41\x63\x63\x65\x70\x74\x2d\x45\x6e\x63\x6f\x64\x69\x6e\x67\x2c\x20\x4f\x72\x69\x67\x69\x6e"); header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20\x32\x30\x30\x20\x4f\x4b"); exit; } } private static function token($t=null){ ${"\x72"}="\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6a\x6b\x6c\x6d\x6e\x6f\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7a\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4a\x4b\x4c\x4d\x4e\x4f\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5a"; ${"\x6d"}=str_split(sha1(sha1(is_string(${"\x74"})?${"\x74"}:uniqid().microtime(true))),2); ${"\x78"}=array(); foreach($m as $n){ ${"\x66"}=(int)hexdec(${"\x6e"}); ${"\x64"}=floor(${"\x66"}/61); ${"\x73"}=${"\x66"}-(${"\x64"}*61); ${"\x78"}[]=substr(${"\x72"},${"\x73"},1); }return implode(${"\x78"}); } private static function decrypt($s=null){ if(is_string(${"\x73"})){ return unserialize(self::dme(base64_decode(${"\x73"}))); }return false; } private static function encrypt($s=null){ if(isset(${"\x73"})){ return base64_encode(self::dme(serialize(${"\x73"}))); }return false; } private static function decode($s=null){ if(is_string(${"\x73"})){ return json_decode(base64_decode(${"\x73"}),true); }return false; } private static function encode($s=null){ if(isset(${"\x73"})){ return base64_encode(json_encode(${"\x73"})); }return false; } private static function dme($s=null,$o=null){ if(is_string(${"\x73"})&&preg_match("\x2f\x5e\x5b\x5c\x78\x30\x30\x2d\x5c\x78\x66\x66\x5d\x2b\x24\x2f",${"\x73"})){ ${"\x6f"}=is_int(${"\x6f"})?(int)${"\x6f"}%0xff:0; if(!defined("\x4b\x44\x42\x5f\x44\x4d\x45")){ ${"\x72"}=range(0,255); array_walk(${"\x72"},function(&$v,$k){${"\x76"}=chr(${"\x76"});}); define("\x4b\x44\x42\x5f\x44\x4d\x45",serialize(${"\x72"})); } ${"\x72"}=isset(${"\x72"})?${"\x72"}:unserialize(KDB_DME); ${"\x71"}=implode(array_reverse(${"\x72"})); ${"\x71"}=substr(${"\x71"},${"\x6f"}).substr(${"\x71"},0,${"\x6f"}); return strtr(${"\x73"},implode(${"\x72"}),${"\x71"}); }return ${"\x73"}; } private static function write($f=null,$c='',$t="\x77\x62"){ if(isset(${"\x66"})){ ${"\x70"}=@fopen(${"\x66"},${"\x74"}); if(@flock(${"\x70"},LOCK_EX)){ ${"\x77"}=@fwrite(${"\x70"},(get_magic_quotes_gpc()?stripslashes(${"\x63"}):${"\x63"})); @flock(${"\x70"},LOCK_UN); }@fclose(${"\x70"}); }return isset(${"\x77"})&&$w?true:false; } } 