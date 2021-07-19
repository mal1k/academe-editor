<?php get_template_part( 'templates/partials/document-open' ); ?>

<!-- Site header markup goes here -->
<header>
    <div class="left-part">

        <?php if(is_front_page()) { ?>
            <img src="<?php echo get_template_directory_uri() . '/assets/img/logo.svg'; ?>" class="h-6" />
        <?php } else { ?>
            <a href="/"><img src="<?php echo get_template_directory_uri() . '/assets/img/logo.svg'; ?>" class="h-6" /></a>
        <?php } ?>

        <nav class="top-menu">
            <?php if(is_front_page()) { ?>
                <span class="menu-item">Home</span>
            <?php } else { ?>
                <a href="/" class="menu-item link">Home</a>
            <?php } ?>
            <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
                <a href="/topics" class="menu-item link <?php link_is_active('topics'); ?>"><?php _e('Topics', 'academe-theme'); ?></a>
            <?php } ?>
            <a href="/movies" class="menu-item link <?php link_is_active('movies'); ?>"><?php _e('Movies', 'academe-theme'); ?></a>
            <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
                <a href="/guides" class="menu-item link <?php link_is_active('guides'); ?>"><?php _e('Teaching Guides', 'academe-theme'); ?></a>
                <a href="/courses" class="menu-item link <?php link_is_active('courses'); ?>"><?php _e('Lessons', 'academe-theme'); ?></a>
            <?php } ?>
            <?php if(is_user_logged_in()) { ?>
                <span class="menu-item dropdown ui dark w-200">
                    Me+
                    <?php icon('chevron-bold', 'icon-blue-stroke with-rotate'); ?>
                    <div class="menu">
                        <div class="menu-body">
                            <a href="/my-lessons" class="item <?php link_is_active('my-lessons'); ?>"><?php _e('My Lessons', 'academe-theme'); ?></a>
                            <?php if (!is_user_in_role('teacher')) { ?>
                                <a href="/my-movies" class="item <?php link_is_active('my-movies'); ?>"><?php _e('My Movies', 'academe-theme'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </span>
            <?php } ?>
        </nav>
    </div>
    <div class="right-part">
        <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
            <a href="/wp-admin/post-new.php?post_type=sfwd-courses" class="create-lesson-btn">
                <?php icon('blue-plus'); ?>
                <span>Create a Lesson</span>
            </a>
        <?php } ?>
        <?php if(is_user_logged_in() && function_exists('get_learndash_notifications_modal_template')) { ?>
            <div class="notification-btn ui dropdown">
                <img src="<?php echo get_template_directory_uri() . '/assets/img/notification.svg'; ?>" />
                <div class="menu">
                    <div style="display: none" class="item"></div>
                    <?php get_learndash_notifications_modal_template(); ?>
                </div>
            </div>
        <?php } ?>
        <div class="account-btn ui dropdown">
            <?php $user = wp_get_current_user(); ?>
            <img class="interactive" src="<?php echo get_template_directory_uri() . '/assets/img/user_avatar.svg'; ?>" />
            <div class="menu">
                <?php if ($user->exists()) { ?>
                    <div class="menu-header">
                        <div class="user-name"><?php echo $user->display_name; ?></div>
                        <div class="user-email"><?php echo $user->user_email; ?></div>
                    </div>
                    <div class="menu-body">
                        <a href="/my-lessons" class="item" style="display: block"><?php _e('My Lessons', 'academe-theme'); ?></a>
                        <?php if (!is_user_in_role('student')) { ?>
                            <a href="/reports" class="item" style="display: block"><?php _e('Reports', 'academe-theme'); ?></a>
                        <?php } ?>
                        <?php if (is_user_in_role('teacher')) { ?>
                            <a href="/teacher/<?php echo $user->user_login; ?>" class="item" style="display: block"><?php _e('My Profile', 'academe-theme'); ?></a>
                        <?php } ?>
                        <div class="item-separator"></div>
                        <a href="<?php echo wp_logout_url(); ?>" class="item"><?php _e('Logout', 'academe-theme'); ?></a>
                    </div>
                <?php } else { ?>
                    <div class="menu-header">
                        <div class="user-name"><?php _e('You are not logged in', 'academe-theme'); ?></div>
                    </div>
                    <div class="menu-body">
                        <a href="<?php echo wp_login_url(); ?>" class="item">Login</a>
                    </div>
                <?php } ?>

            </div>

        </div>

    </div>

</header>

<?php if ( !is_search() ) { ?>
<section id="search">
    <form class="search-form" action="/">
        <div class="search-btn"><img src="<?php echo get_template_directory_uri() . '/assets/img/search.svg'; ?>" /></div>
        <input class="search-input" type="text" name="s" placeholder="Search" />
        <?php if (0) { ?>
            <div class="search-separator"></div>
            <div class="search-filter ui dropdown dark">
                <span class="text default">All</span>
                <?php icon('chevron-bold', 'with-rotate'); ?>
                <div class="menu">
                    <div class="menu-body">
                        <div class="item" data-value="0">All</div>
                        <div class="item" data-value="1">Movies</div>
                        <div class="item" data-value="2">Lessons</div>
                        <div class="item" data-value="3">Guides</div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </form>
</section>
<?php } ?>

