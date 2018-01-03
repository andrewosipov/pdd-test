<?

if( defined('DOING_AJAX') && DOING_AJAX ){
     
   add_action('wp_ajax_get_themes', 'get_ajax_themes');
   add_action('wp_ajax_nopriv_get_themes', 'get_ajax_themes'); 
   
   add_action('wp_ajax_get_question', 'get_question');
   add_action('wp_ajax_nopriv_get_question', 'get_question'); 
   
   add_action('wp_ajax_check_answer', 'check_answer');
   add_action('wp_ajax_nopriv_check_answer', 'check_answer'); 
   
} 

// берем темы из бвазы по id 
function get_ajax_themes(){
   global $wp;
   
   $terms=get_terms( 'pdd_theme', array( 'hide_empty' => false ) ); 
   $id=array();
   $name=array();
    
   foreach($terms as $key=>$item){
      array_push( $id, $item->term_id );
      array_push( $name, '"'.htmlspecialchars($item->name).'"' );
   }
   
   echo '{ "themes_id": ['.implode( ",", $id ).'], "themes_name": ['.implode( ",", $name ).'] }';
   
   wp_die();
}

// берем вопросы из бвазы по параметрам 
function get_question(){
   global $wp;   
   $count;
   
   if( $_REQUEST['count']=='all' ){ $count="-1"; }  
   elseif( preg_match("/([0-9]+)/i", $_REQUEST['count'], $matches) ){  $count=$matches[1]; } 
   else{ $count=20; }      
   
   if( $_REQUEST['orderby']=='rand' ){  $orderby="rand"; }else{ $orderby="menu_order"; }
   if( $_REQUEST['invert']=='1' ){  $order="desc"; $orderby="id"; }else{ $order="asc";  }     
   if( preg_match("/([0-9,]+)/i", $_REQUEST['theme'], $matches) ){ $category= array( 'relation' => 'OR', array( 'taxonomy' => 'pdd_theme', 'field' => 'id', operator=>'IN', 'terms' => explode( ",", $matches[1] ) ) ); }
   if( preg_match("/([0-9,]+)/i", $_REQUEST['ticket'], $matches) ){ $category= array( 'relation' => 'OR', array( 'taxonomy' => 'pdd_oficial_ticket', 'field' => 'id', operator=>'IN', 'terms' => explode( ",", $matches[1] ) ) ); }
   if( preg_match("/([0-9]+)/i", $_REQUEST['custom_ticket'], $matches) ){ $custom_ticket = $matches[1]; }
   
   
   $posts=array();   
   if( $_REQUEST['custom_ticket']!='' ){  // echo $custom_ticket;        
      
      $mode = get_field( 'ticket_mode', $custom_ticket );
      
      if( $mode=='questions' ){ $posts = get_field( 'questions', $custom_ticket ); }
      if( $mode=='ticket' || $mode=='ticket_theme' ){ 
        $tickets = get_field( 'official_ticket', $custom_ticket ); 
        $query = new WP_Query(  array(
          'posts_per_page'  => $count,
          'post_type'       => 'question',
          'orderby'         => $orderby,
          'order'           => $order,
          'tax_query'       => array( 'relation' => 'OR', array( 'taxonomy' => 'pdd_oficial_ticket', 'field' => 'id', operator=>'IN', 'terms' => $tickets ) )
        ));
        $posts=$query->posts;  //print_r( $query );
      }
      if( $mode=='theme'  || $mode=='ticket_theme' ){ 
        $themes = get_field( 'theme', $custom_ticket );
        $query = new WP_Query(  array(
          'posts_per_page'  => $count,
          'post_type'       => 'question',
          'orderby'         => $orderby,
          'order'           => $order,
          'tax_query'       => array( 'relation' => 'OR', array( 'taxonomy' => 'pdd_theme', 'field' => 'id', operator=>'IN', 'terms' => $themes ) )
        ));
        $posts=array_merge( $posts, $query->posts ); 
      } 
              
   }else{
   
       $query = new WP_Query(  array(
          'posts_per_page'  => $count,
          'post_type'       => 'question',
          'orderby'         => $orderby,
          'order'           => $order,
          'tax_query'       => $category
        ));
        $posts=$query->posts;
       
      // print_r( $query ); exit;
   
   }
   
   //print_r( $posts ); exit;
   ?><div class="questions">
   <?
   foreach($posts as $key=>$post){ setup_postdata($post);
          $answers=get_field( "answers", $post->ID );
          $thumb=get_the_post_thumbnail( $post->ID, 'full' );
          $themes=get_the_terms( $post->ID,'pdd_theme' );
          $themes_id_arr=array(); $themes_name_arr=array();
          foreach( $themes as $key=>$item ){
            array_push( $themes_id_arr, $item->term_id );
            array_push( $themes_name_arr, '"'. htmlspecialchars( $item->name ).'"' );
          }
          //print_r( $themes_id_arr ); exit;
          ?>
            <div class="question-item" data-id="<?=$post->ID;?>" data-themes-id="[<?=implode( ',', $themes_id_arr );?>]" data-themes-names='[<?=implode( ',', $themes_name_arr );?>]'>
               <div class="image"><?=($thumb=='')?'<img src="'.plugin_dir_url(__FILE__).'no-img.jpg" alt="" />':$thumb;?></div>
               <div class="question"><?=$post->post_title;?></div>
               <ul class="answers">
                 <?foreach( $answers as $kkey=>$answer ): 
                    $answer['text']=wp_strip_all_tags($answer['text']);
                    ?><li data-status="<?=$answer["is_it_true"];?>" data-id="<?=$kkey;?>" data-question="<?=$key;?>" data-answer="<?=$kkey;?>"><a href="" data-question="<?=$key;?>" data-answer="<?=$kkey;?>"><span><?=$kkey+1;?>. </span><?=$answer['text'];?></a></li><?
                 endforeach;?>
               </ul>
               <div class="comment hide">
                  <div class="title">Комментарий к вопросу</div>
                  <div class="text"><?=get_field( "comment", $post->ID );;?></div>
               </div>
            </div>
            
          <?
    }
    ?></div><?
   
   wp_reset_postdata();
   
   wp_die();
}


function check_answer(){
   global $wp;
   
   wp_die();
}

/*function check_answer(){
   global $wp;
   wp_die();
} */


?>