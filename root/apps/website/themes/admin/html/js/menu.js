/* menu.js */
var menu={
/* save manu */
save:function(){
  var setNames=['type','order','name','slug'];
  var setError=false,setEl=[];
  var setData={};
  for(var i=0;i<setNames.length;i++){
    var tagName=setNames[i]==='type'||setNames[i]==='order'?'select':'input';
    var el=document.querySelector(tagName+'[name="'+setNames[i]+'"]');
    if(!el){setError='Element "'+setNames[i]+'" is not detected.';break;}
    setData[setNames[i]]=el.value;
    setEl.push(el);
  }
  if(setError){return _admin.error('Error: '+setError);}
  var submit=document.querySelector('input[name="submit"]');
  var menuid=document.querySelector('td[data-menuid]');
  if(!submit||!menuid){return _admin.error('Error: Some element is not detected.');}
  setData['id']=menuid.dataset.menuid;
  setEl.push(submit);
  _admin.disabled(setEl,true);
  submit.value='Saving...';
  submit.blur();
  return _admin.request('saveMenu',function(r){
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
  var type=document.querySelector('select[name="type"]');
  var order=document.querySelector('select[name="order"]');
  var submit=document.querySelector('input[name="submit"]');
  var menuid=document.querySelector('td[data-menuid]');
  if(!type||!submit||!menuid){
    return _admin.error('Error: Some element is not detected.');
  }type.value=_admin.PAGE.data.type;
  order.value=_admin.PAGE.data.order;
  submit.onclick=this.save;
  return this;
}
};menu.init();


