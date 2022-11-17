/* menuNew.js */
var menuNew={
/* save menu */
save:function(){
  var setNames=['type','slug','name','order'];
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
  if(!submit){return _admin.error('Error: Some element is not detected.');}
  setEl.push(submit);
  _admin.disabled(setEl,true);
  submit.value='Saving...';
  submit.blur();
  return _admin.request('addMenu',function(r){
    _admin.disabled(setEl,false);
    submit.value='Save';
    if(r!=='OK'){return _admin.error(r);}
    return _admin.success(r,function(){
      return _admin.go('menus/menu');
    });
  },function(e){
    _admin.disabled(setEl,false);
    submit.value='Save';
    return _admin.error(e);
  },setData);
},
/* initialize */
init:function(){
  var submit=document.querySelector('input[name="submit"]');
  if(!submit){
    return _admin.error('Error: Element "submit" is not detected.');
  }submit.onclick=this.save;
  return this;
}
};menuNew.init();


