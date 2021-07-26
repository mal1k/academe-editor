<?php 
       ini_set("display_errors",1);
       error_reporting(E_ALL);
        //code goes here
?>
<?php wp_footer() ?>
<script src="<?php echo get_template_directory_uri() . '/build/scripts.js'; ?>"></script>

<?php if (is_page_template('page-lesson-editor.php')) { ?>
    <?php $partner_id = get_field('partner_id', 'option'); ?>
    <script src="https://cdnapisec.kaltura.com/p/<?php echo $partner_id; ?>/sp/253884200/embedIframeJs/uiconf_id/46602743/partner_id/<?php echo $partner_id; ?>"></script>
    <script src="<?php echo get_template_directory_uri() . '/build/app.js'; ?>"></script>
<?php } ?>

</body>
</html>