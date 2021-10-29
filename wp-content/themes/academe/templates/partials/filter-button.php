<div class="filter-btn <?php echo $args['button_text'] ? 'textual' : 'iconic'; ?>">
    <?php if (!$args['button_text']) { ?>
        <?php $icon_classes =  (isset($_GET['faculty']) || isset($_GET['subject']) || isset($_GET['topic']) || isset($_GET['genre']) || isset($_GET['teacher']) || isset($_GET['status'])) ? 'icon-blue icon-blue-stroke' : ''; ?>
        <?php icon('filter', $icon_classes); ?>
    <?php } else { ?>
    <div class="icon" style="color: #51ACFD">
    <?php _e( 'Advanced<br>Search', 'academe-theme' );?>
    </div>
    <?php } ?>
    <div class="menu">
        <div class="menu-body">
            <div class="filter-top item">
                <div><?php _e( 'Applied filters', 'academe-theme' );?> (<span class="filters-applied-count">0</span>)</div>
                <div class="reset-filters"><?php _e( 'Reset', 'academe-theme' );?></div>
            </div>
            <?php if (in_array('faculty', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div><?php _e( 'Faculty', 'academe-theme' );?></div>
                    <div class="dropdown ui search movies-filter dark" data-post-type="movie" data-taxonomy="faculty">
                        <input name="faculty" type="hidden" value="<?php echo (isset($_GET['faculty'])) ? $_GET['faculty'] : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <?php $terms = get_terms( 'faculty', [
                                    'hide_empty' => true,
                                ] );
                                foreach ($terms as $term) { ?>
                                    <div class="item" data-value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (in_array('grade', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div>Grades</div>
                    <div class="dropdown ui search movies-filter dark" data-post-type="movie" data-taxonomy="grade">
                        <input name="grade" type="hidden" value="<?php echo (isset($_GET['grade'])) ? $_GET['grade'] : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <?php $terms = get_terms( 'grade', [
                                    'hide_empty' => true,
                                ] );
                                foreach ($terms as $term) { ?>
                                    <div class="item" data-value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (in_array('subject', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div>Subject</div>
                    <div class="dropdown ui search movies-filter dark" data-post-type="movie" data-taxonomy="subject">
                        <input name="subject" type="hidden" value="<?php echo (isset($_GET['subject'])) ? $_GET['subject'] : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <?php $terms = get_terms( 'subject', [
                                    'hide_empty' => true,
                                ] );
                                foreach ($terms as $term) { ?>
                                    <div class="item" data-value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (in_array('topic', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div>Topic</div>
                    <div class="dropdown ui search movies-filter dark" data-post-type="movie" data-taxonomy="topic">
                        <input name="topic" type="hidden" value="<?php echo (isset($_GET['topic'])) ? $_GET['topic'] : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <?php $terms = get_terms( 'topic', [
                                    'hide_empty' => true,
                                ] );
                                foreach ($terms as $term) { ?>
                                    <div class="item" data-value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (in_array('age', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div>Age</div>
                    <div class="dropdown ui search movies-filter dark" data-post-type="movie" data-taxonomy="age">
                        <input name="age" type="hidden" value="<?php echo (isset($_GET['age'])) ? $_GET['age'] : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <?php $terms = get_terms( 'age', [
                                    'hide_empty' => true,
                                ] );
                                foreach ($terms as $term) { ?>
                                    <div class="item" data-value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (in_array('genre', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div><?php _e( 'Genre', 'academe-theme' );?></div>
                    <div class="dropdown ui search movies-filter dark" data-post-type="movie" data-taxonomy="genre">
                        <input name="genre" type="hidden" value="<?php echo (isset($_GET['genre'])) ? strtolower(urlencode($_GET['genre'])) : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <?php $terms = get_terms( 'genre', [
                                    'hide_empty' => true,
                                ] );
                                foreach ($terms as $term) { ?>
                                    <div class="item" data-value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (in_array('session_teacher', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div><?php _e( 'Teacher', 'academe' );?></div>
                    <div class="dropdown ui search movies-filter dark">
                        <input name="teacher" type="hidden" value="<?php echo (isset($_GET['teacher'])) ? strtolower(urlencode($_GET['teacher'])) : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <?php global $wpdb;
                                $authors = $wpdb->get_results( $wpdb->prepare(
                                    "SELECT u.ID, u.display_name, u.user_nicename
                                        FROM $wpdb->posts as p
                                        RIGHT JOIN $wpdb->users u ON p.post_author = u.ID
                                        WHERE p.post_type = 'session'
                                        AND p.post_status = 'publish'
                                        GROUP BY u.ID")
                                );
                                foreach ($authors as $author) { ?>
                                    <div class="item" data-value="<?php echo $author->user_nicename; ?>"><?php echo $author->display_name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (in_array('session_status', $args['filters'])) { ?>
                <div class="filter-row-block">
                    <div><?php _e( 'Status', 'academe' );?></div>
                    <div class="dropdown ui search movies-filter dark">
                        <input name="status" type="hidden" value="<?php echo (isset($_GET['status'])) ? strtolower(urlencode($_GET['status'])) : ''; ?>" >
                        <span class="default text"><?php _e( 'All', 'academe-theme' );?></span>
                        <?php icon('chevron-bold', 'with-rotate'); ?>
                        <div class="menu">
                            <div class="menu-body">
                                <div class="item" data-value="live"><?php _e('LIVE', 'academe-theme'); ?></div>
                                <div class="item" data-value="upcoming"><?php _e('Upcoming', 'academe-theme'); ?></div>
                                <div class="item" data-value="active"><?php _e('Active', 'academe-theme'); ?></div>
                                <div class="item" data-value="expired"><?php _e('Expired', 'academe-theme'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="filter-bottom">
                <div class="cancel-filters secondary-btn"><?php _e( 'Cancel', 'academe-theme' );?></div>
                <div class="<?php echo $args['apply_async'] ? 'apply-filters-async' : 'apply-filters'; ?> primary-btn"><?php _e( 'Apply', 'academe-theme' );?></div>
            </div>
        </div>
    </div>
</div>