<?php $custom_fields = get_fields($post->ID); ?>
<?php global $mediaEntry, $search_query; ?>
<?php $sponsored =  (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses']) && $custom_fields['sponsored']) ? true : false ?>

<?php if ($post->post_type != 'movie' || isset($custom_fields['kaltura_id'])) { ?>
    <div class="swiper-slide movie-block <?php echo $sponsored ? 'sponsored' : ''; ?>" data-movie-id="<?php echo $post->ID; ?>" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.95) 11.29%, rgba(0, 0, 0, 0) 100%) no-repeat center / cover;">
        <div class="slide-info">
            <div class="info-top">
                <div class="title"><?php echo $post->post_title; ?></div>
                <?php if( in_array($post->post_type, ['movie', 'teaching-guide']) ) { ?>
                    <div class="tags">
                <span class="tags-list">
                <?php $tags = wp_get_post_tags($post->ID);
                if ($tags) {
                    $tags_counter = 3;
                    $tags_string = '';
                    foreach ($tags as $tag) {
                        if ($tags_counter) {
                            $tags_string .= '#' . $tag->name . ' ';
                            $tags_counter--;
                        } else {
                            break;
                        }
                    }
                    echo rtrim($tags_string) . '...';
                } ?>
                </span>
                    </div>
                <?php } ?>
            </div>
            <div class="meta">
                <div class="left-part">
                    <?php if($post->post_type == 'movie') { ?>
                        <?php if(0) { ?>
                            <div>
                                <div class="plus-18">+18</div>
                            </div>
                        <?php } ?>
                        <div class="duration">
                            <?php icon('duration', 'icon-white');
                            if (isset($custom_fields['duration'])) {
                                $duration = explode(":", $custom_fields['duration']);
                                echo (int)$duration[0].'h '. (int)$duration[1].'m';
                            } ?>
                        </div>
                        <div class="lessons"><?php icon('white-board', 'icon-white'); ?><?php echo rand(2, 15); ?></div>
                    <?php } ?>
                    <?php if (in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses'])) { ?>
                        <div class="students"><?php
                            icon('student', 'icon-light-gray');
                            echo rand(5, 500); ?>
                        </div>
                    <?php } ?>
                    <div class="views">
                        <?php icon('views', 'icon-white');
                        if(PVC_ACTIVE) echo pvc_get_post_views(); ?>
                    </div>
                </div>

                <div class="right-part">
                    <?php icon('circle-down', 'icon-32 show-meta'); ?>
                </div>

            </div>
            <a href="<?php the_permalink(); ?>" class="watch">
                <div class="start-watch"><?php icon('play-rounded'); ?></div>
            </a>
        </div>
        <?php if (0) { ?>
        <div class="actions-more ui dropdown link item dark">
            <div class="slide-actions">
                <div></div> <div></div> <div></div>
            </div>
            <div class="menu">
                <div class="menu-body">
                    <?php if(is_user_logged_in()) { ?>
                        <div class="item"><?php the_my_list_button($post->ID); ?></div>
                    <?php } ?>
                    <div class="item"><a href="<?php the_permalink(); ?>"><?php _e('Movie info', 'academe-theme'); ?></a></div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($post->post_type == 'movie') { ?>
            <img class="slide-image" src="<?php echo get_movie_thumbnail($custom_fields['kaltura_id'], 280, 175); ?>" />
        <?php } ?>
        <?php if ( in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses', 'teaching-guide']) ) { ?>
            <?php if(has_post_thumbnail($post->ID)) { ?>
                <img class="slide-image" src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium'); ?>" />
            <?php } ?>
        <?php } ?>
        <?php if ($post->post_type == 'clip') { ?>
            <?php if(has_post_thumbnail($post->ID)) { ?>
                <img class="slide-image" src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium'); ?>" />
            <?php } else { ?>
                <?php
                $movie_kaltura_id = get_field('kaltura_id', $custom_fields['movie_id']);
                $play_from = format_time_to_seconds($custom_fields['play_from']);
                ?>
                <img class="slide-image" src="<?php echo get_movie_thumbnail($movie_kaltura_id, 280, 175, 45, $play_from); ?>" />
            <?php } ?>
        <?php } ?>
        <div class="slide-label">
            <?php icon($post->post_type); ?>
        </div>
        <?php if ($sponsored) { ?>
            <img class="sponsor-icon"
                 src="<?php echo $custom_fields['sponsor_icon']; ?>"
                 alt="<?php echo $custom_fields['sponsor_name']; ?>"
                 title="<?php echo $custom_fields['sponsor_name']; ?>"
            />
        <?php } ?>
    </div>
    <div class="modal ui overlay post-meta-highlighted" data-movie-id="<?php echo $post->ID; ?>">
        <?php if ($post->post_type == 'movie') { ?>
            <img class="thumbnail" src="<?php echo get_movie_thumbnail($custom_fields['kaltura_id'], 650, 350); ?>" />
        <?php } ?>
        <?php if ( in_array($post->post_type, ['sfwd-lessons', 'sfwd-courses', 'teaching-guide']) ) { ?>
            <?php if(has_post_thumbnail($post->ID)) { ?>
                <img class="thumbnail" src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium'); ?>" />
            <?php } ?>
        <?php } ?>
        <?php if ($post->post_type == 'clip') { ?>
            <?php if(has_post_thumbnail($post->ID)) { ?>
                <img class="thumbnail" src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium'); ?>" />
            <?php } else { ?>
                <?php
                $movie_kaltura_id = get_field('kaltura_id', $custom_fields['movie_id']);
                $play_from = format_time_to_seconds($custom_fields['play_from']);
                ?>
                <img class="thumbnail" src="<?php echo get_movie_thumbnail($movie_kaltura_id, 650, 350, 45, $play_from); ?>" />
            <?php } ?>
        <?php } ?>
        <div class="meta-content">
            <?php icon('circle-down', 'close'); ?>
            <div class="title"><?php echo highlight($search_query,$post->post_title); ?></div>
            <div class="description">
                <?php echo highlight($search_query,$post->post_content); ?>
            </div>
            <?php

            if ($mediaEntry->itemsData) {
                foreach($mediaEntry->itemsData  as $itemsData) {
                    if ($itemsData->itemsType == "metadata") { ?>
                        <div class="caption_title">
                            <?php print "Metadata" . " (" . count($itemsData->items) . ")"; ?>
                        </div>
                        <div class="caption_text">
                            <ul>
                                <?php foreach ($mediaEntry->itemsData[0]->items as $item) { ?>
                                    <li>
                                        <a href="<?php print get_post_permalink($post->ID); ?>">
                                            <?php print highlight($search_query,$item->valueText); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="clearfix"></div>
                    <?php }

                    if ($itemsData->itemsType == "caption") { ?>
                        <div class="caption-wrap">
                            <div class="caption_title chapters">
                                <span class="tooltips" ><?php print "subtitles" ;?> </span> cc
                            </div>
                            <div class="caption_text">
                                <ul>
                                    <?php $subtitles_counter = 0; ?>
                                    <?php foreach ($itemsData->items as $item) { ?>
                                        <li class="subtitle <?php echo ($subtitles_counter > 3) ? 'subtitle-hidden' : ''; ?>">
                                            <a href="<?php print get_post_permalink($post->ID); ?>?start_from=<?php print $item->startsAt; ?>">
                                                <span class="text-blue"><?php print formatMilliseconds($item->startsAt) . "</span> - " . highlight($search_query,$item->line); ?>
                                            </a>
                                        </li>
                                        <?php $subtitles_counter++; ?>
                                    <?php } ?>
                                    <?php if(count($itemsData->items) > 4) { ?>
                                        <li class="subtitles-more clickable"><?php _e('more', 'academe-theme'); ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php }
                }
            } ?>
            <?php if ($tags) { ?>
                <div class="caption-wrap">
                    <div class="caption_title chapters">
                        <span class="tooltips">  <?php print "tags" ; ?></span> #
                    </div>
                    <div class="caption-text tags">
                        <?php $tags = wp_get_post_tags($post->ID);
                        $tags_counter = 0; ?>
                        <ul class="tags-list">
                            <?php foreach ($tags as $tag) { ?>
                                <li class="tag <?php echo ($tags_counter > 3) ? 'tag-hidden' : ''; ?>">
                                    <a href="/tag/<?php print $tag->slug; ?>">#<?php print highlight($search_query,$tag->name); ?></a>
                                </li>
                                <?php $tags_counter++;
                            }
                            if(count($tags) > 4) { ?>
                                <li class="tags-more clickable"><?php _e('more', 'academe-theme'); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>

        </div>

    </div>
<?php } ?>