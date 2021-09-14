<?php
// Slider strip filter (all posts)
add_action("wp_ajax_async_filter_all_movies_related" , "async_filter_all_posts");
add_action('wp_ajax_nopriv_async_filter_all_movies_related', 'async_filter_all_posts');

add_action("wp_ajax_async_filter_all_posts" , "async_filter_all_posts");
add_action('wp_ajax_nopriv_async_filter_all_posts', 'async_filter_all_posts');
function async_filter_all_posts() {
    if ($_POST['action'] == 'async_filter_all_movies_related') {
        $term = get_term($_POST['term'], $_POST['taxonomy']);
        $title = sprintf(__( 'More %1$s Movies' ), $term->name);
    } else {
        $title = $_POST['section_title'];
    }

    $posts = get_filtered_posts($_POST['post_type'], $_POST['taxonomy'], $_POST['term']);
    get_template_part( 'templates/partials/slider-strip', 'null', [
        'title' => $title,
        'filter' => [
            'active' => true,
            'post_type' => $_POST['post_type'],
            'taxonomy' => $_POST['taxonomy'],
            'term' => $_POST['term'],
            'action' => $_POST['action'],
        ],
        'posts' => $posts
    ]);
    wp_die();
}

add_action("wp_ajax_async_filter_my_lessons" , "async_filter_my_lessons");
add_action('wp_ajax_nopriv_async_filter_my_lessons', 'async_filter_my_lessons');
function async_filter_my_lessons() {
    $args = ['post_type' => 'sfwd-lessons','post_status' => 'publish', 'author__in' => [get_current_user_id()],'posts_per_page' => 15];
    if ($_POST['taxonomy'] && $_POST['term']) {
        $args['tax_query'] = [[
            'taxonomy' => $_POST['taxonomy'],
            'field'    => 'id',
            'terms'    => $_POST['term'],
            'operator' => 'IN',
        ]];
    }
    $lessons = new WP_Query($args);
    get_template_part( 'templates/partials/slider-strip', 'null', [
        'title' => $_POST['section_title'],
        'filter' => [
            'active' => true,
            'post_type' => $_POST['post_type'],
            'taxonomy' => $_POST['taxonomy'],
            'term' => $_POST['term'],
            'action' => $_POST['action'],
        ],
        'posts' => $lessons->posts
    ]);
    wp_die();
}

add_action("wp_ajax_async_filter_my_courses" , "async_filter_my_courses");
add_action('wp_ajax_nopriv_async_filter_my_courses', 'async_filter_my_courses');
function async_filter_my_courses() {
    $args = ['post_type' => 'sfwd-courses','post_status' => 'publish', 'author__in' => [get_current_user_id()],'posts_per_page' => 15];
    if ($_POST['taxonomy'] && $_POST['term']) {
        $args['tax_query'] = [[
            'taxonomy' => $_POST['taxonomy'],
            'field'    => 'id',
            'terms'    => $_POST['term'],
            'operator' => 'IN',
        ]];
    }
    $courses = new WP_Query($args);
    get_template_part( 'templates/partials/slider-strip', 'null', [
        'title' => $_POST['section_title'],
        'filter' => [
            'active' => true,
            'post_type' => $_POST['post_type'],
            'taxonomy' => $_POST['taxonomy'],
            'term' => $_POST['term'],
            'action' => $_POST['action'],
        ],
        'posts' => $courses->posts
    ]);
    wp_die();
}

add_action("wp_ajax_async_filter_recommended_movies" , "async_filter_recommended_movies");
add_action('wp_ajax_nopriv_async_filter_recommended_movies', 'async_filter_recommended_movies');
function async_filter_recommended_movies() {
    $slides = get_field('recommended_movies', 'option');
    $slides_ids = [];
    foreach ($slides as $slide) {
        $slides_ids[] = $slide->ID;
    }

    $args = ['post_type' => $_POST['post_type'], 'posts_per_page' => 30, 'post__in' => $slides_ids];
    if ($_POST['taxonomy'] && $_POST['term']) {
        $args['tax_query'] = [[
            'taxonomy' => $_POST['taxonomy'],
            'field'    => 'id',
            'terms'    => $_POST['term'],
            'operator' => 'IN',
        ]];
    }
    $query = new WP_Query($args);
    $query_posts = $query->posts;
    get_template_part( 'templates/partials/slider-strip', 'null', [
        'title' => $_POST['section_title'],
        'filter' => [
            'active' => true,
            'post_type' => $_POST['post_type'],
            'taxonomy' => $_POST['taxonomy'],
            'term' => $_POST['term'],
            'action' => $_POST['action'],
        ],
        'posts' => $query_posts
    ]);
    wp_die();
}

