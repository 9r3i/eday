/* users.js */
var users={
/* user data toggle */
toggle:function(){
  var uid=this.dataset.userid;
  var el=document.getElementById('users-data-'+uid);
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
/* edit user */
edit:function(){
  return _admin.go('user/users/'+this.dataset.userid);
},
/* delete user */
delete:function(){
  var uid=this.dataset.userid;
  return _admin.confirm('Delete User','Delete this user?',function(yes){
    if(!yes){return false;}
    _admin.loader(true,'Deleting...');
    return _admin.request('deleteUser',function(r){
      _admin.loader(false);
      if(r!='OK'){
        return _admin.error(r);
      }
      var li=document.querySelector('li[data-userid="'+uid+'"]');
      if(li){li.parentElement.removeChild(li);}
      return _admin.success(r);
    },function(e){
      _admin.loader(false);
      return _admin.error(e);
    },{uid:uid});
  });
},
/* initialize users data */
init:function(){
  var ul=document.querySelector('ol#users-list');
  if(!ul||!Array.isArray(_admin.PAGE.data)){
    return false;
  }var data=_admin.PAGE.data;
  var uli=['aid','username','privilege','name','email'];
  var ulix={
    aid:'UserID',
    username:'Username',
    privilege:'Privilege',
    name:'Name',
    email:'Email',
  };
  for(var i=0;i<data.length;i++){
    var li=document.createElement('li');
    var div=document.createElement('div');
    var an=document.createElement('span');
    var liul=document.createElement('ul');
    div.classList.add('user-list-data');
    an.innerText=data[i].name;
    an.dataset.userid=data[i].aid;
    an.onclick=this.toggle;
    li.dataset.userid=data[i].aid;
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
    edit.dataset.userid=data[i].aid;
    del.dataset.userid=data[i].aid;
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
    ul.appendChild(li);
    /* set div height */
    div.dataset.height=div.offsetHeight+'px';
    div.style.height='0px';
    div.id='users-data-'+data[i].aid;
  }return true;
}
};users.init();


