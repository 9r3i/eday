/* plugins.js */
var plugins={
/* plugin data toggle */
toggle:function(){
  var uid=this.dataset.namespace;
  var el=document.getElementById('plugins-data-'+uid);
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
/* view plugin */
view:function(){
  var href=WEBSITE_ADDRESS+'?'+ADMIN_KEY+'=plugin/'+this.dataset.namespace;
  return _admin.externalPage(href,this.dataset.name);
},
/* edit plugin */
edit:function(){
  return _admin.go('pluginEdit/'+this.dataset.namespace);
},
/* delete plugin */
delete:function(){
  var uid=this.dataset.namespace;
  return _admin.confirm('Delete Plugin','Delete this plugin?',function(yes){
    if(!yes){return false;}
    _admin.loader(true,'Deleting...');
    return _admin.request('deletePlugin',function(r){
      _admin.loader(false);
      if(r!='OK'){
        return _admin.error(r);
      }
      var li=document.querySelector('li[data-namespace="'+uid+'"]');
      if(li){li.parentElement.removeChild(li);}
      return _admin.success(r);
    },function(e){
      _admin.loader(false);
      return _admin.error(e);
    },{namespace:uid});
  });
},
/* initialize plugins data */
init:function(){
  var ul=document.querySelector('ol#plugins-list');
  if(!ul||!_admin.PAGE.data){
    return false;
  }var data=_admin.PAGE.data;
  var uli=['namespace','name','version','author','author-uri','description'];
  var ulix={
    namespace:'Namespace',
    name:'Name',
    version:'Version',
    author:'Author',
    'author-uri':'Author-URI',
    description:'Description',
  };
  for(var i in data){
    var li=document.createElement('li');
    var div=document.createElement('div');
    var an=document.createElement('span');
    var liul=document.createElement('ul');
    div.classList.add('plugin-list-data');
    an.innerText=data[i].info.name;
    an.dataset.namespace=data[i].info.namespace;
    an.onclick=this.toggle;
    li.dataset.namespace=data[i].info.namespace;
    li.appendChild(an);
    for(var u=0;u<uli.length;u++){
      var k=uli[u];
      var lili=document.createElement('li');
      var dkey=document.createElement('div');
      var dval=document.createElement('div');
      dkey.innerText=ulix[k];
      dval.innerText=data[i].info[k].replace(/\\n|\\r/g,'\r\n');
      lili.appendChild(dkey);
      lili.appendChild(dval);
      liul.appendChild(lili);
    }
    /* create button */
    var view=document.createElement('button');
    var del=document.createElement('button');
    var edit=document.createElement('button');
    var viewIcon=document.createElement('i');
    var deli=document.createElement('i');
    var editi=document.createElement('i');
    view.innerText='View';
    del.innerText='Delete';
    edit.innerText='Edit';
    view.dataset.namespace=data[i].info.namespace;
    view.dataset.name=data[i].info.name;
    del.dataset.namespace=data[i].info.namespace;
    edit.dataset.namespace=data[i].info.namespace;
    view.classList.add('button');
    view.classList.add('button-blue');
    del.classList.add('button');
    del.classList.add('button-red');
    edit.classList.add('button');
    edit.classList.add('button-soft-green');
    viewIcon.classList.add('fa');
    viewIcon.classList.add('fa-search');
    deli.classList.add('fa');
    deli.classList.add('fa-trash');
    editi.classList.add('fa')
    editi.classList.add('fa-edit')
    /* appending elements */
    div.appendChild(liul);
    /* view events */
    if(data[i].config.hasOwnProperty('admin')
      &&parseInt(data[i].config.admin)===1){
      view.insertBefore(viewIcon,view.firstChild);
      view.onclick=this.view;
      div.appendChild(view);
    }
    /* edit events */
    if(data[i].config.hasOwnProperty('edit')
      &&parseInt(data[i].config.edit)===1){
      edit.insertBefore(editi,edit.firstChild);
      edit.onclick=this.edit;
      div.appendChild(edit);
    }
    /* delete events */
    if(data[i].config.hasOwnProperty('delete')
      &&parseInt(data[i].config.delete)===1){
      del.insertBefore(deli,del.firstChild);
      del.onclick=this.delete;
      div.appendChild(del);
    }
    /* appending elements */
    li.appendChild(div);
    ul.appendChild(li);
    /* set div height */
    div.dataset.height=div.offsetHeight+'px';
    div.style.height='0px';
    div.id='plugins-data-'+data[i].info.namespace;
  }return true;
}
};plugins.init();


