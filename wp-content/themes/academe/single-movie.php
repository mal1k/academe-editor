<?php get_header(); ?>
<?php $custom_fields = get_fields($post->ID);?>
<?php 
$kaltura_id = get_field('kaltura_id', $post->ID);

$cc_args = array(
    'posts_per_page'   => -1,
    'post_type'        => 'teaching-guide',
    'meta_key'         => 'main_movie',
    'meta_value'       => $kaltura_id,
    'orderby'   => 'ID',
    'order' => 'DESC',
);
$teaching_guides = get_posts( $cc_args );
$guideLink = get_permalink($teaching_guides[0]->ID);
?>
<main class="main single-movie-page" data-movie-id="<?php echo $post->ID; ?>">
    <section id="movieInfo">
        <div class="movie-poster">
            <?php if(is_user_logged_in()) { ?>
                <?php if (is_user_in_role('student')) { ?>
                    <div class="start-watch start-movie-preview" data-movie-id="<?php echo $post->ID; ?>" data-mode="advanced">
                        <?php icon('play-rounded'); ?>
                        <span><?php _e('Play Now', 'academe-theme'); ?></span>
                    </div>
                <?php } else { ?>
                    <?php $cs_modal_id = uniqid(); ?>
                    <!-- create-session-btn -->
                    <div class="start-watch start-movie-preview" data-movie-id="<?php echo $post->ID; ?>" data-mode="advanced">
                        <?php icon('play-rounded'); ?>
                        <span><?php _e('Present Now', 'academe-theme'); ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="poster-label">
                <img src="<?php echo get_template_directory_uri() . '/assets/img/movie.svg'; ?>"/>
                <span class="title"><?php _e('Movie', 'academe-theme'); ?></span>
            </div>
            <img class="poster-image" src="<?php echo get_movie_thumbnail($custom_fields['kaltura_id']); ?>" />
        </div>
        <div class="movie-info">
            <div class="movie-top">
                <div class="title-wrap">
                    <h1 class="title"><?php the_title(); ?></h1>
                    <?php if (1) { ?>
                        <div class="plus-18">+18</div>
                    <?php } ?>
                </div>
                <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
                    <div class="movie-actions">
                        <a href="/lesson-editor?movie_id=<?php echo $post->ID; ?>" class="create-lesson-btn">
                            <?php icon('blue-plus', 'icon-white'); ?>
                            <span><?php _e('Create a Lesson', 'academe-theme'); ?></span>
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
                                    <div class="item start-movie-preview" data-movie-id="<?php echo $post->ID; ?>" data-mode="advanced"><?php _e( 'Preview', 'academe-theme' );?></div>
                                    <div class="item goto-related-lessons"><?php _e( 'View related Lessons!', 'academe-theme' );?></div>
                                    <div class="item start-movie-trailer" data-mode="basic"><?php _e( 'View trailer', 'academe-theme' );?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>

            <div class="meta">
                <div class="year"><?php echo isset($custom_fields['year']) ? $custom_fields['year'] : ''; ?></div>
                <div class="duration">
                    <?php icon('duration', 'icon-light-gray');
                    if (isset($custom_fields['duration'])) {
                        $duration = explode(":", $custom_fields['duration']);
                        echo (int)$duration[0].'h '. (int)$duration[1].'m';
                    } ?>
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
            <?php if(isset($custom_fields['director'])) { ?>
                <div class="info-subtitle"><?php _e('Directed by', 'academe-theme'); ?></div>
            <div class="directed"><?php echo $custom_fields['director']; ?></div>
            <?php } ?>
            <div class="info-subtitle"><?php _e('Movie Overview', 'academe-theme'); ?></div>
            <?php $content = get_separated_read_more_content($post->post_content); ?>
            <div class="overview"><?php echo $content[0] . '<span class="readmore-hidden">' . $content[1] . '</span>'; ?></div>
            <?php if($content[1]) { ?>
                <div class="readmore"><?php _e('Read more...', 'academe-theme'); ?></div>
            <?php } ?>

            <a href="#"><h1 style="margin: 20px 0 15px 0;">
                Watch trailer
            </h1></a>

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

          <?php if ( !empty($teaching_guides) ) : ?>
            <div style="position: absolute; text-align: center; max-width: 120px; right: 40px; margin-top: -50px;">
                <a href="<?php echo $guideLink; ?>" class="create-lesson-btn" style="background-color: orange; border: 0;">
                    Teaching Guide 
                </a>
            </div>
            <?php endif; ?>

            </div>
        </div>
    </section>

    <?php get_template_part( 'templates/partials/modals/create-session', 'null', ['id' => $cs_modal_id]); ?>

    <?php my_movie_courses_list($post->ID, 'grade'); ?> 
    
    <?php this_courses_list($post->ID); ?>

    <?php this_clips_list($post->ID); ?>

    <?php $post_terms = get_the_terms( $post->ID, 'genre' );
    $post_term = $post_terms ? $post_terms[0]->term_id : NULL;
    $post_term_name = $post_terms ? $post_terms[0]->name : '';

    $posts = get_filtered_posts('movie', 'genre', $post_term); 

    ?>
    <?php get_template_part( 'templates/partials/slider-strip', 'null', [
        'title' => "More $post_term_name movies",
        'filter' => [
            'active' => true,
            'post_type' => 'movie',
            'taxonomy' => 'genre',
            'term' => NULL,
            'action' => 'async_filter_all_movies_related'
        ],
        'posts' => $posts
    ]); ?>

    <?php get_template_part( 'templates/partials/slider-clips', 'null', [
        'clips' => $custom_fields['related_clips'],
        'title' => 'Related clips',
    ]); ?>

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