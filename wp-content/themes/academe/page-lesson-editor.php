<?php /* Template Name: Lesson Editor */ ?>

<?php if (!is_user_logged_in() || is_user_in_role('student')) {
    wp_redirect(home_url());
} ?>

<?php
if (!$_GET['lesson_id']) {
    require_once ABSPATH . 'wp-admin/includes/post.php';
    $lesson = get_default_post_to_edit( 'sfwd-courses', true );
    $lesson_id = $lesson->ID;
} else {
    $lesson_id = $_GET['lesson_id'];
}
$current_user = wp_get_current_user();
?>

<?php get_template_part( 'templates/partials/document-open' ); ?>
    <div id="app" class="main">
        <lesson-editor-layout>
            <template v-slot:header>
                <header-component></header-component>
            </template>
            <template v-slot:editor>
                <lesson-editor v-slot="editor"
                               :post="<?php echo $lesson_id; ?>"
                               :movie="<?php echo $_GET['movie_id'] ?? 'null'; ?>"
                               author="<?php echo $current_user->user_firstname . ' ' . $current_user->user_lastname; ?>">

                </lesson-editor>
            </template>

        </lesson-editor-layout>

    </div>
<?php get_template_part( 'templates/partials/document-close' ); ?>