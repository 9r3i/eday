/* menu.js */
var W,D,MENU_ACTION_URL,MENU_DATA,MENU_PARENT,MENU_TEMP={};

/* initial */
setTimeout(function(){
  if(MENU_DATA){
    return menuParseTypes();
  }
},10);

W.onmousedown=function(e){
  menuMakeMovableDown(e);
};
W.onmousemove=function(e){
  menuMakeMovableMove(e);
};
W.onmouseup=function(e){
  menuMakeMovableUp(e);
};


/* delete menu by id */
function menuDelete(id){
  if(!id){return false;}
  var s=gebi('menu-parent-'+id);
  if(!s){return false;}
  var c=ce('div');
  var pr=s.parentElement;
  c.id='menu-parent-'+id;
  c.classList.add('menu-parent');
  c.innerHTML=s.innerHTML;
  MENU_TEMP[id]=c;
  pr.removeChild(s);
  var data={
    request:'deleteMenu',
    id:id,
  };
  W.post(MENU_ACTION_URL,function(r){
    if(r=='OK'){console.log(r);return true;}
    pr.appendChild(c);
    if(r.match(/^error/ig)){return error(r);}
    console.log(r);
    return salert('Something is going wrong.');
  },data,null,null,null,null,function(e){
    pr.appendChild(c);
    return error(e);
  });
}

/* menu type onchange */
function menuChangetype(){
  var type=qs('input[name="type"]');
  var parent=qs('select[name="parent"]');
  var options='<option value="">[Blank Parent]</option>';
  for(var i in MENU_PARENT){
    if(type.value!=MENU_PARENT[i].type){continue;}
    options+='<option value="'+MENU_PARENT[i].id+'">'+MENU_PARENT[i].name+'</option>';
  }parent.innerHTML=options;
  return true;
}

/* submit add menu */
function menuSubmitAdd(){
  var name=qs('input[name="name"]');
  var uri=qs('input[name="uri"]');
  var type=qs('input[name="type"]');
  var parent=qs('select[name="parent"]');
  var data={
    name:name.value,
    uri:uri.value,
    type:type.value,
    parent:parent.value,
    request:'addMenu',
  };
  adminLoader();
  W.post(MENU_ACTION_URL,function(r){
    adminLoader(false);
    if(r.toString().match(/^error/ig)){return error(r);}
    else if(!r.id){
      console.log(r);
      return salert('Something is going wrong.');
    }
    name.value='';
    uri.value='';
    type.value='';
    menuChangetype();
    menuAddForm();
    var d1=gebi('menu-child-'+data.parent);
    var d2=gebi('menu-child-'+data.type);
    var d=d1?d1:d2;
    if(!d){
      return error('Menu type does not exist.\r\n'
        +'The page must be refreshed.',function(y){
        W.location.reload();
      });
    }
    var c=ce('div');
    c.id='menu-parent-'+r.id;
    c.classList.add('menu-parent');
    c.innerHTML='<div class="menu-parent-name" id="movable-menu-'+r.id+'" data-id="'+r.id+'">'
      +data.name+'</div>'
      +'<div class="menu-children" id="menu-child-'+r.id+'" data-parent="'+r.id+'"></div>';
    d.appendChild(c);
    MENU_PARENT[r.id]={
      id:r.id,
      name:data.name,
      uri:data.rui,
      type:data.type,
      parent:data.parent,
    };
    return true;
  },data,null,null,null,null,function(e){
    adminLoader(false);
    return error(e);
  });
}

/* toggle add menu form */
function menuAddForm(){
  var el=gebi('menu-add-form');
  var button=gebi('menu-add-button');
  if(!el||!button){console.log('error');return false;}
  if(button.dataset.status=='open'){
    el.style.height='0px';
    button.dataset.status='close';
    button.innerHTML='<i class="fa fa-plus"></i>';
    return;
  }
  el.style.height='330px';
  button.innerHTML='<i class="fa fa-minus"></i>';
  button.dataset.status='open';
}