add_action("wp_ajax_async_filter_popular_movies" , "async_filter_popular_movies");
add_action('wp_ajax_nopriv_async_filter_popular_movies', 'async_filter_popular_movies');
function async_filter_popular_movies() {
    $args = ['post_type' => 'movie', 'orderby' => 'post_views', 'order' => 'desc'];
    if ($_POST['taxonomy'] && $_POST['term']) {
        $args['tax_query'] = [[
            'taxonomy' => $_POST['taxonomy'],
            'field'    => 'id',
            'terms'    => $_POST['term'],
            'operator' => 'IN',
        ]];
    }
    $query = new WP_Query($args);
    $query_posts = $query->posts;
    get_template_part( 'templates/partials/slider-strip', 'null', [
        'title' => $_POST['section_title'],
        'filter' => [
            'active' => true,
            'post_type' => $_POST['post_type'],
            'taxonomy' => $_POST['taxonomy'],
            'term' => $_POST['term'],
            'action' => $_POST['action'],
        ],
        'posts' => $query_posts
    ]);
    wp_die();
}

// Slider strip filter (user favorites)
add_action("wp_ajax_async_filter_my_list" , "async_filter_my_list");
add_action('wp_ajax_nopriv_async_filter_my_list', 'async_filter_my_list');
function async_filter_my_list() {
    $user_id = get_current_user_id();
    $my_list = get_my_list($user_id, 'ids');

    if ($my_list) {
        $args = ['post_type' => 'movie', 'post__in' => array_reverse($my_list), 'orderby' => 'post__in', 'posts_per_page' => 15];
        if ($_POST['term']) {
            $term = get_term($_POST['term'], $_POST['taxonomy']);
            $args['tax_query'] = [[
                'taxonomy' => $_POST['taxonomy'],
                'field'    => 'id',
                'terms'    => $term->term_id,
                'operator' => 'IN',
            ]];
        }
        $query = new WP_Query($args);
        $query_posts = $query->posts;
    } else {
        $query_posts = [];
    }
    get_template_part('templates/partials/slider-strip', 'null', [
        'title' => 'My List',
        'filter' => [
            'active' => true,
            'post_type' => $_POST['post_type'],
            'taxonomy' => $_POST['taxonomy'],
            'term' => $_POST['term'],
            'action' => $_POST['action'],
        ],
        'posts' => $query_posts
    ]);
    wp_die();
}

// Slider strip filter (user continue watching)
add_action("wp_ajax_async_filter_continue_watching" , "async_filter_continue_watching");
add_action('wp_ajax_nopriv_async_filter_continue_watching', 'async_filter_continue_watching');
function async_filter_continue_watching() {
    $user_id = get_current_user_id();
    $cw_list = get_continue_watching_list($user_id, 'ids');

    if ($cw_list) {
        $args = ['post_type' => 'movie', 'post__in' => array_reverse($cw_list), 'orderby' => 'post__in', 'posts_per_page' => 15];
        if ($_POST['term']) {
            $term = get_term($_POST['term'], $_POST['taxonomy']);
            $args['tax_query'] = [[
                'taxonomy' => $_POST['taxonomy'],
                'field'    => 'id',
                'terms'    => $term->term_id,
                'operator' => 'IN',
            ]];
        }
        $query = new WP_Query($args);
        $query_posts = $query->posts;
    } else {
        $query_posts = [];
    }
    get_template_part('templates/partials/slider-strip', 'null', [
        'title' => 'Continue watching',
        'filter' => [
            'active' => true,
            'post_type' => $_POST['post_type'],
            'taxonomy' => $_POST['taxonomy'],
            'term' => $_POST['term'],
            'action' => $_POST['action'],
        ],
        'posts' => $query_posts
    ]);
    wp_die();
}

