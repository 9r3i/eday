/* pluginEdit.js */
var pluginEdit={
/* load plugin */
load:function(){
  var files=document.querySelector('select[name="plugin-files"]');
  var sbutton=document.querySelector('td#save-button');
  var elns=document.querySelector('td#plugin-namespace');
  var tdcontent=document.querySelector('td#plugin-file-content');
  var pns=elns.innerText;
  var filename=files.value;
  tdcontent.innerHTML='';
  sbutton.innerHTML='';
  if(filename==''){return false;}
  sbutton.innerHTML='Loading...';
  return _admin.request('pluginLoadFile',function(r){
    if(typeof r==='string'
        &&r.match(/^error/i)){return _admin.error(r);}
    var textarea=document.createElement('textarea');
    textarea.placeholder='Content';
    textarea.name='content';
    textarea.id='plugin-content-text';
    textarea.dataset.filename=filename;
    textarea.dataset.namespace=pns;
    textarea.textContent=r.content;
    var save=document.createElement('button');
    save.classList.add('button');
    save.classList.add('button-blue');
    save.id='save-plugin-content';
    save.innerText='Save';
    tdcontent.innerHTML='';
    tdcontent.appendChild(textarea);
    sbutton.innerHTML='';
    sbutton.appendChild(save);
    save.onclick=pluginEdit.save;
    return true;
  },function(e){
    return _admin.error(e);
  },{
    namespace:pns,
    filename:filename
  });
},
/* save plugin file */
save:function(){
  var save=document.querySelector('button#save-plugin-content');
  var content=document.querySelector('textarea#plugin-content-text');
  var pns=content.dataset.namespace;
  var filename=content.dataset.filename;
  var text=content.value;
  save.innerText='Saving...';
  save.disabled=true;
  return _admin.request('pluginSaveFile',function(r){
    save.disabled=false;
    save.innerText='Save';
    if(typeof r==='string'
        &&r.match(/^error/i)){return _admin.error(r);}
    return _admin.success(r);
  },function(e){
    save.disabled=false;
    save.innerText='Save';
    return _admin.error(e);
  },{
    namespace:pns,
    filename:filename,
    content:text
  });
},
/* initialize */
init:function(){
  var pns=_admin.path.split('/')[1];
  var files=document.querySelector('select[name="plugin-files"]');
  var elns=document.querySelector('td#plugin-namespace');
  if(!files||!elns){
    return _admin.error('Error: Some element is not detected.');
  }elns.innerText=pns;
  _admin.request('pluginFiles',function(r){
    if(typeof r==='string'
        &&r.match(/^error/i)){return _admin.error(r);}
    //console.log(r);
    for(var i in r.files){
      var elop=document.createElement('option');
      elop.value=r.files[i];
      elop.innerText=r.files[i];
      files.appendChild(elop);
    }
    return true;
  },function(e){
    return _admin.error(e);
  },{
    namespace:pns
  });
  files.onchange=this.load;
  return this;
}
};pluginEdit.init();


