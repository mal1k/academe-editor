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
    register_rest_route( 'academe/v1', '/my-movies', [
        'methods'  => 'GET',
        'callback' => function() {

    $user_id = get_current_user_id();
    $my_list = get_my_list($user_id, 'ids');
    $query_posts = [];
    if ($my_list) {
        $query = new WP_Query(['post_type' => 'movie', 'post__in' => $my_list, 'orderby' => 'post__in', 'suppress_filters' => true]);
        $query_posts = $query->posts;
    }
    $i = 0;

    if ( $_GET['global_info'] ) {
        // partner ID
        if ( get_field('kaltura_player_id', 'option') ) {
            $kalturaPlayerID = get_field('kaltura_player_id', 'option');
        } else {
            $kalturaPlayerID = 46602743;
        }
        $array['partnerID'] = get_field('partner_id', 'option');

        // kaltura Player ID
        $array['kalturaPlayerID'] = $kalturaPlayerID;
        // ks
        $array['ks'] = get_kaltura_ks();
        print_r(json_encode($array));
        exit;
    }

    foreach ($query_posts as $post) {
        $addToArray = null;
        $custom_fields = get_fields($post->ID);

        // kaltura id
            $array['movies'][$i]['kalturaID'] = get_field('kaltura_id');
        // image
            $array['movies'][$i]['image'] = get_movie_thumbnail($custom_fields['kaltura_id'], 280, 175);
        // title
            $array['movies'][$i]['title'] = $post->post_title;
        // link
            $array['movies'][$i]['link'] = $post->guid;
        // duration
            if (isset($custom_fields['duration'])) {
                $duration = explode(":", $custom_fields['duration']);
                $durationTime = (int)$duration[0].'h '. (int)$duration[1].'m';
            }
            $array['movies'][$i]['time'] = $durationTime;
        // tags
            if( in_array($post->post_type, ['movie', 'teaching-guide']) ) {
                $tags = wp_get_post_tags($post->ID);
                if ($tags) {
                    $tags_counter = -1;
                    $tags_string = '';
                    foreach ($tags as $tag) {
                        if ($tags_counter) {
                            $tags_string .= '#' . $tag->name . ', ';
                            $tags_counter--;
                            $array['movies'][$i]['tags'][] = array('tag'=>$tag->name);
                        }
                    }
                } 
            }
        // year
            $array['movies'][$i]['year'] = $custom_fields['year'];
        // views
            $array['movies'][$i]['views'] = pvc_get_post_views();
        // genres
            $genres = wp_get_post_terms($post->ID, 'genre', ['fields' => 'names']);
            if ($genres) {
                foreach ($genres as $genre) {
                    $array['movies'][$i]['genres'][] = array('genre'=>$genre);
                }
            }
        // director
            $array['movies'][$i]['director'] = $custom_fields['director'];
        // content
            $content = get_separated_read_more_content($post->post_content);
            $array['movies'][$i]['description'] = $content;
            
        $i++;
    }
    
    return $array;
        }
    ] );
});

/*add_action( 'rest_api_init', function () {
    require_once ACADEME_THEME_DIR . 'includes/get-movies-list.php';
	register_rest_route( 'academe/v1', '/list/(?P<id>\d+)', array(
		'methods'  => 'GET',
        'callback' => 'my_rest_api_movies_list',
	) );
} );*/