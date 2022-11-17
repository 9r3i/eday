<?php
/* EdayProductTable
 * ~ EdayProductTable
 * authored by 9r3i
 * https://github.com/9r3i
 * started at april 10th 2022
 */
class EdayProductTable{
  const version='1.0.0';
  private $dir=null;
  private $db=null;
  private $user=null;
  private $cloud=null;
  private $methods=[
    'getScript',
    'pictureUpload',
    'categoryPut',
    'categoryGet',
    'store',
    'orders',
    'storeUpdate',
    'products',
    'newProduct',
    'insertProduct',
    'deleteProduct',
    'updateProduct',
    'editProduct',
    'deleteOrders',
  ];
  private $publicMethods=[
    'publicRate',
    'publicRating',
    'publicProducts',
    'publicProduct',
    'publicCategory',
    'publicCategories',
    'publicStores',
    'publicStore',
    'buyerLogin',
    'buyerRegister',
    'buyerUpdate',
    'buyerOrders',
    'orderPlace',
    'rateSend',
  ];
  public function __construct($db){
    $this->dir=defined('TEMP')?TEMP:__DIR__.'/';
    $this->db=$db;
    $this->header();
    $this->userlog();
    if(isset($_POST['token'],$_POST['method'],$_POST['user'])
      &&$this->validToken($_POST['token'])
      &&in_array($_POST['method'],$this->methods)
      &&method_exists($this,$_POST['method'])){
      $this->user=$_POST['user'];
      $method=$_POST['method'];
      unset($_POST['token']);
      unset($_POST['method']);
      unset($_POST['user']);
      unset($_POST['app']);
      if(isset($_POST['cloud'])){
        $this->cloud=$_POST['cloud'];
        unset($_POST['cloud']);
      }
      $this->tableCheck();
      $this->userCheck();
      $res=@\call_user_func_array([$this,$method],[$_POST]);
      $json=@json_encode($res);
      $err='Error: Something is going wrong.';
      $out=$json?$json:$err;
      return $this->output($out);
    }elseif(isset($_POST['webAdminToken'],$_POST['method'])
      &&in_array($_POST['method'],$this->methods)
      &&method_exists($this,$_POST['method'])){
      $user=$this->db->select('logs','token='.$_POST['webAdminToken']);
      if(isset($user[0])){
        $this->user=$user[0];
        $method=$_POST['method'];
        unset($_POST['method']);
        unset($_POST['webAdminToken']);
        $this->tableCheck();
        $this->userCheck();
        $res=@\call_user_func_array([$this,$method],[$_POST]);
        $json=@json_encode($res);
        $err='Error: Something is going wrong.';
        $out=$json?$json:$err;
        return $this->output($out);
      }
    }elseif(isset($_POST['method'])
      &&in_array($_POST['method'],$this->publicMethods)
      &&method_exists($this,$_POST['method'])){
        $method=$_POST['method'];
        unset($_POST['method']);
        $this->tableCheck();
        $res=@\call_user_func_array([$this,$method],[$_POST]);
        //$json=@json_encode($res,JSON_PRETTY_PRINT);
        $json=@json_encode($res);
        $err='Error: Something is going wrong.';
        $out=$json?$json:$err;
        return $this->output($out);
    }
    /* final error handler */
    header('HTTP/1.1 401 Unauthorized');
    $text='Error: 401 Unauthorized';
    header('Content-Length: '.strlen($text));
    exit($text);
  }
  /* public */
  private function publicReviewGet($post){
    $columns=[
      'review_id'=>0,
      'user_id'=>0,
      'comment'=>'',
      'pictures'=>[],
      'product_id'=>0,
      'product_rating'=>0,
      'seller_id'=>0,
      'seller_rating'=>0,
    ];
    $table='review_table';
    
  }
  private function publicStore($post){
    $whereSeller='seller_id='.$post['seller_id'];
    $seller=$this->db->select('seller_table',$whereSeller);
    $def=[
      'seller_id'=>$post['seller_id'],
      'name'=>'StoreID='.$post['seller_id'],
      'picture'=>'[]',
      'phone'=>'',
      'location'=>'',
      'about'=>'',
      'address'=>'',
    ];
    $seldata=isset($seller[0])?$seller[0]:$def;
    return [
      'type'=>'seller',
      'data'=>$seldata,
    ];
  }
  private function publicStores($post){
    $whereSeller='seller_id='.$post['store'];
    $where='access=public&status=publish&type=product'
      .'&template=standard&author='.$post['store'];
    $sel=$this->db->select('posts',$where);
    $seller=$this->db->search('seller_table',$whereSeller);
    $def=[
      'seller_id'=>$post['store'],
      'name'=>'StoreID='.$post['store'],
      'picture'=>'[]',
      'phone'=>'',
      'location'=>'',
      'about'=>'',
      'address'=>'',
    ];
    $seldata=isset($seller[0])?$seller[0]:$def;
    return [
      'length'=>count($sel),
      'data'=>$sel,
      'seller'=>$seldata,
    ];
  }
  private function publicCategories($post){
    $where='keywords='.$post['tag'];
    $sel=$this->db->search('posts',$where);
    return [
      'length'=>count($sel),
      'data'=>$sel,
    ];
  }
  private function publicCategory($post){
    if(!isset($post['id'])){
      return 'Error: Require category ID.';
    }
    $table='category_table';
    $sel=$this->db->select($table,'aid='.$post['id']);
    if(is_array($sel)&&isset($sel[0])){
      return [
        'type'=>'category',
        'data'=>$sel[0],
      ];
    }
    return 'Error: Failed to save category.';
  }
  private function publicProduct($post){
    if(!isset($post['id'])){
      return 'Error: Require productID.';
    }
    $where='access=public&status=publish&type=product'
      .'&template=standard&url='.$post['id'];
    $sel=$this->db->select('posts',$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data is not found.';
    }
    return [
      'type'=>'single',
      'data'=>$sel[0],
    ];
  }
  private function publicProducts($post){
    $where='access=public&status=publish&type=product'
      .'&template=standard';
    $sel=$this->db->select('posts',$where);
    return [
      'length'=>count($sel),
      'data'=>$sel,
    ];
  }
  private function publicRating($post){
    $def=[
      'product_id'=>0,
      'point'=>0,
      'length'=>0,
    ];
    if(!isset($post['pid'])){
      return $def;
    }
    $def['product_id']=$post['pid'];
    $sel=$this->db->select('rating_table','product_id='
      .$post['pid']);
    if(!is_array($sel)||count($sel)<1){
      return $def;
    }
    $def['length']=count($sel);
    $point=0;
    foreach($sel as $rate){
      $point+=intval($rate['value']);
    }
    $def['point']=$point;
    return $def;
  }
  private function publicRate($post){
    $def=[
      'product_id'=>0,
      'rate'=>'0.0',
    ];
    if(!isset($post['pid'])){
      return $def;
    }
    $def['product_id']=$post['pid'];
    $sel=$this->db->select('rating_table','product_id='
      .$post['pid']);
    if(!is_array($sel)||count($sel)<1){
      return $def;
    }
    $length=count($sel);
    $point=0;
    foreach($sel as $rate){
      $point+=intval($rate['value']);
    }
    $max=5;
    $total=$length*$max;
    $value=floor(($point/$total)*($max*10));
    $rate=$value/10;
    $def['rate']=number_format($rate,1);
    return $def;
  }
  /* public buyer */
  private function buyerUpdate($post){
    if(!isset($post['phone'],$post['password'],
      $post['address'],$post['name'],$post['aid'])){
      return 'Error: Invalid form.';
    }
    if(trim($post['password'])==''){
      unset($post['password']);
    }
    $table='buyer_table';
    $where='aid='.$post['aid'];
    $sel=$this->db->select($table,$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: User is not available.';
    }
    $upd=$this->db->update($table,$where,$post);
    $out=$post;
    unset($out['password']);
    return [
      'type'=>'buyer-update',
      'status'=>'success',
      'data'=>$out,
    ];
  }
  private function buyerLogin($post){
    if(!isset($post['phone'],$post['password'])){
      return 'Error: Invalid form.';
    }
    $table='buyer_table';
    $where='phone='.$post['phone'];
    $valid=$this->db->valid_password($table,$where,$post['password']);
    $sel=$this->db->select($table,$where);
    if(!$valid||!is_array($sel)||!isset($sel[0])){
      return 'Error: Invalid phone or password.';
    }
    $out=$sel[0];
    unset($out['password']);
    return [
      'type'=>'buyer-login',
      'status'=>'success',
      'data'=>$out,
    ];
  }
  private function buyerRegister($post){
    $data=[
      'name'=>'',
      'address'=>'',
      'phone'=>0,
      'password'=>'',
    ];
    $table='buyer_table';
    $error=false;
    foreach($data as $key=>$value){
      if(!isset($post[$key])){
        $error='Error: Invalid form.';
      }$data[$key]=$post[$key];
    }
    if($error){return $error;}
    $sel=$this->db->select($table,'phone='.$post['phone']);
    if(is_array($sel)&&isset($sel[0])){
      return 'Error: Phone number has been used.';
    }
    $ins=$this->db->insert($table,$data);
    if(!$ins){
      return 'Error: Failed to register.';
    }
    $sel=$this->db->select($table,'phone='.$post['phone']);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Failed to get register data.';
    }
    $out=$sel[0];
    unset($out['password']);
    return [
      'type'=>'buyer-register',
      'status'=>'success',
      'data'=>$out,
    ];
  }
  private function buyerOrders($post){
    if(!isset($post['buyer_id'])){
      return 'Error: Invalid form.';
    }
    /* get data buyer */
    $sel=$this->db->select('buyer_table','aid='.$post['buyer_id']);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data buyer is not found.';
    }$buyer=$sel[0];
    if(!isset($post['buyer_name'],$post['buyer_phone'])
      ||$buyer['name']!=$post['buyer_name']
      ||$buyer['phone']!=intval($post['buyer_phone'])
      ){
      return 'Error: Data buyer is corrupted.';
    }
    $data=$this->db->select('order_table','buyer_id='.$post['buyer_id']);
    return [
      'type'=>'buyer-orders',
      'status'=>'success',
      'data'=>$data,
    ];
  }
  private function orderPlace($post){
    if(!isset($post['buyer_id'],$post['orders'])){
      return 'Error: Invalid form.';
    }
    $def=[
      'order_id'=>0, // auto
      'orders'=>[
        'seller_id'=>0,
        'seller_name'=>'',
        'items'=>[
          [
            'aid'=>0,
            'name'=>'',
            'price'=>0,
            'quantity'=>0,
          ],
        ],
      ],
      'buyer_id'=>0,
      'buyer_name'=>'',
      'buyer_phone'=>'',
      'buyer_address'=>'',
      'is_paid'=>0, // auto
      'total'=>0,
    ];
    $odef=[
      'order_id'=>0,
      'buyer_id'=>0,
      'seller_id'=>0,
      'product_id'=>0,
      'quantity'=>0,
      'price'=>0,
      'total'=>0,
      'is_paid'=>0,
      'expire'=>0,
      'currency'=>'',
    ];
    /* get data buyer */
    $sel=$this->db->select('buyer_table','aid='.$post['buyer_id']);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data buyer is not found.';
    }$buyer=$sel[0];
    if(!isset($post['buyer_name'],$post['buyer_phone'],$post['buyer_address'])
      ||$buyer['name']!=$post['buyer_name']
      ||$buyer['phone']!=intval($post['buyer_phone'])
      ||$buyer['address']!=$post['buyer_address']
      ){
      return 'Error: Data buyer is corrupted.';
    }
    /* data orders */
    $expire=date('Y-m-d H:i:s',strtotime('+7 days'));
    $error=false;
    $total=0;
    $ordersData=[];
    $orderUpdate=[];
    foreach($post['orders'] as $seller_id=>$order){
      /* get data seller */
      $sel=$this->db->select('seller_table','seller_id='.$seller_id);
      if(!is_array($sel)||!isset($sel[0])){
        $error='Error: Data seller "seller_id='.$seller_id.'" is not found.';
        break;
      }$seller=$sel[0];
      if(!isset($order['seller_id'],$order['seller_name'])
        ||$seller['seller_id']!=$order['seller_id']
        ||$seller['name']!=$order['seller_name']
        ){
        $error='Error: Data seller "seller_id='.$seller_id.'" is corrupted.';
        break;
      }
      if(!isset($order['items'],$order['totalItem'],$order['totalPrice'],$order['currency'])){
        $error='Error: Some data of orders are missing.';
        break;
      }
      $totalItem=0;
      $totalPrice=0;
      $currency='';
      /* each item */
      foreach($order['items'] as $aid=>$item){
        if(!isset($item['aid'],$item['quantity'],$item['price'])){
          $error='Error: Data item "item_id='.$item['aid'].'" is missing.';
          break 2;
        }
        /* get data item */
        $sel=$this->db->select('posts','aid='.$item['aid']);
        if(!is_array($sel)||!isset($sel[0])){
          $error='Error: Data item "item_id='.$item['aid'].'" is not found.';
          break 2;
        }$prod=$sel[0];
        if(intval($prod['price'])!=intval($item['price'])){
          $error='Error: Data item "item_id='.$item['aid'].'" is corrupted.';
          break 2;
        }
        if(intval($prod['stock'])<1){
          $error='Error: Data item "item_id='.$item['aid']
            .'" ('.$prod['title'].') is out of stock.';
          break 2;
        }
        if(intval($prod['stock'])<intval($item['quantity'])){
          $error='Error: Data item "item_id='.$item['aid']
            .'" ('.$prod['title'].') is only '
            .$prod['stock'].' left item of stock.';
          break 2;
        }
        if($prod['host']!=$order['currency']){
          $error='Error: Unpair currency in "item_id='.$prod['aid'].'".';
          break 2;
        }
        $currency=$order['currency'];
        $totalPrice+=intval($prod['price'])*intval($item['quantity']);
        $totalItem+=intval($item['quantity']);
        /* order data */
        $picture=@@json_decode($prod['picture'],true)[0];
        $mtime=intval(str_replace('.','',microtime(true)));
        $ordersData[]=[
          'order_id'=>$buyer['aid'].'.'
            .$seller['seller_id'].'.'
            .$item['aid'].'.'
            .$item['price'].'.'
            .$item['quantity'].'.'
            .$mtime,
          'buyer_id'=>$buyer['aid'],
          'buyer_name'=>$buyer['name'],
          'buyer_phone'=>$buyer['phone'],
          'buyer_address'=>$buyer['address'],
          'seller_id'=>$seller['seller_id'],
          'seller_name'=>$seller['name'],
          'item_id'=>intval($item['aid']),
          'item_name'=>$item['name'],
          'item_price'=>intval($item['price']),
          'item_quantity'=>intval($item['quantity']),
          'item_picture'=>$picture,
          'is_paid'=>0,
          'expire'=>$expire,
          'currency'=>$currency,
        ];
        /* prepare update data */
        $newprod=$prod;
        $newprod['stock']=intval($prod['stock'])
          -intval($item['quantity']);
        $newprod['start']=intval($prod['start'])
          +intval($item['quantity']);
        $orderUpdate[$newprod['aid']]=$newprod;
      }
      if($totalPrice!=intval($order['totalPrice'])){
        $error='Error: Total price is not equal.';
        break;
      }
      if($totalItem!=intval($order['totalItem'])){
        $error='Error: Total item is not equal.';
        break;
      }
      $total+=$totalPrice;
    }
    if($error){return $error;}
    if($total!=intval($post['total'])){
      return 'Error: Grand total price is not equal.';
    }
    /* order insert */
    foreach($ordersData as $odata){
      $ins=$this->db->insert('order_table',$odata);
      if(!$ins||$this->db->error){
        $error='Error: Failed to place the order.';
        break;
      }
      $this->db->update('posts','aid='.$odata['item_id'],[
        'stock'=>$orderUpdate[$odata['item_id']]['stock'],
        'start'=>$orderUpdate[$odata['item_id']]['start'],
      ]);
    }
    //*/
    if($error){return $error;}
    return [
      'type'=>'place-order',
      'status'=>'success',
      'data'=>$ordersData,
    ];
  }
  private function rateSend($post){
    if(!isset($post['pid'],$post['bid'],$post['rate'])){
      return 'Error: Invalid data.';
    }
    $rid=$post['pid'].'.'.$post['bid'];
    $review=[
        'rate_id'=>$rid,
        'product_id'=>$post['pid'],
        'buyer_id'=>$post['bid'],
        'value'=>$post['rate'],
    ];
    $sel=$this->db->select('rating_table','rate_id='.$rid);
    if(is_array($sel)&&count($sel)>0){
      $rate=$sel[0];
      return 'You gave the rate to this product with '
        .$rate['value'].' star'
        .($rate['value']>1?'s':'').'.';
    }
    $ins=$this->db->insert('rating_table',$review);
    if(!$ins||$this->db->error){
      return 'Error: Failed to give a rate.';
    }return 'OK';
  }
  /* stock */
  private function stockPlus($post){
    if(!isset($post['aid'])){
      return 'Error: Require AID.';
    }
    $where='access=public&status=publish&type=product'
      .'&template=standard&aid='.$post['aid'];
    $sel=$this->db->select('posts',$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data is not found.';
    }
    $data=$sel[0];
    $stock=intval($data['stock']);
    $quantity=isset($post['quantity'])?intval($post['quantity']):1;
    $data['stock']=$stock+$quantity;
    $update=$this->db->update('posts','aid='.$post['aid'],$data);
    if(!$update||$this->db->error){
      return 'Error: Failed to remove from cart.';
    }
    return [
      'type'=>'removefromcart',
      'status'=>'ok',
      'stock'=>$data['stock'],
    ];
  }
  private function stockMinus($post){
    if(!isset($post['aid'])){
      return 'Error: Require AID.';
    }
    $where='access=public&status=publish&type=product'
      .'&template=standard&aid='.$post['aid'];
    $sel=$this->db->select('posts',$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data is not found.';
    }
    $data=$sel[0];
    $stock=intval($data['stock']);
    $quantity=isset($post['quantity'])?intval($post['quantity']):1;
    if($stock<1){
      return 'Error: Out of stock.';
    }
    $data['stock']=$stock-$quantity;
    $update=$this->db->update('posts','aid='.$post['aid'],$data);
    if(!$update||$this->db->error){
      return 'Error: Failed to add to cart.';
    }
    return [
      'type'=>'addtocart',
      'status'=>'ok',
      'stock'=>$data['stock'],
    ];
  }
  /* admin/member */
  private function deleteOrders($post){
    if(!isset($post['ids'])){
      return 'Error: Require IDS.';
    }
    $ids=json_decode($post['ids'],true);
    if(!is_array($ids)){
      return 'Error: Failed to parse IDS.';
    }
    $error=false;
    foreach($ids as $id){
      $where='seller_id='.$this->user['id']
        .'&order_id='.$id;
      $sel=$this->db->select('order_table',$where);
      if(!is_array($sel)||!isset($sel[0])){
        $error='Error: Data is not found.';
        break;
      }
      $delete=$this->db->delete('order_table','order_id='.$id);
      if(!$delete||$this->db->error){
        $error='Error: Failed to delete data.';
        break;
      }
    }
    if($error){return $error;}
    return 'OK';
  }
  private function orders($post){
    $data=$this->db->select('order_table','seller_id='
      .$this->user['id']);
    return [
      'user'=>[
        'id'=>$this->user['id'],
        'name'=>$this->user['name'],
      ],
      'data'=>$data,
      'length'=>count($data),
    ];
  }
  private function editProduct($post){
    if(!isset($post['aid'])){
      return 'Error: Require AID.';
    }
    $where='access=public&status=publish&type=product'
      .'&template=standard&author='.$this->user['id']
      .'&aid='.$post['aid'];
    $sel=$this->db->select('posts',$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data is not found.';
    }
    return [
      'user'=>$this->user,
      'data'=>$sel[0],
    ];
  }
  private function updateProduct($post){
    if(!isset($post['aid'])){
      return 'Error: Require AID.';
    }
    $where='access=public&status=publish&type=product'
      .'&template=standard&author='.$this->user['id']
      .'&aid='.$post['aid'];
    $sel=$this->db->select('posts',$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data is not found.';
    }
    $old=$sel[0];
    $data=$this->updateData($post,$old);
    if(!is_array($data)){
      return $data;
    }
    $update=$this->db->update('posts','aid='.$post['aid'],$data);
    if(!$update||$this->db->error){
      return 'Error: Failed to update data.';
    }
    return $data;
  }
  private function deleteProduct($post){
    if(!isset($post['aid'])){
      return 'Error: Require AID.';
    }
    $where='access=public&status=publish&type=product'
      .'&template=standard&author='.$this->user['id']
      .'&aid='.$post['aid'];
    $sel=$this->db->select('posts',$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data is not found.';
    }
    $delete=$this->db->delete('posts','aid='.$post['aid']);
    if(!$delete||$this->db->error){
      return 'Error: Failed to delete data.';
    }return 'OK';
  }
  private function insertProduct($post){
    $data=$this->newData($post);
    if(!is_array($data)){
      return $data;
    }
    $insert=$this->db->insert('posts',$data);
    if(!$insert||$this->db->error){
      return 'Error: Failed to save data.';
    }
    return $data;
  }
  private function newProduct($post){
    return $this->user;
  }
  private function products($post){
    $where='access=public&status=publish&type=product'
      .'&template=standard&author='.$this->user['id'];
    $sel=$this->db->select('posts',$where);
    return [
      'length'=>count($sel),
      'data'=>$sel,
    ];
  }
  private function storeUpdate($post){
    if(!isset($post['aid'])){
      return 'Error: Require AID.';
    }
    $where='seller_id='.$this->user['id'];
    $sel=$this->db->select('seller_table',$where);
    if(!is_array($sel)||!isset($sel[0])){
      return 'Error: Data is not found.';
    }
    $data=[
      'seller_id'=>$this->user['id'],
      'name'=>$post['name'],
      'picture'=>$post['picture'],
      'phone'=>$post['phone'],
      'location'=>$post['location'],
      'address'=>$post['address'],
      'about'=>$post['about'],
    ];
    $update=$this->db->update('seller_table','aid='.$post['aid'],$data);
    if(!$update||$this->db->error){
      return 'Error: Failed to update data.';
    }
    return $data;
  }
  private function store($post){
    $def=[
      'seller_id'=>$this->user['id'],
      'name'=>$this->user['name'],
      'picture'=>'',
      'phone'=>'[]',
      'location'=>'',
      'about'=>'',
      'address'=>'',
    ];
    $where='seller_id='.$this->user['id'];
    $sel=$this->db->select('seller_table',$where);
    if(!isset($sel[0])){
      $ins=$this->db->insert('seller_table',$def);
    }
    $data=isset($sel[0])?$sel[0]:$def;
    return [
      'user'=>[
        'id'=>$this->user['id'],
        'name'=>$this->user['name'],
      ],
      'data'=>$data,
    ];
    
  }
  private function categoryGet($post){
    if(!isset($post['aid'])){
      return 'Error: Require category ID.';
    }
    $table='category_table';
    $sel=$this->db->select($table,'aid='.$post['aid']);
    if(is_array($sel)&&isset($sel[0])){
      return $sel[0];
    }
    return 'Error: Failed to save category.';
  }
  private function categoryPut($post){
    if(!isset($post['name'])){
      return 'Error: Require category name.';
    }
    $table='category_table';
    $slug=preg_replace('/[^a-z0-9]+/i','-',strtolower($post['name']));
    $sel=$this->db->select($table,'slug='.$slug);
    if(is_array($sel)&&isset($sel[0])){
      return $sel[0];
    }
    $put=$this->db->insert($table,[
      'name'=>$post['name'],
      'slug'=>$slug,
    ]);
    $sel=$this->db->select($table,'slug='.$slug);
    if(is_array($sel)&&isset($sel[0])){
      return $sel[0];
    }
    return 'Error: Failed to save category.';
  }
  private function pictureUpload($post){
    $ptrn='/^data:image\/png;base64,/';
    if(!isset($post['data'])
      ||!preg_match($ptrn,$post['data'])){
      return 'Error: Invalid file.';
    }
    $base=preg_replace($ptrn,'',$post['data']);
    $data=base64_decode($base);
    $md5=md5($data);
    $size=strlen($data);
    $path='files/upload/users/'.$this->user['id'].'/';
    $dir=EDAY_DOC_ROOT.'/'.$path;
    if(!is_dir($dir)){@mkdir($dir,0755,true);}
    $file=$md5.'.jpg';
    if(is_file($dir.$file)){
      return [
        'file'=>$path.$file,
        'size'=>$size,
        'save'=>true,
      ];
    }
    $im=imagecreatefromstring($data);
    imagejpeg($im,$dir.$file,70);
    $save=is_file($dir.$file)?true:false;
    return [
      'file'=>$path.$file,
      'size'=>$size,
      'save'=>$save,
    ];
  }
  private function getScript(){
    $f=$this->dir.'eday.script.js';
    if(!is_file($f)){return false;}
    $c=@file_get_contents($f);
    if(!is_string($c)){return false;}
    $j=@json_encode([
      'status'=>'ok',
      'script'=>$c
    ]);
    return is_string($j)?$j:false;
  }
  /* helper */
  private function tableCheck(){
    $tables=$this->tables('all');
    $created=$this->db->show_tables();
    foreach($tables as $table=>$data){
      if(!in_array($table.'_table',$created)){
        $this->db->create_table($table.'_table');
      }
    }
  }
  private function userCheck(){
    $user=$this->db->select('user_table','user_id='.$this->user['id']);
    if(is_array($user)&&isset($user[0])){return true;}
    $data=$this->tables('user');
    $data['user_id']=$this->user['id'];
    $data['email']=$this->user['email'];
    $data['name']=$this->user['name'];
    $data['privilege']=$this->user['privilege'];
    return $this->db->insert('user_table',$data);
  }
  private function updateData(array $data,array $old){
    $def=$this->prodCol();
    $int=['price','place','stock'];
    $string=['title','host','content'];
    $array=['picture','keywords'];
    $object=['description'];
    $error=false;
    foreach($int as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      if(!preg_match('/^\d+$/',$data[$col])){
        $error='Error: Invalid value data column "'.$col.'".';
        break;
      }
      $def[$col]=intval($data[$col]);
    }
    if($error){return $error;}
    foreach($string as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      $def[$col]=$data[$col];
    }
    if($error){return $error;}
    foreach($array as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      if(!is_array(@json_decode($data[$col]))){
        $error='Error: Invalid value data column "'.$col.'".';
        break;
      }
      $def[$col]=$data[$col];
    }
    if($error){return $error;}
    foreach($object as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      if(!is_object(@json_decode($data[$col]))){
        $error='Error: Invalid value data column "'.$col.'".';
        break;
      }
      $def[$col]=$data[$col];
    }
    if($error){return $error;}
    $def['slug']=$old['slug'];
    $def['start']=$old['start'];
    $def['author']=$old['author'];
    return $def;
  }
  private function newData(array $data){
    $def=$this->prodCol();
    $int=['price','place','stock'];
    $string=['title','host','content'];
    $array=['picture','keywords'];
    $object=['description'];
    $error=false;
    foreach($int as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      if(!preg_match('/^\d+$/',$data[$col])){
        $error='Error: Invalid value data column "'.$col.'".';
        break;
      }
      $def[$col]=intval($data[$col]);
    }
    if($error){return $error;}
    foreach($string as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      $def[$col]=$data[$col];
    }
    if($error){return $error;}
    foreach($array as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      if(!is_array(@json_decode($data[$col]))){
        $error='Error: Invalid value data column "'.$col.'".';
        break;
      }
      $def[$col]=$data[$col];
    }
    if($error){return $error;}
    foreach($object as $col){
      if(!isset($data[$col])){
        $error='Error: Data column "'.$col.'" is not found.';
        break;
      }
      if(!is_object(@json_decode($data[$col]))){
        $error='Error: Invalid value data column "'.$col.'".';
        break;
      }
      $def[$col]=$data[$col];
    }
    if($error){return $error;}
    return $def;
  }
  private function prodCol(){
    /*
    ========================= auto
    aid   :int   :auto:123
    time  :int   :auto:1573879062
    cid   :string:auto:5dcf7d16
    ========================= default
    status  :string:publish (draft/trash)
    type    :string:product (locked)
    access  :string:public (locked)
    template:string:standard (locked)
    ========================= auto-generate
    url        :string:slug
    datetime   :string:datetime/last_updated
    start      :string: --sold(int)
    ========================= modified
    title      :string:name
    price      :int   :price
    stock      :int   :stock
    host       :string: --currency
    place      :string: --bprice(int)
    author     :string: --seller/seller_id(int)
    keywords   :string: --categories(array--json)
    picture    :string: --pictures(array--json)
    description:string: --specification(array--json)
    content    :string:description
    trainer    :string: --badge
    end        :string: --other(array--json)
    =========================
    */
    return [
      'status'=>'publish',
      'access'=>'public', // locked
      'type'=>'product', // locked
      'template'=>'standard', // locked
      
      'datetime'=>date('Y-m-d H:i:s'), // auto-generate
      'url'=>$this->newSlug(), // auto-generate
      'start'=>0, // sold (auto-generate)
      'author'=>$this->user['id'], // seller_id
      
      'title'=>'', // name
      'price'=>0,
      'stock'=>0,
      'host'=>'Rp', // currency
      'place'=>0, // before_discount
      'content'=>'', // description
      'keywords'=>'[]', // categories (array--json)
      'picture'=>'[]', // pictures (array--json)
      'description'=>'{}', // specification (object--json)
      
      'trainer'=>'', // badge
      'end'=>'{}', // other (object--json)
    ];
  }
  private function newSlug():string{
    $bx=1600110939;
    $cx=microtime(true)-$bx;
    $dx=implode('',explode('.',$cx));
    $ex=mt_rand(pow(10,strlen($dx)-1),$dx);
    $xx=base_convert($ex,10,36);
    return strrev($xx);
  }
  private function toSlug($data):string{
    $ndata=@json_encode($data)
          .uniqid()
          .mt_rand()
          .microtime(true);
    $raw=base64_encode(md5($ndata.strlen($ndata),true));
    return preg_replace('/[^a-z0-9]+/i','',$raw);
  }
  private function tables(string $key){
    $tables=[
      'product'=>[
        'product_id'=>0,
        'name'=>'',
        'price'=>0,
        'before_discount'=>0,
        'currency'=>'IDR',
        'seller_id'=>0,
        'stock'=>0,
        'pictures'=>[],
        'sold'=>0,
        'badge'=>'',
        'description'=>'',
        'category_id'=>0,
        'size'=>0,
        'colors'=>[],
        'specification'=>[],
        'status'=>'publish',
      ],
      'seller'=>[
        'seller_id'=>0,
        'name'=>'',
        'picture'=>'',
        'about'=>'',
        'location'=>'',
        'username'=>'',
        'password'=>'',
      ],
      'review'=>[
        'review_id'=>0,
        'user_id'=>0,
        'comment'=>'',
        'pictures'=>[],
        'product_id'=>0,
        'product_rating'=>0,
        'seller_id'=>0,
        'seller_rating'=>0,
      ],
      'rating'=>[
        'rate_id'=>0,
        'product_id'=>0,
        'buyer_id'=>0,
        'value'=>0,
      ],
      'voucher'=>[
        'voucher_id'=>0,
        'name'=>'',
        'value'=>0,
        'currency'=>'IDR',
        'seller_id'=>0,
        'expire'=>0,
      ],
      'category'=>[
        'category_id'=>0,
        'name'=>'',
        'slug'=>'',
        'parent'=>0,
      ],
      'user'=>[
        'user_id'=>0,
        'name'=>'',
        'address'=>'',
        'picture'=>'',
        'phone'=>0,
        'password'=>'',
        'gender'=>0,
        'email'=>'',
        'privilege'=>'user',
        'vouchers'=>[],
      ],
      'order'=>[
        'order_id'=>0,
        'orders'=>[
          'seller_id'=>0,
          'seller_name'=>'',
          'items'=>[
            [
              'aid'=>0,
              'name'=>'',
              'price'=>0,
              'quantity'=>0,
            ],
          ],
        ],
        'buyer_id'=>0,
        'buyer_name'=>'',
        'buyer_phone'=>'',
        'buyer_address'=>'',
        'is_paid'=>0,
        'total'=>0,
      ],
      'invoice'=>[
        'invoice_id'=>0,
        'seller_id'=>0,
        'user_id'=>0,
        'content'=>'',
        'total'=>0,
        'is_paid'=>0,
      ],
      'payment'=>[
        'payment_id'=>0,
        'invoice_id'=>0,
        'order_id'=>0,
        'total'=>0,
      ],
      'buyer'=>[
        'buyer_id'=>0,
        'name'=>'',
        'address'=>'',
        'phone'=>0,
        'password'=>'',
      ],
    ];
    return isset($tables[$key])?$tables[$key]
      :($key=='all'?$tables:null);
  }
  private function validToken($token):bool{
    $time=intval(base_convert(strtolower($token),36,10));
    return $time>time()?true:false;
  }
  private function output(string $out=''){
    header('Content-Length: '.strlen($out));
    header('HTTP/1.1 200 OK');
    exit($out);
  }
  private function userlog(){
    $ip=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
    $time=date('ymd-His');
    $o=@fopen($this->dir.'userlog.txt','ab');
    $get=@json_encode($_GET);
    $post=@json_encode($_POST);
    $ua=@json_encode($_SERVER['HTTP_USER_AGENT']);
    $w=@fwrite($o,$time.'|'.$ip.'|'
      .$_SERVER['REQUEST_METHOD'].'|'
      .($get?$get:'FAILED').'|'
      .($post?$post:'FAILED').'|'
      .($ua?$ua:'FAILED')
      ."\n");
    @fclose($o);
    return true;
  }
  private function header(){
    /* set time limit */
    @set_time_limit(false);
    /* set default timezone */
    date_default_timezone_set('Asia/Jakarta');
    /* access control - to allow the access via ajax */
    header('Access-Control-Allow-Origin: *'); // allow origin
    header('Access-Control-Request-Method: POST, GET, OPTIONS'); // request method
    header('Access-Control-Request-Headers: X-PINGOTHER, Content-Type'); // request header
    header('Access-Control-Max-Age: 86400'); // max age (24 hours)
    header('Access-Control-Allow-Credentials: true'); // allow credentials
    /* set content type of response header */
    header('Content-Type: text/plain;charset=utf-8;');
    /* checking options */
    if(isset($_SERVER['REQUEST_METHOD'])
      &&strtoupper($_SERVER['REQUEST_METHOD'])=='OPTIONS'){
      header('Content-Language: en-US');
      header('Content-Encoding: gzip');
      header('Content-Length: 0');
      header('Vary: Accept-Encoding, Origin');
      header('HTTP/1.1 200 OK');
      exit;
    }
  }
}