/* move menu to a target */
function menuMoveTo(id,to){
  if(!id||!to){return false;}
  if(to=='__delete__'){
    return confirmation('Delete this menu?','',function(yes){
      if(!yes){return false;}
      return menuDelete(id);
    });
  }
  var s=gebi('menu-parent-'+id);
  var d=gebi('menu-child-'+to);
  if(!s||!d){return false;}
  var c=ce('div');
  var pr=s.parentElement;
  c.id='menu-parent-'+id;
  c.classList.add('menu-parent');
  c.innerHTML=s.innerHTML;
  MENU_TEMP[id]=c;
  d.appendChild(c);
  //d.insertBefore(c,d.firstChild);
  pr.removeChild(s);
  var data={
    request:'moveMenu',
    id:id,
    parent:to,
  };
  W.post(MENU_ACTION_URL,function(r){
    if(r=='OK'){
      MENU_PARENT[id].parent=to;
      console.log(r);
      return true;
    }
    d.removeChild(c);
    pr.appendChild(c);
    if(r.match(/^error/ig)){return error(r);}
    console.log(r);
    return salert('Something is going wrong.');
  },data,null,null,null,null,function(e){
    d.removeChild(c);
    pr.appendChild(c);
    return error(e);
  });
}

/* parse menu data */
function menuParseData(d,p){
  if(!d||!p){console.log(d,p);return false;}
  var el=gebi('menu-child-'+p);
  if(!el){
    console.log('Failed to get menu element "'+p+'".');
    return false;
  }
  el.innerHTML='';
  for(var i in d){
    el.innerHTML+='<div class="menu-parent" id="menu-parent-'+i+'">'
      +'<div class="menu-parent-name" id="movable-menu-'+i+'" data-id="'+i+'">'+d[i][1]+'</div>'
      +'<div class="menu-children" id="menu-child-'+i+'" data-parent="'+i+'"></div>'
      +'</div>';
    if(typeof d[i][2]==='object'){
      menuParseData(d[i][2],i);
    }
  }
}

/* parse menu types */
function menuParseTypes(){
  var index=gebi('menu-index');
  if(!index){return error('Some element is not detected.');}
  if(!MENU_DATA){return error('Something is going wrong.');}
  index.innerHTML='';
  for(var i in MENU_DATA){
    index.innerHTML+='<div class="menu-types-parent">'
      +'<div class="menu-type-name" data-id="'+i+'">'+i+'</div>'
      +'<div class="menu-types" id="menu-child-'+i+'" data-type="'+i+'"></div>'
      +'</div>';
    menuParseData(MENU_DATA[i],i);
  }
  /* removal */
  index.innerHTML+='<div class="menu-types-parent">'
    +'<div class="menu-type-name" data-id="__delete__">[DELETE]</div>'
    +'</div>';
}

/* make movable up */
function menuMakeMovableUp(e){
  if(!window.MOB){return false;}
  //console.log(e.target.dataset.id,window.MOB.id);
  var to=e.target.dataset.id;
  var id=window.MOB.id;
  var el=window.MOB.el;
  el.style.removeProperty('position');
  el.style.removeProperty('z-index');
  el.style.removeProperty('width');
  el.style.removeProperty('height');
  el.style.removeProperty('top');
  el.style.removeProperty('left');
  el.style.removeProperty('background-color');
  window.MOB=false;
  menuMoveTo(id,to);
}

/* make movable move */
function menuMakeMovableMove(e){
  if(!window.MOB){return false;}
  var el=window.MOB.el;
  var x=e.changedTouches?e.changedTouches[0].pageX:e.screenX;
  var y=e.changedTouches?e.changedTouches[0].pageY:e.screenY;
  var top=y-150;
  var left=(x-window.MOB.x)+window.MOB.l;
  el.style.top=top+'px';
  el.style.left=left+'px';
}

/* make movable down */
function menuMakeMovableDown(e){
  if(!e.target.id||!e.target.id.match(/^movable/ig)){return false;}
  var el=e.target;
  window.MOB={
    x:e.changedTouches?e.changedTouches[0].pageX:e.screenX,
    y:e.changedTouches?e.changedTouches[0].pageY:e.screenY,
    w:el.offsetWidth,
    h:el.offsetHeight,
    t:el.offsetTop,
    l:el.offsetLeft,
    el:el,
    id:el.dataset.id,
  };
  el.style.width=el.offsetWidth+'px';
  el.style.height=el.offsetHeight+'px';
  el.style.top=(window.MOB.y-120)+'px';
  el.style.left=el.offsetLeft+'px';
  el.style.position='fixed';
  el.style.backgroundColor='#bdf';
  el.style.zIndex=7;
}


