<?

wp_enqueue_script( 'nicescroll', plugin_dir_url(__FILE__)."jquery.nicescroll.min.js", array( 'jquery' ) );
wp_enqueue_script( 'pdd_test_js', plugin_dir_url(__FILE__)."script.js", array( 'jquery' ) );
wp_enqueue_style( 'pdd_test_css', plugin_dir_url(__FILE__)."style.css" );

add_action( 'wp_enqueue_scripts', 'myajax_data', 99 );
function myajax_data(){
	wp_localize_script('jquery', 'myajax', 
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);   
}

add_shortcode('pdd_test', 'pdd_test_shorcode');
function pdd_test_shorcode( $attr ) {   //print_r($attr); exit;
 if( is_user_logged_in() ){ 
   $ticket; $theme; $count; $orderby; 
	 if( !empty($attr['ticket']) ){ $ticket=$attr['ticket']; }
   if( !empty($attr['theme']) ){  $theme=$attr['theme']; }
   if( !empty($attr['count']) ){  $count=$attr['count']; }
   if( !empty($attr['orderby']) ){ $orderby=$attr['orderby']; }
   ?>
   <div class="pdd-test-container">
      <div class="title-container">
          <div class="title">Экзамен ПДД 2016 онлайн 1</div>
          <div class="descr">С изменениями от 1 сентября 2016 года</div>
      </div>
      <div class="panel">
          <a href="" class="button square rand-question" title="Случайные 20 вопросов">20</a>
          <a href="" class="button square rand-ticket" title="Случайный билет из 40 билетов">Б</a>
          <div class="button select ticket" title="">
            <select name="ticket">
              <option value="">Выбрать билет</option>
            </select>
          </div>
          <div class="button select theme" title="">
            <select name="theme">
              <option value="" selected>Выбрать тему</option>
              <option value="">Выбрать тему</option>
              <option value="">Выбрать тему</option>
              <option value="">Выбрать тему</option>
            </select>
          </div>
          <a href="" class="button square hard-question" title="40 самых сложных вопросов">40</a>
          <a href="" class="button square all-question" title="Отвечать на все 800 вопросов">800</a>
      </div>
      <div class="left">
         <ul class="numbers">
          </ul>
      </div>
      <div class="right">
        <div class="buttons-cover hide">
            <a href="#" class="prev"><span>(Ctrl + &larr;)</span> &laquo; Назад </a> &nbsp;
            <a href="#" class="next">Вперед &raquo; <span>(Ctrl + &rarr;)</span></a>
        </div>
        <div class="question-content">
          <div class="question-item">
            <div class="image"></div>
            <div class="question"></div>
            <ul class="answers">
              
            </ul>
          </div>          
        </div>
        
        <div class="buttons-cover-2">
            <a href="#" class="prev">&laquo; Назад</a>
            <a href="#" class="next">Вперед &raquo; </a>
          </div>
        
        <div class="numbers-cover hide">
          <div class="title">Вопросы </div>
          <ul class="numbers">
          </ul>
          
          <div class="buttons-cover">
            <a href="#" class="prev">&laquo; Назад</a>
            <a href="#" class="next">Вперед &raquo; </a>
          </div>
        </div>
        
        <div class="questions-container"></div>
      </div>
      <div class="result hide">
        <div class="title"></div>
        <div class="descr">
          <p class="count">Число вопросов в тесте: <b></b></p>
          <p class="correct">Число правильных ответов: <b></b></p>
          <p class="error">Число неправильных ответов: <b></b></p> 
        </div>
        <div class="show-answers"><a href="">Посмотреть свои ответы</a></div>
        <div class="note">Ваш результат записан в ваш профиль. <br /><a href="/?page_id=8987" class="statistic">Просмотреть статистику</a></div>
        <div class="result-answers">
        
        </div>
      </div>
   </div>
   <script>
      jQuery(document).ready(function(){ 
        jQuery('.pdd-test-container').pddTest({ url:myajax.url, action:'get_question', ticket:'<?=$ticket?>', theme:'<?=$theme?>', count:'<?=$count?>', orderby:'<?=$orderby?>' }); 
      })
   </script>
   <?
  }else{
    ?>Пользователь не авторизован. Тест не работает<?
  }
}

add_shortcode('pdd_test_statistic', 'pdd_test_statistic_shorcode');
function pdd_test_statistic_shorcode( $attr ) { 
   ?><div class="statistic-container"><?
   $status=get_user_meta( get_current_user_id(), "pdd_test_status" );
   foreach( $status as $item ):
      $stat=( $item['status']=='yes' )?"Тест сдан":"Тест не сдан";
      //$date=new DateTime($item['time']);       
     // if( !$date ){ 
      //$date_=date_format($date,"H:i:s d.m.Y"); 
      //print_r($date_);
      //}
   ?>
     <div class="item">
        <div class="time">Дата теста: <?= $item['time']; ?></div> 
        <div class="status">Статус: <?=$stat;?></div>
     </div>
   <?endforeach;
   //print_r( $status );   
   ?></div><?
}

?>