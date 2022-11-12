/* user.js */
var W,D,USER_ACTION_URL,USER_IS_USER;

/* add a user */
function userSubmitAdd(){
  var username=qs('input[name="username"]').value;
  var password=qs('input[name="password"]').value;
  var cpassword=qs('input[name="cpassword"]').value;
  if((password!==''||cpassword!=='')&&password!==cpassword){
    return error('Password and confirm password is not equal.');
  }
  return confirmation('Add a user?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    var data={
      request:'addUser',
      username:username,
      password:password,
      cpassword:cpassword,
    };
    W.post(USER_ACTION_URL,function(r){
      adminLoader(false);
      if(r.match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.assign('?admin=user/all');
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

/* save a user */
function userSubmitSave(){
  var id=qs('input[name="id"]').value;
  var username=qs('input[name="username"]').value;
  var password=qs('input[name="password"]').value;
  var cpassword=qs('input[name="cpassword"]').value;
  if(USER_ID!=id){
    return error('Invalid user ID.');
  }
  if((password!==''||cpassword!=='')&&password!==cpassword){
    return error('Password and confirm password is not equal.\r\n'
      +'Leave this blank means no change.');
  }
  return confirmation('Save this user?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    var data={
      request:'saveUser',
      username:username,
      password:password,
      cpassword:cpassword,
      id:id
    };
    W.post(USER_ACTION_URL,function(r){
      adminLoader(false);
      if(r.match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          if(!USER_IS_USER){
            W.location.assign('?admin=user/all');
          }
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

/* make a user an admin by id */
function userMakeAdminID(id,isAdmin){
  if(!id||!id.toString().match(/^\d+$/)){return error('Invalid ID.');}
  var make=isAdmin?'Remove as admin':'Make admin';
  return confirmation(make+' this user?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    var data={request:'makeAdmin',id:id,isAdmin:isAdmin};
    W.post(USER_ACTION_URL,function(r){
      adminLoader(false);
      if(r.match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.assign('?admin=user/all');
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

/* delete a user by id */
function userDeleteID(id){
  if(!id||!id.toString().match(/^\d+$/)){return error('Invalid ID.');}
  return confirmation('Delete this user?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    var data={request:'deleteUser',id:id};
    W.post(USER_ACTION_URL,function(r){
      adminLoader(false);
      if(r.match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.assign('?admin=user/all');
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


