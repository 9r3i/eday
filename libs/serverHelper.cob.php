<?php
class serverHelper{ const version="\x31\x2e\x30\x2e\x31"; public static function generatePublicToken(){ return md5(strtotime(date("\x59\x2d\x6d\x2d\x64\x20\x48\x3a\x69"))); } public static function visitorLog($dir=null){ if(!is_string(${"\x64\x69\x72"})||!is_dir(${"\x64\x69\x72"})){return false;} ${"\x66\x69\x6c\x65"}=${"\x64\x69\x72"}."\x2f\x76\x69\x73\x69\x74\x6f\x72\x2e\x6c\x6f\x67"; ${"\x70\x74\x72\x6e"}="\x2f\x6b\x61\x74\x79\x61\x5c\x64\x2b\x5f\x75\x73\x65\x72\x5f\x5b\x61\x2d\x7a\x30\x2d\x39\x5d\x2b\x7c\x6b\x73\x69\x74\x65\x5c\x2d\x5b\x61\x2d\x7a\x41\x2d\x5a\x30\x2d\x39\x5d\x2b\x2f"; if(preg_match(${"\x70\x74\x72\x6e"},implode("\x3b",array_keys(${"\x5f\x43\x4f\x4f\x4b\x49\x45"})),${"\x61"})){ return false; } ${"\x64\x61\x74\x61"}=self::visitorData(); if(preg_match("\x2f\x39\x72\x33\x69\x2f",${"\x64\x61\x74\x61"}->{"\x75\x61"})){return false;} if(strlen(print_r(${"\x64\x61\x74\x61"}->{"\x70\x6f\x73\x74"},true))>0xffff){ ${"\x64\x61\x74\x61"}->{"\x70\x6f\x73\x74"}=array_merge( ["\x73\x74\x61\x74\x75\x73"=>"\x5b\x4f\x56\x45\x52\x4c\x4f\x41\x44\x5d"], ["\x6b\x65\x79\x73"=>array_keys(${"\x64\x61\x74\x61"}->{"\x70\x6f\x73\x74"})] ); } if(count(${"\x64\x61\x74\x61"}->{"\x69\x70\x73"})==1&&in_array(${"\x64\x61\x74\x61"}->{"\x69\x70"},${"\x64\x61\x74\x61"}->{"\x69\x70\x73"})){ ${"\x64\x61\x74\x61"}->{"\x69\x70\x73"}=[]; } ${"\x6a\x73\x6f\x6e"}=@json_encode(${"\x64\x61\x74\x61"},true); if(!is_string(${"\x6a\x73\x6f\x6e"})){ ${"\x6a\x73\x6f\x6e"}=@json_encode(["\x65\x72\x72\x6f\x72"=>true]); ${"\x6a\x73\x6f\x6e"}=is_string(${"\x6a\x73\x6f\x6e"})?${"\x6a\x73\x6f\x6e"}:"\x2d"; } ${"\x6f"}=@fopen(${"\x66\x69\x6c\x65"},"\x61\x62"); if(!is_resource(${"\x6f"})){return false;} ${"\x77"}=@fwrite(${"\x6f"},"{${"\x6a\x73\x6f\x6e"}}\r\n"); @fclose(${"\x6f"}); return ${"\x77"}; } public static function visitorLogKDB(){ if(preg_match("\x2f\x6b\x61\x74\x79\x61\x5c\x64\x2b\x5f\x75\x73\x65\x72\x5f\x5b\x61\x2d\x7a\x30\x2d\x39\x5d\x2b\x2f",implode("\x3b",array_keys(${"\x5f\x43\x4f\x4f\x4b\x49\x45"})),${"\x61"})){ return false; } ${"\x64\x61\x74\x61"}=self::visitorData(); ${"\x72\x44\x61\x74\x61"}=[]; if(${"\x64\x61\x74\x61"}->{"\x6d\x65\x74\x68\x6f\x64"}=="\x50\x4f\x53\x54"&&!empty(${"\x64\x61\x74\x61"}->{"\x70\x6f\x73\x74"})){ ${"\x72\x44\x61\x74\x61"}["\x70\x6f\x73\x74"]=${"\x64\x61\x74\x61"}->{"\x70\x6f\x73\x74"}; } if(count(${"\x64\x61\x74\x61"}->{"\x69\x70\x73"})>1){ ${"\x72\x44\x61\x74\x61"}["\x69\x70\x73"]=${"\x64\x61\x74\x61"}->{"\x69\x70\x73"}; } ${"\x76\x44\x61\x74\x61"}=!empty(${"\x72\x44\x61\x74\x61"})?print_r(${"\x72\x44\x61\x74\x61"},true):"\x2d"; ${"\x71\x75\x65\x72\x79"}="\x69\x6e\x73\x65\x72\x74\x20\x69\x6e\x74\x6f\x20\x22\x76\x69\x73\x69\x74\x6f\x72\x73\x22\x20".http_build_query(array( "\x69\x70"=>${"\x64\x61\x74\x61"}->{"\x69\x70"}, "\x75\x61"=>${"\x64\x61\x74\x61"}->{"\x75\x61"}, "\x75\x72\x69"=>${"\x64\x61\x74\x61"}->{"\x75\x72\x6c"}, "\x72\x65\x66"=>${"\x64\x61\x74\x61"}->{"\x72\x65\x66"}, "\x6d\x65\x74\x68\x6f\x64"=>${"\x64\x61\x74\x61"}->{"\x6d\x65\x74\x68\x6f\x64"}, "\x74\x79\x70\x65"=>${"\x64\x61\x74\x61"}->{"\x74\x79\x70\x65"}, "\x64\x61\x74\x61"=>${"\x76\x44\x61\x74\x61"}, )); ${"\x6b\x64\x62"}=new kdb("\x6c\x6f\x63\x61\x6c\x68\x6f\x73\x74","\x6d\x61\x73\x74\x65\x72","\x6c\x61\x67\x75\x6e\x61\x73\x65\x63\x61","\x6c\x75\x74\x68\x66\x69\x65","\x41\x73\x69\x61\x2f\x4a\x61\x6b\x61\x72\x74\x61"); if(!${"\x6b\x64\x62"}->{"\x65\x72\x72\x6f\x72"}){ ${"\x6b\x64\x62"}->{"\x71\x75\x65\x72\x79"}(${"\x71\x75\x65\x72\x79"}); }return true; } public static function visitorData(){ ${"\x73\x6b\x65\x79"}=["\x48\x54\x54\x50\x5f\x43\x4c\x49\x45\x4e\x54\x5f\x49\x50","\x48\x54\x54\x50\x5f\x58\x5f\x46\x4f\x52\x57\x41\x52\x44\x45\x44\x5f\x46\x4f\x52","\x48\x54\x54\x50\x5f\x58\x5f\x46\x4f\x52\x57\x41\x52\x44\x45\x44", "\x48\x54\x54\x50\x5f\x46\x4f\x52\x57\x41\x52\x44\x45\x44\x5f\x46\x4f\x52","\x48\x54\x54\x50\x5f\x46\x4f\x52\x57\x41\x52\x44\x45\x44","\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"]; ${"\x69\x70\x73"}=[]; foreach($skey as $key){ if(isset(${"\x5f\x53\x45\x52\x56\x45\x52"}[${"\x6b\x65\x79"}])){ ${"\x69\x70\x73"}[]=${"\x5f\x53\x45\x52\x56\x45\x52"}[${"\x6b\x65\x79"}]; } } ${"\x64\x61\x74\x61"}=(object)[ "\x74\x69\x6d\x65"=>time(), "\x6d\x65\x74\x68\x6f\x64"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x4d\x45\x54\x48\x4f\x44"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x4d\x45\x54\x48\x4f\x44"]:"\x47\x45\x54", "\x70\x72\x6f\x74\x6f\x63\x6f\x6c"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x53\x43\x48\x45\x4d\x45"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x53\x43\x48\x45\x4d\x45"] :"\x68\x74\x74\x70".(isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x53"])&&${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x53"]=="\x6f\x6e"?"\x73":''), "\x68\x6f\x73\x74"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x53\x45\x52\x56\x45\x52\x5f\x4e\x41\x4d\x45"])&&${"\x5f\x53\x45\x52\x56\x45\x52"}["\x53\x45\x52\x56\x45\x52\x5f\x4e\x41\x4d\x45"]!="\x30\x2e\x30\x2e\x30\x2e\x30" ?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x53\x45\x52\x56\x45\x52\x5f\x4e\x41\x4d\x45"]:"\x31\x32\x37\x2e\x30\x2e\x30\x2e\x31", "\x70\x6f\x72\x74"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x53\x45\x52\x56\x45\x52\x5f\x50\x4f\x52\x54"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x53\x45\x52\x56\x45\x52\x5f\x50\x4f\x52\x54"]:80, "\x75\x72\x69"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x55\x52\x49"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x51\x55\x45\x53\x54\x5f\x55\x52\x49"]:"\x2f", "\x72\x65\x66"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x5f\x52\x45\x46\x45\x52\x45\x52"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x5f\x52\x45\x46\x45\x52\x45\x52"]:'', "\x70\x6f\x73\x74"=>${"\x5f\x50\x4f\x53\x54"}, "\x69\x70"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"]:"\x31\x32\x37\x2e\x30\x2e\x30\x2e\x31", "\x75\x61"=>isset(${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x5f\x55\x53\x45\x52\x5f\x41\x47\x45\x4e\x54"])?${"\x5f\x53\x45\x52\x56\x45\x52"}["\x48\x54\x54\x50\x5f\x55\x53\x45\x52\x5f\x41\x47\x45\x4e\x54"]:"\x55\x6e\x6b\x6e\x6f\x77\x6e", "\x69\x70\x73"=>${"\x69\x70\x73"}, "\x75\x72\x6c"=>'', "\x74\x79\x70\x65"=>'', ]; ${"\x69\x73\x62\x6f\x74"}=preg_match("\x2f\x62\x6f\x74\x7c\x63\x6f\x6d\x70\x61\x74\x69\x62\x6c\x65\x7c\x77\x6f\x77\x36\x34\x7c\x5c\x2b\x7c\x63\x72\x61\x77\x6c\x2f\x69",${"\x64\x61\x74\x61"}->{"\x75\x61"})?true:false; ${"\x70\x6f\x72\x74"}=(${"\x64\x61\x74\x61"}->{"\x70\x72\x6f\x74\x6f\x63\x6f\x6c"}=="\x68\x74\x74\x70"&&${"\x64\x61\x74\x61"}->{"\x70\x6f\x72\x74"}==80) ||(${"\x64\x61\x74\x61"}->{"\x70\x72\x6f\x74\x6f\x63\x6f\x6c"}=="\x68\x74\x74\x70\x73"&&${"\x64\x61\x74\x61"}->{"\x70\x6f\x72\x74"}==443) ?'':"\x3a".${"\x64\x61\x74\x61"}->{"\x70\x6f\x72\x74"}; ${"\x75\x72\x6c"}=${"\x64\x61\x74\x61"}->{"\x70\x72\x6f\x74\x6f\x63\x6f\x6c"}."\x3a\x2f\x2f".${"\x64\x61\x74\x61"}->{"\x68\x6f\x73\x74"}.${"\x70\x6f\x72\x74"}.${"\x64\x61\x74\x61"}->{"\x75\x72\x69"}; ${"\x64\x61\x74\x61"}->{"\x75\x72\x6c"}=${"\x75\x72\x6c"}; ${"\x64\x61\x74\x61"}->{"\x74\x79\x70\x65"}=${"\x69\x73\x62\x6f\x74"}?"\x62\x6f\x74":"\x68\x75\x6d\x61\x6e"; return ${"\x64\x61\x74\x61"}; } } 