// Kaltura player: init
add_action("wp_ajax_request_kaltura_movie" , "request_kaltura_movie");
add_action('wp_ajax_nopriv_request_kaltura_movie', 'request_kaltura_movie');
function request_kaltura_movie() {
    $client = get_kaltura_session();

    $start_from = 0;
    $kaltura_id = '';
    if ($_POST['movie_id']) { //Try to get watchtime only if movie id is present
        $cw_list = get_continue_watching_list(get_current_user_id());
        if ($cw_list && isset($cw_list[$_POST['movie_id']])) {
            $start_from = $cw_list[$_POST['movie_id']]['time'];
        }
        $kaltura_id = get_field('kaltura_id', $_POST['movie_id']);
    } else if ($_POST['kaltura_id']) {
        $kaltura_id = $_POST['kaltura_id'];
    }

    echo json_encode([
        'wid' => get_field('partner_id', 'option'),
        'ks' => $client->getKS(),
        'uiconf_id' => 46602743,
        'start_from' => $start_from,
        'kaltura_id' => $kaltura_id,
    ]);
    wp_die();
}

// Kaltura player: update continue watching user list
add_action("wp_ajax_remove_from_continue_watching" , "update_continue_watching");
add_action('wp_ajax_nopriv_remove_from_continue_watching', 'update_continue_watching');

add_action("wp_ajax_update_continue_watching" , "update_continue_watching");
add_action('wp_ajax_nopriv_update_continue_watching', 'update_continue_watching');
function update_continue_watching() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $cw_list =  get_continue_watching_list($user_id, 'all'); //continue watching list from DB

        switch ($_POST['action']) {
            case 'remove_from_continue_watching' :
                if (is_array($cw_list)) {
                    unset($cw_list[$_POST['post_id']]);
                    update_user_meta($user_id, 'continue_watching', json_encode($cw_list));
                }
                break;
            case 'update_continue_watching' :
                if (!$cw_list || !isset($cw_list[$_POST['post_id']]) || $cw_list[$_POST['post_id']]['time'] < $_POST['time']) {
                    $cw_list[$_POST['post_id']] = [
                        'upd' => time(),
                        'time' => $_POST['time']
                    ];
                    update_user_meta($user_id, 'continue_watching', json_encode($cw_list));
                }
                break;
        }
    }
    wp_die();
}

add_action("wp_ajax_update_my_list" , "update_my_list");
add_action('wp_ajax_nopriv_update_my_list', 'update_my_list');
function update_my_list() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $my_list =  get_my_list($user_id, 'all'); //my list from DB

        if (is_array($my_list)) {
            if (isset($my_list[$_POST['post_id']])) { //movie exists in list
                unset($my_list[$_POST['post_id']]);
                if ($_POST['button_type'] == 'text') {
                    $return =  __('Add to my list', 'academe-theme');
                } else if ($_POST['button_type'] == 'icon') {
                    ob_start();
                    icon('star', 'icon-24');
                    $return = ob_get_clean();
                }

            } else { //movie not exists in list
                $my_list[$_POST['post_id']] = [
                    'upd' => time()
                ];
                if ($_POST['button_type'] == 'text') {
                    $return =  __('Remove from my list', 'academe-theme');
                } else if ($_POST['button_type'] == 'icon') {
                    ob_start();
                    icon('star', 'icon-24 icon-blue-stroke');
                    $return = ob_get_clean();
                }
            }
            update_user_meta($user_id, 'my_list', json_encode($my_list));
            echo json_encode($return);
        }
    }
    wp_die();
}

add_action("wp_ajax_create_lesson_session" , "create_lesson_session");
add_action('wp_ajax_nopriv_create_lesson_session', 'create_lesson_session');
function create_lesson_session() {
    if (is_user_logged_in()) {

        $post = json_decode(file_get_contents('php://input'), true);
        $lesson_id = $post['lesson_id'];

        $session_data = array(
            'post_title'    => sanitize_text_field( $_POST['session_title'] ),
            'post_type'     => 'session',
            'post_status'   => 'publish',
            'post_content'  => '',
            'post_author'   => get_current_user_id(),
            'post_name'     => generateSessionId(),
            'comment_status' => 'closed',
        );
        $post_id = wp_insert_post( $session_data );

        if ($post_id) {
            update_field( "based_on", $lesson_id, $post_id );
            update_field( "related_lesson", $lesson_id, $post_id );
            update_field( "session_type", 'lesson-editor', $post_id );

            $session_starts = current_time('Y-m-d H:i:s');
            update_field( "session_starts", $session_starts, $post_id );

            $session_ends = date( 'Y-m-d H:i:s', strtotime( $session_starts ) + 921600 ); // 3600 seconds = 1 hours
            update_field( "session_ends", $session_ends, $post_id ); //1 hour after session start

            echo json_encode(['success' => get_post_permalink($post_id)]);
        } else {
            echo json_encode(['error' => __('Something went wrong', 'academe-theme')]);
        }

    }
    wp_die();
}

