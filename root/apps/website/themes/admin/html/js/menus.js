/* menus.js */
var menus={
/* menu data toggle */
toggle:function(){
  var uid=this.dataset.menuid;
  var el=document.getElementById('menus-data-'+uid);
  if(!el){return false;}
  var height=el.style.height;
  if(height=='0px'){
    el.style.height=el.dataset.height;
    setTimeout(function(){
      el.style.removeProperty('height');
      setTimeout(function(){
        el.dataset.height=el.offsetHeight+'px';
      },100);
    },300);
  }else{
    el.style.height='0px';
  }return true;
},
/* edit menu */
edit:function(){
  return _admin.go('menu/menu/'+this.dataset.menuid);
},
/* delete menu */
delete:function(){
  var menuid=this.dataset.menuid;
  return _admin.confirm('Delete Menu','Delete this menu?',function(yes){
    if(!yes){return false;}
    _admin.loader(true,'Deleting...');
    return _admin.request('deleteMenu',function(r){
      _admin.loader(false);
      if(r!='OK'){
        return _admin.error(r);
      }
      var li=document.querySelector('li[data-menuid="'+menuid+'"]');
      if(li){li.parentElement.removeChild(li);}
      return _admin.success(r);
    },function(e){
      _admin.loader(false);
      return _admin.error(e);
    },{menuid:menuid});
  });
},
/* initialize menus data */
init:function(){
  var ul=document.querySelector('ol#menus-list');
  if(!ul||!Array.isArray(_admin.PAGE.data)){
    return false;
  }var data=_admin.PAGE.data;
  var uli=['aid','type','slug','name','order'];
  var ulix={
    aid:'MenurID',
    type:'Type',
    slug:'Slug',
    name:'Name',
    order:'Order',
  };
  var types={
    top:'top',
    header:'header',
    headbar:'headbar',
    sidebar:'sidebar',
    leftbar:'leftbar',
    rightbar:'rightbar',
    footbar:'footbar',
    footer:'footer',
    bottom:'bottom',
    otherone:'otherone',
    othertwo:'othertwo',
    otherthree:'otherthree',
  };
  var typeEl={};
  for(var ni in types){
    var li=document.createElement('li');
    var div=document.createElement('div');
    li.classList.add('menu-head-type-list');
    div.classList.add('menu-head-type');
    div.innerText=ni;
    li.appendChild(div);
    ul.appendChild(li);
    typeEl[ni]=li;
  }
  for(var i=0;i<data.length;i++){
    var li=document.createElement('li');
    var div=document.createElement('div');
    var an=document.createElement('span');
    var liul=document.createElement('ul');
    var ni=data[i].type;
    div.classList.add('menu-list-data');
    an.innerText=data[i].name;
    an.dataset.menuid=data[i].aid;
    an.onclick=this.toggle;
    li.dataset.menuid=data[i].aid;
    li.appendChild(an);
    for(var u=0;u<uli.length;u++){
      var k=uli[u];
      var lili=document.createElement('li');
      var dkey=document.createElement('div');
      var dval=document.createElement('div');
      dkey.innerText=ulix[k];
      dval.innerText=data[i][k];
      lili.appendChild(dkey);
      lili.appendChild(dval);
      liul.appendChild(lili);
    }
    /* create button */
    var edit=document.createElement('button');
    var del=document.createElement('button');
    var editi=document.createElement('i');
    var deli=document.createElement('i');
    edit.innerText='Edit';
    del.innerText='Delete';
    edit.dataset.menuid=data[i].aid;
    del.dataset.menuid=data[i].aid;
    edit.classList.add('button');
    edit.classList.add('button-blue');
    del.classList.add('button');
    del.classList.add('button-red');
    editi.classList.add('fa');
    editi.classList.add('fa-edit');
    deli.classList.add('fa');
    deli.classList.add('fa-trash');
    edit.insertBefore(editi,edit.firstChild);
    del.insertBefore(deli,del.firstChild);
    /* button events */
    edit.onclick=this.edit;
    del.onclick=this.delete;
    /* appending elements */
    div.appendChild(liul);
    div.appendChild(edit);
    div.appendChild(del);
    li.appendChild(div);
    typeEl[ni].appendChild(li);
    /* set div height */
    div.dataset.height=div.offsetHeight+'px';
    div.style.height='0px';
    div.id='menus-data-'+data[i].aid;
  }return true;
}
};menus.init();


