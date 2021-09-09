<section id="movieFilters" class="filters-section">
    <h1><?php the_title(); ?></h1>
</section>
<?php continue_editing_course_list(); ?>
<section id="lessonsList" class="teacher">
    <?php
    global $wp_query;
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    $wp_query = new WP_Query([
        'post_type' => 'sfwd-courses',
        'post_status' => 'publish',
        'author__in' => [get_current_user_id()],
        'posts_per_page' => 10,
        'paged' => $paged
    ]);
    if($wp_query->posts) { ?>
        <div class="lesson-rows">
            <?php foreach ($wp_query->posts as $post) { ?>
                <?php setup_postdata($post);
                get_template_part( 'templates/partials/my-lessons/teacher/session-row-block', 'null', []); ?>
            <?php } ?>
            <?php numeric_posts_nav(); ?>
            <?php wp_reset_query(); ?>
        </div>
    <?php } ?>

    <div class="modal ui sessions-list">
        <?php icon('cross', 'close'); ?>
        <h3 class="sessions-title">"<span class="lesson-title"></span>" <?php _e('sessions list', 'academe-theme'); ?></h3>
        <div class="sessions-list-wrap"></div>
    </div>
</section>