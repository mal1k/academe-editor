<?php
/* Favorites list start */
function my_list($filter = true) {
    $user_id = get_current_user_id();
    $my_list = get_my_list($user_id, 'ids');
    if ($my_list) {
        $query = new WP_Query(['post_type' => 'movie', 'post__in' => $my_list, 'orderby' => 'post__in', 'suppress_filters' => true]);
        $query_posts = $query->posts;
    } else {
        $query_posts = [];
    }
    if($query_posts) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => 'My List',
            'filter' => [
                'active' => $filter,
                'post_type' => 'movie',
                'taxonomy' => 'genre',
                'term' => NULL,
                'action' => 'async_filter_my_list',
            ],
            'posts' => $query_posts
        ]);
    }
}
/* Favorites end */

/* Continue watching list start */
function continue_watching_list($filter = true) {
    $user_id = get_current_user_id();
    $cw_list = get_continue_watching_list($user_id, 'ids');
    if ($cw_list) {
        $query = new WP_Query(['post_type' => 'movie', 'post__in' => $cw_list, 'orderby' => 'post__in', 'suppress_filters' => true]);
        $query_posts = $query->posts;
    } else {
        $query_posts = [];
    }
    if($query_posts) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => 'Continue watching',
            'filter' => [
                'active' => $filter,
                'post_type' => 'movie',
                'taxonomy' => 'genre',
                'term' => NULL,
                'action' => 'async_filter_continue_watching',
            ],
            'posts' => $query_posts
        ]);
    }
}
/* Continue watching list end */

/* Continue editing lesson list start */
function continue_editing_lesson_list() {
    $lessons = ld_lesson_list(['post_status' => ['draft'], 'author__in' => [get_current_user_id()], 'array' => true]); ?>
   <!-- <section class="slider-strip">
        <div class="strip-top">
            <h2 class="strip-heading"><?php //_e('Continue editing', 'academe-theme'); ?></h2>
        </div>
        <div class="swiper-container swiper-strip">
            <div class="swiper-wrapper">
                <?php
               /* if ($lessons) {
                    global $post;
                    foreach ($lessons as $post) {
                        setup_postdata($post);
                        get_template_part('templates/partials/movie-block', 'null');
                    }
                    wp_reset_postdata(); ?>
                <?php }*/ ?>
                <a href="/wp-admin/post-new.php?post_type=sfwd-lessons" class="swiper-slide movie-block create-new-lesson">
                    <div class="slide-info">
                        <div class="icon-wrap"><?php //icon('blue-plus', 'icon-white'); ?></div>
                        <span class="action-text"><?php //_e('Create new', 'academe-theme'); ?></span>
                    </div>
                </a>
            </div>
            Add Arrows -->
           <!-- <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>-->
<?php
}
/* Continue editing lesson list end */

/* Continue editing course list start */
function continue_editing_course_list() {
    $courses = ld_course_list(['post_status' => ['draft'], 'author__in' => [get_current_user_id()], 'array' => true]); ?>
    <!--<section class="slider-strip">
        <div class="strip-top">
            <h2 class="strip-heading"><?php //_e('Continue editing2', 'academe-theme'); ?></h2>
        </div>
        <div class="swiper-container swiper-strip">
            <div class="swiper-wrapper">
                <?php
                /*if ($courses) {
                    global $post;
                    foreach ($courses as $post) {
                        setup_postdata($post);
                        get_template_part('templates/partials/movie-block', 'null');
                    }
                    wp_reset_postdata(); ?>
                <?php }*/ ?>
                <a href="/wp-admin/post-new.php?post_type=sfwd-courses" class="swiper-slide movie-block create-new-lesson">
                    <div class="slide-info">
                        <div class="icon-wrap"><?php //icon('blue-plus', 'icon-white'); ?></div>
                        <span class="action-text"><?php //_e('Create new', 'academe-theme'); ?></span>
                    </div>
                </a>
            </div>
            Add Arrows -->
            <!--<div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>-->
    <?php
}
/* Continue editing lesson list end */

