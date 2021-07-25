<?php
function wptp_create_post_type()
{
    $labels = array(
        'name' => __('Movies'),
        'singular_name' => __('Movie'),
        'add_new' => __('New Movie'),
        'add_new_item' => __('Add New Movie'),
        'edit_item' => __('Edit Movie'),
        'new_item' => __('New Movie'),
        'view_item' => __('View Movie'),
        'search_items' => __('Search Movies'),
        'not_found' => __('No Movies Found'),
        'not_found_in_trash' => __('No Movies found in Trash'),
    );
    $args = array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => 'movies'),
        'menu_position' => 6,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'custom-fields',
            'thumbnail'
        ),
        'menu_icon' => 'dashicons-format-video',
        'taxonomies' => array('category', 'post_tag'),
    );
    register_post_type('movie', $args);

    /**
     * Post Type: Sessions.
     */

    $labels = [
        "name" => __( "Sessions", "academe-theme" ),
        "singular_name" => __( "Session", "academe-theme" ),
        "menu_name" => __( "Sessions", "academe-theme" ),
        "all_items" => __( "All Sessions", "academe-theme" ),
        "add_new_item" => __( "Add New Session", "academe-theme" ),
        "edit_item" => __( "Edit Session", "academe-theme" ),
        "new_item" => __( "New Session", "academe-theme" ),
        "view_item" => __( "View Session", "academe-theme" ),
        "view_items" => __( "View Sessions", "academe-theme" ),
        "search_items" => __( "Search Sessions", "academe-theme" ),
        "not_found" => __( "No Sessions Found", "academe-theme" ),
        "not_found_in_trash" => __( "No Sessions Found in Trash", "academe-theme" ),
        "parent" => __( "Parent Session", "academe-theme" ),
        "archives" => __( "Session Archives", "academe-theme" ),
        "insert_into_item" => __( "Insert into Session", "academe-theme" ),
        "items_list" => __( "Sessions List", "academe-theme" ),
        "attributes" => __( "Sessions Attributes", "academe-theme" ),
        "name_admin_bar" => __( "Session", "academe-theme" ),
        "item_published" => __( "Session Published", "academe-theme" ),
        "item_reverted_to_draft" => __( "Session reverted to draft", "academe-theme" ),
        "item_scheduled" => __( "Session Scheduled", "academe-theme" ),
        "item_updated" => __( "Session Updated", "academe-theme" ),
        "parent_item_colon" => __( "Parent Session", "academe-theme" ),
    ];

    $args = [
        "label" => __( "Sessions", "academe-theme" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "sessions", "with_front" => true ],
        "query_var" => true,
        'menu_position' => 9,
        "supports" => [ "title", "editor", "thumbnail", "author" ],
        'menu_icon' => 'dashicons-clock',
        "taxonomies" => [ "faculty", "subject", "topic", "genre" ],
    ];

    register_post_type( "session", $args );

    /**
     * Post Type: Teaching guides.
     */

    $labels = [
        "name" => __( "Teaching guides", "academe-theme" ),
        "singular_name" => __( "Teaching guide", "academe-theme" ),
        "menu_name" => __( "Teaching guides", "academe-theme" ),
        "all_items" => __( "All Teaching guides", "academe-theme" ),
        "add_new_item" => __( "Add New Teaching guide", "academe-theme" ),
        "edit_item" => __( "Edit Teaching guides", "academe-theme" ),
        "new_item" => __( "New Teaching guide", "academe-theme" ),
        "view_item" => __( "View Teaching guide", "academe-theme" ),
        "view_items" => __( "View Teaching guides", "academe-theme" ),
        "search_items" => __( "Search Teaching guides", "academe-theme" ),
        "not_found" => __( "No Teaching guides Found", "academe-theme" ),
        "not_found_in_trash" => __( "No Teaching guides Found in Trash", "academe-theme" ),
        "parent" => __( "Parent Teaching guide", "academe-theme" ),
        "archives" => __( "Teaching guide Archives", "academe-theme" ),
        "insert_into_item" => __( "Insert into Teaching guides", "academe-theme" ),
        "items_list" => __( "Teaching guides List", "academe-theme" ),
        "attributes" => __( "Teaching guides Attributes", "academe-theme" ),
        "name_admin_bar" => __( "Teaching guide", "academe-theme" ),
        "item_published" => __( "Teaching guide Published", "academe-theme" ),
        "item_reverted_to_draft" => __( "Teaching guide reverted to draft", "academe-theme" ),
        "item_scheduled" => __( "Teaching guide Scheduled", "academe-theme" ),
        "item_updated" => __( "Teaching guide Updated", "academe-theme" ),
        "parent_item_colon" => __( "Parent Teaching guide", "academe-theme" ),
    ];

    $args = [
        "label" => __( "Teaching Guides", "academe-theme" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "guides", "with_front" => true ],
        "query_var" => true,
        'menu_position' => 8,
        "supports" => [ "title", "editor", "thumbnail", "author" ],
        'menu_icon' => 'dashicons-book',
        "taxonomies" => [ "faculty", "subject", "topic", "post_tag" ],
    ];

    register_post_type( "teaching-guide", $args );

    /**
     * Post Type: Clips.
     */

    $labels = array(
        'name' => __('Clips'),
        'singular_name' => __('Clip'),
        'add_new' => __('New Clip'),
        'add_new_item' => __('Add New Clip'),
        'edit_item' => __('Edit Clip'),
        'new_item' => __('New Clip'),
        'view_item' => __('View Clip'),
        'search_items' => __('Search Clips'),
        'not_found' => __('No Clips Found'),
        'not_found_in_trash' => __('No Clips found in Trash'),
    );
    $args = array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,
        'rewrite' => array('slug' => 'clips'),
        'hierarchical' => false,
        'menu_position' => 7,
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'custom-fields',
            'thumbnail'
        ),
        'capability_type' => 'clip',
