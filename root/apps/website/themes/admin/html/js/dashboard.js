/* dashboard.js */
var dashboard={
/* initialize */
init:function(){
  /* prepare types and privilege */
  var types={
    master:'Master',
    admin:'Admin',
    editor:'Editor',
    author:'Author',
    member:'Member',
  };
  var dType=types[_admin.USER.privilege];
  /* get type element */
  var dt=document.getElementById('dashboard-type');
  if(dt){dt.innerText=dType;}
  /* get lines */
  var lines=document.getElementsByClassName('dashboard-line');
  var i=lines.length;
  while(i--){
    if(lines[i].dataset.level>_admin.USER.level){
      lines[i].style.display='none';
    }
  }
  /* posts */
  var dp=document.querySelector('#dashboard-posts');
  if(dp){this.posts(dp);}
  /*  */
  /* return as this object */
  return this;
},
/* data posts */
posts:function(el){
  /* check data posts */
  if(!el||!_admin.PAGE.data.hasOwnProperty('posts')
    ||!Array.isArray(_admin.PAGE.data.posts)){
    return false;
  }var posts=_admin.PAGE.data.posts;
  /* check post length */
  if(posts.length==0){
    el.innerText='You have no post yet.';
  }else{
    var ct={
      page:0,post:0,
      training:0,article:0,
      product:0,event:0,
    };
    var cto={
      page:0,post:0,
      training:0,article:0,
      product:0,event:0,
    };
    var yt=0,yto=0;
    for(var i=0;i<posts.length;i++){
      if(posts[i].author==_admin.USER.username){
        ct[posts[i].type]++;
        yt++;
      }else{
        cto[posts[i].type]++;
        yto++;
      }
    }
    /* his own post */
    if(yt==0){
      el.innerText='You have no post yet.';
    }else{
      var tt=[];
      for(var k in ct){
        if(ct[k]>0){
          var ptitle=ct[k]+' '+k+(ct[k]>1?'s':'');
          tt.push('<a href="?'+ADMIN_KEY+'=posts/posts//'
            +k+'" title="'+ptitle+'">'+ptitle+'</a>');
        }
      }el.innerHTML='You have '+tt.join(', ')+'.';
    }
    /* other's post */
    if(yto>0){
      var tto=[];
      for(var k in cto){
        if(cto[k]>0){
          var ptitle=cto[k]+' '+k+(cto[k]>1?'s':'');
          tto.push('<a href="?'+ADMIN_KEY+'=posts/posts//'+k
            +'" title="'+ptitle+'">'+ptitle+'</a>');
        }
      }el.innerHTML+='<br />And the others have '+tto.join(', ')+'.';
      /* info */
      el.innerHTML+='<br />Note: You are as <strong>"'
        +_admin.USER.privilege.toUpperCase()
        +'"</strong> also can edit and delete the other\'s posts.<br />';
    }
  }
  /* create new post button */
  var but=document.createElement('button');
  var buti=document.createElement('i');
  var text=document.createElement('div');
  but.classList.add('button');
  but.classList.add('button-soft-green');
  buti.classList.add('fa');
  buti.classList.add('fa-plus');
  but.innerText='New Post';
  but.insertBefore(buti,but.firstChild);
  text.innerText='\r\nPlease, to save your post after you\'re done writting.';
  text.insertBefore(but,text.firstChild);
  el.appendChild(text);
  but.onclick=function(e){
    return _admin.go('new');
  };
  /*  */
  /*  */
  
  
  
}
};dashboard.init();


