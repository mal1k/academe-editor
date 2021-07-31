<?php if (!is_user_logged_in()) {
    wp_redirect(home_url());
} ?>

<?php get_header(); ?>

<script type='text/javascript' id='wpProQuiz_front_javascript-js-extra'>
    /* <![CDATA[ */
    var WpProQuizGlobal = {
        "ajaxurl":"<?php echo str_replace( array( 'http:', 'https:' ), array( '', '' ), admin_url( 'admin-ajax.php' ) ); ?>",
        "loadData":"Loading",
        "questionNotSolved":"You must answer this question.",
        "questionsNotSolved":"You must answer all questions before you can complete the quiz.",
        "fieldsNotFilled":"All fields have to be filled."
    };
    /* ]]> */
</script>
<script src="/wp-content/plugins/sfwd-lms/includes/lib/wp-pro-quiz/js/wpProQuiz_front.min.js"></script>
<script src="/wp-includes/js/jquery/ui/core.min.js?ver=1.12.1"></script>
<script src="/wp-includes/js/jquery/ui/mouse.min.js?ver=1.12.1"></script>
<script src="/wp-includes/js/jquery/ui/sortable.min.js?ver=1.12.1"></script>
<script src="/wp-content/plugins/sfwd-lms/includes/lib/wp-pro-quiz/js/jquery.ui.touch-punch.min.js?ver=0.2.2"></script>

<?php $custom_fields = get_fields($post->ID);
$show_session_content = true; ?>

<div id="app" class="main">

    <?php if (wp_is_mobile() && !is_user_in_role('student')) { ?>
        <div class="session-restrict-message">
            <span>
                <?php _e('As a teacher, you must enter the lesson from your desktop!', 'academe-theme');
                $show_session_content = false; ?>
            </span>
        </div>
    <?php } ?>

    <?php if (!wp_is_mobile() && is_user_in_role('student')) { ?>
        <div class="session-restrict-message">
            <span>
                <?php _e('As a student, you must enter the lesson from your mobile phone!', 'academe-theme');
                $show_session_content = false; ?>
            </span>
        </div>
    <?php } ?>

    <?php if ($show_session_content) { ?>
        <session-slideshow
            :course_id="<?php echo $custom_fields['related_lesson']->ID; ?>"
            :session_id="<?php global $post; echo $post->ID; ?>"
            session_code="<?php global $post; echo $post->post_name; ?>"
            user_role="<?php echo is_user_in_role('student') ? 'student' : 'teacher'; ?>"
            device="<?php echo wp_is_mobile() ? 'mobile' : 'desktop'; ?>"
            :current_slide="<?php echo $custom_fields['current_slide'] ?? 0; ?>"
            websocket_url="<?php echo WEBSOCKET_URL; ?>">

        </session-slideshow>
    <?php } ?>

</div>
<?php get_footer(); ?>