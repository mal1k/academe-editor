<div class="lesson-row">
    <!-- <a class="wrap-link" href="<?php the_permalink(); ?>" target="_blank"></a> -->
    <div class="thumbnail">
        <img src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium'); ?>" />
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
    <div class="progress">
        <?php icon('clock', 'icon-24'); ?>
        1h 23m
    </div>

    <div class="sessions-count">
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
                    <div class="item create-session-btn" data-modal-id="<?php echo $cs_modal_id; ?>">
                        <?php icon('blue-plus'); ?>
                        <span class="text-blue"><?php _e('Create New Session', 'academe-theme'); ?></span>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="item">View Lesson</a>
                </div>
            </div>
        </div>
    </div>

    <?php get_template_part( 'templates/partials/modals/create-session', 'null', ['id' => $cs_modal_id]); ?>

</div>