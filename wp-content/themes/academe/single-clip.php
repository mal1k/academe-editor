<?php get_header(); ?>
<?php $custom_fields = get_fields($post->ID);
$movie_fields = get_fields($custom_fields['movie_id']->ID);
$kaltura_id = get_field('kaltura_id', $custom_fields['movie_id']->ID);

$teaching_args = array(
    'posts_per_page'   => -1,
    'post_type'        => 'teaching-guide',
    'meta_key'         => 'main_movie',
    'meta_value'       => $kaltura_id,
    'orderby'   => 'ID',
    'order' => 'DESC',
);
$teaching_guides = get_posts( $teaching_args );
$guideLink = get_permalink($teaching_guides[0]->ID);
$movie_fields = get_fields($custom_fields['movie_id']->ID);

?>

<main class="main single-movie-page" id="title_id" movie_id="<?php echo $custom_fields['movie_id']->ID; ?>" data-movie-id="<?php echo $custom_fields['movie_id']->ID; ?>">
    <section id="movieInfo">
        <div class="movie-poster">
            <?php if(is_user_logged_in()) { ?>
                <?php if (is_user_in_role('student')) { ?>
                    <div class="start-watch start-movie-preview"
                         data-kaltura-id="<?php echo $kaltura_id; ?>"
                         data-mode="basic"
                         data-play-from="<?php echo format_time_to_seconds($custom_fields['play_from']); ?>"
                         data-play-to="<?php echo format_time_to_seconds($custom_fields['play_to']); ?>">
                        <?php icon('play-rounded'); ?>
                        <span><?php _e('Play Now', 'academe-theme'); ?></span>
                    </div>
                <?php } else { ?>
                    <?php $cs_modal_id = uniqid(); ?>
                    <!-- create-session-btn -->
                    <div class="start-watch start-movie-preview" data-movie-id="<?php echo $custom_fields['movie_id']->ID;?>" data-modal-id="<?php echo $cs_modal_id; ?>">
                        <?php icon('play-rounded'); ?>
                        <span><?php _e('Present Now', 'academe-theme'); ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="poster-label <?php echo $post->post_type; ?>">
                <?php icon('clip', 'icon-24'); ?>
                <span class="title"><?php _e('Clip', 'academe-theme'); ?></span>
            </div>
            <div class="movie-poster-actions">
                <?php the_my_list_button($post->ID, 'icon'); ?>
                <a href="/lesson-editor?clip_id=<?php echo $post->ID; ?>"><?php icon('heavy-plus', 'icon icon-white'); ?></a>
            </div>
            <img class="poster-image" src="<?php echo get_movie_thumbnail($kaltura_id); ?>" />
        </div>
        <div class="movie-info">
            <div class="movie-top">
                <div class="title-wrap">
                    <h1 class="title"><?php the_title(); ?></h1>
                </div>
            </div>

            <div class="meta">
                <div class="duration">
                    <?php icon('duration', 'icon-light-gray');
                    echo $movie_fields['time']; ?>
                </div>
                <div class="lessons"><?php icon('white-board', 'icon-light-gray'); ?>8</div>
                <div class="views"><?php
                    icon('views', 'icon-light-gray');
                    if(PVC_ACTIVE) echo pvc_get_post_views(); ?>
                </div>
            </div>
            <div class="genres">
            <span class="genres-list">
                <?php $genres = wp_get_post_terms($post->ID, 'genre', ['fields' => 'names']);
                if ($genres) {
                    foreach ($genres as $genre) {
                        echo "<span class=\"genre\">$genre</span>";
                    }
                } ?>
            </span>
            </div>
            <div class="info-subtitle">
                <?php _e('Created By:', 'academe-theme');
                $author_name = get_the_author_meta('display_name', $post->post_author);
                echo " " . $author_name; ?>
            </div>
            <div class="directed"></div>
            <?php if ($post->post_content) { ?>
                <div class="info-subtitle"><?php _e('Clip Description', 'academe-theme'); ?></div>
                <?php $content = get_separated_read_more_content($post->post_content); ?>
                <div class="overview"><?php echo $content[0] . '<span class="readmore-hidden">' . $content[1] . '</span>'; ?></div>
                <?php if($content[1]) { ?>
                    <div class="readmore"><?php _e('Read more...', 'academe-theme'); ?></div>
                <?php } ?>
            <?php } ?>
            <div class="tags">
            <span class="tags-list">
                <?php $tags = wp_get_post_tags($post->ID);
                if ($tags) {
                    $tags_counter = 0;
                    foreach ($tags as $tag) { ?>
                        <a class="tag <?php echo ($tags_counter > 3) ? 'tag-hidden' : ''; ?>" href="<?php echo get_tag_link($tag->term_id); ?>">#<?php echo $tag->name; ?></a>
                        <?php $tags_counter++;
                    }
                } ?>
            </span>
            <?php if(count($tags) > 4) { ?>
                <span class="tags-more clickable"><?php _e('more', 'academe-theme'); ?></span>
            <?php } ?>
            </div>
        </div> <!-- .movie-info end -->
        <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
            <div class="movie-actions">
                <div class="create-lesson-btn-wrap">
                    <a href="/lesson-editor?clip_id=<?php echo $post->ID; ?>" class="create-lesson-btn">
                        <?php icon('blue-plus', 'icon-white'); ?>
                        <span><?php _e('Create a<br> Lesson', 'academe-theme'); ?></span>
                        <?php icon('sfwd-lessons', 'icon-white'); ?>
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
                                <div class="item start-movie-preview"
                                     data-kaltura-id="<?php echo $kaltura_id; ?>"
                                     data-mode="basic"
                                     data-play-from="<?php echo format_time_to_seconds($custom_fields['play_from']); ?>"
                                     data-play-to="<?php echo format_time_to_seconds($custom_fields['play_to']); ?>">
                                    <?php _e( 'Preview', 'academe-theme' );?>
                                </div>
                                <div class="item goto-related-lessons"><?php _e( 'View related Lessons!', 'academe-theme' );?></div>
                                <div class="item start-movie-trailer" data-mode="basic"><?php _e( 'View trailer', 'academe-theme' );?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($teaching_guides->posts) { ?>
                    <a href="<?php echo get_post_permalink($teaching_guides->posts[0]); ?>" class="teaching-guide-btn">
                        <span><?php _e('Teaching<br> Guide', 'academe-theme'); ?></span>
                        <?php icon('teaching-guide', 'icon-white'); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </section>

    <?php get_template_part( 'templates/partials/modals/create-session', 'null', ['id' => $cs_modal_id]); ?>

    <?php my_movie_courses_list($custom_fields['movie_id']->ID, 'grade'); ?> 
    <?php this_courses_list($custom_fields['movie_id']->ID); ?>
    <?php this_clips_list($custom_fields['movie_id']->ID); ?>

    <?php 
    // Lessons:
    // $lessons = new WP_Query([
    //         'post_type' => 'sfwd-courses',
    //         'post_status' => ['publish', 'private'],
    //         'author__in' => [get_current_user_id()],
    //         'meta_key' => 'movie_id',
    //         'meta_value' => $custom_fields['movie_id']->ID,
    //         'posts_per_page' => 20]
    // );
    // get_template_part( 'templates/partials/slider-strip', 'null', [
    //     'title' => __('Lessons', 'academe-theme'),
    //     'filter' => [
    //         'active' => false,
    //     ],
    //     'posts' => $lessons->posts,
    // ]);
    // $post_terms = get_the_terms( $post->ID, 'genre' );
    // $post_term = $post_terms ? $post_terms[0]->term_id : NULL;
    // $post_term_name = $post_terms ? $post_terms[0]->name : '';
    // $posts = get_filtered_posts('movie', 'genre', $post_term);
    
    // get_template_part( 'templates/partials/slider-strip', 'null', [
    //     'title' => "More $post_term_name movies",
    //     'filter' => [
    //         'active' => true,
    //         'post_type' => 'movie',
    //         'taxonomy' => 'genre',
    //         'term' => NULL,
    //         'action' => 'async_filter_all_movies_related'
    //     ],
    //     'posts' => $posts
    // ]);
    
    // get_template_part( 'templates/partials/slider-clips', 'null', [
    //     'clips' => $custom_fields['related_clips'],
    //     'title' => 'Related clips',
    // ]); 
    
    ?>

</main>
<script>
    jQuery(document).ready(function($){
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('play')) {
            setTimeout(function () {
                $('.movie-poster .start-movie-preview').trigger('click');
            }, 1000);
            window.history.replaceState({}, document.title, window.location.href.split('?')[0]);
        }
    });
</script>
<?php get_footer(); ?>