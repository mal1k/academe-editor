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
    $new_lesson_button = isset($args['new_lesson_button']);

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
        <?php if($filter) { ?>
            <div class="dropdown ui search strip-filter dark" data-action="<?php echo $filter_action; ?>" data-post-type="<?php echo $args['filter']['post_type']; ?>" data-taxonomy="<?php echo $args['filter']['taxonomy']; ?>">
                <input name="genre" type="hidden" value="<?php echo ($args['filter']['term']) ?? ''; ?>" >
                <span class="default text"><?php echo isset($tax) ? $tax->labels->name : ''; ?></span>
                <?php icon('chevron-bold', 'with-rotate'); ?>
                <div class="menu">
                    <div class="menu-body">
                        <div class="item" data-value=""><?php echo isset($tax) ? $tax->labels->name : ''; ?></div>
                        <?php foreach ($terms as $term) { ?>
                            <div class="item" data-value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="swiper-container swiper-strip">
        <div class="swiper-wrapper">
            <?php if ($new_lesson_button) { ?>
                <a href="/lesson-editor" class="swiper-slide movie-block create-new-lesson">
                    <span class="plus-sign icon-wrap">+</span>
                    <span class="action-text"><?php _e('Create new', 'academe-theme'); ?></span>
                </a>
            <?php } ?>
            <?php if ($args['posts']) {
                foreach ($args['posts'] as $post) {
                    $user = get_userdata( $post->post_author );
                    if ( in_array( 'jif', $user->roles ) ) {
                        $post = get_post($post);
                        setup_postdata($post);
                        get_template_part('templates/partials/movie-block', 'null', ['jif'=>true]);
                    }
                }
                foreach ($args['posts'] as $post) {
                    $user = get_userdata( $post->post_author );
                    if ( !in_array( 'jif', $user->roles ) ) {
                        $post = get_post($post);
                        setup_postdata($post);
                        get_template_part('templates/partials/movie-block', 'null', ['jif'=>false]);
                    }
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