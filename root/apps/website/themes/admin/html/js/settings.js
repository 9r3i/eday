/* settings.js */
var settings={save:function(){
  var cNames={
    core:[
        'allowDatabaseAPI','mainPage','mainPageID',
        'admin','theme','feedLimit',
      ],
    database:['driver','dbhost','dbname','dbuser','dbpass'],
    website:['name','title','description','keywords','robots'],
    load:['page01','page02','page03'],
  },
  cData={},cError=false,cEl=[],
  submit=document.querySelector('input[name="submit"]');
  if(!submit){return _admin.error('Error: Some element is not detected.');}
  for(var sec in cNames){
    cData[sec]={};
    for(var i=0;i<cNames[sec].length;i++){
      var el=document.querySelector('input[name="'+cNames[sec][i]+'"]');
      if(!el){
        cError='Element "'+cNames[sec][i]+'" is not detected.';
        break;
      }
      if(cError){break;}
      cData[sec][cNames[sec][i]]=el.value;
      cEl.push(el);
    }
  }
  if(cError){return _admin.error('Error: '+cError);}
  cEl.push(submit);
  _admin.disabled(cEl,true);
  submit.value='Saving...';
  submit.blur();
  return _admin.request('saveSettings',function(r){
    _admin.disabled(cEl,false);
    submit.value='Save';
    if(r!=='OK'){return _admin.error(r);}
    return _admin.success(r);
  },function(e){
    _admin.disabled(cEl,false);
    submit.value='Save';
    return _admin.error(e);
  },cData);
}};


