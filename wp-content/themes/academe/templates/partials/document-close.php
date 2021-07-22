<?php wp_footer() ?>
<script src="<?php echo get_template_directory_uri() . '/build/scripts.js'; ?>"></script>

<?php if (is_page_template('page-lesson-editor.php')) { ?>
    <script src="<?php echo get_template_directory_uri() . '/build/app.js'; ?>"></script>
<?php } ?>

</body>
</html>