/* My lessons list start */
function my_lessons_list() {
    $lessons = new WP_Query(['post_type' => 'sfwd-lessons','post_status' => 'publish', 'author__in' => [get_current_user_id()],'posts_per_page' => 20]);
    if($lessons->posts) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => 'My lessons',
            'filter' => [
                'active' => true,
                'post_type' => 'sfwd-lessons',
                'taxonomy' => 'subject',
                'term' => NULL,
                'action' => 'async_filter_my_lessons',
            ],
            'posts' => $lessons->posts
        ]);
    }
}
/* My lessons list end */

/* Top lessons list start */
function top_lessons_list($filter = true, $title = 'Top lessons') {
    $slides = get_field('top_lessons', 'option');
    if ($slides) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => $title,
            'filter' => [
                'active' => $filter,
            ],
            'posts' => $slides
        ]);
    }
}
/* Top lessons list end */

/* My courses list start */
function my_courses_list() {
    $lessons = new WP_Query(['post_type' => 'sfwd-courses','post_status' => 'publish', 'author__in' => [get_current_user_id()],'posts_per_page' => 20]);
    if($lessons->posts) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => 'My lessons',
            'filter' => [
                'active' => true,
                'post_type' => 'sfwd-courses',
                'taxonomy' => 'subject',
                'term' => NULL,
                'action' => 'async_filter_my_courses',
            ],
            'posts' => $lessons->posts
        ]);
    }
}
/* My courses list end */

/* Teacher's courses list start */
function teachers_courses_list($teacher_id) {
    global $post;
    $lessons = new WP_Query(['post_type' => 'sfwd-courses','post_status' => 'publish', 'author' => $teacher_id,'posts_per_page' => 20]);
    if($lessons->posts) { ?>
        <section class="slider-strip">
            <div class="swiper-container swiper-strip">
                <div class="swiper-wrapper">
                    <?php foreach ($lessons->posts as $post) {
                        setup_postdata($post);
                        get_template_part('templates/partials/movie-block', 'null');
                    }
                    wp_reset_postdata(); ?>
                </div>
                <!-- Add Arrows -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </section>
    <?php }
}
/* Teacher's courses list end */

/* Top lessons list start */
function top_courses_list($filter = true, $title = 'Top lessons') {
    $slides = get_field('top_courses', 'option');
    if ($slides) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => $title,
            'filter' => [
                'active' => $filter,
            ],
            'posts' => $slides
        ]);
    }
}
/* Top lessons list end */

/* Recommended movies list start */
function recommended_movies_list($filter = true, $title = 'Recommended movies') {
    $slides = get_field('recommended_movies', 'option');
    if ($slides) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => $title,
            'filter' => [
                'active' => $filter,
                'post_type' => 'movie',
                'taxonomy' => 'genre',
                'term' => NULL,
                'action' => 'async_filter_recommended_movies',
            ],
            'posts' => $slides
        ]);
    }
}
/* Recommended movies list end */

/* Popular movies list start */
function popular_movies_list() {
    $query = new WP_Query(['post_type' => 'movie', 'orderby' => 'post_views', 'order' => 'desc']);
    $query_posts = $query->posts;
    if ($query_posts) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => 'Popular movies',
            'filter' => [
                'active' => true,
                'post_type' => 'movie',
                'taxonomy' => 'genre',
                'term' => NULL,
                'action' => 'async_filter_popular_movies',
            ],
            'posts' => $query_posts
        ]);
    }
}
/* Popular movies list end */

/* Recent teacher guides list start */
function recent_teacher_guides_list($filter = true, $title = 'New releases') {
    $teacher_guides = get_filtered_posts('teaching-guide');
    if($teacher_guides) {
        get_template_part('templates/partials/slider-strip', 'null', [
            'title' => $title,
            'filter' => [
                'active' => $filter,
                'post_type' => 'teaching-guide',
                'taxonomy' => 'topic',
                'term' => NULL,
                'action' => 'async_filter_all_posts',
            ],
            'posts' => $teacher_guides
        ]);
    }
}
/* Recent teacher guides list end */