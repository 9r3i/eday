<?php
 class dataObject{ const version="\x31\x2e\x31\x2e\x30"; public function __construct(array $data=[]){ foreach($data as $key=>${"\x76\x61\x6c\x75\x65"}){ if(is_string(${"\x6b\x65\x79"})&&!empty(${"\x6b\x65\x79"})){ ${"\x74\x68\x69\x73"}->{${"\x6b\x65\x79"}}=is_array(${"\x76\x61\x6c\x75\x65"}) ?new ${"\x74\x68\x69\x73"}(${"\x76\x61\x6c\x75\x65"}):${"\x76\x61\x6c\x75\x65"}; } } } public function toArray(){ ${"\x64\x61\x74\x61"}=[]; foreach(${"\x74\x68\x69\x73"} as $key=>${"\x76\x61\x6c\x75\x65"}){ ${"\x64\x61\x74\x61"}[${"\x6b\x65\x79"}]=is_object(${"\x76\x61\x6c\x75\x65"}) &&method_exists(${"\x76\x61\x6c\x75\x65"},"\x74\x6f\x41\x72\x72\x61\x79") &&is_callable([${"\x76\x61\x6c\x75\x65"},"\x74\x6f\x41\x72\x72\x61\x79"],true) ?${"\x76\x61\x6c\x75\x65"}->{"\x74\x6f\x41\x72\x72\x61\x79"}() :${"\x76\x61\x6c\x75\x65"}; }return ${"\x64\x61\x74\x61"}; } public function add(string $key,$value=null){ ${"\x74\x68\x69\x73"}->{${"\x6b\x65\x79"}}=is_array(${"\x76\x61\x6c\x75\x65"}) ?new ${"\x74\x68\x69\x73"}(${"\x76\x61\x6c\x75\x65"}):${"\x76\x61\x6c\x75\x65"}; return true; } public function length(){ return count(${"\x74\x68\x69\x73"}->{"\x74\x6f\x41\x72\x72\x61\x79"}()); } } 