<div class="index">
<?php
global $posts,$error,$row,$next,$limit;
if($error||!is_array($posts)){
  echo '<div class="post-error">Error: '.$error.'</div>';
  $posts=[];
  goto endOfPostAll;
}
echo '<div class="post-row">Total posts: '.$row.'</div>';
foreach($posts as $post){
  $image_link=$post['picture']&&is_file(EDAY_INDEX_DIR.$post['picture'])
    ?'<a href="'.$post['picture'].'" target="_blank" class="submit-yellow">'
    .'<i class="fa fa-image"></i> Picture</a> ':'';
  echo '<div class="post-row">'
    .'<div class="post-row-title">'.$post['title'].'</div>'
    .'<div class="post-row-detail">Last update: '.base::timeAgo($post['datetime']).'</div>'
    .'<div class="post-row-option">'
    .'<a href="'.site::url.'?post_id='.$post['id'].'" target="_blank" class="submit-blue">'
      .'<i class="fa fa-share-square-o"></i> View</a> '
    .'<a href="'.site::url.'?admin=post/edit/'.$post['id'].'" class="submit-green">'
      .'<i class="fa fa-edit"></i> Edit</a> '
    .'<a href="javascript:postDeleteID('.$post['id'].')" class="submit-red">'
      .'<i class="fa fa-trash"></i> Delete</a> '
    .$image_link
    .'</div>'
    .'</div>';
}
if($next){
  echo '<div class="post-row" style="text-align:center;padding:20px;">'
    .'<a href="'.site::url.'?admin=post/all/'.$next.'/'.$limit.'" class="submit submit-blue">Next Posts</a>'
    .'</div>';
}
?>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/post.js')?>"></script>
<script type="text/javascript">
var POST_ACTION_URL=SITE_URL+'?admin=post/ajax';
</script>
<?php
endOfPostAll:


