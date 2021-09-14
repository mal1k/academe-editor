<?php $custom_fields = get_fields($post->ID);  ?>

<div class="lesson-row">
    <!--<a class="wrap-link" href="<?php //the_permalink(); ?>" target="_blank"></a>-->
    <div class="thumbnail">
        <img src="<?php echo $custom_fields['cover_image_url']; ?>" />
    </div>
    <div class="session-info">
        <div class="name">
            <?php the_title(); ?>
        </div>
        <div class="teacher">
            <?php _e('Created by: ', 'academe-theme'); ?> <span><?php the_author(); ?></span>
        </div>
        <div class="tags">
            <span class="tags-list">
            <?php $tags = wp_get_post_tags($post->ID);
            if ($tags) {
                $tags_counter = 2;
                $tags_string = '';
                foreach ($tags as $tag) {
                    if ($tags_counter) {
                        $tags_string .= '#' . $tag->name . ' ';
                        $tags_counter--;
                    } else {
                        break;
                    }
                }
                echo rtrim($tags_string);
            } ?>
            </span>
        </div>
    </div>
    <div class="stretcher"></div>
    <?php if (0) { ?>
        <div class="progress">
            <?php icon('clock', 'icon-24'); ?>
            1h 23m
        </div>
    <?php } ?>

    <div class="sessions-count" data-lesson-id="<?php echo $post->ID; ?>">
        <?php
        $wp_query = new WP_Query([
            'post_type' => 'session',
            'meta_query' => [
                array(
                    'key' => 'related_lesson',
                    'value' => $post->ID,
                    'compare' => '=',
                )
            ]
        ]);
        printf( _nx( '%s session', '%s sessions', $wp_query->found_posts, 'session count', 'academe-theme' ), number_format_i18n( $wp_query->found_posts ) );
        ?>
    </div>
    <?php $cs_modal_id = uniqid(); ?>
    <div class="movie-actions">
        <div class="actions-more ui dropdown link item dark">
            <div class="icon-three-dots">
                <div></div><div></div><div></div>
            </div>
            <div class="menu">
                <div class="menu-body">
                    <a href="#" class="item" data-modal-id="<?php echo $cs_modal_id; ?>">Play Now</a>
                    <a href="#" class="item create-session-btn-schedule" data-modal-id="<?php echo $cs_modal_id; ?>">Schedule</a>
                    <a href="/lesson-editor?lesson_id=<?php echo $post->ID; ?>" class="item">Edit Lesson</a>
                    <a href="/sessions/<?php echo $wp_query->posts[0]->post_name; ?>" class="item">View Lesson</a>
                </div>
            </div>
        </div>
    </div>

    <?php get_template_part( 'templates/partials/modals/create-session', 'null', ['id' => $cs_modal_id]); ?>

</div>