add_action("wp_ajax_create_session" , "create_session");
add_action('wp_ajax_nopriv_create_session', 'create_session');
function create_session() {
    if (is_user_logged_in()) {

        if (!isset($_POST['schedule']) || empty($_POST['schedule'])) {
            echo json_encode(['error' => __('Date/Time should be selected', 'academe-theme')]);
            exit;
        }

        $session_data = array(
            'post_title'    => sanitize_text_field( $_POST['session_title'] ),
            'post_type'     => 'session',
            'post_status'   => 'publish',
            'post_content'  => $_POST['notes'],
            'post_author'   => get_current_user_id(),
            'comment_status' => 'closed',
        );
        $post_id = wp_insert_post( $session_data );

        if ($post_id) {
            update_field( "based_on", $_POST['based_on'], $post_id );
            update_field( "related_".$_POST['based_on'], $_POST['related_item'], $post_id );
            update_field( "session_type", $_POST['session_type'], $post_id );

            if ($_POST['session_type'] == 'async') {
                update_field( "access_duration", $_POST['access_duration'], $post_id );
                $shift = $_POST['access_duration'];
            } else {
                $shift = 1;
            }

            $tz = new DateTimeZone(get_option('timezone_string'));
            $session_starts = DateTime::createFromFormat('d/m/Y G:i', $_POST['schedule'], $tz);
            update_field( "session_starts", $session_starts->format('Y-m-d H:i:s'), $post_id );

            $session_ends = $session_starts->add(new DateInterval('PT'.$shift.'H'));
            update_field( "session_ends", $session_ends->format('Y-m-d H:i:s'), $post_id ); //1 hour after session start

            echo json_encode(['success' => get_post_permalink($post_id)]);
        } else {
            echo json_encode(['error' => __('Something went wrong', 'academe-theme')]);
        }

    }
    wp_die();
}


add_action("wp_ajax_load_more_sessions" , "load_more_sessions");
add_action('wp_ajax_nopriv_load_more_sessions', 'load_more_sessions');
function load_more_sessions() {

    $limit_per_page = 5;
    $args = [
        'post_type' => 'session',
        'orderby' => 'date',
        'order' => 'DESC' ,
        'posts_per_page' => $limit_per_page,
        'offset' => $_GET['offset'],
        'meta_query' => [
            'relation'		=> 'AND',
            array(
                'key'	 	=> 'session_ends',
                'value'	  	=> time(),
                'compare' 	=> '<',
            )
        ]
    ];

    $wp_query = new WP_Query($args);

    ob_start();

    global $post;
    foreach ($wp_query->posts as $post) {
        setup_postdata($post);
        get_template_part( 'templates/partials/my-lessons/student/session-row-block', 'null');
    }
    $data = ob_get_clean();

    $offset = $_GET['offset'] + $wp_query->post_count;
    echo json_encode([
        'sessions' => $data, //$wp_query->posts,
        'offset' => ($offset !== $wp_query->found_posts) ? $offset : 0,
    ]);

    wp_die();
}

