<?php

// Template name: Get selected Movies

// if ( !empty($_GET['kaltura']) )
//     echo json_encode(get_field('partner_id', 'option'));

// exit;

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
    
    print_r(json_encode($array));

        // $arr = 
        // array( 'movies' =>
        //     array(
        //         array('title'=>"1", 'image'=>"2", 'kalturaID'=>"3", 'time'=>"4"),
        //         array('title'=>"1", 'image'=>"2", 'kalturaID'=>"3", 'time'=>"4")
        //     )
        // );
    
    ?>