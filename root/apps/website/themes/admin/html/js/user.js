/* user.js */
var user={
/* save user */
save:function(){
  var setNames=['username','privilege','name','email','password'];
  var setError=false,setEl=[];
  var setData={};
  for(var i=0;i<setNames.length;i++){
    var tagName=setNames[i]==='privilege'?'select':'input';
    var el=document.querySelector(tagName+'[name="'+setNames[i]+'"]');
    if(!el){setError='Element "'+setNames[i]+'" is not detected.';break;}
    setData[setNames[i]]=el.value;
    setEl.push(el);
  }
  if(setError){return _admin.error('Error: '+setError);}
  var submit=document.querySelector('input[name="submit"]');
  var user=document.querySelector('td[data-userid]');
  if(!submit||!user){return _admin.error('Error: Some element is not detected.');}
  setData['id']=user.dataset.userid;
  setEl.push(submit);
  _admin.disabled(setEl,true);
  submit.value='Saving...';
  submit.blur();
  return _admin.request('saveUser',function(r){
    _admin.disabled(setEl,false);
    submit.value='Save';
    if(r!=='OK'){return _admin.error(r);}
    return _admin.success(r);
  },function(e){
    _admin.disabled(setEl,false);
    submit.value='Save';
    return _admin.error(e);
  },setData);
},
/* initialize */
init:function(){
  var privilege=document.querySelector('select[name="privilege"]');
  var submit=document.querySelector('input[name="submit"]');
  var user=document.querySelector('td[data-userid]');
  if(!privilege||!submit||!user){
    return _admin.error('Error: Some element is not detected.');
  }privilege.value=_admin.PAGE.data.privilege;
  submit.onclick=this.save;
  return this;
}
};user.init();


