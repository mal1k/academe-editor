<?php

// Template name: Test movie editor template

    $user_id = get_current_user_id();
    $my_list = get_my_list($user_id, 'ids');
    $query_posts = [];
    if ($my_list) {
        $query = new WP_Query(['post_type' => 'movie', 'post__in' => $my_list, 'orderby' => 'post__in', 'suppress_filters' => true]);
        $query_posts = $query->posts;
    }

    foreach ($query_posts as $post) {
            echo 'Title: ' . $post->post_title . '<br>';
            echo 'Link: ' . $post->guid  . '<br><br>';
    }

?>