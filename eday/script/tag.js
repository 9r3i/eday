/* tag.js */
var W,D,TAG_TEMP={};
var TAG_AJAX_URL=SITE_URL+'?admin=tag/ajax';



/* event get a tag */
function tagEventGet(pid,tid,type){
  var pel=gebi(pid);
  if(!pel||!tid||!type||!tid.toString().match(/^\d+$/g)){return false;}
  if(!type.toString().match(/^[a-z0-9]+$/g)){return false;}
  pel.innerText='Loading...';
  W.post(TAG_AJAX_URL,function(r){
    //console.log(r);
    if(!Array.isArray(r)){
      pel.innerHTML='';
      return false;
    }
    pel.innerHTML='';
    for(var i=0;i<r.length;i++){
      var nl=ce('span');
      nl.classList.add('tag-each');
      nl.id='tag-each-'+r[i].id;
      nl.innerHTML='<i class="fa fa-close" '
        +'onclick="tagEventDelete('+r[i].id+')"></i> '+r[i].name;
      nl.title=r[i].name;
      TAG_TEMP[r[i].id]=nl;
      pel.appendChild(nl);
      pel.innerHTML+=' ';
    }return true;
  },{request:'getTags',tid:tid,type:type},null,null,null,null,function(e){
    //console.log(e);
    pel.innerText=e;
    return false;
  });
}

/* event add a tag */
function tagEventAdd(id,pid,tid,type){
  var el=gebi(id);
  var pel=gebi(pid);
  if(!el||!pel||!tid||!type){return false;}
  if(!tid.toString().match(/^\d+$/g)){return false;}
  if(!type.toString().match(/^[a-z0-9]+$/g)){return false;}
  el.onkeyup=function(e){
    if(e.keyCode!==13){return false;}
    var val=this.value;
    this.value='';
    return tagAdd(tid,val,type,function(r){
      if(!r||!r.id){console.log(e);return false;}
      var nl=ce('span');
      nl.classList.add('tag-each');
      nl.id='tag-each-'+r.id;
      nl.innerHTML='<i class="fa fa-close" '
        +'onclick="tagEventDelete('+r.id+')"></i> '+val;
      nl.title=val;
      TAG_TEMP[r.id]=nl;
      pel.appendChild(nl);
      pel.innerHTML+=' ';
      //console.log(r);
    },function(e){
      //console.log(e);
    });
  };
}

/* event delete a tag */
function tagEventDelete(id){
  if(!id||!id.toString().match(/^\d+$/g)){return false;}
  var pid='tag-each-'+id;
  var el=gebi(pid);
  if(!el){return false;}
  var pr=el.parentElement;
  pr.removeChild(el);
  return tagDelete(id,function(r){
    //console.log(r);
  },function(e){
    //console.log(e);
    if(TAG_TEMP[id]){
      pr.appendChild(TAG_TEMP[id]);
      TAG_TEMP[id]=null;
    }
  });
}

/* delete a tag */
function tagDelete(id,cb,er){
  cb=typeof cb==='function'?cb:function(){};
  er=typeof er==='function'?er:function(){};
  if(!id||!id.toString().match(/^\d+$/g)){return er('Error: Invalid ID.');}
  var data={request:'deleteTag',id:id};
  W.post(TAG_AJAX_URL,function(r){
    if(r.toString().match(/^error/ig)){return er(r);}
    return cb(r);
  },data,null,null,null,null,function(e){
    return er(e);
  });
}

/* add a tag */
function tagAdd(tid,name,type,cb,er){
  cb=typeof cb==='function'?cb:function(){};
  er=typeof er==='function'?er:function(){};
  if(!tid||!name||!type){return er('Error: Require TID, tag name and tag type.');}
  if(!tid.toString().match(/^\d+$/g)){return er('Error: Invalid ID.');}
  if(typeof name!=='string'){return er('Error: Invalid tag name.');}
  if(typeof type!=='string'||!type.toString().match(/^[a-z0-9]+$/g)){
    return er('Error: Invalid tag type.');
  }var data={request:'addTag',tid:tid,name:name,type:type};
  W.post(TAG_AJAX_URL,function(r){
    if(r.toString().match(/^error/ig)){return er(r);}
    return cb(r);
  },data,null,null,null,null,function(e){
    return er(e);
  });
}


