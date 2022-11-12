/* post.js */
var W,D,POST_ACTION_URL,POST_PICTURE_FILE=null,POST_CKE_ABOUT=false;








/* submit save the post */
function postSubmitSave(){
  var title=qs('input[name="title"]').value;
  var content=editorGetValue();
  var picture=typeof POST_PICTURE_FILE==='object'&&POST_PICTURE_FILE!==null?POST_PICTURE_FILE:null;
  var picture_url=typeof POST_PICTURE_FILE==='string'?POST_PICTURE_FILE:'';
  var unform=false;
  var data={
    id:POST_ID,
    title:title,
    content:content,
    picture:picture_url,
    request:'savePost',
  };
  if(picture){
    var data=new FormData();
    data.append('id',POST_ID);
    data.append('title',title);
    data.append('content',content);
    data.append('picture',picture);
    data.append('request','savePost');
    unform=true;
  }
  adminLoader();
  W.post(POST_ACTION_URL,function(r){
    adminLoader(false);
    if(r.match(/^error/ig)){return error(r);}
    else if(r=='OK'){
      return success(r,function(y){
        //W.location.assign('?admin=post/all');
      });
    }
    console.log(r);
    return salert('Something is going wrong.');
  },data,unform,null,null,null,function(e){
    adminLoader(false);
    return error(e);
  });
}

/* picture in edit post */
function postPicturePreviewEdit(id){
  var picture=gebi(id);
  var preview=gebi('preview');
  if(!preview||!picture||POST_PICTURE==''){return false;}
      POST_PICTURE_FILE=POST_PICTURE;
      preview.style.display='block';
      picture.style.display='none';
      preview.innerHTML='<img src="'+POST_PICTURE+'" />';
      preview.firstChild.onclick=function(e){
        return confirmation('Delete this picture?','',function(yes){
          if(!yes){return false;}
          picture.style.display='block';
          preview.innerHTML='';
          preview.style.display='none';
          POST_PICTURE_FILE=null;
        });
      };
}

/* delete a post by id */
function postDeleteID(id){
  if(!id||!id.toString().match(/^\d+$/)){return error('Invalid ID.');}
  return confirmation('Delete this post?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    var data={request:'deletePost',id:id};
    W.post(POST_ACTION_URL,function(r){
      adminLoader(false);
      if(r.match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.assign('?admin=post/all');
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

/* submit add post */
function postSubmitAdd(){
  var title=qs('input[name="title"]').value;
  var content=editorGetValue();
  var picture=POST_PICTURE_FILE;
  var unform=false;
  var data={
    title:title,
    content:content,
    picture:'',
    request:'addPost',
  };
  if(picture){
    var data=new FormData();
    data.append('title',title);
    data.append('content',content);
    data.append('picture',picture);
    data.append('request','addPost');
    unform=true;
  }
  adminLoader();
  W.post(POST_ACTION_URL,function(r){
    adminLoader(false);
    if(r.match(/^error/ig)){return error(r);}
    else if(r=='OK'){
      return success(r,function(y){
        W.location.assign('?admin=post/all');
      });
    }
    console.log(r);
    return salert('Something is going wrong.');
  },data,unform,null,null,null,function(e){
    adminLoader(false);
    return error(e);
  });
}

/* picture preview */
function postPicturePreview(id){
  var picture=gebi(id);
  var preview=gebi('preview');
  if(!preview||!picture){return false;}
  preview.style.display='none';
  POST_PICTURE_FILE=null;
  picture.addEventListener('change',function(e){
    POST_PICTURE_FILE=null;
    preview.style.display='block';
    var file=this.files[0];
    if(!file.type.match(/^image\//g)){
      preview.innerHTML='<span style="color:red;">Error: File is not image. '
        +'<em>(jpg/jpeg/png/gif)</em>.</span>';
      return false;
    }
    if(file.size>Math.pow(1024,2)*2){
      preview.innerHTML='<span style="color:red;">Error: File size is too large. '
        +'<em>(Max. 2 MB)</em>.</span>';
      return false;
    }
    var FR=new FileReader();
    FR.onloadend=function(e){
      picture.style.display='none';
      preview.innerHTML='<img src="'+e.target.result+'" />';
      POST_PICTURE_FILE=file;
      preview.firstChild.onclick=function(e){
        return confirmation('Delete this picture?','',function(yes){
          if(!yes){return false;}
          picture.style.display='block';
          preview.innerHTML='';
          preview.style.display='none';
          POST_PICTURE_FILE=null;
        });
      };
    };FR.readAsDataURL(file);
  },false);
}


