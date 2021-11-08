<?php get_header(); ?>
<?php $custom_fields = get_fields($post->ID);
$kaltura_id = get_field('kaltura_id', $post->ID);

$tg = array(
    'posts_per_page'   => -1,
    'post_type'        => 'teaching-guide',
    'meta_key'         => 'main_movie',
    'meta_value'       => $kaltura_id,
    'orderby'   => 'ID',
    'order' => 'DESC',
);
$teaching_guides = get_posts( $tg );
$guideLink = get_permalink($teaching_guides[0]->ID);
?>

<style>
    .icon-three-dots > div {
        background: #f98c40;
    }
</style>

<main class="main single-movie-page" id="title_id" movie_id="<?php echo $post->ID; ?>" data-movie-id="<?php echo $post->ID; ?>">
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
                    <!-- create-session-btn --> <!-- create-session-btn-schedule -->
                    <div class="start-watch start-movie-preview" data-movie-id="<?php echo $post->ID; ?>" data-mode="advanced">
                        <?php icon('play-rounded'); ?>
                        <span><?php _e('Present Now', 'academe-theme'); ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="poster-label <?php echo $post->post_type; ?>">
                <?php icon('movie', 'icon-24'); ?>
                <span class="title"><?php _e('Movie', 'academe-theme'); ?></span>
            </div>
            <div class="movie-poster-actions">
            <a class="create-session-btn-schedule" data-modal-id="<?php echo $cs_modal_id; ?>"><?php icon('share', 'icon icon-white icon-24'); ?></a>
                <?php the_my_list_button($post->ID, 'icon'); ?>
                <a href="/lesson-editor?movie_id=<?php echo $post->ID; ?>"><?php icon('heavy-plus', 'icon icon-white'); ?></a>
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
            <?php if ($post->post_content) { ?>
                <div class="info-subtitle"><?php _e('Movie Overview', 'academe-theme'); ?></div>
                <?php $content = get_separated_read_more_content($post->post_content); ?>
                <div class="overview"><?php echo $content[0] . '<span class="readmore-hidden">' . $content[1] . '</span>'; ?></div>
                <?php if($content[1]) { ?>
                    <div class="readmore"><?php _e('Read more...', 'academe-theme'); ?></div>
                <?php } ?>

                <a href="#"><h1 style="margin: 20px 0 15px 0;">Watch trailer</h1></a>

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


            <?php if ( !empty($teaching_guides) ) { ?>
                    <div style="position: absolute; text-align: center; max-width: 120px; right: 90px; margin-top: 20px; top: 200px;">
                        <a href="<?php echo $guideLink; ?>" class="create-lesson-btn" style="background-color: #D04A07; border: 0;">
                            <p style="position: relative;">Teaching Guide</p> 
                            <svg style="right: 0px;" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 21.5641C12.7614 21.5641 15 19.4116 15 16.7564C15 14.1012 12.7614 11.9487 10 11.9487C7.23858 11.9487 5 14.1012 5 16.7564C5 19.4116 7.23858 21.5641 10 21.5641Z" fill="white"/>
                                <path d="M36.6673 3.93604H13.334V7.14116H36.6673V3.93604Z" fill="white"/>
                                <path d="M36.6667 10.3462H20V13.5513H36.6667V10.3462Z" fill="white"/>
                                <path d="M36.668 16.7563H26.668V19.9615H36.668V16.7563Z" fill="white"/>
                                <path d="M16.6673 30.154L24.6173 20.9873L22.0507 18.936L15.684 26.2758C14.9271 25.0887 13.7888 24.1709 12.4412 23.6611C11.0936 23.1513 9.61003 23.0773 8.21482 23.4502C6.81961 23.8232 5.58863 24.6228 4.70803 25.7282C3.82743 26.8336 3.3451 28.1847 3.33398 29.5771V35.9873H16.6673V30.154Z" fill="white"/>
                            </svg>
                        </a>
                    </div>
                <?php } ?>



            </div>
        </div> <!-- .movie-info end -->
        <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
            <div class="movie-actions" style=" top: 150px; position: absolute; right: 50px;">
                <!--<div class="create-lesson-btn-wrap">-->
                <div style="position: relative; text-align: center; max-width: 120px; right: 40px; margin-top: -10px; background-color: #F98C40; border-radius: 3px;">
                            <a href="/lesson-editor?movie_id=<?php echo $post->ID; ?>" class="create-lesson-btn" style="background-color: #F98C40; border: 0; border-radius: 3px;">
                                    <p style="position: relative;">Create a lesson</p> 
                                    <svg style="right: 0;" width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <rect width="31" height="31" fill="url(#pattern0)"/>
                                        <defs>
                                        <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_1397:7788" transform="scale(0.00195312)"/>
                                        </pattern>
                                        <image id="image0_1397:7788" width="512" height="512" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAgAAAAIACAYAAAD0eNT6AAAACXBIWXMAAAsTAAALEwEAmpwYAAAFGmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNi4wLWMwMDYgNzkuMTY0NjQ4LCAyMDIxLzAxLzEyLTE1OjUyOjI5ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjIuMiAoTWFjaW50b3NoKSIgeG1wOkNyZWF0ZURhdGU9IjIwMjEtMDktMTRUMTQ6Mjc6MjgrMDM6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDIxLTA5LTE0VDE0OjM0OjQ5KzAzOjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDIxLTA5LTE0VDE0OjM0OjQ5KzAzOjAwIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMyIgcGhvdG9zaG9wOklDQ1Byb2ZpbGU9InNSR0IgSUVDNjE5NjYtMi4xIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjgzYWEwZDdlLTBjMTAt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"/>
                                        </defs>
                                    </svg>
                            </a>
                        </div>
                    <div class="actions-more ui dropdown link item dark" style=" position: absolute; right: -40px; margin-top: -10px;">
                        <div class="icon-three-dots">
                            <div></div><div></div><div></div>
                        </div>
                        <div class="menu">
                            <div class="menu-body">
                                <?php if(is_user_logged_in()) { ?>
                                    <div class="item"><?php the_my_list_button($custom_fields['movie_id']->ID); ?></div>
                                <?php } ?>
            <div class="item start-movie-preview" data-movie-id="<?php echo $custom_fields['movie_id']->ID; ?>" data-mode="advanced"><?php _e( 'Preview', 'academe-theme' );?></div>
                                <div class="item goto-related-lessons"><?php _e( 'View related Lessons!', 'academe-theme' );?></div>
                                <div class="item start-movie-trailer" data-mode="basic"><?php _e( 'View trailer', 'academe-theme' );?></div>
                            </div>
                        </div>
                    </div>
                <!--</div>-->
            </div>
        <?php } ?>
    </section>

    <?php get_template_part( 'templates/partials/modals/create-session', 'null', ['id' => $cs_modal_id]); ?>


    <?php this_clips_list($post->ID); ?>
    <?php my_movie_courses_list($post->ID, 'grade'); ?> 
    <?php this_courses_list($post->ID); ?>


    <?php 
    // More <genre> Movies:
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