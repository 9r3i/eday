<div class="index">
<?php
if(EDAY_ADMIN_TYPE=='user'){
  echo '<div class="post-error">Error: Have no access to this page.</div>';
  goto endOfUserAll;
}
global $users,$error,$row,$next,$limit;
if($error||!$users){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfUserAll;
}
echo '<div class="post-row">Total users: '.$row
  .'<a href="'.site::url.'?admin=user/add" class="submit-green menu-add-button" title="Add User">'
  .'<i class="fa fa-user-plus"></i></a>'
  .'</div>';
foreach($users as $user){
  if($user['type']=='master'&&$user['type']!==EDAY_ADMIN_TYPE){
    continue;
  }
  $isAdmin=$user['type']==='admin'?1:0;
  $option='';
  if(EDAY_ADMIN_TYPE=='admin'||EDAY_ADMIN_TYPE=='master'){
    $option='<div class="post-row-option">';
    if(EDAY_ADMIN_TYPE=='master'||EDAY_ADMIN_ID==$user['id']){
      $option.='<a href="'.site::url.'?admin=user/edit/'.$user['id'].'" class="submit-green">'
        .'<i class="fa fa-edit"></i> Edit</a> ';
    }
    if($user['type']!=='master'&&$user['id']!=EDAY_ADMIN_ID){
      $option.='<a href="javascript:userDeleteID('.$user['id'].')" class="submit-red">'
        .'<i class="fa fa-trash"></i> Delete</a> ';
      $option.='<a href="javascript:userMakeAdminID('.$user['id'].','.$isAdmin.')" class="submit-blue">'
        .'<i class="fa fa-user"></i> '.($isAdmin?'Remove as Admin':'Make Admin').'</a> ';
    }
    $option.='</div>';
  }
  echo '<div class="post-row">'
    .'<div class="post-row-title">'.$user['username'].'</div>'
    .'<div class="post-row-detail">'.$user['id'].' &#8213; '.$user['type'].'</div>'
    .$option.'</div>';
}
if($next){
  echo '<div class="post-row" style="text-align:center;padding:20px;">'
    .'<a href="'.site::url.'?admin=user/all/'.$next.'/'.$limit.'" class="submit submit-blue">Next Users</a>'
    .'</div>';
}
?>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/user.js')?>"></script>
<script type="text/javascript">
var USER_ACTION_URL=SITE_URL+'?admin=user/ajax';
</script>
<?php endOfUserAll: ?>

