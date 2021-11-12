<?php get_header(); ?>
    <main class="main">
        <?php $slides = get_field('homepage_slides', 'option');
        if ($slides) {
            get_template_part( 'templates/partials/big-slider', 'null', [
                'slides' => $slides,
            ]);
        } ?>

        <?php if (is_user_logged_in() && !is_user_in_role('student')) { ?>
            <section class="two-strips">
                <?php continue_watching_list(false);
                // continue_editing_course_list(); ?>
            </section>
        <?php } ?>

        <?php 
            recommended_lessons_list(); 
            recommended_movies_list(); 
            recommended_clips_list(); 
            recommended_teaching_guides_list(); 
        
            continue_watching_list(false);

            popular_movies_list();
            recent_teacher_guides_list(true, __('Recent Teaching Guides', 'academe-theme'));
            recent_courses_list(true, __('Recent Courses', 'academe-theme'));
        ?>
    </main>

<?php get_footer(); ?>