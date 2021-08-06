<?php

function get_next_lesson_id( $object ) {
    return learndash_next_post_link('', 'id', get_post($object['id']));
}

function get_prev_lesson_id( $object ) {
    return learndash_previous_post_link('', 'id', get_post($object['id']));
}

add_action( 'rest_api_init', function() {

    register_rest_field('sfwd-lessons', 'next_lesson', [
            'get_callback' => 'get_next_lesson_id',
            'schema' => null,
        ]
    );
    register_rest_field('sfwd-lessons', 'prev_lesson', [
            'get_callback' => 'get_prev_lesson_id',
            'schema' => null,
        ]
    );

    require_once ACADEME_THEME_DIR . 'includes/lesson-slide-templates.php';
    register_rest_route( 'academe/v1', '/slide-templates', [
        'methods'  => 'GET',
        'callback' => 'get_lesson_slide_templates',
    ] );

    register_rest_route('academe/v1', '/kaltura-config',[
        'methods' => 'GET',
        'callback' => function() {
            $client = get_kaltura_session();

            return [
                'wid' => get_field('partner_id', 'option'),
                'ks' => $client->getKS(),
                'uiconf_id' => 46602743,
            ];
        }
    ]);

    register_rest_route( 'academe/v1', '/get-movie-meta-by-kaltura', [
        'methods'  => 'GET',
        'callback' => function($data) {
            $kaltura_id = $data->get_param( 'id' );

            $args = array(
                'post_type' => 'movie',
                'meta_key' => 'kaltura_id',
                'meta_value' => $kaltura_id
            );

            $post = get_posts($args);
            return getMovieDataFromPost($post[0]);
        }
    ]);

    register_rest_route( 'academe/v1', '/movies/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => function() {
            $movie_id = $request->get_param( 'id' );
            $post = get_post($movie_id);
            return getMovieDataFromPost($post);
        }
    ]);

    register_rest_route( 'academe/v1', '/my-movies', [
        'methods'  => 'GET',
        'callback' => function() {
            $array = [];
            $user_id = get_current_user_id();
            $my_list = get_my_list($user_id, 'ids');
            $query_posts = [];
            
            if ($my_list) {
                $query = new WP_Query(['post_type' => 'movie', 'post__in' => $my_list, 'orderby' => 'post__in', 'suppress_filters' => true]);
                $query_posts = $query->posts;
            }

            foreach ($query_posts as $post) {
                array_push($array, getMovieDataFromPost($post));
            }
            
            return $array;
        }
    ] );

    register_rest_route( 'academe/v1', '/get-movie-images/(?P<id>[a-zA-Z0-9-_]+)', array(
		'methods'  => 'GET',
		'callback' => 'movieImagesFromKaltura',
	) );

});

function getMovieDataFromPost($movie_post) {
    $array = Array();
    $custom_fields = get_fields($movie_post->ID);

    $array['kaltura_id'] = $custom_fields['kaltura_id'];
    $array['image'] = get_movie_thumbnail($custom_fields['kaltura_id'], 280, 175);
    $array['title'] = $movie_post->post_title;
    $array['link'] = $movie_post->guid;

    // duration
    if (isset($custom_fields['duration'])) {
        $duration = explode(":", $custom_fields['duration']);
        $durationTime = (int)$duration[0].'h '. (int)$duration[1].'m';
    }
    $array['time'] = $durationTime;
    
        // tags
        if( in_array($movie_post->post_type, ['movie', 'teaching-guide']) ) {
            $tags = wp_get_post_tags($movie_post->ID);
            if ($tags) {
                $tags_counter = -1;
                $tags_string = '';
                foreach ($tags as $tag) {
                    if ($tags_counter) {
                        $tags_string .= '#' . $tag->name . ', ';
                        $tags_counter--;
                        $array['tags'][] = array('tag'=>$tag->name);
                    }
                }
            } 
        }
        // year
            $array['year'] = $custom_fields['year'];
        // views
            $array['views'] = pvc_get_post_views();
        // genres
            $genres = wp_get_post_terms($movie_post->ID, 'genre', ['fields' => 'names']);
            if ($genres) {
                foreach ($genres as $genre) {
                    $array['genres'][] = array('genre'=>$genre);
                }
            }
        // director
            $array['director'] = $custom_fields['director'];
        // content
            $content = get_separated_read_more_content($movie_post->post_content);
            $array['description'] = $content;
            
        return $array;
}

function movieImagesFromKaltura( WP_REST_Request $request ) {
    $kaltura_id = $request->get_param( 'id' );
    $count = 3; // images amount
    $time_step = 120; // in seconds

    for ( $i = 1; $i <= $count; $i++ ) {
        $images[] = get_movie_thumbnail($kaltura_id, 1920, 1080, 50, $time_step);
        $time_step += $time_step;
    }

    return $images;
}