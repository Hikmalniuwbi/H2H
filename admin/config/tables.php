<?php
// Table configurations for AdminLTE CRUD H2H
global $tables;
$tables = [
 'news' => ['label'=>'News','pk'=>'news_id','fields'=>[
  'title'=>['label'=>'News Title','type'=>'text','required'=>true],
  'tagline'=>['label'=>'Tagline / Category','type'=>'text'],
  'content'=>['label'=>'News Content','type'=>'textarea','required'=>true],
  'published_at'=>['label'=>'Published At','type'=>'datetime'],
  'image'=>['label'=>'News Image','type'=>'image','folder'=>'news']
 ]],
 'members' => ['label'=>'Members','pk'=>'member_id','fields'=>[
  'name'=>['label'=>'Member Name','type'=>'text','required'=>true],
  'position'=>['label'=>'Position','type'=>'text'],
  'birth_date'=>['label'=>'Birth Date','type'=>'date'],
  'blood_type'=>['label'=>'Blood Type','type'=>'text'],
  'zodiac'=>['label'=>'Zodiac','type'=>'text'],
  'debut_year'=>['label'=>'Debut Year','type'=>'number'],
  'height_cm'=>['label'=>'Height (cm)','type'=>'number'],
  'mbti'=>['label'=>'MBTI','type'=>'text'],
  'nationality'=>['label'=>'Nationality','type'=>'text'],
  'about'=>['label'=>'About Member','type'=>'textarea'],
  'fun_facts'=>['label'=>'Fun Facts','type'=>'textarea'],
  'profile_image'=>['label'=>'Profile Image','type'=>'image','folder'=>'members']
 ]],
 'albums' => ['label'=>'Albums','pk'=>'album_id','fields'=>[
  'album_title'=>['label'=>'Album Title','type'=>'text','required'=>true],
  'release_date'=>['label'=>'Release Date','type'=>'date'],
  'album_type'=>['label'=>'Album Type','type'=>'text'],
  'cover_image'=>['label'=>'Album Cover','type'=>'image','folder'=>'albums']
 ]],
 'songs' => ['label'=>'Songs','pk'=>'song_id','fields'=>[
  'album_id'=>['label'=>'Album','type'=>'fk','table'=>'albums','key'=>'album_id','display'=>'album_title','required'=>true],
  'song_title'=>['label'=>'Song Title','type'=>'text','required'=>true],
  'duration'=>['label'=>'Duration (HH:MM:SS)','type'=>'time'],
  'audio_file'=>['label'=>'Audio File','type'=>'audio','folder'=>'songs']
 ]],
 'merchandise' => ['label'=>'Merchandise','pk'=>'merchandise_id','fields'=>[
  'product_name'=>['label'=>'Product Name','type'=>'text','required'=>true],
  'category'=>['label'=>'Category','type'=>'enum','options'=>['accessories','lightstick','apparel','photocard']],
  'description'=>['label'=>'Description','type'=>'textarea'],
  'price'=>['label'=>'Price','type'=>'number','step'=>'0.01','required'=>true],
  'image'=>['label'=>'Product Image','type'=>'image','folder'=>'merchandise'],
  'stock'=>['label'=>'Stock','type'=>'number','required'=>true]
 ]],
 'event_categories' => ['label'=>'Event Categories','pk'=>'category_id','fields'=>[
  'category_name'=>['label'=>'Category Name','type'=>'text','required'=>true],
  'description'=>['label'=>'Category Description','type'=>'textarea']
 ]],
 'events' => ['label'=>'Events','pk'=>'event_id','fields'=>[
  'event_name'=>['label'=>'Event Name','type'=>'text','required'=>true],
  'event_date'=>['label'=>'Date & Time','type'=>'datetime'],
  'location'=>['label'=>'Location','type'=>'text'],
  'price'=>['label'=>'Ticket Price','type'=>'number','step'=>'0.01'],
  'ticket_stock'=>['label'=>'Ticket Quota','type'=>'number'],
  'ticket_link'=>['label'=>'Ticket Link (External)','type'=>'text'],
  'description'=>['label'=>'Description','type'=>'textarea'],
  'category_id'=>['label'=>'Event Category','type'=>'fk','table'=>'event_categories','key'=>'category_id','display'=>'category_name'],
  'image'=>['label'=>'Event Poster','type'=>'image','folder'=>'events']
 ]],
 'gallery' => ['label'=>'Gallery','pk'=>'gallery_id','fields'=>[
  'member_id'=>['label'=>'Member','type'=>'fk','table'=>'members','key'=>'member_id','display'=>'name','required'=>true],
  'image'=>['label'=>'Gallery Image','type'=>'image','folder'=>'gallery','required'=>true],
  'caption'=>['label'=>'Caption','type'=>'text']
 ]],
 'users' => ['label'=>'Users','pk'=>'user_id','fields'=>[
  'name'=>['label'=>'Full Name','type'=>'text','required'=>true],
  'email'=>['label'=>'Email Address','type'=>'email','required'=>true],
  'password'=>['label'=>'Password (hashed)','type'=>'password','required'=>true],
  'phone'=>['label'=>'Phone Number','type'=>'text'],
  'address'=>['label'=>'Address','type'=>'textarea'],
  'role'=>['label'=>'Role','type'=>'enum','options'=>['admin','customer'],'required'=>true]
 ]],
 'orders' => ['label'=>'Orders','pk'=>'order_id','fields'=>[
  'user_id'=>['label'=>'Customer','type'=>'fk','table'=>'users','key'=>'user_id','display'=>'name','required'=>true],
  'order_date'=>['label'=>'Order Date','type'=>'datetime'],
  'total_amount'=>['label'=>'Total Amount','type'=>'number','step'=>'0.01','required'=>true],
  'shipping_address'=>['label'=>'Shipping Address','type'=>'textarea','required'=>true],
  'order_status'=>['label'=>'Order Status','type'=>'text']
 ]],
 'order_items' => ['label'=>'Order Items','pk'=>'order_item_id','fields'=>[
  'order_id'=>['label'=>'Order ID','type'=>'fk','table'=>'orders','key'=>'order_id','display'=>'order_id','required'=>true],
  'merchandise_id'=>['label'=>'Merchandise Item','type'=>'fk','table'=>'merchandise','key'=>'merchandise_id','display'=>'product_name','required'=>true],
  'quantity'=>['label'=>'Quantity','type'=>'number','required'=>true],
  'price'=>['label'=>'Unit Price','type'=>'number','step'=>'0.01','required'=>true],
  'subtotal'=>['label'=>'Subtotal','type'=>'number','step'=>'0.01','required'=>true]
 ]],
 'feedbacks' => ['label'=>'Fan Feedbacks','pk'=>'feedback_id','fields'=>[
  'user_id'=>['label'=>'S2U Member','type'=>'fk','table'=>'users','key'=>'user_id','display'=>'name','required'=>true],
  'name'=>['label'=>'Sender Name','type'=>'text','required'=>true],
  'email'=>['label'=>'Sender Email','type'=>'email','required'=>true],
  'message'=>['label'=>'Feedback Message','type'=>'textarea','required'=>true],
  'created_at'=>['label'=>'Submitted At','type'=>'datetime']
 ]]
];
