<?php if (isset($args['slides'])) {
    $slides = $args['slides']; ?>
    <section id="bigSlider">
        <div class="swiper-container swiper-large-home">
            <div class="swiper-wrapper">
                <?php foreach ($slides as $post) {
                    global $post;
                    setup_postdata($post);
                    $custom_fields = get_fields($post->ID);
                    if ($post->post_type != 'movie' || isset($custom_fields['kaltura_id'])) { //check kaltura ID for movies?>
                        <div class="swiper-slide"
                             style="background: linear-gradient(90deg, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0) 60%) no-repeat center / cover;">
                            <div class="slide-info">
                                <div class="title-wrap">
                                    <div class="title"><?php the_title(); ?></div>
                                </div>
                                <?php if ($post->post_type == 'movie') { ?>
                                    <div class="created-by">
                                        <?php _e('Created By:', 'academe-theme'); ?>
                                        <span class="name"><?php the_author(); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses', 'teaching-guide'])) { ?>

                                    <div class="description">
                                        <?php echo get_the_excerpt(); ?>
                                    </div>
                                <?php } ?>
                                <?php if ($post->post_type == 'movie') { ?>
                                    <div class="genres">
                                            <span class="genres-list">
                                                <?php $genres = wp_get_post_terms($post->ID, 'genre', ['fields' => 'names']);
                                                if ($genres) {
                                                    $genres_counter = 3;
                                                    foreach ($genres as $genre) {
                                                        if ($genres_counter) {
                                                            echo "<span class=\"genre\">$genre</span>";
                                                            $genres_counter--;
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                } ?>
                                            </span>
                                    </div>
                                <?php } ?>
                                <div class="meta">
                                    <?php if ($post->post_type == 'movie') { ?>
                                        <div>
                                            <div class="plus-18">+18</div>
                                        </div>
                                        <div class="year"><?php echo isset($custom_fields['year']) ? $custom_fields['year'] : ''; ?></div>
                                        <div class="duration">
                                            <?php icon('duration', 'icon-light-gray');
                                            if (isset($custom_fields['duration'])) {
                                                $duration = explode(":", $custom_fields['duration']);
                                                echo (int)$duration[0].'h '. (int)$duration[1].'m';
                                            } ?>
                                        </div>
                                        <div class="lessons"><?php icon('white-board', 'icon-light-gray'); ?>8</div>
                                    <?php } ?>
                                    <?php if (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses'])) { ?>
                                        <div class="students"><?php
                                            icon('student', 'icon-light-gray');
                                            echo rand(5, 500); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="views"><?php
                                        icon('views', 'icon-light-gray');
                                        if(PVC_ACTIVE) echo pvc_get_post_views(); ?>
                                    </div>

                                </div>
                                <div class="watch">
                                    <?php if ($post->post_type == 'movie') { ?>
                                        <a href="<?php the_permalink(); ?>" class="start-watch">
                                            <?php icon('play-rounded'); ?>
                                            <span><?php _e('Present Now', 'academe-theme'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses'])) { ?>
                                        <a href="<?php the_permalink(); ?>" class="start-watch">
                                            <?php icon('play-rounded'); ?>
                                            <span><?php _e('Start Lesson', 'academe-theme'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if ($post->post_type == 'teaching-guide') { ?>
                                        <a href="<?php the_permalink(); ?>" class="start-watch">
                                            <div class="info">i</div>
                                            <span><?php _e('More info', 'academe-theme'); ?></span>
                                        </a>
                                    <?php } ?>

                                    <?php if (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses', 'movie'])) { ?>
                                    <div class="actions-more ui dropdown link item dark">
                                        <div class="info">i</div>
                                        <div class="menu">
                                            <div class="menu-body">
                                                <?php if (is_user_logged_in() && $post->post_type == 'movie') { ?>
                                                    <div class="item"><?php the_my_list_button($post->ID); ?></div>
                                                    <a href="<?php echo get_permalink(); ?>" class="item"><?php _e('Movie info', 'academe-theme'); ?></a>
                                                    <div class="item start-movie-trailer"><?php _e('View trailer', 'academe-theme'); ?></div>
                                                <?php } ?>
                                                <?php if (is_user_logged_in() && in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses'])) { ?>
                                                    <a href="<?php echo get_permalink(); ?>" class="item"><?php _e('Lesson info', 'academe-theme'); ?></a>
                                                    <div class="item goto-related-lessons"><?php _e('View related Lessons', 'academe-theme'); ?></div>
                                                <?php } ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                </div>
                                <div class="tags">
                                    <?php $tags = wp_get_post_tags($post->ID); ?>
                                    <?php if ($tags) { ?>
                                        <span class="tags-list">
                                            <?php $tags_counter = 3;
                                            $tags_string = '';
                                            foreach ($tags as $tag) {
                                                if ($tags_counter) {
                                                    $tags_string .= '#' . $tag->name . ' ';
                                                    $tags_counter--;
                                                } else {
                                                    break;
                                                }
                                            }
                                            echo rtrim($tags_string) . '...'; ?>
                                        </span>
                                        <a class="tags-more" href="<?php the_permalink(); ?>">more</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if ($post->post_type == 'movie') { ?>
                                <img class="slide-image" src="<?php echo get_movie_thumbnail($custom_fields['kaltura_id']); ?>" />
                            <?php } ?>
                            <?php if (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses', 'teaching-guide'])) { ?>
                                <img class="slide-image" src="<?php echo get_the_post_thumbnail_url(); ?>" />
                            <?php } ?>
                            <div class="slide-label">
                                <?php icon($post->post_type); ?>
                                <?php $post_type = get_post_type_object($post->post_type); ?>
                                <span class="title"><?php echo str_ireplace('course', 'lesson', $post_type->labels->singular_name); ?></span>
                            </div>
                        </div>
                    <?php }
                }
                wp_reset_postdata(); ?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </section>
<?php } ?>