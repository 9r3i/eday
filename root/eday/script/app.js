/* app.js */
var W,D,APP_ACTION_URL,APP_NAMESPACE;


/* save a file */
function appSaveFile(f,c){
  if(typeof f!=='string'||typeof c!=='string'){return error('Invalid file.');}
  var data={request:'saveFile',content:c,file:f,namespace:APP_NAMESPACE};
  return confirmation('Save the file?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    W.post(APP_ACTION_URL,function(r){
      adminLoader(false);
      if(r.toString().match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
        });
      }console.log(r);
      return salert('Something is going wrong.');
    },data,false,null,null,null,function(e){
      adminLoader(false);
      return error(e);
    });
  });
}

/* select a file */
function appSelectFile(f){
  if(typeof f!=='string'){return error('Invalid file.');}
  var el=gebi('file-selected');
  if(!el){return error('Some elements are not detected.');}
  el.classList.remove('post-row');
  el.innerHTML='';
  if(f==''){return false;}
  var data={request:'fileContent',namespace:APP_NAMESPACE,file:f};
  adminLoader();
    W.post(APP_ACTION_URL,function(r){
      adminLoader(false);
      if(r.toString().match(/^error/ig)){return error(r);}
      var tx=ce('textarea');
      tx.id='app-file-content';
      tx.classList.add('textarea');
      tx.classList.add('app-repair-content');
      tx.spellcheck=false;
      tx.value=r;
      el.appendChild(tx);
      var row=ce('div');
      row.classList.add('form-row');
      row.style.margin='20px 5px';
      row.style.textAlign='center';
      var sub=ce('a');
      sub.classList.add('submit');
      sub.classList.add('submit-blue');
      sub.innerHTML='<i class="fa fa-save"></i> Save File';
      row.appendChild(sub);
      el.appendChild(row);
      sub.onclick=function(){
        if(r==tx.value){return error('File has no changed.');}
        return appSaveFile(f,tx.value);
      };
    },data,false,null,null,null,function(e){
      adminLoader(false);
      return error(e);
    });
}

/* save config */
function appConfigSave(){
  var raw=qsa('input[app="config"]');
  if(!raw){return error('Failed to get data config.');}
  var parsed={};
  var data={request:'saveAppConfig',data:{},namespace:APP_NAMESPACE};
  for(var i=0;i<raw.length;i++){
    var type=raw[i].dataset.parent;
    var key=raw[i].name;
    if(!parsed.hasOwnProperty(type)){
      parsed[type]={};
    }parsed[type][key]=raw[i].value;
  }data.data=JSON.stringify(parsed);
  return confirmation('Save application configuration?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    W.post(APP_ACTION_URL,function(r){
      adminLoader(false);
      if(r.toString().match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.assign('?'+SITE_ADMIN_KEY+'=app/all');
        });
      }
      console.log(r);
      return salert('Something is going wrong.');
    },data,false,null,null,null,function(e){
      adminLoader(false);
      return error(e);
    });
  });
}

/* activate a app */
function appActivate(id){
  if(!id||!id.toString().match(/^[a-z0-9]+$/)){return error('Invalid namespace.');}
  return confirmation('Activate this application?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    var data={request:'activateApp',namespace:id};
    W.post(APP_ACTION_URL,function(r){
      adminLoader(false);
      if(r.match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.assign('?'+SITE_ADMIN_KEY+'=app/all');
        });
      }
      console.log(r);
      return salert('Something is going wrong.');
    },data,false,null,null,null,function(e){
      adminLoader(false);
      return error(e);
    });
  });
}


