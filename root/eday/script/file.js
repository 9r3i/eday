/* file.js */
var W,D,SITE_URL,FILE_CURRENT_PATH,FILE_LIST;
var FILE_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=file/ajax';



/* upload a file */
function fileUpload(el){
  var empty=FILE_CURRENT_PATH==''?true:false;
  if(empty||typeof FILE_CURRENT_PATH==='undefined'){return false;}
  if(!el){return error('Some elements are not detected.');}
  var pr=el.parentElement;
  if(!el.files[0]){return error('Invalid file input.');}
  var file=el.files[0];
  if(file.size>Math.pow(1024,2)*2){return error('File size is too large. Max 2 MB.');}
  var data=new FormData();
  data.append('request','uploadFile');
  data.append('file',file);
  data.append('path',FILE_CURRENT_PATH);
  var pro=ce('div');
  var bar=ce('div');
  pro.classList.add('file-manager-progress');
  bar.classList.add('file-manager-progress-bar');
  pro.appendChild(bar);
  pr.appendChild(pro);
  el.style.display='none';
  W.post(FILE_ACTION_URL,function(r){
    pr.removeChild(pro);
    el.style.display='block';
    if(r.toString().match(/^error/ig)){return error(r);}
    else if(r=='OK'){
      return success(r,function(y){
        W.location.assign(SITE_URL+'?'+SITE_ADMIN_KEY+'=file/manager/'
          +encodeURIComponent(btoa(FILE_CURRENT_PATH)));
      });
    }console.log(r);
    return salert('Something is going wrong.');
  },data,true,function(e){
    bar.style.width=(e.loaded/e.total*100)+'%';
  },null,null,function(e){
    pr.removeChild(pro);
    el.style.display='block';
    return error(e);
  });
}

/* submit rename a file */
function fileRenameSubmit(f,nf){
  if(typeof f!=='string'||typeof nf!=='string'){
    fileRename();
    return error('Invalid file name.');
  }
  if(FILE_LIST.indexOf(nf)>0){
    fileRename();
    return error('File name has been taken.');
  }
  var data={request:'renameFile',file:f,nfile:nf,path:FILE_CURRENT_PATH};
  adminLoader();
  W.post(FILE_ACTION_URL,function(r){
    fileRename();
    adminLoader(false);
    if(r.toString().match(/^error/ig)){return error(r);}
    else if(r=='OK'){
      return success(r,function(y){
        W.location.assign(SITE_URL+'?'+SITE_ADMIN_KEY+'=file/manager/'
          +encodeURIComponent(btoa(FILE_CURRENT_PATH)));
      });
    }console.log(r);
    return salert('Something is going wrong.');
  },data,false,null,null,null,function(e){
    fileRename();
    adminLoader(false);
    return error(e);
  });
}

/* rename file */
function fileRename(f){
  var fm=gebcn('file-manager-input');
  if(fm){i=fm.length;while(i--){
    fm[i].parentElement.firstChild.style.display='block';
    fm[i].parentElement.removeChild(fm[i]);
  }}
  if(typeof f!=='string'){return false;}
  var el=qs('td[data-filename="'+f+'"]');
  if(!el){return false;}
  var fc=el.firstChild;
  var put=ce('input');
  put.classList.add('file-manager-input');
  put.value=f;
  put.style.width=(el.offsetWidth-26)+'px';
  fc.style.display='none';
  el.appendChild(put);
  put.focus();
  put.onblur=function(e){
    if(f==this.value){
      return fileRename();
    }return fileRenameSubmit(f,this.value);
  };
  put.onkeyup=function(e){
    if(e.keyCode!==13){return false;}
    if(f==this.value){
      return fileRename();
    }return fileRenameSubmit(f,this.value);
  };
}

/* delete a file */
function fileDelete(f){
  if(typeof f!=='string'){return false;}
  var data={request:'deleteFile',path:FILE_CURRENT_PATH,file:f};
  return confirmation('Delete the file?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    W.post(FILE_ACTION_URL,function(r){
      adminLoader(false);
      if(r.toString().match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.reload();
        });
      }console.log(r);
      return salert('Something is going wrong.');
    },data,false,null,null,null,function(e){
      adminLoader(false);
      return error(e);
    });
  });
}


