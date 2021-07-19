<?php get_header(); ?>
    <main class="main my-movies-page">
        <section id="myMoviesSort">
            <h1><?php the_title(); ?></h1>
            <div class="sort-wrap">
                <span>
                    <?php _e('Sort by:', 'academe-theme'); ?>
                </span>
                <div class="dropdown ui search my-movies-sort dark">
                    <input name="orderby" type="hidden" value="<?php echo ($_GET['orderby']) ?? 'post__in'; ?>" >
                    <span class="default text"></span>
                    <?php icon('chevron-bold', 'with-rotate'); ?>
                    <div class="menu">
                        <div class="menu-body">
                            <div class="item" data-value="post__in"><?php _e("Last Seen/Updated", "academe-theme"); ?></div>
                            <div class="item" data-value="title"><?php _e("Name", "academe-theme"); ?></div>
                            <?php if (0) { ?>
                                <div class="item" data-value="views"><?php _e("Most Popular", "academe-theme"); ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <?php
        $user_id = get_current_user_id();
        $movies_list = get_my_movies($user_id, 'all');
        $order_by = ($_GET['orderby'] == 'views') ? 'post_views' : $_GET['orderby'];

        if ($movies_list) {
            global $wp_query;
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $args = [
                'post_type' => 'movie',
                'post__in' => array_keys($movies_list),
                'orderby' => $order_by ?? 'post__in',
                'order' => isset($_GET['orderby']) && in_array($_GET['orderby'], ['title']) ? 'ASC' : 'DESC' ,
                'posts_per_page' => 10,
                'paged' => $paged
            ];
            $wp_query = new WP_Query($args);
            if($wp_query->posts) { ?>
                <div class="movie-rows">
                <?php foreach ($wp_query->posts as $post) { ?>
                    <?php setup_postdata($post); ?>
                    <?php $custom_fields = get_fields($post->ID);?>

                    <div class="movie-row">
                        <div class="thumbnail">
                            <img src="<?php echo get_movie_thumbnail($custom_fields['kaltura_id'], 200, 120, 45 ); ?>" />
                        </div>
                        <div class="name">
                            <?php the_title();
                            echo ($custom_fields['year']) ? ' ('.$custom_fields['year'].')' : '';
                            ?>
                        </div>
                        <div class="continue-watching-time">
                            <?php if (isset($movies_list[$post->ID]['time'])) { ?>
                                <?php icon('clock', 'icon-24'); ?>
                                <span>
                                    <span class="time-watched">
                                        <?php $time_watched =  gmdate("H:i", $movies_list[$post->ID]['time']);
                                        $temp = explode(":", $time_watched);
                                        echo (int)$temp[0].'h '. (int)$temp[1].'m';
                                        ?>
                                    </span>
                                    &nbsp;/&nbsp;
                                    <span class="time-full">
                                        <?php if (isset($custom_fields['duration'])) {
                                            $duration = explode(":", $custom_fields['duration']);
                                            echo (int)$duration[0].'h '. (int)$duration[1].'m';
                                        } else {
                                            _e('unknown', 'academe-theme');
                                        } ?>
                                    </span>
                                </span>
                            <?php } ?>
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
                        <div class="movie-actions">
                            <a href="<?php echo get_permalink() . '?play'; ?>" class="play-movie-btn">
                                <?php if (isset($movies_list[$post->ID]['time'])) { ?>
                                    <span><?php _e('Continue Watching', 'academe-theme'); ?></span>
                                <?php } else { ?>
                                    <span><?php _e('Play Now', 'academe-theme'); ?></span>
                                <?php } ?>
                            </a>
                            <div class="actions-more ui dropdown link item dark">
                                <div class="icon-three-dots">
                                    <div></div><div></div><div></div>
                                </div>
                                <div class="menu">
                                    <div class="menu-body">
                                        <?php if(is_user_logged_in()) { ?>
                                            <div class="item"><?php the_my_list_button($post->ID); ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                    <?php numeric_posts_nav(); ?>
                    <?php wp_reset_query(); ?>
                </div>
            <?php }
        }
        ?>

    </main>
<?php get_footer(); ?>