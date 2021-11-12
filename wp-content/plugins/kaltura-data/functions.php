<?php

if ( ! class_exists( 'Timber' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
    } );
    return;
}

Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

    function __construct() {
        add_theme_support( 'post-formats' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
        add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        parent::__construct();
    }

    function register_post_types() {
        //this is where you can register custom post types
        function form_post_type() {
            $labels = array(
            'name'                => _x( 'Forms', 'Post Type General Name', 'app' ),
            'singular_name'       => _x( 'form', 'Post Type Singular Name', 'app' ),
            'menu_name'           => __( 'Forms', 'app' ),
            'name_admin_bar'      => __( 'Forms', 'app' ),
            'parent_item_colon'   => __( 'Parent Item:', 'app' ),
            'all_items'           => __( 'All Items', 'app' ),
            'add_new_item'        => __( 'Add New Item', 'app' ),
            'add_new'             => __( 'Add New', 'app' ),
            'new_item'            => __( 'New Item', 'app' ),
            'edit_item'           => __( 'Edit Item', 'app' ),
            'update_item'         => __( 'Update Item', 'app' ),
            'view_item'           => __( 'View Item', 'app' ),
            'search_items'        => __( 'Search Item', 'app' ),
            'not_found'           => __( 'Not found', 'app' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'app' ),
        );
        $args = array(
            'label'               => __( 'form', 'app' ),
            'description'         => __( 'For Form page', 'app' ),
            'labels'              => $labels,
            'supports'            => array( 'title','editor' ),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => false,
            'show_in_menu'        => true,
            'menu_position'       => 26,
            'show_in_admin_bar'   => false,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );

        register_post_type( 'form', $args );
        }
        form_post_type();
    }

    function register_taxonomies() {
        //add_image_size( 'post-doctor', 130, 184, true );
        register_sidebar(array(
            'id' => 'side_bar_1',
            'name' => 'Side Bar 1',
            'before_widget' => '<div id="%1$s" class="%2$s widget widget_side block">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'id' => 'side_bar_2',
            'name' => 'Side Bar 2',
            'before_widget' => '<div id="%1$s" class="%2$s widget widget_side block">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
         register_sidebar(array(
            'id' => 'seo-link-1',
            'name' => 'seo-link-1',
            'before_widget' => '<div id="%1$s" class="%2$s widget seo">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'id' => 'seo-link-2',
            'name' => 'seo-link-2',
            'before_widget' => '<div id="%1$s" class="%2$s widget seo">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'id' => 'seo-link-3',
            'name' => 'seo-link-3',
            'before_widget' => '<div id="%1$s" class="%2$s widget seo">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'id' => 'seo-link-4',
            'name' => 'seo-link-4',
            'before_widget' => '<div id="%1$s" class="%2$s widget seo">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'id' => 'seo-link-5',
            'name' => 'seo-link-5',
            'before_widget' => '<div id="%1$s" class="%2$s widget seo">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'id' => 'seo-link-6',
            'name' => 'seo-link-6',
            'before_widget' => '<div id="%1$s" class="%2$s widget seo">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="title">',
            'after_title' => '</h4>',
        ));
         register_sidebar(array(
            'id' => 'header_script',
            'name' => 'Header Scripts',
            'before_widget' => '<div class="scripts" id="%1$s">',
            'after_widget' => '</div>',
        ));
         register_sidebar(array(
            'id' => 'all_tour_banner',
            'name' => 'AllTour Banner',
            'before_widget' => '<div class="imgb" id="%1$s">',
            'after_widget' => '</div>',
        ));
        register_sidebar(array(
            'id' => 'footer_script',
            'name' => 'Footer Scripts',
            'before_widget' => '<div class="scripts" id="%1$s">',
            'after_widget' => '</div>',
        ));
          register_sidebar(array(
            'id' => 'guide_menu1',
            'name' => 'Guide menu',
            'before_widget' => '<div class="gmenu" id="%1$s">',
            'after_widget' => '</div>',
        ));
         register_sidebar(array(
            'id' => 'newsletter-form',
            'name' => 'Newsletter Form',
            'before_widget' => '<section class="newsletter-form" id="%1$s">',
            'after_widget' => '</section>',
        ));
         register_sidebar(array(
            'id' => 'toph_text',
            'name' => 'Top text',
            'before_widget' => '<section class="toph_text" id="%1$s">',
            'after_widget' => '</section>',
        ));
        
    }

    function add_to_context( $context ) {
        $context['foo'] = 'bar';
        $context['request'] ="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $context['stuff'] = 'I am a value set in your functions.php file';
        $context['notes'] = 'These values are available everytime you call Timber::get_context();';
        $context['menu'] = new TimberMenu('Main');
        $context['footer_menu'] = new TimberMenu('Footer');
        $context['search_form'] = Timber::get_sidebar('search_form.php');
        $context['social_menu'] = get_field('social_menu', 'option');
        $context['lang_menu'] = get_field('lang_menu', 'option');
        $context['admin_menu'] = get_field('admin_menu', 'option');
        $context['phone'] = get_field('phone', 'option');
        $context['email_site'] = get_field('email_site', 'option');
        $context['address'] = get_field('address', 'option');
        $context['copyright'] = get_field('copyright', 'option');
        $context['read_more'] = get_field('read_more', 'option');
        $context['name_block_4'] = get_field('name_block_4', 'option');
        $context['site'] = $this;
        $context['header_script'] = Timber::get_widgets('Header Scripts');
        $context['footer_script'] = Timber::get_widgets('Footer Scripts');
        $context['all_tour_banner'] = Timber::get_widgets('AllTour Banner');
        $context['newsletter_form'] = Timber::get_widgets('Newsletter Form');
        $context['sidebar_1'] = Timber::get_widgets('Side Bar 1');
        $context['sidebar_2'] = Timber::get_widgets('Side Bar 2');
        $context['seo_1'] = Timber::get_widgets('seo-link-1');
        $context['seo_2'] = Timber::get_widgets('seo-link-2');
        $context['seo_3'] = Timber::get_widgets('seo-link-3');
        $context['seo_4'] = Timber::get_widgets('seo-link-4');
        $context['seo_5'] = Timber::get_widgets('seo-link-5');
        $context['seo_6'] = Timber::get_widgets('seo-link-6');
        $context['toph_text'] = Timber::get_widgets('Top text');
        $context['guide_menu1'] = Timber::get_widgets('Guide menu');

        $context['ajax_url'] = admin_url( 'admin-ajax.php');

         $context['short_form'] = Timber::get_sidebar('short_form.php');
         $context['big_form'] = Timber::get_sidebar('big_form.php');
        return $context;
    }

    function add_to_twig( $twig ) {
        /* this is where you can add your own fuctions to twig */
        $twig->addExtension( new Twig_Extension_StringLoader() );
        $twig->addFilter( 'myfoo', new Twig_Filter_Function( 'myfoo' ) );
        return $twig;
    }

}

new StarterSite();

function myfoo( $text ) {
    $text .= ' bar!';
    return $text;
}

function insert_jquery(){
    wp_enqueue_script('jquery', false, array(), false, false);
}
add_filter('wp_enqueue_scripts','insert_jquery',1);


//js/css
function upbootwp_jquery () {
    //printf('<link media="all" rel="stylesheet" type="text/css" href="%s" />', get_template_directory_uri().'/css/app.css');
    printf('<link media="all" rel="stylesheet/less" type="text/css" href="%s" />', get_template_directory_uri().'/less/app.less');
    printf('<script src="'.get_template_directory_uri().'/js/less.min.js" type="text/javascript"');
    
}
add_action('wp_head', 'upbootwp_jquery');


function upbootwp_footer () {
    printf('<script type="text/javascript" src="%s"></script>', get_template_directory_uri().'/js/bootstrap.min.js');
    printf('<script type="text/javascript" src="%s"></script>', get_template_directory_uri().'/js/jquery.validate.min.js');
    printf('<script type="text/javascript" src="%s"></script>', get_template_directory_uri().'/js/carouselswipe.js');
    printf('<script type="text/javascript" src="%s"></script>', get_template_directory_uri().'/js/jquery.pajinate.js');
    
    printf('<script type="text/javascript" src="%s"></script>', get_template_directory_uri().'/js/site.js');
    printf('<script type="text/javascript" src="%s"></script>', get_template_directory_uri().'/js/menu.js');
}
add_action('wp_footer', 'upbootwp_footer');
add_action( 'wp_enqueue_scripts', 'enqueue_font_awesome' );
function enqueue_font_awesome() {
 wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
}

//autoCompile less
function autoCompileLess() {
    // include lessc.inc
    require_once( get_template_directory().'/less.php-master/lessc.inc.php' );
    $lang_less ='app.less';
    $lang_css ='app.css';

    // input and output location

    $inputFile = get_template_directory().'/less/'. $lang_less ;
    $outputFile = get_template_directory().'/css/'.$lang_css ;

    // load the cache
    $cacheFile = $inputFile.".cache";

    if (file_exists($cacheFile)) {
        $cache = unserialize(file_get_contents($cacheFile));
    } else {
        $cache = $inputFile;
    }

    $less = new lessc;
    $less->setFormatter("compressed");

    $newCache = $less->cachedCompile($cache);
    //var_dump($cache);
    // create a new cache object, and compile
    $newCache = $less->cachedCompile($cache);

    // output a LESS file, and cache file only if it has been modified since last compile
    if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
        file_put_contents($cacheFile, serialize($newCache));
        file_put_contents($outputFile, $newCache['compiled']);
    }

    try {
        $less->compile("} invalid LESS }}}");
    } catch (Exception $ex) {
        // echo "lessphp fatal error: ".$ex->getMessage();
    }
}
if(is_user_logged_in()) {
    //add_action('init', 'autoCompileLess');
}
if (current_user_can('manage_options')) {
    show_admin_bar(true);
}
else {
    show_admin_bar(false);
}

function get_thumb_url($id, $size = 'thumbnail' ){
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($id), $size );
    $url = $thumb['0'];
    return $url;
}

