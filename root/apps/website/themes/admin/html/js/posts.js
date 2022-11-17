/* posts.js */
var posts={
/* load page of add new post */
newPost:function(){
  return _admin.go('new');
},
/* parse posts regard to status, access and type */
parse:function(status,access,type){
  /* prepare types */
  var types={
    status:['publish','draft','trash'],
    access:['public','private'],
    type:['post','page','training','article','product','event'],
  };
  /* prepare arguments */
  status=typeof status==='string'
    &&types.status.indexOf(status)>=0
    ?status:types.status[0];
  access=typeof access==='string'
    &&types.access.indexOf(access)>=0
    ?access:types.access[0];
  type=typeof type==='string'
    &&types.type.indexOf(type)>=0
    ?type:types.type[0];
  /* prepare element option detail */
  var pod=document.getElementById('posts-option-detail');
  if(pod){
    pod.innerText='Status: '+status
      +'; Access: '+access
      +'; Type: '+type
      +';';
  }
  /* prepare element */
  var pl=document.querySelector('#posts-list');
  /* check element and data */
  if(!pl||!Array.isArray(_admin.PAGE.data)){
    return false;
  }var data=_admin.PAGE.data,i=data.length;
  /* clear the field */
  _admin.clearElement(pl);
  /* parse data */
  while(i--){
    /* check status and access privilege */
    if(data[i].status!=status
      ||data[i].access!=access
      ||data[i].type!=type){
      continue;
    }
    /* parse line */
    var pa=window.posts.parseLine(data[i]);
    /* append to posts list */
    pl.appendChild(pa);
  }
  /* re-construct all anchors */
  return _admin.initAnchors();
},
/* line field */
parseLine:function(post){
  /* check post object */
  if(typeof post!=='object'||post===null){return false;}
  /* create elements */
  var pa=document.createElement('div');
  var ph=document.createElement('div');
  var pb=document.createElement('div');
  var pd=document.createElement('div');
  var view=document.createElement('button');
  var edit=document.createElement('button');
  var del=document.createElement('button');
  var viewIcon=document.createElement('i');
  var editIcon=document.createElement('i');
  var delIcon=document.createElement('i');
  /* add classes */
  pa.classList.add('post-data-each');
  ph.classList.add('post-data-head');
  pb.classList.add('post-data-body');
  pd.classList.add('post-data-detail');
  view.classList.add('button-blue');
  edit.classList.add('button-green');
  del.classList.add('button-red');
  viewIcon.classList.add('fa');
  viewIcon.classList.add('fa-search');
  editIcon.classList.add('fa');
  editIcon.classList.add('fa-edit');
  delIcon.classList.add('fa');
  delIcon.classList.add('fa-trash');
  /* add value */
  pa.id='post-id-'+post.aid;
  ph.innerText=post.title;
  ph.title=post.title;
  pd.innerText=post.datetime+' - '+post.author
    +(post.author==_admin.USER.username?' (Owner)':'');
  view.innerText='View';
  view.title='View this post';
  view.dataset.url=WEBSITE_ADDRESS+post.url+'.html';
  edit.innerText='Edit';
  edit.title='Edit this post';
  edit.dataset.id=post.aid;
  del.innerText=post.status=='trash'?'Delete':'Trash';
  del.delete=post.status=='trash'?'Delete this post':'Move to trash';
  del.dataset.id=post.aid;
  del.dataset.status=post.status;
  /* click event */
  view.onclick=function(e){
    return window.open(this.dataset.url,'_blank');
  };
  edit.onclick=function(e){
    return _admin.go('edit/posts/'+this.dataset.id);
  };
  del.onclick=function(e){
    var postID=this.dataset.id;
    var postStatus=this.dataset.status;
    var cTitle=postStatus=='trash'?'Delete':'Trash';
    var cText=postStatus=='trash'?'Delete this post?':'Move to trash?';
    var cTextLoader=postStatus=='trash'?'Deleting...':'Moving...';
    var rmethod=postStatus=='trash'?'deletePost':'trashPost';
    return _admin.confirm(cTitle,cText,function(yes){
      if(!yes){return false;}
      _admin.loader(true,cTextLoader);
      return _admin.request(rmethod,function(r){
        _admin.loader(false);
        if(r!=='OK'){return _admin.error(r);}
        if(postStatus!='trash'){
          window.posts.update(postID,{status:'trash'});
        }
        var pas=document.getElementById('post-id-'+postID);
        if(pas){pas.parentElement.removeChild(pas);}
        return _admin.success(r);
      },function(e){
        _admin.loader(false);
        return _admin.error(e);
      },{id:postID});
    });
  };
  /* prepend button elements */
  view.insertBefore(viewIcon,view.firstChild);
  edit.insertBefore(editIcon,edit.firstChild);
  del.insertBefore(delIcon,del.firstChild);
  /* append elements */
  pb.appendChild(view);
  pb.appendChild(edit);
  pb.appendChild(del);
  pa.appendChild(ph);
  pa.appendChild(pd);
  pa.appendChild(pb);
  /* return post element */
  return pa;
},
/* update data */
update:function(id,update){
  if(!id.match(/^\d+$/)
    ||!_admin.PAGE.data
    ||typeof update!=='object'
    ||update===null){return false;}
  var done=false;
  for(var i=0;i<_admin.PAGE.data.length;i++){
    if(_admin.PAGE.data[i].aid==id){
      for(var k in update){
        _admin.PAGE.data[i][k]=update[k];
      }done=true;break;
    }
  }return done;
},
/* initailize */
init:function(){
  /* prepare elements */
  var status=document.querySelector('select[name="status"]');
  var access=document.querySelector('select[name="access"]');
  var type=document.querySelector('select[name="type"]');
  if(!status||!access||!type){
    return _admin.error('Error: Some element is not detected.');
  }
  /* pre request */
  var preType=_admin.path.split('/');
  if(preType.length>=4){
    type.value=preType[3];
    if(preType.length>=5){
      status.value=preType[4];
      if(preType.length>=6){
        access.value=preType[5];
      }
    }
  }
  /* on change event */
  status.onchange=function(e){
    return window.posts.parse(status.value,access.value,type.value);
  };
  access.onchange=function(e){
    return window.posts.parse(status.value,access.value,type.value);
  };
  type.onchange=function(e){
    return window.posts.parse(status.value,access.value,type.value);
  };
  /* return parse the post */
  return posts.parse(status.value,access.value,type.value);
}
};posts.init();


