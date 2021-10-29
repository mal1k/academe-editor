<?php wp_footer() ?>
<script src="<?php echo get_template_directory_uri() . '/build/scripts.js'; ?>"></script>

<?php $partner_id = get_field('partner_id', 'option');
      $kaltura_player_id = get_field('kaltura_player_id', 'option');
if (empty($partner_id)) {
    $partner_id = '2538842';
} ?>
<script src="https://cdnapisec.kaltura.com/p/<?php echo $partner_id; ?>/sp/253884200/embedIframeJs/uiconf_id/<?php echo $kaltura_player_id; ?>/partner_id/<?php echo $partner_id; ?>"></script>

<?php if (is_page_template('page-lesson-editor.php') || is_singular( 'session' )) { ?>
    <script src="<?php echo get_template_directory_uri() . '/build/app.js'; ?>"></script>
<?php } else { ?>

    <?php if (is_user_logged_in() && !is_singular('teaching-guide')) { ?>
        <div class="modal ui overlay movie-player sized">
            <?php icon('cross', 'close'); ?>
            <div id="kalturaPlayer">

            </div>
        </div>
    <?php } ?>
<?php } ?>


</body>
</html>