function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

//short codes
function my_formatter($content) {
    $new_content = '';
    $pattern_full = '{(\[raw\].*?\[/raw\])}is';
    $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
    $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($pieces as $piece) {
        if (preg_match($pattern_contents, $piece, $matches)) {
            $new_content .= $matches[1];
        } else {
            $new_content .= wptexturize(wpautop($piece));
        }
    }

    return $new_content;
}

remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

add_filter('the_content', 'my_formatter', 99);

function wpex_clean_shortcodes($content){
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );
    $content = strtr($content, $array);
    return $content;
}
add_filter('acf_the_content', 'wpex_clean_shortcodes');

//
add_filter('the_content', 'inject_content_filter', 999);

function inject_content_filter($content) {
    $myMarkup = "my markup here<br>";
    $content = preg_replace('/<span id\=\"(more\-\d+)"><\/span>/', '<span id="\1"></span>'."\n\n". $myMarkup ."\n\n", $content);
    //var_dump($content);
    return $content;
}

// get blocks
function get_add_blocks($ar){
    if($ar) {
    $ar_id =array();
    $res =array();
    foreach ($ar as $item ) {
        $it = $item['page'];
        if($it){
            $ID =  $it ->ID;
            $ar_id[]= $ID;
        }
    }
    $res = Timber::get_posts($ar_id);
    //var_dump($res);
        return $res;
    }else{
        return false;
    }
}

