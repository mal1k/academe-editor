<?php

define( 'ACADEME_THEME_DIR', trailingslashit(get_stylesheet_directory()) );

require_once ACADEME_THEME_DIR . 'includes/cpt.php';
require_once ACADEME_THEME_DIR . 'includes/svg-icons.php';
require_once ACADEME_THEME_DIR . 'includes/ajax-handlers.php';
require_once ACADEME_THEME_DIR . 'includes/user-lists.php';
require_once ACADEME_THEME_DIR . 'includes/posts-nav.php';
require_once ACADEME_THEME_DIR . 'includes/api.php';

require_once ACADEME_THEME_DIR . 'includes/plugins/instructor-role.php';

require_once ACADEME_THEME_DIR . 'includes/plugins/quiz-reporting-extension.php';

add_theme_support( 'post-thumbnails' );

if( function_exists('acf_add_options_page') ) {
    $parent = acf_add_options_page(array(
        'page_title'  => __('Theme General Settings'),
        'menu_title'  => __('Theme Settings'),
        'redirect'    => false,
    ));

    $child = acf_add_options_page(array(
        'page_title'  => __('Post Types Settings'),
        'menu_title'  => __('Post Types Settings'),
        'parent_slug' => $parent['menu_slug'],
    ));
}

function register_my_session() {
    if( !session_id() ) {
        session_start();
    }
}
add_action('init', 'register_my_session');

add_action( 'wp_enqueue_scripts', 'print_ajaxurl', 99 );
function print_ajaxurl(){
    wp_localize_script( 'jquery', 'ajaxurl', admin_url('admin-ajax.php'));
}

// BAD CODE (API REQUEST WITH 0.5s HANDLING)
//add_action('init', 'connect_session_frontend_only');

function connect_session_frontend_only() {
    if (!is_admin()) { // exit function if not on front-end
        global $client;
        $client = get_kaltura_session();
    }
}
// END BAD CODE (API REQUEST WITH 0.5s HANDLING)

function get_filtered_posts($post_type, $taxonomy = '', $terms = []) {
    $args = [
        'post_type' => $post_type,
        'posts_per_page' => 15,
        'orderby'        => 'ID',
        'order'          => 'DESC',
    ];
    if ($taxonomy && $terms) {
        $args['tax_query'] = [
//            'relation' => 'AND',
//            [
//                'taxonomy' => 'tax_name',
//                'field'    => 'id',
//                'terms'    => [1, 2, 3],
//            ],
            [
                'taxonomy' => $taxonomy,
                'field'    => 'id',
                'terms'    => $terms,
                'operator' => 'IN',
            ]
        ];
    }
    $query = new WP_Query($args);
    return $query->posts;
}

add_filter('pre_get_posts','modifyOrderBy');
function modifyOrderBy($query) {
    if ($query->is_post_type_archive('movie') && $query->is_main_query() && !is_admin() ) { //only for archives
        $query->set( 'orderby',  ['date' => 'DESC', 'ID' => 'DESC'] );
    }

    return $query;
}

function get_separated_read_more_content($content, $words = 60) {
    $trimmed = wp_trim_words($content, $words, '');
    return [
        $trimmed,
        str_replace($trimmed, '', $content)
    ];
}

function get_continue_watching_list($user_id, $fields = 'all') {
    $movies_meta = json_decode(get_user_meta($user_id, 'continue_watching', true), true);

    $movies = [];
    //order by timestamp (updated at)
    if($movies_meta) {
        uasort($movies_meta, function ($a, $b) { return $b['upd'] - $a['upd']; });

        switch ($fields) {
            case 'all' :
                $movies = $movies_meta;
                break;
            case 'ids' :
                $movies = array_keys($movies_meta);
                break;
        }
    }

    return $movies;
}

add_filter('excerpt_more', function($more) {
    return '...';
});
add_filter( 'excerpt_length', function(){
    return 20;
} );

function link_is_active($uri) {
    $url_array =  explode('/', $_SERVER['REQUEST_URI']) ;
    $url = end($url_array);
    if($uri == $url){
        echo 'active';
    }
}