//        'capabilities' => array(
//            'create_posts' => 'create_clips',
//            'edit_post' => 'edit_clip',
//            'edit_posts' => 'edit_clips',
//            'edit_others_posts' => 'edit_other_clips',
//            'edit_published_posts' => 'edit_published_clips',
//            'publish_posts' => 'publish_clips',
//            'read_post' => 'read_clip',
//            'read_private_posts' => 'read_private_clips',
//            'delete_posts' => 'delete_clips'
//        ),
        'map_meta_cap' => true,
        'menu_icon' => 'dashicons-images-alt',
        'taxonomies' => ["category", "topic", "post_tag"],
    );

    register_post_type('clip', $args);

    // clips capabilities for teachers:
    $teacher = get_role( 'wdm_instructor' );

    $teacher->add_cap( 'publish_clips' );
    $teacher->add_cap( 'edit_clips' );
    $teacher->add_cap( 'edit_published_clips' );
    $teacher->add_cap( 'delete_clips' );
    $teacher->add_cap( 'delete_published_clips' );

}

add_action('init', 'wptp_create_post_type');


add_action('init', 'create_taxonomy');
function create_taxonomy()
{
    /**
     * Taxonomy: Genres.
     */
    // список параметров: wp-kama.ru/function/get_taxonomy_labels
    register_taxonomy('genre', ['movie'], [
        'label' => '', // определяется параметром $labels->name
        'labels' => [
            'name' => 'Genres',
            'singular_name' => 'Genre',
            'search_items' => 'Search Genres',
            'all_items' => 'All Genres',
            'view_item ' => 'View Genre',
            'parent_item' => 'Parent Genre',
            'parent_item_colon' => 'Parent Genre:',
            'edit_item' => 'Edit Genre',
            'update_item' => 'Update Genre',
            'add_new_item' => 'Add New Genre',
            'new_item_name' => 'New Genre Name',
            'menu_name' => 'Genres',
            "choose_from_most_used" => __( "Choose from the most used genres", "academe-theme" ),
            "separate_items_with_commas" => __( "Separate genres with commas", "academe-theme" ),
        ],
        'description' => '', // описание таксономии
        'public' => true,
        // 'publicly_queryable'    => null, // равен аргументу public
        // 'show_in_nav_menus'     => true, // равен аргументу public
        // 'show_ui'               => true, // равен аргументу public
        // 'show_in_menu'          => true, // равен аргументу show_ui
        // 'show_tagcloud'         => true, // равен аргументу show_ui
        // 'show_in_quick_edit'    => null, // равен аргументу show_ui
        'hierarchical' => true,
        'rewrite' => true,
        //'query_var'             => $taxonomy, // название параметра запроса
        'capabilities' => array(),
        'meta_box_cb' => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
        'show_admin_column' => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
        'show_in_rest' => null, // добавить в REST API
        'rest_base' => null, // $taxonomy
        // '_builtin'              => false,
        //'update_count_callback' => '_update_post_term_count',
    ]);

    /**
     * Taxonomy: Faculties.
     */

    $labels = [
        "name" => __( "Faculties", "academe-theme" ),
        "singular_name" => __( "Faculty", "academe-theme" ),
        "choose_from_most_used" => __( "Choose from the most used faculties", "academe-theme" ),
        "separate_items_with_commas" => __( "Separate faculties with commas", "academe-theme" ),
    ];

    $args = [
        "label" => __( "Faculties", "academe-theme" ),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'faculty', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "rest_base" => "faculty",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => false,
    ];
    register_taxonomy( "faculty", [ "sfwd-lessons", "sfwd-courses", "movie", "teaching-guide" ], $args );

    /**
     * Taxonomy: Subjects.
     */

    $labels = [
        "name" => __( "Subjects", "academe-theme" ),
        "singular_name" => __( "Subject", "academe-theme" ),
        "choose_from_most_used" => __( "Choose from the most used subjects", "academe-theme" ),
        "separate_items_with_commas" => __( "Separate subjects with commas", "academe-theme" ),
    ];

    $args = [
        "label" => __( "Subjects", "academe-theme" ),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'subject', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "rest_base" => "subject",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => false,
    ];
    register_taxonomy( "subject", [ "sfwd-lessons", "sfwd-courses", "movie", "teaching-guide" ], $args );

    /**
     * Taxonomy: Topics.
     */

    $labels = [
        "name" => __( "Topics", "academe-theme" ),
        "singular_name" => __( "Topic", "academe-theme" ),
        "choose_from_most_used" => __( "Choose from the most used topics", "academe-theme" ),
        "separate_items_with_commas" => __( "Separate topics with commas", "academe-theme" ),
    ];

    $args = [
        "label" => __( "Topics", "academe-theme" ),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'topic', 'with_front' => true, ],
        "show_admin_column" => false,
        "show_in_rest" => true,
        "rest_base" => "topic",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => false,
    ];
    register_taxonomy( "topic", [ "sfwd-lessons", "sfwd-courses", "movie", "teaching-guide", "clip" ], $args );

}