//
//remove_filter( 'the_content', 'wpautop' );
//add_filter( 'the_content', 'wpautop' , 12);

function link_shorttag($atts, $content = null) {
    extract(shortcode_atts(array(
        'href' => false,
        'id_page' => false,
        'id_video' => false,
        'target' => false,
        'icon' => false,
        'class' => false,
        'class_cnt' => false,
    ), $atts));


    $link = ($id_page)?get_permalink($id_page) : false;


    return Timber::compile('link-short.twig', array(
        'content'=> do_shortcode($content),
        'href' => $href,
        'target' => $target,
        'link' => $link,
        'id_video' => $id_video,
        'class'=> $class,
        'class_cnt'=> $class_cnt,
        'icon'=> $icon));

}
add_shortcode('button', 'link_shorttag');

add_shortcode('row_cnt', 'row_cnt_shorttag');
add_shortcode('col_cnt', 'col_cnt_shorttag');
add_shortcode('buttons_cnt', 'btn_cnt_shorttag');
add_shortcode('title_h1', 'title_shorttag');
add_shortcode('figure_full', 'image_f_shorttag');
add_shortcode('video_full', 'video_f_shorttag');

function row_cnt_shorttag($atts,$content = null) {
    $content = wpautop(trim($content));
    return '<div class="' .$atts["class"].'">' . do_shortcode($content) . '</div>';
}
function btn_cnt_shorttag($atts,$content = null) {
    //$content = wpautop(trim($content));
    return '<div class="' .$atts["class"].'">' . do_shortcode($content) . '</div>';
}


function col_cnt_shorttag($atts,$content = null) {
    $content = wpautop(trim($content));
    extract(shortcode_atts(array(), $atts));
    return '<div class="' .$atts["class"].'">' . do_shortcode($content) . '</div>';
}

function title_shorttag($atts,$content = null) {
    //$content = wpautop(trim($content));
    return '<header class="head-article"><h2 class="h1 title-mh">' . do_shortcode($content) . '</h2><div class="break"></div></header>';
}
function image_f_shorttag($atts,$content = null) {
    //$content = wpautop(trim($content));
    return '<div class="img-f-pr block-md"><div class="shad_icon"></div><div class="img-f-pr-in">' . do_shortcode($content) . '</div></div>';
}