//plugin active check:
if (is_plugin_active('post-views-counter/post-views-counter.php')) {
    define('PVC_ACTIVE', true);
} else {
    define('PVC_ACTIVE', false);
}

function is_user_in_role($role) {
    $roles_list = [
        'student' => 'subscriber',
        'teacher' => 'wdm_instructor',
        'administrator' => 'administrator'
    ];
    if (isset($roles_list[$role])) {
        $user = wp_get_current_user();
        if ( $user->exists() && in_array( $roles_list[$role], (array) $user->roles ) ) {
            return true;
        }
    }
    return false;
}

function get_my_list($user_id, $fields = 'all') {
    $movies_meta = json_decode(get_user_meta($user_id, 'my_list', true), true);

    $movies = [];
    //order by timestamp (updated at)
    if($movies_meta) {
        uasort($movies_meta, function ($a, $b) { return $b['upd'] - $a['upd']; });

        switch ($fields) {
            case 'all' :
                $movies = $movies_meta;
                break;
            case 'ids' :
                $movies = array_keys($movies_meta);
                break;
        }
    }

    return $movies;
}

function get_my_movies($user_id, $fields = 'all') {
    $all_movies = [];
    $my_list = json_decode(get_user_meta($user_id, 'my_list', true), true);
    $cw_list = json_decode(get_user_meta($user_id, 'continue_watching', true), true);

    if (is_array($my_list) && is_array($cw_list)) { //remove duplicates
        foreach ($cw_list as $key => $movie) {
            unset($my_list[$key]);
        }
    }

    if (is_array($my_list)) {
        $all_movies = $all_movies + $my_list;
    }
    if (is_array($cw_list)) {
        $all_movies = $all_movies + $cw_list;
    }

    $movies = [];
    //order by timestamp (updated at)
    if ($all_movies) {
        uasort($all_movies, function ($a, $b) { return $b['upd'] - $a['upd']; });

        switch ($fields) {
            case 'all' :
                $movies = $all_movies;
                break;
            case 'ids' :
                $movies = array_keys($all_movies);
                break;
        }
    }

    return $movies;
}

function set_global_my_list() {
    global $my_list;
    $my_list = get_my_list(get_current_user_id(), 'ids');
}
add_action( 'after_setup_theme', 'set_global_my_list' );

function the_my_list_button($post_id, $type = 'text') {
    global $my_list;
    echo "<div class=\"my-list-button\" data-button-type=\"$type\" data-movie-id=\"$post_id\">";
    if ($type === 'text') {
        echo (in_array($post_id, $my_list)) ? __('Remove from my videos', 'academe-theme') : __('Add to my videos', 'academe-theme');
    } else if ($type === 'icon') {
        (in_array($post_id, $my_list)) ? icon('star', 'icon-24 icon-blue-stroke icon-blue-fill') : icon('star', 'icon-24');
    }
    echo "</div>";
}

function generateSessionId() {
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < 5; $i++)
        $random_string .= $characters[rand(0, $characters_length - 1)];
    return $random_string;
}

add_action( 'save_post_session', 'save_post_generate_session_slug' );
function save_post_generate_session_slug( $post_id ) {
    // verify post is not a revision
    if ( ! wp_is_post_revision( $post_id ) ) {

        // unhook this function to prevent infinite looping
        remove_action( 'save_post_session', 'save_post_generate_session_slug' );

        // update the post slug
        $post = get_post($post_id);
        if (strlen($post->post_name) != 12) {
            wp_update_post( array(
                'ID' => $post_id,
                'post_name' => generateSessionId(),
            ));
        }

        // re-hook this function
        add_action( 'save_post_session', 'save_post_generate_session_slug' );

    }
}

