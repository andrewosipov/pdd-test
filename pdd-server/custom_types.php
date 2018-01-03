<?

  // новый тип данных Вопрос
  add_action( 'init', 'create_question' );
  function create_question() {
      register_post_type( 'question',
          array(
              'labels' => array(
                  'name' => "Вопросы",
                  'singular_name' => "Вопрос",
                  'add_new'         => 'Добавить вопрос',
                  'add_new_item'    => 'Добавить вопрос',
                  'edit'            => 'Редактировать',
                  'edit_item'       => 'Редактировать вопрос',
                  'new_item'        => 'Новый вопрос',
                  'all_items'       => 'Все вопросы',
                  'view'            => 'Посмотреть вопрос',
                  'view_item'       => 'Посмотреть вопрос',
                  'search_items'    => 'Найти вопросы',
                  'not_found'       => 'Вопросы не найдены',
              ),
              'public'    => true,
              'menu_icon' => 'dashicons-images-alt2',
              'menu_position' => 4,
              'taxonomies'    => array('pdd_oficial_ticket','pdd_theme'),
              'hierarchical'  => 'false',
              'supports' 	    => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),              
              'has_archive'   => true,
              'rewrite' => array( 'slug'=>'question' ),
          )
      );
  } 
  
  
  /* 
    заполняем колонки в админке 
    для видеогалереи
  */
  function fill_columns_question( $column ) {
  	global $post;  
    //$term=wp_get_object_terms( $post->ID, 'pdd_oficial_ticket' );
     //print_r($term); exit;
    //$screen_info = get_current_screen(); 
  	switch ( $column ) {
  		case 'preview':
  			if( $post->post_parent=='0' ){ 
           echo '<a href="'.get_edit_post_link().'">'.get_the_post_thumbnail($post->ID, array(100,100), array() ).'</a>';
        }else{
           echo '<div class="child"><a href="'.get_edit_post_link().'">'.get_the_post_thumbnail($post->ID, array(100,100), array() ).'</a></div>';
        }
  			break;
      case 'theme':{
          $terms=wp_get_object_terms( $post->ID, 'pdd_theme' );
          foreach( $terms as $item ){
             echo $item->name.", ";
          }                
      } break;
      case 'ticket':{
        $terms=wp_get_object_terms( $post->ID, 'pdd_oficial_ticket' );
          foreach( $terms as $item ){
             echo $item->name.", ";
          }  
      } break;
      case 'number':{
        echo get_field( 'number_in_ticket', $post->ID );
      } break;
      
  	}
  }     
  
  /* 
    создаем колонки в админке 
    для видеогалереи
    error_log( print_r( 111 , true ) );
  */
  function add_columns_question($my_columns) {
  	$my_columns = array(
  		'cb' => '<input type="checkbox" />',
  		'preview'  => '',
      'title'    => 'Заголовок',
      'theme'    => 'Тема',
      'ticket'   => 'Номе билета',
      'number'   => 'Номер вопроса',
      'date'     => 'Опубликовано',
  		); 
    add_action( 'manage_pages_custom_column', 'fill_columns_question',10,2 ); 
  	return $my_columns;
  }             
  add_filter("manage_edit-question_columns", 'add_columns_question'); 
  
  // настройки css админки
  function admin_css_setup() {
    $style=array();
    // разделители в меню
    //array_push($style, "#adminmenu li.wp-menu-separator {margin: 0; background: #444;}");
    // ширина колонки с превью
    array_push($style, ".column-preview{ width:100px; }");
    array_push($style, ".column-title .child{ display:inline-block; width:110px; vertical-align:top; float:left; }");
    array_push($style, ".column-title .child img{ width:100px; height:auto; }");
    array_push($style, ".acf-input .thumbnail img, .acf-input .thumbnail{ display:inline-block; width:30px !important; height:22px !important; }");
    array_push($style, ".acf-input span.acf-rel-item:after{ clear:both; content:''; }");
    echo "<style type='text/css'>".implode($style)."</style>\n";
  }
  add_action( 'admin_head', 'admin_css_setup' ); 
  
  // новый тип данных Билет
  add_action( 'init', 'create_ticket' );
  function create_ticket() {
      register_post_type( 'ticket',
          array(
              'labels' => array(
                  'name' => "Кастомный Билет",
                  'singular_name' => "Кастомный Билет",
                  'add_new'         => 'Добавить кастомный билет',
                  'add_new_item'    => 'Добавить кастомный билет',
                  'edit'            => 'Редактировать',
                  'edit_item'       => 'Редактировать кастомный билет',
                  'new_item'        => 'Новый кастомный билет',
                  'all_items'       => 'Все кастомные билеты',
                  'view'            => 'Посмотреть кастомный билет',
                  'view_item'       => 'Посмотреть кастомный билет',
                  'search_items'    => 'Найти билеты',
                  'not_found'       => 'Билеты не найдены',
              ),
              'public'    => true,
              'menu_icon' => 'dashicons-images-alt2',
              'menu_position' => 4,
              'hierarchical'  => 'false',
              'supports' 	    => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),              
              'has_archive'   => true,
              'rewrite' => array( 'slug'=>'ticket' ),
          )
      );
  } 
  
  // новая таксономия Тема
  add_action( 'init', 'create_taxonomy_theme' );
  function create_taxonomy_theme() {
     register_taxonomy(
        'pdd_theme',
        array( 'question' ),
        array(
      		'label'                 => '', // определяется параметром $labels->name
      		'labels'                => array(
          		'name'              => 'Тема вопроса',
          		'singular_name'     => 'Тема вопроса',
          		'search_items'      => 'Найти темы',
          		'all_items'         => 'Все темы',
          		'parent_item'       => 'Родительская тема',
          		'parent_item_colon' => 'Родительская тема:',
          		'edit_item'         => 'Редактировать тему',
          		'update_item'       => 'Обновить тему',
          		'add_new_item'      => 'Добавить новую тему',
          		'new_item_name'     => 'Название новой тему',
          		'menu_name'         => 'Темы',
          ),
      		'description'           => '', // описание таксономии
      		'public'                => true,
      		'publicly_queryable'    => null, // равен аргументу public
      		'show_in_nav_menus'     => true, // равен аргументу public
      		'show_ui'               => true, // равен аргументу public
      		'show_tagcloud'         => !true, // равен аргументу show_ui
      		'hierarchical'          => true,
      		'update_count_callback' => '',
      		'rewrite'               => true,
      		//'query_var'             => $taxonomy, // название параметра запроса
      		'capabilities'          => array(),
      		'meta_box_cb'           => null, // callback функция. Отвечает за html код метабокса (с версии 3.8): post_categories_meta_box или post_tags_meta_box. Если указать false, то метабокс будет отключен вообще
      		'show_admin_column'     => false, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
      		'_builtin'              => false,
      		'show_in_quick_edit'    => null, // по умолчанию значение show_ui
      	)        
     );
  }
  
  // новая таксономия Официальный Билет
  add_action( 'init', 'create_taxonomy_official_ticket' );
  function create_taxonomy_official_ticket() {
     register_taxonomy(
        'pdd_oficial_ticket',
        array( 'question' ),
        array(
      		'label'                 => '', // определяется параметром $labels->name
      		'labels'                => array(
          		'name'              => 'Официальный билет',
          		'singular_name'     => 'Официальный билет',
          		'search_items'      => 'Найти официальный билет',
          		'all_items'         => 'Все официальные билеты',
          		'parent_item'       => 'Родительская тема',
          		'parent_item_colon' => 'Родительская тема:',
          		'edit_item'         => 'Редактировать официальный билет',
          		'update_item'       => 'Обновить официальный билет',
          		'add_new_item'      => 'Добавить новый официальный билет',
          		'new_item_name'     => 'Название нового официального билета',
          		'menu_name'         => 'Официальные билеты',
          ),
      		'description'           => '', // описание таксономии
      		'public'                => true,
      		'publicly_queryable'    => null, // равен аргументу public
      		'show_in_nav_menus'     => true, // равен аргументу public
      		'show_ui'               => true, // равен аргументу public
      		'show_tagcloud'         => !true, // равен аргументу show_ui
      		'hierarchical'          => true,
      		'update_count_callback' => '',
      		'rewrite'               => true,
      		//'query_var'             => $taxonomy, // название параметра запроса
      		'capabilities'          => array(),
      		'meta_box_cb'           => null, // callback функция. Отвечает за html код метабокса (с версии 3.8): post_categories_meta_box или post_tags_meta_box. Если указать false, то метабокс будет отключен вообще
      		'show_admin_column'     => false, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
      		'_builtin'              => false,
      		'show_in_quick_edit'    => null, // по умолчанию значение show_ui
      	)        
     );
  }
  
  // новая таксономия Официальный Билет
  //add_action( 'init', 'create_taxonomy_ticket' );
  function create_taxonomy_ticket() {
     register_taxonomy(
        'pdd_ticket',
        array( 'question' ),
        array(
      		'label'                 => '', // определяется параметром $labels->name
      		'labels'                => array(
          		'name'              => 'Билет',
          		'singular_name'     => 'Билет',
          		'search_items'      => 'Найти Билет',
          		'all_items'         => 'Все Билеты',
          		'parent_item'       => 'Родительская тема',
          		'parent_item_colon' => 'Родительская тема:',
          		'edit_item'         => 'Редактировать Билет',
          		'update_item'       => 'Обновить Билет',
          		'add_new_item'      => 'Добавить новый Билет',
          		'new_item_name'     => 'Название нового Билета',
          		'menu_name'         => 'Билеты',
          ),
      		'description'           => '', // описание таксономии
      		'public'                => true,
      		'publicly_queryable'    => null, // равен аргументу public
      		'show_in_nav_menus'     => true, // равен аргументу public
      		'show_ui'               => true, // равен аргументу public
      		'show_tagcloud'         => !true, // равен аргументу show_ui
      		'hierarchical'          => true,
      		'update_count_callback' => '',
      		'rewrite'               => true,
      		//'query_var'             => $taxonomy, // название параметра запроса
      		'capabilities'          => array(),
      		'meta_box_cb'           => null, // callback функция. Отвечает за html код метабокса (с версии 3.8): post_categories_meta_box или post_tags_meta_box. Если указать false, то метабокс будет отключен вообще
      		'show_admin_column'     => false, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
      		'_builtin'              => false,
      		'show_in_quick_edit'    => null, // по умолчанию значение show_ui
      	)        
     );
  }

?>