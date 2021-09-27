<?php if ($args['clips']) { ?>
    <section class="slider-strip">
        <div class="strip-top">
            <h2 class="strip-heading"><?php _e($args['title'], 'academe-theme'); ?></h2>
        </div>
        <div class="swiper-container swiper-strip">
            <div class="swiper-wrapper">
                <?php foreach ($args['clips'] as $clip) { ?>
                    <?php $play_from = format_time_to_seconds($clip['play_from']);
                    $play_to = format_time_to_seconds($clip['play_to']); ?>
                    <div class="swiper-slide movie-block clip" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.95) 11.29%, rgba(0, 0, 0, 0) 100%) no-repeat center / cover;">
                        <div class="slide-info">
                            <div class="title"><?php echo $clip['clip_name']; ?></div>
                            <span class="watch start-movie-preview"
                                  data-kaltura-id="<?php echo $clip['clip_kaltura_id']; ?>"
                                  data-mode="basic"
                                  data-play-from="<?php echo $play_from; ?>"
                                  data-play-to="<?php echo $play_to; ?>">
                                <div class="start-watch"><?php icon('play-rounded'); ?></div>
                            </span>
                        </div>
                        <object class="slide-image" data="<?php echo get_movie_thumbnail($clip['clip_kaltura_id'], 280, 175, 45, $play_from ); ?>" type="image/png">
                            <img src="<?php echo get_movie_thumbnail($clip['clip_kaltura_id'], 280, 175, 45 ); ?>" />
                        </object>
                    </div>
                <?php } ?>
            </div>
            <!-- Add Arrows -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>
<?php } ?>
