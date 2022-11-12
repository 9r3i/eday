/* option.js */
var W,D,OPTION_ACTION_URL;


function settingSubmitSave(){
  var name=qs('input[name="name"]').value;
  var description=qs('input[name="description"]').value;
  var keyword=qs('input[name="keyword"]').value;
  var theme=qs('select[name="theme"]').value;
  var timezone=qs('select[name="timezone"]').value;
  var api=qs('select[name="api"]').value;
  var dbapi=qs('select[name="dbapi"]').value;
  var unform=false;
  var data={
    name:name,
    description:description,
    keyword:keyword,
    theme:theme,
    timezone:timezone,
    api:api,
    dbapi:dbapi,
    request:'saveSettings',
  };
  adminLoader();
  W.post(OPTION_ACTION_URL,function(r){
    adminLoader(false);
    if(r.match(/^error/ig)){return error(r);}
    else if(r=='OK'){
      return success(r,function(y){
        
      });
    }
    console.log(r);
    return salert('Something is going wrong.');
  },data,unform,null,null,null,function(e){
    adminLoader(false);
    return error(e);
  });
}


