<?php get_header(); ?>
<main class="main">
        <?php if (is_user_logged_in() && !is_user_in_role('student')) { ?>
            <section class="two-strips">
                <?php continue_watching_list(false);
                // continue_editing_course_list(); ?>
            </section>
        <?php } ?>

        <?php recommended_movies_list(); ?>

        <?php if (is_user_in_role('student')) {
            continue_watching_list(false);
        } ?>

        <?php popular_movies_list(); ?>

        <?php if (is_user_logged_in() && !is_user_in_role('student')) {
            recent_teacher_guides_list(true, __('Recent Teaching Guides', 'academe-theme'));
        } ?>

        <?php get_template_part( 'templates/partials/slider-clips', 'null', [
            'clips' => get_field('recommended_clips', 'option'),
            'title' => 'Recommended clips',
        ]); ?>

    </main>
<?php get_footer(); ?>