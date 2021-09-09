<div class="lesson-row">
    <?php $custom_fields = get_fields($post->ID); ?>
    <?php if ($custom_fields['based_on'] == 'movie') {
        $related_custom_fields = get_fields($custom_fields['related_movie']->ID);
    } else if ($custom_fields['based_on'] == 'lesson') {
        $related_custom_fields = get_fields($custom_fields['related_lesson']->ID);
    } ?>

    <div class="thumbnail">
        <?php if (0) { // no need, because we want to select image inside lesson editor and then use it here ?>
            <?php if ($custom_fields['based_on'] == 'movie') { ?>
                <img src="<?php echo get_movie_thumbnail($related_custom_fields['kaltura_id'], 200, 120, 45 ); ?>" />
            <?php } else if ($custom_fields['based_on'] == 'lesson') { ?>
                <img src="<?php echo $custom_fields['cover_image_url']; ?>" />
            <?php } ?>
        <?php } ?>
        <img src="<?php echo $custom_fields['cover_image_url']; ?>" />
    </div>
    <div class="session-info">
        <div class="name">
            <?php the_title(); ?>
        </div>
        <div class="teacher">
            <?php _e('Teacher: ', 'academe-theme'); ?> <span><?php the_author(); ?></span>
        </div>
        <div class="tags">
            <span class="tags-list">
            <?php $tags = wp_get_post_tags($custom_fields['related_movie']->ID);
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
    <div class="progress">
        <?php icon('list', 'icon-24'); ?>
        0/10
    </div>
    <div class="grade">
        <?php icon('achievement', 'icon-24'); ?>
        -
    </div>
    <div class="status">
        <?php
            $now = new DateTime;
            $tz = new DateTimeZone(get_option('timezone_string'));
            $now->setTimezone($tz);
            $session_starts = DateTime::createFromFormat('Y-m-d H:i:s', $custom_fields['session_starts']);
            $session_ends = DateTime::createFromFormat('Y-m-d H:i:s', $custom_fields['session_ends']);

            if ($session_starts <= $now && $session_ends >= $now && $custom_fields['session_type'] === 'sync') { // Live
                echo '<div class="status-title blue">' . __('LIVE !', 'academe-theme') . '</div>';
            }
            if ($session_starts > $now) { // Upcoming
                echo '<div class="status-title green">' . __('Upcoming', 'academe-theme') . '</div>';
                echo '<div class="expiration">' . __('Opens in', 'academe-theme') . ' ' . $session_starts->diff($now)->format("%dd %hh") . '</div>';
            }
            if ($session_starts <= $now && $session_ends >= $now && $custom_fields['session_type'] === 'async') { // Active
                echo '<div class="status-title white">' . __('Active', 'academe-theme') . '</div>';
                echo '<div class="expiration">' . __('Expires in', 'academe-theme') . ' ' . $session_ends->diff($now)->format("%dd %hh") . '</div>';
            }
            if ($now > $session_ends) { // Expired
                echo '<div class="status-title white">' . __('Expired', 'academe-theme') . '</div>';
                echo '<div class="expiration bold">' . $session_ends->format('l d/m/Y H:i') . '</div>';
            }
        ?>
    </div>
    <div class="movie-actions">
        <?php if ($session_starts <= $now && $session_ends >= $now && $custom_fields['session_type'] === 'sync') { // Live?>
            <a href="#" class="play-movie-btn">
                <span><?php _e('Join', 'academe-theme'); ?></span>
            </a>
        <?php } ?>
        <?php if ($session_starts > $now) { // Upcoming ?>
            <div class="empty-action-filler"></div>
        <?php } ?>
        <?php if ($session_starts <= $now && $session_ends >= $now && $custom_fields['session_type'] === 'async') { // Active ?>
            <a href="#" class="play-movie-btn">
                <span><?php _e('Continue', 'academe-theme'); ?></span>
            </a>
        <?php } ?>
        <?php if ($now > $session_ends) { // Expired ?>
            <div class="empty-action-filler"></div>
        <?php } ?>

        <div class="actions-more ui dropdown link item dark">
            <div class="icon-three-dots">
                <div></div><div></div><div></div>
            </div>
            <div class="menu">
                <div class="menu-body">
                    <div class="item">Nothing here yet</div>
                </div>
            </div>
        </div>
    </div>
</div>