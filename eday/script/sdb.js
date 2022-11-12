/* sdb client, require: header-1.5.1.js or higher */
function sdb(h,q,cb,er){
  if(!h||!q||typeof W.post!=='function'){return;}
  var r=btoa(JSON.stringify(q));
  var m=(new Date()).getTime();
  er=typeof er==='function'?er:function(){};
  cb=typeof cb==='function'?cb:function(){};
  W.post(h+'?client=ajax',function(s){
    if(s.match(/^error/gi)){return er(s);}
    try{var f=JSON.parse(atob(s));}catch(e){}
    if(!f||!f.status){return er('error: failed to parse data response');}
    if(f.status=='error'){return er(f.message);}
    if(f.info){
      f.info['response_length']=s.length;
      f.info['request_time']=((new Date()).getTime()-m)/1000;
      f.info['host']=h;
    }return cb(f);
  },{"sdb":r},false,null,null,null,function(e){
    return er(e);
  });
}