function redirect_after_login($redirect_to, $request, $user) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        // check for students and teachers

        if ( $_SESSION['session'] != null ) {
            $link = home_url() . '/sessions/' . $_SESSION['session'];
            $_SESSION['session'] = null;
            return $link;
        }

        if (in_array( 'subscriber', $user->roles ) || in_array( 'wdm_instructor', $user->roles )) {
            return home_url();
        }
        else {
            return $redirect_to;
        }
    } else {
        return $redirect_to;
    }
}
add_filter('login_redirect', 'redirect_after_login', 1000, 3);

add_action('ld_after_course_status_template_container', 'print_course_title');
function print_course_title()
{
    echo '<h1>' . get_the_title() . '</h1>';
}

/**
 * Convert values of ACF core date time pickers from Y-m-d H:i:s to timestamp
 * @param  string $value   unmodified value
 * @param  int    $post_id post ID
 * @param  object $field   field object
 * @return string          modified value
 */
function acf_save_as_timestamp( $value, $post_id, $field  ) {
    if( $value ) {
        $tz = new DateTimeZone(get_option('timezone_string'));
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value, $tz);
        $value = $date->getTimestamp();
    }

    return $value;
}

add_filter( 'acf/update_value/type=date_time_picker', 'acf_save_as_timestamp', 10, 3 );

/**
 * Convert values of ACF core date time pickers from timestamp to Y-m-d H:i:s
 * @param  string $value   unmodified value
 * @param  int    $post_id post ID
 * @param  object $field   field object
 * @return string          modified value
 */
function acf_load_as_timestamp( $value, $post_id, $field  ) {
    if( $value ) {
        $tz = new DateTimeZone(get_option('timezone_string'));
        $date = DateTime::createFromFormat('U', $value);
        $value = $date->setTimezone($tz)->format('Y-m-d H:i:s');
    }

    return $value;
}

add_filter( 'acf/load_value/type=date_time_picker', 'acf_load_as_timestamp', 10, 3 );

/**
 * Remove actions and filters called from class (without having the same class instance from global variable)
 */
function remove_filters_with_method_name( $hook_name = '', $method_name = '', $priority = 0 ) {
    global $wp_filter;
    // Take only filters on right hook name and priority
    if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
        return false;
    }
    // Loop on filters registered
    foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
        // Test if filter is an array ! (always for class/method)
        if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
            // Test if object is a class and method is equal to param !
            if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && $filter_array['function'][1] == $method_name ) {
                // Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
                if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
                    unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
                } else {
                    unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
                }
            }
        }
    }
    return false;
}

function highlight($text_highlight, $text_search) {
    $str = preg_replace('#'. preg_quote($text_highlight) .'#i', '<span class=\'highlighted\'>\\0</span>', $text_search);
    return $str;
}

add_action( 'wp_enqueue_scripts', 'load_wp_api_settings_for_student', 99 );
function load_wp_api_settings_for_student(){
    if (is_user_in_role('student')) {
        wp_localize_script( 'jquery', 'wpApiSettings', array(
            'root'          => esc_url_raw( get_rest_url() ),
            'nonce'         => ( wp_installing() && ! is_multisite() ) ? '' : wp_create_nonce( 'wp_rest' ),
            'versionString' => 'wp/v2/',
        ) );
    }
}


// add_action( 'template_redirect', function() {
//     if ( wp_is_mobile() && !is_admin() && !is_singular('session') && !is_page_template( 'page-enter-lesson.php' ) ) {
//         wp_redirect('/enter-lesson', 302);
//         exit;
//     }
// } );


/**
 * Login page restyle
 */
function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/assets/css/login.css' );
    //wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/style-login.js' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet' );

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo.svg);
            height:40px;
            width:260px;
            background-size: 260px 40px;
            background-repeat: no-repeat;
            padding-bottom: 20px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

// remove "Private: " from titles
function remove_private_prefix($title) {
    $title = str_replace('Private: ', '', $title);
    return $title;
}
add_filter('the_title', 'remove_private_prefix');

