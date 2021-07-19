<?php get_header(); ?>
<main class="main my-lessons-page">
    <?php
    // Teacher
    if (!is_user_in_role('student')) {
        get_template_part('templates/partials/my-lessons/teacher/content', 'null', []);
    }
    // Student
    if (is_user_in_role('student')) {
        get_template_part('templates/partials/my-lessons/student/content', 'null', []);
    }
    ?>
</main>
<?php get_footer(); ?>