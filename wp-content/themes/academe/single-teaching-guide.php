<?php get_header(); ?>

<?php setup_postdata($post); ?>
<?php $custom_fields = get_fields($post->ID);?>
<?php $partner_id = get_field('partner_id', 'option'); ?>

<main class="main single-teaching-guide-page" data-movie-id="<?php echo $post->ID; ?>">
    <section id="guideInfo">
        <div class="guide-info">
            <div class="title-wrap">
                <h1 class="title"><?php the_title(); ?></h1>
            </div>
            <div class="tags">
                <?php $tags = wp_get_post_tags($post->ID); ?>
                <?php if ($tags) { ?>
                    <span class="tags-list">
                        <?php $tags_counter = 3;
                        $tags_string = '';
                        foreach ($tags as $tag) {
                            if ($tags_counter) {
                                $tags_string .= '#' . $tag->name . ' ';
                                $tags_counter--;
                            } else {
                                break;
                            }
                        }
                        echo rtrim($tags_string); ?>
                    </span>
                <?php } ?>
            </div>

            <div class="meta">
                <?php if(isset($custom_fields['class'])) { ?>
                    <div>
                        <div class="info-subtitle"><?php _e('Class', 'academe-theme'); ?></div>
                        <div class="whitespace-nowrap"><?php echo $custom_fields['class']; ?></div>
                    </div>
                <?php } ?>
                <div>
                    <div class="info-subtitle"><?php _e('Created by', 'academe-theme'); ?></div>
                    <div class="text-blue whitespace-nowrap"><?php the_author(); ?></div>
                </div>
                <div>
                    <div class="info-subtitle"><?php _e('Created Date', 'academe-theme'); ?></div>
                    <div class="whitespace-nowrap"><?php the_date('d/m/Y'); ?></div>
                </div>
                <div>
                    <?php if ($custom_fields['teacher_guide_type'] == 'topic' && $custom_fields['related_movies']) { ?>
                        <div class="info-subtitle"><?php _e('Referenced Movies', 'academe-theme'); ?></div>
                        <div class="text-blue">
                            <?php $movies = [];
                            foreach ($custom_fields['related_movies'] as $movie) {
                                $permalink = get_permalink($movie->ID);
                                $movies[] = "<a href=\"$permalink\">$movie->post_title</a>";
                            }
                            echo implode(', ', $movies); ?>
                        </div>
                    <?php } else if ($custom_fields['teacher_guide_type'] == 'movie') { ?>
                        <div class="info-subtitle"><?php _e('Related Topics', 'academe-theme'); ?></div>
                        <div class="text-blue">
                            <?php $tg_topics = get_the_terms($post->ID, 'topic');
                            if ($tg_topics) {
                                $tg_topics_output = [];
                                foreach ($tg_topics as $topic) {
                                    $tg_topics_output[] = $topic->name;
                                }
                                echo implode(', ', $tg_topics_output);
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="info-subtitle"><?php _e('About', 'academe-theme'); ?></div>
            <div class="overview"><?php echo $post->post_content; ?></div>
            <div class="action-buttons">
                <a class="download-pdf blue-btn" href="<?php echo $custom_fields['pdf_document']; ?>" target="_blank"><?php _e('Download PDF', 'academe-theme'); ?></a>
                <?php if ($custom_fields['teacher_guide_type'] == 'movie') { ?>
                    <div class="start-watch blue-btn start-movie-preview" data-movie-id="<?php echo $custom_fields['related_movie']->ID; ?>" data-mode="basic">
                        <?php icon('play-rounded', 'icon-16'); ?>
                        &nbsp;<?php _e('Watch Movie', 'academe-theme'); ?>
                    </div>
                    <div class="start-watch blue-btn start-movie-trailer" data-movie-id="" data-mode="basic">
                        <?php icon('play-rounded', 'icon-16'); ?>
                        &nbsp;<?php _e('Watch Trailer', 'academe-theme'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="movie-poster">
            <div class="start-watch start-movie-preview" data-kaltura-id="<?php echo $custom_fields['main_movie']; ?>" data-mode="basic">
                <?php icon('play-rounded'); ?>
                <span><?php _e('PLAY', 'academe-theme'); ?></span>
            </div>

            <div class="poster-label">
                <?php icon($post->post_type); ?>
                <span class="title"><?php _e('Teaching guide', 'academe-theme'); ?></span>
            </div>
            <img class="poster-image" src="<?php echo get_movie_thumbnail($custom_fields['main_movie']); ?>" />
        </div>
    </section>
    <section id="guideContent">
        <div class="background-wrapper" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>)">
        </div>

        <div class="tabs-group">
            <div class="ui menu">
                <span class="item" data-tab="notes"><?php _e('Notes To the Teacher', 'academe-theme'); ?></span>
                <span class="item" data-tab="questions"><?php _e('Reflection Questions', 'academe-theme'); ?></span>
                <span class="item" data-tab="lessons"><?php _e('Related Lessons', 'academe-theme'); ?></span>
                <?php if ($custom_fields['teacher_guide_type'] == 'topic') { ?>
                <span class="item" data-tab="movies"><?php _e('Referenced Movies', 'academe-theme'); ?></span>
                <?php } else { ?>
                    <span class="item" data-tab="topics"><?php _e('Related Topics', 'academe-theme'); ?></span>
                <?php } ?>
                <?php if (isset($custom_fields['ccss_text']) && $custom_fields['ccss_text']) { ?>
                    <span class="item" data-tab="ccss"><?php _e('CCSS', 'academe-theme'); ?></span>
                <?php } ?>

            </div>
            <div class="ui tab segment" data-tab="notes">
                <div class="text-content">
                    <?php echo $custom_fields['notes_text']; ?>
                </div>
            </div>
            <div class="ui tab segment questions" data-tab="questions">
                <div class="tab-content">
                    <?php if ($custom_fields['questions_by_movies']) { ?>
                        <?php foreach ($custom_fields['questions_by_movies'] as $movie) { ?>
                            <div class="movie-title"><?php echo $movie['questions_movie']->post_title; ?></div>
                            <?php foreach ($movie['segments'] as $segment) { ?>
                                <?php $play_from = format_time_to_seconds($segment['timeline']['time_from']);
                                $play_to = format_time_to_seconds($segment['timeline']['time_to']); ?>
                                <div class="movie-segment">
                                    <div class="media-content">
                                        <object data="<?php echo get_movie_thumbnail(get_field('kaltura_id', $movie['questions_movie']->ID), $width = 280, $height = 180, $quality = 45, $vid_sec = $play_from); ?>" type="image/png">
                                            <img src="<?php echo get_movie_thumbnail(get_field('kaltura_id', $movie['questions_movie']->ID), $width = 280, $height = 180, $quality = 45); ?>" />
                                        </object>
                                        <div class="watch start-movie-preview"
                                             data-movie-id="<?php echo $movie['questions_movie']->ID; ?>"
                                             data-mode="basic"
                                             data-play-from="<?php echo $play_from; ?>"
                                             data-play-to="<?php echo $play_to; ?>">
                                            <div class="start-watch"><?php icon('play-rounded'); ?></div>
                                        </div>
                                    </div>
                                    <div class="text-content">
                                        <div class="question-title">
                                            <?php echo $segment['subtitle']; ?>
                                            <span class="question-timeline start-movie-preview"
                                                  data-movie-id="<?php echo $movie['questions_movie']->ID; ?>"
                                                  data-mode="basic"
                                                  data-play-from="<?php echo $play_from; ?>"
                                                  data-play-to="<?php echo $play_to; ?>">
                                                (<?php echo $segment['timeline']['time_from'] . '-' . $segment['timeline']['time_to']; ?>)
                                            </span>
                                        </div>
                                        <div class="question-content">
                                            <ul class="reflection-questions-list">
                                                <?php foreach ( $segment['questions_list'] as $question_item ) { ?>
                                                    <li class="question"><?php echo $question_item['question']; ?></li>
                                                    <li class="answer"><?php echo $question_item['answer']; ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="ui tab segment" data-tab="lessons">
                <?php
                /* Related lessons (courses) list start */
                if ($custom_fields['related_lessons']) {
                    $query = new WP_Query([
                        'post_type' => 'sfwd-courses',
                        'post__in' => $custom_fields['related_lessons'],
                        'meta_key' => 'sponsored',
                        'orderby' => [
                            'meta_value' => 'DESC',
                            'date' => 'DESC'
                        ],
                        'suppress_filters' => true
                    ]);
                    $query_posts = $query->posts;
                    if ($query_posts) {
                        get_template_part('templates/partials/slider-strip', 'null', [
                            'title' => __('Related Lessons', 'academe-theme'),
                            'filter' => [
                                'active' => false,
                            ],
                            'posts' => $query_posts,
                        ]);
                    }
                }
                /* Related lessons list end */
                ?>
            </div>
            <?php if ($custom_fields['teacher_guide_type'] == 'topic') { ?>
                <div class="ui tab segment" data-tab="movies">
                    <?php
                    /* Related movies list start */
                    if ($custom_fields['teacher_guide_type'] == 'topic' && $custom_fields['related_movies']) {
                        get_template_part('templates/partials/slider-strip', 'null', [
                            'title' => __('Referenced Movies', 'academe-theme'),
                            'filter' => [
                                'active' => false,
                            ],
                            'posts' => $custom_fields['related_movies']
                        ]);
                    }
                    /* Related movies list end */
                    ?>
                </div>
            <?php } else { ?>
                <div class="ui tab segment" data-tab="topics">
                    <?php
                    /* Related topics list start */
                    // $tg_topics

                    foreach ($tg_topics as $topic) {
                        $args = [
                            'post_type' => 'movie',
                            'posts_per_page' => 20,
                            'tax_query' => [
                                [
                                    'taxonomy' => 'topic',
                                    'field'    => 'id',
                                    'terms'    => $topic->term_id,
                                ]
                            ]
                        ];
                        $movies = $lessons = new WP_Query($args);
                        if ($movies->posts) {
                            get_template_part('templates/partials/slider-strip', 'null', [
                                'title' => "More $topic->name movies",
                                'filter' => [
                                    'active' => false,
                                ],
                                'posts' => $movies->posts
                            ]);
                        }
                    }



                    /* Related topics list end */
                    ?>
                </div>
            <?php } ?>
            <?php if (isset($custom_fields['ccss_text']) && $custom_fields['ccss_text']) { ?>
                <div class="ui tab segment" data-tab="ccss">
                    <div class="text-content">
                        <?php echo $custom_fields['ccss_text']; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
    <div class="modal ui overlay movie-player">
        <?php icon('cross', 'close'); ?>
        <?php $partner_id = get_field('partner_id', 'option'); ?>
        <script src="https://cdnapisec.kaltura.com/p/<?php echo $partner_id; ?>/sp/253884200/embedIframeJs/uiconf_id/46602743/partner_id/<?php echo $partner_id; ?>"></script>
        <div id="kalturaPlayer" style="height: 500px;">

        </div>
        <div class="movie-questions">
            <div class="movie-questions-content"></div>
            <div class="hide-answers"><span>Hide/show answers</span></div>
        </div>

    </div>
</main>
<script>
    jQuery(document).ready(function($){
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('play')) {
            setTimeout(function () {
                $('.start-movie-preview').trigger('click');
            }, 1000);
            window.history.replaceState({}, document.title, window.location.href.split('?')[0]);
        }
    });
</script>
<?php get_footer(); ?>