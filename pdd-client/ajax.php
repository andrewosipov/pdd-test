<?

if( defined('DOING_AJAX') && DOING_AJAX ){
     
   add_action('wp_ajax_get_question', 'get_question_from_server');
   add_action('wp_ajax_nopriv_get_question', 'get_question_from_server'); 
   
   add_action('wp_ajax_add_to_profile', 'add_to_profile');
   add_action('wp_ajax_nopriv_add_to_profile', 'add_to_profile'); 
   
} 

function get_question_from_server(){
   global $wp;
   $params=array();
   
   if( $_REQUEST['action']=='get_question' ){ array_push( $params, "action=get_question" ); }
   if( $_REQUEST['orderby']=='rand' ){  array_push( $params, "orderby=rand" ); }else{ array_push( $params, "orderby=id" ); }   
   if( preg_match("/([0-9,]+)/i", $_REQUEST['theme'], $matches) ){  array_push( $params, "theme=".$matches[1] ); }
   if( preg_match("/([0-9,]+)/i", $_REQUEST['ticket'], $matches) ){ array_push( $params, "ticket=".$matches[1] ); }
   if( $_REQUEST['count']=='all' ){ array_push( $params, "count=all" ); }
   elseif( preg_match("/([0-9]+)/i", $_REQUEST['count'], $matches) ){  array_push( $params, "count=".$matches[1] ); } 
   else{ array_push( $params, "count=20" ); }
  
   //echo SERVER_URL. join( "&", $params ); exit;
   echo get_curl( SERVER_URL. join( "&", $params ) );
   //echo get_curl( SERVER_URL."action=get_question&". );
   
   wp_die();
}


function add_to_profile(){
   global $wp;
   
   if( $_REQUEST['status']=='yes' ){ $status='yes'; }
   else{ $status='no'; }
   
   $date=new DateTime("now"); 
   add_user_meta( get_current_user_id(), "pdd_test_status", array( "status"=>$status, "time"=> date_format( $date, "H:i:s d.m.Y" )  ) );
   
   wp_die();
}

function get_curl($url){  //$url="http://driver-1/";
  
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_USERAGENT, "Vorious API 1.1 Windows (15/4.0.4; 160dpi; 320x480; Sony; MiniPro; mango; semc; en_Us)");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_ENCODING, '');
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:8888");
    $result = curl_exec($ch);  
    
    print_r($url);
    
    curl_close($ch);
    
    
 
  return $result;
}


?>