function fetch_search_results(){
    global $search_query;
    $search_query = $_GET['s'];

    if (!$search_query) { ?>
        <div class="container"><?php _e('The search query is too small...', 'academe-theme'); ?></div>
    <?php exit; }

    global $client;
    global $post;
    global $wpdb;

    $client = get_kaltura_session();

    $cuePointItem = new KalturaESearchCuePointItem();
    $cuePointItem->itemType = KalturaESearchItemType::PARTIAL;
    $cuePointItem->fieldName = KalturaESearchCuePointFieldName::TEXT;
    $cuePointItem->searchTerm = $search_query;

    $unifiedItem = new KalturaESearchUnifiedItem ();
    $unifiedItem->searchTerm = $search_query;
    $unifiedItem->itemType = KalturaESearchItemType::EXACT_MATCH;
    $unifiedItem->addHighlight = true;

    $metadataItem = new KalturaESearchEntryMetadataItem();
    $metadataItem->searchTerm = $search_query;
    $metadataItem->itemType = KalturaESearchItemType::STARTS_WITH;
    $metadataItem->metadataProfileId  = 11616691;
    $metadataItem->addHighlight = true;

    $searchOperator = new KalturaESearchEntryOperator();
    $searchOperator->operator = KalturaESearchOperatorType::OR_OP;
    $searchOperator->searchItems = array($unifiedItem,$metadataItem,$cuePointItem);

    $searchParams = new KalturaESearchEntryParams();
    $searchParams->searchOperator = $searchOperator;
    $elasticsearchPlugin = KalturaElasticSearchClientPlugin::get($client);
    $searchResults = $elasticsearchPlugin->eSearch->searchEntry($searchParams, null);

    $filters_and = [];
    $filters = ['faculty', 'subject', 'topic', 'genre', 'grade'];
    foreach ($filters as $filter)
    {
        if ($_GET[$filter]) {
            $filters_and[] = "(tt.taxonomy='$filter' AND t.slug='$_GET[$filter]')";
        }
    }
    $filters_and_print = $filters_and ? "AND (" . implode(' OR ', $filters_and) . ")" : '';

    $sql_movie = "SELECT p.* FROM $wpdb->posts as p
    LEFT JOIN $wpdb->term_relationships AS tr ON tr.object_id=p.ID
    LEFT JOIN $wpdb->term_taxonomy as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
    LEFT JOIN $wpdb->terms AS t ON (t.term_id = tt.term_id)
    LEFT JOIN $wpdb->postmeta as pm ON pm.post_id = p.ID
    WHERE p.post_type='movie'
    AND p.post_status='publish'
    AND (
        p.post_title LIKE '%".$search_query."%' 
        OR p.post_content LIKE '%".$search_query."%' 
        OR pm.meta_value LIKE '%".$search_query."%' 
        OR (tt.taxonomy='post_tag' AND t.slug LIKE '%".$search_query."%')
    ) 
    $filters_and_print GROUP BY p.ID";

    $sql_result_movie = $wpdb->get_results( $sql_movie, OBJECT);

    if ($searchResults || $sql_result_movie) { ?>

        <section class="slider-strip">
            <div class="strip-top">
                <h2 class="strip-heading"><?php _e('Related movies', 'academe-theme'); ?></h2>
            </div>
            <div class="swiper-container swiper-strip">
                <div class="swiper-wrapper">
                    <?php
                    $kaltura_ids = array();
                    global $mediaEntry;
                    if ($searchResults->objects) {
                        foreach ($searchResults->objects as $mediaEntry) {
                            $kaltura_id = $mediaEntry->object->id;
                            $kaltura_ids[] = $kaltura_id;
                            $posts = get_posts(array(
                                'numberposts' => -1,
                                'post_type' => 'movie',
                                'meta_key' => 'kaltura_id',
                                'meta_value' => $mediaEntry->object->id
                            ));

                            if ($posts) {
                                $post = $posts[0];
                                setup_postdata($post);
                                get_template_part('templates/partials/movie-block-search', 'null');
                            }
                        }

                        wp_reset_postdata();
                    }
                    $mediaEntry = null;
                    if ($sql_result_movie) {
                        foreach ($sql_result_movie as $post) {
                            $kaltura_id = get_field('kaltura_id', $post->ID);

                            if(!in_array($kaltura_id,$kaltura_ids)) {
                                $kaltura_ids[]=$kaltura_id;
                                setup_postdata($post);
                                get_template_part('templates/partials/movie-block-search', 'null');
                            }
                        }
                        wp_reset_postdata();
                    } ?>
                </div>
                <!-- Add Arrows -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </section>

    <?php }

    $types = [
        'sfwd-courses' => 'Related lessons',
        'teaching-guide' => 'Related teaching guides',
        'clip' => 'Related clips'
    ];

    foreach ($types as $type => $heading) {

        if ((!is_user_logged_in() || is_user_in_role('student')) && in_array($type, ['teaching-guide', 'sfwd-courses']))  {
            continue;
        }

        $sql = "SELECT p.* FROM $wpdb->posts as p
        LEFT JOIN $wpdb->term_relationships AS tr ON tr.object_id=p.ID
        LEFT JOIN $wpdb->term_taxonomy as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        LEFT JOIN $wpdb->terms AS t ON (t.term_id = tt.term_id)
        LEFT JOIN $wpdb->postmeta as pm ON pm.post_id = p.ID
        WHERE p.post_type='$type'
        AND p.post_status='publish'
        AND (
            p.post_title LIKE '%".$search_query."%' 
            OR p.post_content LIKE '%".$search_query."%' 
            OR pm.meta_value LIKE '%".$search_query."%' 
            OR (tt.taxonomy='post_tag' AND t.slug LIKE '%".$search_query."%')
        ) 
        $filters_and_print GROUP BY p.ID";

        $sql_result = $wpdb->get_results( $sql, OBJECT);

        if ($sql_result) { ?>
            <section class="slider-strip">
                <div class="strip-top">
                    <h2 class="strip-heading"><?php _e($heading, 'academe-theme'); ?></h2>
                </div>
                <div class="swiper-container swiper-strip">
                    <div class="swiper-wrapper">
                        <?php foreach ($sql_result as $post) {
                            setup_postdata($post);
                            get_template_part('templates/partials/movie-block-search', 'null');
                        }
                        wp_reset_postdata(); ?>
                    </div>
                    <!-- Add Arrows -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </section>
        <?php }
    }

    exit;
}
add_action('wp_ajax_nopriv_fetch_search_results','fetch_search_results');
add_action('wp_ajax_fetch_search_results','fetch_search_results');


