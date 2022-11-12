<?php
/* class get for e-day
 * started at august 24th 2018
 */
class get{
  /* tag tid */
  public static function tagTID($tid=null,$type=null){
    if(!isset($tid,$type)){return false;}
    if(!preg_match('/^\d+$/',$tid)||!preg_match('/^[a-z0-9]+$/',$type)){return false;}
    $db=site::db();
    $tags=$db->query('select * from tags where tid='.$tid.' and type="'.$type.'"');
    return $tags;
  }
  /* tag data */
  public static function tagData($get=null){
    if(!preg_match('/^([a-z0-9]+)\/([a-zA-Z0-9-]+)$/',trim($get),$ak)){return false;}
    $db=site::db();$table=$ak[1].'s';$res=[];$ore=[];
    $tags=$db->query('select * from tags where type="'.$ak[1].'" and tag="'.$ak[2].'"');
    if($tags){
      foreach($tags as $tag){$ore[]='id='.$tag['tid'];}
      $res=$db->query('select * from "'.$table.'" where '.implode(' or ',$ore));
    }return $res;
  }
  /* tags */
  public static function tags(){
    $db=site::db();
    $sel=$db->query('select tag,name,type,count(id) as count from tags group by type,tag');
    if(!$sel||$db->error){return [];}
    $r=[];
    foreach($sel as $tag){
      $size=$tag['count']>15?24:($tag['count']>10?18:12);
      $r[]=[
        site::url.'?tag='.$tag['type'].'/'.$tag['tag'],
        $tag['name'].' ('.$tag['count'].')',$size
      ];
    }return $r;
  }
  /* menu parents */
  public static function menuParents(){
    $db=site::db();
    $sel=$db->query('select id,name,type from menus');
    return $sel;
  }
  /* site info */
  public static function info(){
    return (object)[
      'name'=>site::config('name'),
      'description'=>site::config('description'),
      'keyword'=>site::config('keyword'),
    ];
  }
  /* posts */
  public static function posts($limit=9){
    $prod=site::post(null,$error);
    if($error){return [];}
    $x=[];$count=0;
    foreach($prod as $r){
      $count++;
      $x[]=[
        'id'=>$r['id'],
        'title'=>$r['title'],
        'content'=>$r['content'],
        'picture'=>$r['picture'],
        'time'=>strtotime($r['datetime']),
        'url'=>site::url.'?post_id='.$r['id'],
      ];
      if($count>=$limit){break;}
    }return $x;
  }
  /* menus */
  public static function menus($type=null){
    $db=site::db();
    $type=is_string($type)&&preg_match('/^[a-z0-9]+$/i',$type)?$type:null;
    $sel=$db->query('select * from menus '.($type?'where type="'.$type.'"':''));
    $parsed=base::parseMenuChildren($sel);
    $menus=base::parseMenu($parsed);
    return isset($type,$menus[$type])?$menus[$type]:$menus;
  }
  /* products */
  public static function products($limit=9){
    $prod=site::product(null,$error);
    if($error){return [];}
    $x=[];$count=0;
    foreach($prod as $r){
      $count++;
      $x[]=[
        'id'=>$r['id'],
        'name'=>$r['name'],
        'currency'=>$r['currency'],
        'price'=>$r['price'],
        'discount'=>$r['discount'],
        'picture'=>$r['picture'],
        'ribbon'=>$r['ribbon'],
        'url'=>site::url.'?product_id='.$r['id'],
        'dialog'=>['Order Sekarang',$r['order_to']],
      ];
      if($count>=$limit){break;}
    }return $x;
  }
  /* get site visitors */
  public static function visitors(){
    $f=EDAY_THEME_DIR.'site.visitors';$d=0x01;
    $o=fopen($f,is_file($f)?'rb+':'wb');
    if(!$o||!flock($o,LOCK_EX)){fclose($o);}
    if(!is_file($f)){
      $w=fwrite($o,@gzencode($d));
      flock($o,LOCK_UN);
      fclose($o);
      return $d;
    }
    $r=fread($o,1024);
    $g=@gzdecode($r);
    $n=intval($g)+$d;
    fseek($o,0);
    $w=fwrite($o,@gzencode($n));
    flock($o,LOCK_UN);
    fclose($o);
    return $n;
  }
  /* timezones */
  public static function timezones(){
    return [
    'Pacific/Midway'=>'(GMT-11:00) Midway',
    'Pacific/Niue'=>'(GMT-11:00) Niue',
    'Pacific/Pago_Pago'=>'(GMT-11:00) Pago Pago',
    'Pacific/Johnston'=>'(GMT-10:00) Hawaii Time',
    'Pacific/Rarotonga'=>'(GMT-10:00) Rarotonga',
    'Pacific/Tahiti'=>'(GMT-10:00) Tahiti',
    'Pacific/Marquesas'=>'(GMT-09:30) Marquesas',
    'America/Anchorage'=>'(GMT-09:00) Alaska Time',
    'Pacific/Gambier'=>'(GMT-09:00) Gambier',
    'America/Los_Angeles'=>'(GMT-08:00) Pacific Time',
    'America/Tijuana'=>'(GMT-08:00) Pacific Time - Tijuana',
    'America/Vancouver'=>'(GMT-08:00) Pacific Time - Vancouver',
    'America/Whitehorse'=>'(GMT-08:00) Pacific Time - Whitehorse',
    'Pacific/Pitcairn'=>'(GMT-08:00) Pitcairn',
    'America/Shiprock'=>'(GMT-07:00) Mountain Time',
    'America/Phoenix'=>'(GMT-07:00) Mountain Time - Arizona',
    'America/Mazatlan'=>'(GMT-07:00) Mountain Time - Chihuahua, Mazatlan',
    'America/Dawson_Creek'=>'(GMT-07:00) Mountain Time - Dawson Creek',
    'America/Edmonton'=>'(GMT-07:00) Mountain Time - Edmonton',
    'America/Hermosillo'=>'(GMT-07:00) Mountain Time - Hermosillo',
    'America/Yellowknife'=>'(GMT-07:00) Mountain Time - Yellowknife',
    'America/Belize'=>'(GMT-06:00) Belize',
    'America/Chicago'=>'(GMT-06:00) Central Time',
    'America/Mexico_City'=>'(GMT-06:00) Central Time - Mexico City',
    'America/Regina'=>'(GMT-06:00) Central Time - Regina',
    'America/Tegucigalpa'=>'(GMT-06:00) Central Time - Tegucigalpa',
    'America/Winnipeg'=>'(GMT-06:00) Central Time - Winnipeg',
    'America/Costa_Rica'=>'(GMT-06:00) Costa Rica',
    'Pacific/Easter'=>'(GMT-06:00) Easter Island',
    'America/El_Salvador'=>'(GMT-06:00) El Salvador',
    'Pacific/Galapagos'=>'(GMT-06:00) Galapagos',
    'America/Guatemala'=>'(GMT-06:00) Guatemala',
    'America/Managua'=>'(GMT-06:00) Managua',
    'America/Bogota'=>'(GMT-05:00) Bogota',
    'America/Cayman'=>'(GMT-05:00) Cayman',
    'America/New_York'=>'(GMT-05:00) Eastern Time',
    'America/Iqaluit'=>'(GMT-05:00) Eastern Time - Iqaluit',
    'America/Montreal'=>'(GMT-05:00) Eastern Time - Montreal',
    'America/Toronto'=>'(GMT-05:00) Eastern Time - Toronto',
    'America/Grand_Turk'=>'(GMT-05:00) Grand Turk',
    'America/Guayaquil'=>'(GMT-05:00) Guayaquil',
    'America/Havana'=>'(GMT-05:00) Havana',
    'America/Jamaica'=>'(GMT-05:00) Jamaica',
    'America/Lima'=>'(GMT-05:00) Lima',
    'America/Nassau'=>'(GMT-05:00) Nassau',
    'America/Panama'=>'(GMT-05:00) Panama',
    'America/Port-au-Prince'=>'(GMT-05:00) Port-au-Prince',
    'America/Rio_Branco'=>'(GMT-05:00) Rio Branco',
    'America/Caracas'=>'(GMT-04:30) Caracas',
    'America/Antigua'=>'(GMT-04:00) Antigua',
    'America/Asuncion'=>'(GMT-04:00) Asuncion',
    'America/Halifax'=>'(GMT-04:00) Atlantic Time - Halifax',
    'America/Barbados'=>'(GMT-04:00) Barbados',
    'Atlantic/Bermuda'=>'(GMT-04:00) Bermuda',
    'America/Boa_Vista'=>'(GMT-04:00) Boa Vista',
    'America/Campo_Grande'=>'(GMT-04:00) Campo Grande',
    'America/Cuiaba'=>'(GMT-04:00) Cuiaba',
    'America/Curacao'=>'(GMT-04:00) Curacao',
    'America/Guyana'=>'(GMT-04:00) Guyana',
    'America/La_Paz'=>'(GMT-04:00) La Paz',
    'America/Manaus'=>'(GMT-04:00) Manaus',
    'America/Martinique'=>'(GMT-04:00) Martinique',
    'Antarctica/Palmer'=>'(GMT-04:00) Palmer',
    'America/Tortola'=>'(GMT-04:00) Port of Spain',
    'America/Porto_Velho'=>'(GMT-04:00) Porto Velho',
    'America/Puerto_Rico'=>'(GMT-04:00) Puerto Rico',
    'America/Santiago'=>'(GMT-04:00) Santiago',
    'America/Santo_Domingo'=>'(GMT-04:00) Santo Domingo',
    'America/Thule'=>'(GMT-04:00) Thule',
    'America/St_Johns'=>'(GMT-03:30) Newfoundland Time - St. Johns',
    'America/Araguaina'=>'(GMT-03:00) Araguaina',
    'America/Belem'=>'(GMT-03:00) Belem',
    'America/Argentina/Buenos_Aires'=>'(GMT-03:00) Buenos Aires',
    'America/Cayenne'=>'(GMT-03:00) Cayenne',
    'America/Fortaleza'=>'(GMT-03:00) Fortaleza',
    'America/Godthab'=>'(GMT-03:00) Godthab',
    'America/Maceio'=>'(GMT-03:00) Maceio',
    'America/Miquelon'=>'(GMT-03:00) Miquelon',
    'America/Montevideo'=>'(GMT-03:00) Montevideo',
    'America/Paramaribo'=>'(GMT-03:00) Paramaribo',
    'America/Recife'=>'(GMT-03:00) Recife',
    'Antarctica/Rothera'=>'(GMT-03:00) Rothera',
    'America/Bahia'=>'(GMT-03:00) Salvador',
    'America/Sao_Paulo'=>'(GMT-03:00) Sao Paulo',
    'Atlantic/Stanley'=>'(GMT-03:00) Stanley',
    'America/Noronha'=>'(GMT-02:00) Noronha',
    'Atlantic/South_Georgia'=>'(GMT-02:00) South Georgia',
    'Atlantic/Azores'=>'(GMT-01:00) Azores',
    'Atlantic/Cape_Verde'=>'(GMT-01:00) Cape Verde',
    'America/Scoresbysund'=>'(GMT-01:00) Scoresbysund',
    'Africa/Abidjan'=>'(GMT+00:00) Abidjan',
    'Africa/Accra'=>'(GMT+00:00) Accra',
    'Africa/Timbuktu'=>'(GMT+00:00) Bamako',
    'Africa/Banjul'=>'(GMT+00:00) Banjul',
    'Africa/Bissau'=>'(GMT+00:00) Bissau',
    'Atlantic/Canary'=>'(GMT+00:00) Canary Islands',
    'Africa/Casablanca'=>'(GMT+00:00) Casablanca',
    'Africa/Conakry'=>'(GMT+00:00) Conakry',
    'Africa/Dakar'=>'(GMT+00:00) Dakar',
    'America/Danmarkshavn'=>'(GMT+00:00) Danmarkshavn',
    'Europe/Dublin'=>'(GMT+00:00) Dublin',
    'Africa/El_Aaiun'=>'(GMT+00:00) El Aaiun',
    'Atlantic/Faeroe'=>'(GMT+00:00) Faeroe',
    'Africa/Freetown'=>'(GMT+00:00) Freetown',
    'Etc/GMT'=>'(GMT+00:00) GMT (no daylight saving)',
    'Europe/Lisbon'=>'(GMT+00:00) Lisbon',
    'Africa/Lome'=>'(GMT+00:00) Lome',
    'Europe/London'=>'(GMT+00:00) London',
    'Africa/Monrovia'=>'(GMT+00:00) Monrovia',
    'Africa/Nouakchott'=>'(GMT+00:00) Nouakchott',
    'Africa/Ouagadougou'=>'(GMT+00:00) Ouagadougou',
    'Atlantic/Reykjavik'=>'(GMT+00:00) Reykjavik',
    'Africa/Sao_Tome'=>'(GMT+00:00) Sao Tome',
    'Atlantic/St_Helena'=>'(GMT+00:00) St Helena',
    'Africa/Algiers'=>'(GMT+01:00) Algiers',
    'Europe/Amsterdam'=>'(GMT+01:00) Amsterdam',
    'Europe/Andorra'=>'(GMT+01:00) Andorra',
    'Africa/Bangui'=>'(GMT+01:00) Bangui',
    'Europe/Berlin'=>'(GMT+01:00) Berlin',
    'Africa/Brazzaville'=>'(GMT+01:00) Brazzaville',
    'Europe/Brussels'=>'(GMT+01:00) Brussels',
    'Europe/Budapest'=>'(GMT+01:00) Budapest',
    'Europe/Zagreb'=>'(GMT+01:00) Central European Time - Belgrade',
    'Europe/Prague'=>'(GMT+01:00) Central European Time - Prague',
    'Africa/Ceuta'=>'(GMT+01:00) Ceuta',
    'Europe/Copenhagen'=>'(GMT+01:00) Copenhagen',
    'Africa/Douala'=>'(GMT+01:00) Douala',
    'Europe/Gibraltar'=>'(GMT+01:00) Gibraltar',
    'Africa/Kinshasa'=>'(GMT+01:00) Kinshasa',
    'Africa/Lagos'=>'(GMT+01:00) Lagos',
    'Africa/Libreville'=>'(GMT+01:00) Libreville',
    'Africa/Luanda'=>'(GMT+01:00) Luanda',
    'Europe/Luxembourg'=>'(GMT+01:00) Luxembourg',
    'Europe/Madrid'=>'(GMT+01:00) Madrid',
    'Africa/Malabo'=>'(GMT+01:00) Malabo',
    'Europe/Malta'=>'(GMT+01:00) Malta',
    'Europe/Monaco'=>'(GMT+01:00) Monaco',
    'Africa/Ndjamena'=>'(GMT+01:00) Ndjamena',
    'Africa/Niamey'=>'(GMT+01:00) Niamey',
    'Europe/Oslo'=>'(GMT+01:00) Oslo',
    'Europe/Paris'=>'(GMT+01:00) Paris',
    'Africa/Porto-Novo'=>'(GMT+01:00) Porto-Novo',
    'Europe/Vatican'=>'(GMT+01:00) Rome',
    'Europe/Stockholm'=>'(GMT+01:00) Stockholm',
    'Europe/Tirane'=>'(GMT+01:00) Tirane',
    'Africa/Tunis'=>'(GMT+01:00) Tunis',
    'Europe/Vienna'=>'(GMT+01:00) Vienna',
    'Europe/Warsaw'=>'(GMT+01:00) Warsaw',
    'Africa/Windhoek'=>'(GMT+01:00) Windhoek',
    'Europe/Zurich'=>'(GMT+01:00) Zurich',
    'Europe/Athens'=>'(GMT+02:00) Athens',
    'Asia/Beirut'=>'(GMT+02:00) Beirut',
    'Africa/Blantyre'=>'(GMT+02:00) Blantyre',
    'Europe/Bucharest'=>'(GMT+02:00) Bucharest',
    'Africa/Bujumbura'=>'(GMT+02:00) Bujumbura',
    'Africa/Cairo'=>'(GMT+02:00) Cairo',
    'Europe/Chisinau'=>'(GMT+02:00) Chisinau',
    'Asia/Damascus'=>'(GMT+02:00) Damascus',
    'Africa/Gaborone'=>'(GMT+02:00) Gaborone',
    'Asia/Gaza'=>'(GMT+02:00) Gaza',
    'Africa/Harare'=>'(GMT+02:00) Harare',
    'Europe/Mariehamn'=>'(GMT+02:00) Helsinki',
    'Europe/Istanbul'=>'(GMT+02:00) Istanbul',
    'Asia/Jerusalem'=>'(GMT+02:00) Jerusalem',
    'Africa/Johannesburg'=>'(GMT+02:00) Johannesburg',
    'Europe/Kiev'=>'(GMT+02:00) Kiev',
    'Africa/Kigali'=>'(GMT+02:00) Kigali',
    'Africa/Lubumbashi'=>'(GMT+02:00) Lubumbashi',
    'Africa/Lusaka'=>'(GMT+02:00) Lusaka',
    'Africa/Maputo'=>'(GMT+02:00) Maputo',
    'Africa/Maseru'=>'(GMT+02:00) Maseru',
    'Africa/Mbabane'=>'(GMT+02:00) Mbabane',
    'Europe/Nicosia'=>'(GMT+02:00) Nicosia',
    'Europe/Riga'=>'(GMT+02:00) Riga',
    'Europe/Sofia'=>'(GMT+02:00) Sofia',
    'Europe/Tallinn'=>'(GMT+02:00) Tallinn',
    'Africa/Tripoli'=>'(GMT+02:00) Tripoli',
    'Europe/Vilnius'=>'(GMT+02:00) Vilnius',
    'Africa/Addis_Ababa'=>'(GMT+03:00) Addis Ababa',
    'Asia/Aden'=>'(GMT+03:00) Aden',
    'Asia/Amman'=>'(GMT+03:00) Amman',
    'Indian/Antananarivo'=>'(GMT+03:00) Antananarivo',
    'Africa/Asmera'=>'(GMT+03:00) Asmera',
    'Asia/Baghdad'=>'(GMT+03:00) Baghdad',
    'Asia/Bahrain'=>'(GMT+03:00) Bahrain',
    'Indian/Comoro'=>'(GMT+03:00) Comoro',
    'Africa/Dar_es_Salaam'=>'(GMT+03:00) Dar es Salaam',
    'Africa/Djibouti'=>'(GMT+03:00) Djibouti',
    'Africa/Kampala'=>'(GMT+03:00) Kampala',
    'Africa/Khartoum'=>'(GMT+03:00) Khartoum',
    'Asia/Kuwait'=>'(GMT+03:00) Kuwait',
    'Indian/Mayotte'=>'(GMT+03:00) Mayotte',
    'Europe/Minsk'=>'(GMT+03:00) Minsk',
    'Africa/Mogadishu'=>'(GMT+03:00) Mogadishu',
    'Europe/Kaliningrad'=>'(GMT+03:00) Moscow-01 - Kaliningrad',
    'Africa/Nairobi'=>'(GMT+03:00) Nairobi',
    'Asia/Qatar'=>'(GMT+03:00) Qatar',
    'Asia/Riyadh'=>'(GMT+03:00) Riyadh',
    'Antarctica/Syowa'=>'(GMT+03:00) Syowa',
    'Asia/Tehran'=>'(GMT+03:30) Tehran',
    'Asia/Baku'=>'(GMT+04:00) Baku',
    'Asia/Dubai'=>'(GMT+04:00) Dubai',
    'Indian/Mahe'=>'(GMT+04:00) Mahe',
    'Indian/Mauritius'=>'(GMT+04:00) Mauritius',
    'Europe/Moscow'=>'(GMT+04:00) Moscow+00',
    'Europe/Samara'=>'(GMT+04:00) Moscow+00 - Samara',
    'Asia/Muscat'=>'(GMT+04:00) Muscat',
    'Indian/Reunion'=>'(GMT+04:00) Reunion',
    'Asia/Tbilisi'=>'(GMT+04:00) Tbilisi',
    'Asia/Yerevan'=>'(GMT+04:00) Yerevan',
    'Asia/Kabul'=>'(GMT+04:30) Kabul',
    'Asia/Aqtau'=>'(GMT+05:00) Aqtau',
    'Asia/Aqtobe'=>'(GMT+05:00) Aqtobe',
    'Asia/Ashgabat'=>'(GMT+05:00) Ashgabat',
    'Asia/Dushanbe'=>'(GMT+05:00) Dushanbe',
    'Asia/Karachi'=>'(GMT+05:00) Karachi',
    'Indian/Kerguelen'=>'(GMT+05:00) Kerguelen',
    'Indian/Maldives'=>'(GMT+05:00) Maldives',
    'Antarctica/Mawson'=>'(GMT+05:00) Mawson',
    'Asia/Tashkent'=>'(GMT+05:00) Tashkent',
    'Asia/Colombo'=>'(GMT+05:30) Colombo',
    'Asia/Calcutta'=>'(GMT+05:30) India Standard Time',
    'Asia/Katmandu'=>'(GMT+05:45) Katmandu',
    'Asia/Almaty'=>'(GMT+06:00) Almaty',
    'Asia/Bishkek'=>'(GMT+06:00) Bishkek',
    'Indian/Chagos'=>'(GMT+06:00) Chagos',
    'Asia/Dhaka'=>'(GMT+06:00) Dhaka',
    'Asia/Yekaterinburg'=>'(GMT+06:00) Moscow+02 - Yekaterinburg',
    'Asia/Thimphu'=>'(GMT+06:00) Thimphu',
    'Antarctica/Vostok'=>'(GMT+06:00) Vostok',
    'Indian/Cocos'=>'(GMT+06:30) Cocos',
    'Asia/Rangoon'=>'(GMT+06:30) Rangoon',
    'Asia/Bangkok'=>'(GMT+07:00) Bangkok',
    'Indian/Christmas'=>'(GMT+07:00) Christmas',
    'Antarctica/Davis'=>'(GMT+07:00) Davis',
    'Asia/Saigon'=>'(GMT+07:00) Hanoi',
    'Asia/Hovd'=>'(GMT+07:00) Hovd',
    'Asia/Jakarta'=>'(GMT+07:00) Jakarta',
    'Asia/Omsk'=>'(GMT+07:00) Moscow+03 - Omsk, Novosibirsk',
    'Asia/Phnom_Penh'=>'(GMT+07:00) Phnom Penh',
    'Asia/Vientiane'=>'(GMT+07:00) Vientiane',
    'Asia/Brunei'=>'(GMT+08:00) Brunei',
    'Antarctica/Casey'=>'(GMT+08:00) Casey',
    'Asia/Shanghai'=>'(GMT+08:00) China Time - Beijing',
    'Asia/Choibalsan'=>'(GMT+08:00) Choibalsan',
    'Asia/Hong_Kong'=>'(GMT+08:00) Hong Kong',
    'Asia/Kuala_Lumpur'=>'(GMT+08:00) Kuala Lumpur',
    'Asia/Macau'=>'(GMT+08:00) Macau',
    'Asia/Makassar'=>'(GMT+08:00) Makassar',
    'Asia/Manila'=>'(GMT+08:00) Manila',
    'Asia/Krasnoyarsk'=>'(GMT+08:00) Moscow+04 - Krasnoyarsk',
    'Asia/Singapore'=>'(GMT+08:00) Singapore',
    'Asia/Taipei'=>'(GMT+08:00) Taipei',
    'Asia/Ulaanbaatar'=>'(GMT+08:00) Ulaanbaatar',
    'Australia/Perth'=>'(GMT+08:00) Western Time - Perth',
    'Asia/Dili'=>'(GMT+09:00) Dili',
    'Asia/Jayapura'=>'(GMT+09:00) Jayapura',
    'Asia/Irkutsk'=>'(GMT+09:00) Moscow+05 - Irkutsk',
    'Pacific/Palau'=>'(GMT+09:00) Palau',
    'Asia/Pyongyang'=>'(GMT+09:00) Pyongyang',
    'Asia/Seoul'=>'(GMT+09:00) Seoul',
    'Asia/Tokyo'=>'(GMT+09:00) Tokyo',
    'Australia/Adelaide'=>'(GMT+09:30) Central Time - Adelaide',
    'Australia/Darwin'=>'(GMT+09:30) Central Time - Darwin',
    'Antarctica/DumontDUrville'=>'(GMT+10:00) Dumont D\'Urville',
    'Australia/Brisbane'=>'(GMT+10:00) Eastern Time - Brisbane',
    'Australia/Hobart'=>'(GMT+10:00) Eastern Time - Hobart',
    'Australia/Sydney'=>'(GMT+10:00) Eastern Time - Melbourne, Sydney',
    'Pacific/Guam'=>'(GMT+10:00) Guam',
    'Asia/Yakutsk'=>'(GMT+10:00) Moscow+06 - Yakutsk',
    'Pacific/Port_Moresby'=>'(GMT+10:00) Port Moresby',
    'Pacific/Saipan'=>'(GMT+10:00) Saipan',
    'Pacific/Yap'=>'(GMT+10:00) Truk',
    'Pacific/Efate'=>'(GMT+11:00) Efate',
    'Pacific/Guadalcanal'=>'(GMT+11:00) Guadalcanal',
    'Pacific/Kosrae'=>'(GMT+11:00) Kosrae',
    'Asia/Vladivostok'=>'(GMT+11:00) Moscow+07 - Yuzhno-Sakhalinsk',
    'Pacific/Noumea'=>'(GMT+11:00) Noumea',
    'Pacific/Ponape'=>'(GMT+11:00) Ponape',
    'Pacific/Norfolk'=>'(GMT+11:30) Norfolk',
    'Pacific/Auckland'=>'(GMT+12:00) Auckland',
    'Pacific/Fiji'=>'(GMT+12:00) Fiji',
    'Pacific/Funafuti'=>'(GMT+12:00) Funafuti',
    'Pacific/Kwajalein'=>'(GMT+12:00) Kwajalein',
    'Pacific/Majuro'=>'(GMT+12:00) Majuro',
    'Asia/Magadan'=>'(GMT+12:00) Moscow+08 - Magadan',
    'Asia/Kamchatka'=>'(GMT+12:00) Moscow+08 - Petropavlovsk-Kamchatskiy',
    'Pacific/Nauru'=>'(GMT+12:00) Nauru',
    'Pacific/Tarawa'=>'(GMT+12:00) Tarawa',
    'Pacific/Wake'=>'(GMT+12:00) Wake',
    'Pacific/Wallis'=>'(GMT+12:00) Wallis',
    'Pacific/Apia'=>'(GMT+13:00) Apia',
    'Pacific/Enderbury'=>'(GMT+13:00) Enderbury',
    'Pacific/Fakaofo'=>'(GMT+13:00) Fakaofo',
    'Pacific/Tongatapu'=>'(GMT+13:00) Tongatapu',
    'Pacific/Kiritimati'=>'(GMT+14:00) Kiritimati',
    ];
  }
}