function video_f_shorttag($atts,$content = null) {
    //$content = wpautop(trim($content));
    return '<div class="embed-responsive embed-responsive-16by9 block-sm">' . do_shortcode($content) . '</div>';
}
function wph_inline_css_admin() {
echo '<style>
 
#formatdiv{
	display: none;
}
 </style>';
}
add_action('admin_head', 'wph_inline_css_admin');
//inline подключение стилей в админке end


function sub_title($atts,$content = null) {
    //$content = wpautop(trim($content));
    return '<h2><span style="color: #ed1c24;">' . do_shortcode($content) . '</span></h2>';
}

add_shortcode( 'sub_title', 'sub_title' );




 $labels_table = array(
            'name'                => _x( 'FormsData', 'Post Type General Name', 'app' ),
            'singular_name'       => _x( 'FormData', 'Post Type Singular Name', 'app' ),
            'menu_name'           => __( 'FormsData', 'app' ),
            'name_admin_bar'      => __( 'FormsData', 'app' ),
            'parent_item_colon'   => __( 'Parent Item:', 'app' ),
            'all_items'           => __( 'All Items', 'app' ),
            'add_new_item'        => __( 'Add New Item', 'app' ),
            'add_new'             => __( 'Add New', 'app' ),
            'new_item'            => __( 'New Item', 'app' ),
            'edit_item'           => __( 'Edit Item', 'app' ),
            'update_item'         => __( 'Update Item', 'app' ),
            'view_item'           => __( 'View Item', 'app' ),
            'search_items'        => __( 'Search Item', 'app' ),
            'not_found'           => __( 'Not found', 'app' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'app' ),
        );
        $args_table  = array(
            'label'               => __( 'formsdata', 'app' ),
            'description'         => __( 'For FormsData page', 'app' ),
            'labels'              => $labels_table,
            'supports'            => array( 'title','editor' ),
            'hierarchical'        => false,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'menu_position'       => 26,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => false,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );

        register_post_type( 'FormsData', $args_table );