add_action("wp_ajax_render_quiz_content" , "render_quiz_content");
add_action('wp_ajax_nopriv_render_quiz_content', 'render_quiz_content');
function render_quiz_content() {
    if ($_GET['quiz_id']) {
        new WpProQuiz_Controller_Front();
        //echo do_shortcode('[ld_quiz quiz_id="'.$_GET['quiz_id'].'"]');
        $sc = learndash_quiz_shortcode(
            array(
                'quiz_id'     => $_GET['quiz_id'],
                'course_id'   => absint($_GET['course_id']),
                'quiz_pro_id' => '',
            ),
            '',
            true
        );
        print_r($sc);
    }

    wp_die();
}

add_action("wp_ajax_update_session_current_slide" , "update_session_current_slide");
add_action('wp_ajax_nopriv_update_session_current_slide', 'update_session_current_slide');
function update_session_current_slide() {
    if ($_GET['session_id'] && $_GET['slide_id']) {
        update_field('current_slide', $_GET['slide_id'], $_GET['session_id']);
    }
    print_r(json_encode(['success' => 'Current slide updated!']));

    wp_die();
}
add_action("wp_ajax_get_session_link" , "get_session_link");
add_action('wp_ajax_nopriv_get_session_link', 'get_session_link');
function get_session_link() {
    if (!$_GET['code']) {
        echo json_encode(['error' => __('You have not entered the lesson code', 'academe-theme')]);
        wp_die();
    }

    $session = get_page_by_path( $_GET['code'], 'OBJECT', 'session' );
    echo $session ? json_encode(['success' => get_permalink($session)]) : json_encode(['error' => __('Lesson not found.', 'academe-theme')]);

    wp_die();
}

add_action("wp_ajax_get_sessions_list" , "get_sessions_list");
add_action('wp_ajax_nopriv_get_sessions_list', 'get_sessions_list');
function get_sessions_list() {
    $wp_query = new WP_Query([
        'post_type' => 'session',
        'meta_query' => [
            array(
                'key' => 'related_lesson',
                'value' => $_GET['lesson'],
                'compare' => '=',
            )
        ]
    ]); ?>

    <?php foreach ($wp_query->posts as $post) {
        $post->session_starts = get_field('session_starts', $post->ID); ?>
        <div class="session-row">
            <span class="session-code"><?php echo $post->post_name; ?></span>
            <span class="session-starts"><span class="static-text"><?php echo __('Starts at ', 'academe-theme'); ?></span><?php echo $post->session_starts; ?></span>
            <a class="primary-btn" href="/sessions/<?php echo $post->post_name; ?>"><?php _e('Go to session', 'academe-theme'); ?></a>
        </div>
    <?php } ?>

    <?php

    wp_die();
}
