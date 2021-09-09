<?php
    $filter = false;
    if(isset($args['filter']) && $args['filter']['active']) {
        $tax = get_taxonomy($args['filter']['taxonomy']);
        $terms = get_terms( $args['filter']['taxonomy'], [
            'hide_empty' => true,
        ] );
        $filter = true;
        $filter_action = $args['filter']['action'];
    }
?>
<section class="slider-strip">
    <div class="strip-top">
        <h2 class="strip-heading"><?php _e($args['title'], 'academe-theme'); ?></h2>
        <?php if (isset($args['show_all_link'])) { ?>
            <a class="show-all" href="<?php echo $args['show_all_link']; ?>">
                <span class="text"><?php _e('Show all', 'academe-theme'); ?></span>
                <i class="show-all-icon"><?php icon('chevron-bold', 'icon-rotated-270'); ?></i>
            </a>
        <?php } ?>
        
    </div>
    <div class="swiper-container swiper-strip">
        <div class="swiper-wrapper">
            <?php
            if ($args['posts']) {
                foreach ($args['posts'] as $post) {
                    setup_postdata($post);
                    get_template_part('templates/partials/movie-block', 'null');
                }
                wp_reset_postdata();
            } else { ?>
                <div class="no-posts-found">
                    <?php _e("No items found. Please try to use other filter parameters.", "academe-theme"); ?>
                </div>
            <?php } ?>
        </div>
        <!-- Add Arrows -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>