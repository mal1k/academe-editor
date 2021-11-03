<?php $custom_fields = get_fields($post->ID);  ?>

<?php $sponsored =  (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses']) && $custom_fields['sponsored']) ? true : false ?>

<?php if ($post->post_type != 'movie' || isset($custom_fields['kaltura_id'])) { ?>
    <div class="swiper-slide movie-block <?php echo $sponsored ? 'sponsored' : ''; ?>" data-movie-id="<?php echo $post->ID; ?>" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.95) 11.29%, rgba(0, 0, 0, 0) 100%) no-repeat center / cover; <?php if ( $args['jif'] ) echo 'border: 1px solid blue;'; ?>">
        <div class="slide-info">
            <div class="info-top">
                <div class="title"><?php echo $post->post_title; ?></div>
                <?php //if( in_array($post->post_type, ['movie', 'teaching-guide']) ) { ?>

                <?php if ( $post->post_type == 'sfwd-courses' ) : ?>
                    <div class="created_by" style="font-size: 12px; color: white;">
                        <?php 
                        $user = get_userdata( $post->post_author );
                        echo 'Created By: ' . $user->display_name; ?></div>
                <?php endif; ?>    

                    <div class="tags">
                <span class="tags-list" style="font-size: 11px;">
                <?php $tags = wp_get_post_tags($post->ID);
                if ($tags) {
                    $tags_counter = 3;
                    $tags_string = '';
                    foreach ($tags as $tag) {
                        if ($tags_counter) {
                            $tags_string .= '#' . $tag->name . ' ';
                            $tags_counter--;
                        } else {
                            break;
                        }
                    }
                    echo rtrim($tags_string) . '...';
                } ?>
                </span>
                    </div>
                <?php //} ?>
            </div>
            <div class="meta">
                <div class="left-part">
                    <?php if($post->post_type == 'movie') { ?>
                        <?php if(0) { ?>
                            <div>
                                <div class="plus-18">+18</div>
                            </div>
                        <?php } ?>
                        <div class="duration">
                            <?php icon('duration', 'icon-white');
                            if (isset($custom_fields['duration'])) {
                                $duration = explode(":", $custom_fields['duration']);
                                echo (int)$duration[0].'h '. (int)$duration[1].'m';
                            } ?>
                        </div>
                        <div class="lessons"><?php icon('white-board', 'icon-white'); ?><?php echo rand(2, 15); ?></div>
                    <?php } ?>
                    <?php if (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses'])) { ?>
                        <div class="students"><?php
                            icon('student', 'icon-light-gray');
                            echo rand(5, 500); ?>
                        </div>
                    <?php } ?>
                    <div class="views">
                        <?php icon('views', 'icon-white');
                        if(PVC_ACTIVE) echo pvc_get_post_views(); ?>
                    </div>
                </div>

                <div class="right-part">
                    <?php if ($post->post_status == 'private') {
                        icon('hide', 'visibility icon-24');
                    } ?>
                    <?php if(is_user_logged_in()) {
                        the_my_list_button($post->ID, 'icon');
                    } ?>
                    <?php if ($post->post_type ==  'sfwd-courses' && is_user_logged_in() && !is_user_in_role('student')) {
                        $wp_query = new WP_Query([
                            'post_type' => 'session',
                            'meta_query' => [
                                array(
                                    'key' => 'related_lesson',
                                    'value' => $post->ID,
                                    'compare' => '=',
                                )
                            ]
                        ]); ?>

                        <?php if ($post->post_status === 'draft') { ?>
                            <a href="/lesson-editor?lesson_id=<?php echo $post->ID; ?>">
                                <?php icon('element', 'icon-24'); ?>
                            </a>
                        <?php } elseif ($post->post_type == 'sfwd-courses') { ?>    
                            <a href="<?php echo $post->guid; ?>">
                                <?php icon('element', 'icon-24'); ?>
                            </a>
                        <?php } else { ?>
                            <a href="/sessions/<?php echo $wp_query->posts[0]->post_name; ?>">
                                <?php icon('element', 'icon-24'); ?>
                            </a>
                        <?php } ?>

                    <?php } else { ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php icon('element', 'icon-24'); ?>
                        </a>
                    <?php } ?>

                </div>

            </div>
            <?php if ($post->post_type ==  'sfwd-courses' && is_user_logged_in() && !is_user_in_role('student')) {
                    switch ( $post->post_status ) {
                        case "draft":
                            $link = "/lesson-editor?lesson_id=$post->ID";
                            break;
                        default:
                            $link = get_permalink();
                    } ?>

                    <a href="<?php echo $link; ?>" class="watch">
                        <div class="start-watch"><?php icon('play-rounded'); ?></div>
                    </a>
            <?php } else { ?>
            <a href="<?php the_permalink(); ?>" class="watch">
                <div class="watch">
                    <div class="start-watch <?php if ($post->post_type == 'movie') { ?> start-movie-preview<?php } ?> " <?php if ($post->post_type == 'movie') { ?> data-movie-id="<?php echo $post->ID; ?>" onclick="return false;" data-mode="advanced" style="position: relative; z-index: 1;" <?php } ?>><?php icon('play-rounded'); ?></div>
                </div>
            </a>
            <?php } ?>

        </div>
        <?php if (0) { ?>
        <div class="actions-more ui dropdown link item dark">
            <div class="slide-actions">
                <div></div> <div></div> <div></div>
            </div>
            <div class="menu">
                <div class="menu-body">
                    <?php if(is_user_logged_in()) { ?>
                        <div class="item"><?php the_my_list_button($post->ID); ?></div>
                    <?php } ?>
                    <div class="item"><a href="<?php the_permalink(); ?>"><?php _e('Movie info', 'academe-theme'); ?></a></div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($post->post_type == 'movie') { ?>
            <img class="slide-image" src="<?php echo get_movie_thumbnail($custom_fields['kaltura_id'], 280, 175); ?>" />
        <?php } ?>
        <?php if ( in_array($post->post_type, ['sfwd-lessons', 'teaching-guide']) ) { ?>
            <?php if(has_post_thumbnail($post->ID)) { ?>
                <img class="slide-image" src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium'); ?>" />
            <?php } ?>
        <?php } ?>
        <?php if ($post->post_type ==  'sfwd-courses') { ?>
            <img class="slide-image" src="<?php echo $custom_fields['cover_image_url']; ?>" />
        <?php } ?>

        <?php if ($post->post_type == 'clip') { ?>
            <?php $movie_fields = get_fields($custom_fields['movie_id']->ID); ?>
            <img class="slide-image" src="<?php echo get_movie_thumbnail($movie_fields['kaltura_id'], 280, 175); ?>" />
        <?php } ?>

        <div class="slide-label <?php echo $post->post_type; ?>">
            <?php icon($post->post_type); ?>
        </div>
        <?php if ($sponsored) { ?>
            <img class="sponsor-icon"
                 src="<?php echo $custom_fields['sponsor_icon']; ?>"
                 alt="<?php echo $custom_fields['sponsor_name']; ?>"
                 title="<?php echo $custom_fields['sponsor_name']; ?>"
            />
        <?php } ?>
    </div>
<?php } ?>
<?php wp_reset_query(); ?>