function calculate_clip_duration ($from = '00:00:00', $to = '00:00:00') {
    $from = $from ?? '00:00:00';
    $to = $to ?? '00:00:00';

    $clip_duration = format_time_to_seconds($to) - format_time_to_seconds($from); // in seconds
    $duration = gmdate("H:i:s", $clip_duration); //in timestring

    $duration_arr = explode(":", $duration);
    if ((int)$duration_arr[0] == 0) {
       $time = (int)$duration_arr[1].'m '. (int)$duration_arr[2].'s';
    } else {
        $time = (int)$duration_arr[0].'h '. (int)$duration_arr[1].'m';
    }
    return $time;
}

add_action( 'init', 'create_taxonomy_age' );
function create_taxonomy_age(){
	register_taxonomy( 'age', [ 'movie' ], [
		'label'                 => '', 
		'labels'                => [
			'name'              => 'Ages',
			'singular_name'     => 'Age',
			'search_items'      => 'Search Ages',
			'all_items'         => 'All Ages',
			'view_item '        => 'View Age',
			'parent_item'       => 'Parent Age',
			'parent_item_colon' => 'Parent Age:',
			'edit_item'         => 'Edit Age',
			'update_item'       => 'Update Age',
			'add_new_item'      => 'Add New Age',
			'new_item_name'     => 'New Age Name',
			'menu_name'         => 'Age',
		],
		'description'           => '',
		'public'                => true,
		'publicly_queryable'    => null,
		'show_in_nav_menus'     => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_tagcloud'         => true,
		'show_in_quick_edit'    => null,
		'hierarchical'          => true,

		'rewrite'               => true,
		'capabilities'          => array(),
		'meta_box_cb'           => null, 
		'show_admin_column'     => false, 
		'show_in_rest'          => true, 
		'rest_base'             => null, 
	] );
}


add_filter('the_title', 'remove_private_prefix');

add_action('add_meta_boxes', 'myplugin_add_custom_box');
function myplugin_add_custom_box(){
	$screens = array( 'movie' );
	add_meta_box( 'age_hidden_block', 'Age', 'jquery_for_age_block', $screens );
}

function jquery_for_age_block( $post, $meta ){
    ?>

    <script>
    jQuery( document ).ready(function() {
        jQuery('#age_hidden_block').hide();
        jQuery(document).on('change', '[aria-label="Grades"] .components-checkbox-control__input', function() { 
            checkGrades();
        });

        function checkGrades() {
            if ( jQuery('[aria-label="Grades"] .components-checkbox-control__input:checked').length > 0 ) {
                jQuery('[aria-label="Ages"]').closest('.components-panel__body').show();
            } else {
                jQuery('[aria-label="Ages"]').closest('.components-panel__body').hide();
            }
        }
    });
    </script>

    <?php
}

function teacher_non_wp_admin() {
    if (is_admin() && is_user_in_role('teacher') ) {
        wp_redirect(home_url());
        exit;
    } 
}
add_action('init', 'teacher_non_wp_admin'); 

function javascript_variables(){ ?>
    <script type="text/javascript">
        var ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
        var ajax_nonce = '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>';
    </script><?php
}
add_action ( 'wp_head', 'javascript_variables' );

// Here we register our "send_form" function to handle our AJAX request, do you remember the "superhypermega" hidden field? Yes, this is what it refers, the "send_form" action.
add_action('wp_ajax_passwordModal_form', 'passwordModal_form'); // This is for authenticated users
add_action('wp_ajax_nopriv_passwordModal_form', 'passwordModal_form'); // This is for unauthenticated users.
 
/**
 * In this function we will handle the form inputs and send our email.
 *
 * @return void
 */
 
function passwordModal_form(){
 
    // This is a secure process to validate if this request comes from a valid source.
    check_ajax_referer( 'secure_nonce_name', 'security' );
 
    /**
     * First we make some validations, 
     * I think you are able to put better validations and sanitizations. =)
     */
     
    if ( empty( $_POST["password"] ) )
        $data = 301;
    elseif ( $_POST['password'] != '0543064071' )
        $data = 302;
    else {
        $_SESSION['logged_in'] = true;
        $data = true;
    }

    echo $data;
    wp_die();
}