add_action('gform_after_submission', 'endo_add_entry_to_db', 10, 2);
function endo_add_entry_to_db($entry, $form) {
	
	if ($entry["form_id"]=="8"){
			$new_post  = array('post_type' => 'FormsData',
      					  'post_title' => get_the_title($post->ID)." - ". $form['title'],
      					  'post_status' => 'publish',
      					  'post_content'  => $form['description']
      					 
      					  						 	);
      					  						       $newpost_id=  wp_insert_post( $new_post, true );	
     	      					  						 add_post_meta($newpost_id, 'field_name',  $entry[1]);  	
      					  						 add_post_meta($newpost_id, 'field_email',  $entry[4]);  	
      					  						  add_post_meta($newpost_id, 'field_tel',  $entry[11]);   	
      					  						  add_post_meta($newpost_id, 'field_message',  $entry[6]); 
      					  						 // add_post_meta($newpost_id, 'field_mailchimp',  $entry[10]); 
      					  						    if($entry["10"]){
												  	 add_post_meta($newpost_id, 'field_GDPR',  "OPTED-IN YES"); 
												  }
      					  						 
      					  						  
      					  						  
      					  						   add_post_meta($newpost_id, 'field_request_url',  $_SERVER["HTTP_REFERER"]); 
      					  							}elseif($entry["form_id"]=="2"){
														$new_post  = array('post_type' => 'FormsData',
      					  'post_title' => get_the_title($post->ID)." - ". $form['title'],
      					  'post_status' => 'publish',
      					  'post_content'  => $form['description']
      					 
      					  						 	);
      					  						       $newpost_id=  wp_insert_post( $new_post, true );	
     	      					  						 add_post_meta($newpost_id, 'field_name',  $entry[1]);  	
      					  						 add_post_meta($newpost_id, 'field_email',  $entry[2]);  	
      					  						  add_post_meta($newpost_id, 'field_tel',  $entry[16]);   	
      					  						  add_post_meta($newpost_id, 'field_data_depature',  $entry[11]);   	
      					  						  add_post_meta($newpost_id, 'field_country',  $entry[8]);
      					  						  add_post_meta($newpost_id, 'field_for_group',  get_the_title($entry[7]));   	
      					  						  add_post_meta($newpost_id, 'field_type_of_hotel', $entry[7]);   	
      					  						  add_post_meta($newpost_id, 'field_persons',  $entry[6]); 
      					  						  add_post_meta($newpost_id, 'field_message',  $entry[9]); 
      					  						 // add_post_meta($newpost_id, 'field_mailchimp',  $entry[15]); 
      					  						if($entry["15"]){
												  	 add_post_meta($newpost_id, 'field_GDPR',  "OPTED-IN YES"); 
												  }
      					  						  //add_post_meta($newpost_id, 'field_GDPR',  $entry["14.1"]); 
      					  						   add_post_meta($newpost_id, 'field_request_url',  $_SERVER["HTTP_REFERER"]); 
													}elseif($entry["form_id"]=="3"){
						$new_post  = array('post_type' => 'FormsData',
      					  'post_title' => get_the_title($post->ID)." - ". $form['title'],
      					  'post_status' => 'publish',
      					  'post_content'  => $form['description']	 	);
      					  						       $newpost_id=  wp_insert_post( $new_post, true );	
      					  						       
     	      					  						 add_post_meta($newpost_id, 'field_name',  $entry[1]);  	
      					  						 add_post_meta($newpost_id, 'field_email',  $entry[2]);  	
      					  						  add_post_meta($newpost_id, 'field_tel',  $entry[12]);   	
      					  						  add_post_meta($newpost_id, 'field_data_depature',  $entry[10]);   	
      					  						  add_post_meta($newpost_id, 'field_for_group',  get_the_title($entry[3]));
      					  						   // add_post_meta($newpost_id, 'field_tet', $entry[3]);   	
      					  						  add_post_meta($newpost_id, 'field_persons',  $entry[5]); 
      					  						  add_post_meta($newpost_id, 'field_message',  $entry[7]); 
      					  						  add_post_meta($newpost_id, 'field_type_of_hotel',  $entry[6]); 
      					  						 // add_post_meta($newpost_id, 'field_mailchimp',  $entry[11]); 
      					  						  if($entry["11"]){
												  	 add_post_meta($newpost_id, 'field_GDPR',  "OPTED-IN YES"); 
												  }
      					  						 
      					  						   add_post_meta($newpost_id, 'field_request_url',  $_SERVER["HTTP_REFERER"]); 
													}elseif($entry["form_id"]=="6"){
						$new_post  = array('post_type' => 'FormsData',
      					  'post_title' => get_the_title($post->ID)." - ". $form['title'],
      					  'post_status' => 'publish',
      					  'post_content'  => $form['description']	 	);
      					  						       $newpost_id=  wp_insert_post( $new_post, true );	
      					  						       
     	      					  						 add_post_meta($newpost_id, 'field_name',  $entry[1]);  	
      					  						 add_post_meta($newpost_id, 'field_email',  $entry[2]);  	
      					  						  add_post_meta($newpost_id, 'field_tel',  $entry[12]);   	
      					  						  add_post_meta($newpost_id, 'field_data_depature',  $entry[10]);   	
      					  						  add_post_meta($newpost_id, 'field_for_group',  get_the_title($entry[3]));
      					  						    //add_post_meta($newpost_id, 'field_tet', $entry[3]);
      					  						       	
      					  						  add_post_meta($newpost_id, 'field_persons',  $entry[5]); 
      					  						  add_post_meta($newpost_id, 'field_message',  $entry[7]); 
      					  						  add_post_meta($newpost_id, 'field_type_of_hotel',  $entry[6]); 
      					  						  //add_post_meta($newpost_id, 'field_mailchimp',  $entry[11]); 
      					  						    if($entry["11"]){
												  	 add_post_meta($newpost_id, 'field_GDPR',  "OPTED-IN YES"); 
												  }
      					  						 
      					  						   add_post_meta($newpost_id, 'field_request_url',  $_SERVER["HTTP_REFERER"]); 
      					  						  }



global $post;

	  // echo '<pre>';
	  // var_dump($entry);
	   //echo '</pre>';

	  // echo '<pre>';
	   //var_dump($form);
	   //echo '</pre>';

  
  	global $wpdb;
  
 

}


function function_name() {
	global $wpdb;
	$tt=$_REQUEST['tag_ID'];
 $result = $wpdb->get_results ( "
    SELECT * 
    FROM  $wpdb->term_taxonomy
        WHERE taxonomy = 'category' AND term_id = ". $tt ."
" );

if ($result){
	$wpdb->delete( 'wp_term_taxonomy', array( 'taxonomy' => 'post_tag', 'term_id' =>  $tt  ) );
}


//print_r($tt);
//print_r($result);
   
 
	
}

add_action( 'edit_category_form', 'function_name' );


