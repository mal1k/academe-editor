<?php $custom_fields = get_fields($post->ID);  ?>

<div class="lesson-row" id="lesson_id_<?php echo $post->ID; ?>">
    <!--<a class="wrap-link" href="<?php //the_permalink(); ?>" target="_blank"></a>-->
    <div class="thumbnail">
        <img src="<?php echo $custom_fields['cover_image_url']; ?>" />
    </div>
    <div class="session-info">
        <div class="name">
            <?php the_title(); ?>
        </div>
        <div class="teacher">
            <?php _e('Created by: ', 'academe-theme'); ?> <span><?php the_author(); ?></span>
        </div>
        <div class="tags">
            <span class="tags-list">
            <?php $tags = wp_get_post_tags($post->ID);
            if ($tags) {
                $tags_counter = 2;
                $tags_string = '';
                foreach ($tags as $tag) {
                    if ($tags_counter) {
                        $tags_string .= '#' . $tag->name . ' ';
                        $tags_counter--;
                    } else {
                        break;
                    }
                }
                echo rtrim($tags_string);
            } ?>
            </span>
        </div>
    </div>
    <div class="stretcher"></div>
    <?php if (0) { ?>
        <div class="progress">
            <?php icon('clock', 'icon-24'); ?>
            1h 23m
        </div>
    <?php } ?>

    <?php if ($post->post_status == 'private') {
        icon('hide', 'visibility icon-32');
    } ?>

    <div class="sessions-count" data-lesson-id="<?php echo $post->ID; ?>">
        <?php
        $wp_query = new WP_Query([
            'post_type' => 'session',
            'meta_query' => [
                array(
                    'key' => 'related_lesson',
                    'value' => $post->ID,
                    'compare' => '=',
                )
            ]
        ]);
        printf( _nx( '%s session', '%s sessions', $wp_query->found_posts, 'session count', 'academe-theme' ), number_format_i18n( $wp_query->found_posts ) );
        ?>
    </div>

    <?php $cs_modal_id = uniqid(); ?>
    <div class="movie-actions">
        <div class="actions-more ui dropdown link item dark">
            <div class="icon-three-dots">
                <div></div><div></div><div></div>
            </div>
            <div class="menu">
                <div class="menu-body">
                    <div class="item create-session-btn" data-modal-id="<?php echo $cs_modal_id; ?>">
                        <?php icon('blue-plus'); ?>
                        <span class="text-blue"><?php _e('Play Now', 'academe-theme'); ?></span>
                    </div>  
                    <a href="#" class="item create-session-btn-schedule" data-modal-id="<?php echo $cs_modal_id; ?>"><?php _e( 'Schedule', 'academe-theme' );?></a>
                    <a href="/lesson-editor?lesson_id=<?php echo $post->ID; ?>" class="item"><?php _e( 'Edit Lesson', 'academe-theme' );?></a>
                    <a href="/sessions/<?php echo $wp_query->posts[0]->post_name; ?>" class="item"><?php _e( 'View Lesson', 'academe-theme' );?></a>

                    <a href="#" class="item unpublish-lesson-btn" data-lesson-id="<?php echo $post->ID; ?>"><?php _e( 'Unpublish', 'academe-theme' );?></a>
                    <a href="#" class="item text-red delete-session-btn" data-lesson-id="<?php echo $post->ID; ?>"><?php _e( 'Archive', 'academe-theme' );?></a>
                </div>
            </div>
        </div>
    </div>

    <script>

    function ltiCopy(id) {
        const form = jQuery('#'+id);
        const code = form.find('.lessonCode').text();
        const time = form.find('.schedule').val();
        const postID = form.find('.related_item').val();
        jQuery.ajax('<?php bloginfo('template_directory'); ?>/lti_create_code.php?code=' + code + '&id=' + postID + '&time=' + time,{
        type: 'POST',
        processData: false,
        contentType: false,
        dataType: 'json',
            success: (data)=>{
                var copyText = "<?php echo get_home_url(); ?>/lti-movies?code=" + code;

                navigator.clipboard.writeText(copyText).then(function() {
                    showToast('Copied!', 'The LTI link was successfully copied to your clipboard.');
                });

                function showToast(title, message) {
                    jQuery('body').toast({
                        title: title,
                        message: message,
                        displayTime: 3000,
                        position: 'bottom right',
                        class : 'dark',
                        className: {
                            toast: 'ui toast'
                        }
                    });
                }

            }

        })  
    }
    </script>

    <?php get_template_part( 'templates/partials/modals/create-session', 'null', ['id' => $cs_modal_id]); ?>

</div>