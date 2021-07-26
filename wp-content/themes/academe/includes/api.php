<?php

function create_ACF_meta_in_REST() {
    $postypes_to_exclude = ['acf-field-group','acf-field'];
    $extra_postypes_to_include = ["page"];
    $post_types = array_diff(get_post_types(["_builtin" => false], 'names'),$postypes_to_exclude);

    array_push($post_types, $extra_postypes_to_include);

    foreach ($post_types as $post_type) {
        register_rest_field( $post_type, 'ACF', [
                'get_callback'    => 'expose_ACF_fields',
                'schema'          => null,
            ]
        );
    }

}

function expose_ACF_fields( $object ) {
    $ID = $object['id'];
    return get_fields($ID);
}

add_action( 'rest_api_init', 'create_ACF_meta_in_REST' );


add_action( 'rest_api_init', function(){

    require_once ACADEME_THEME_DIR . 'includes/lesson-slide-templates.php';

    register_rest_route( 'academe/v1', '/slide-templates', [
        'methods'  => 'GET',
        'callback' => 'get_lesson_slide_templates',
    ] );

} );

add_action( 'rest_api_init', function() {
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
            return $kaltura_id;

            $posts = get_posts(array(
                 'post_type' => 'movie',
                 //'meta_key'		=> 'kaltura_id',
	             //'meta_value'	=> $kaltura_id,
                 'meta_query'	=> array(
                      array(
                          'fields'          => 'ids',
                          'key'	  	=> 'kaltura_id',
                          'value'	  	=> $kaltura_id,
                          'compare' 	=> '=',
                      ),
                  ),
             ));
             return getMovieDataFromPost(get_fields($movie_post->ID));
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