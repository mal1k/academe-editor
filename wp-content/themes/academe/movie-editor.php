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
            $custom_fields = get_fields($post->ID); ?>

            <img class='slide-image' src='<?php echo get_movie_thumbnail($custom_fields['kaltura_id'], 280, 175); ?>' /><br>

            <?php
            echo '<b>Title:</b> ' . $post->post_title . '<br>';
            echo '<b>Link:</b> ' . $post->guid . '<br>';?>
            <b>Duration:</b>
            <?php
                if (isset($custom_fields['duration'])) {
                    $duration = explode(":", $custom_fields['duration']);
                    echo (int)$duration[0].'h '. (int)$duration[1].'m';
                }
            ?><br>
            <b>Tags:</b> 
            <?php if( in_array($post->post_type, ['movie', 'teaching-guide']) ) { ?>
                <?php $tags = wp_get_post_tags($post->ID);
                if ($tags) {
                    $tags_counter = 2;
                    $tags_string = '';
                    foreach ($tags as $tag) {
                        if ($tags_counter) {
                            $tags_string .= '#' . $tag->name . ' ';
                            $tags_counter--;
                        }
                    }
                    echo rtrim($tags_string);
                } ?>
            <?php } ?><br>
            </div>
                <b>Year:</b> <?php echo isset($custom_fields['year']) ? $custom_fields['year'] : ''; ?>
                    <?php
                    if (isset($custom_fields['duration'])) {
                        $duration = explode(":", $custom_fields['duration']);
                        echo (int)$duration[0].'h '. (int)$duration[1].'m';
                    } ?><br>
                <b>Views:</b> <?php
                    if(PVC_ACTIVE) echo pvc_get_post_views(); ?><br>
                <b>Genres:</b> <?php $genres = wp_get_post_terms($post->ID, 'genre', ['fields' => 'names']);
                    if ($genres) {
                        foreach ($genres as $genre) {
                            echo "<span class=\"genre\">$genre</span> ";
                        }
                    } ?><br>
                <?php if(isset($custom_fields['director'])) { ?>
                    <b>Directed by:</b> <?php echo $custom_fields['director']; ?>
                <?php } ?><br>
                <b>Description:</b>
                <?php $content = get_separated_read_more_content($post->post_content); ?>
                <div class="overview"><?php echo $content[0] . '<span class="readmore-hidden">' . $content[1] . '</span>'; ?></div><br><br><br>

            <?php
    }

?>