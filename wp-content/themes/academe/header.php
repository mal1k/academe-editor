<?php get_template_part( 'templates/partials/document-open' ); 
$unread = learndash_notifications_unread_count_by_user();
?>
<!-- Site header markup goes here -->

<div class="header-placeholder"></div>
<header>
    <div class="left-part">

        <a href="<?php echo home_url();?>"><img src="<?php echo get_template_directory_uri() . '/assets/img/logo.svg'; ?>" class="h-6 logo-img" /></a>

        <nav class="top-menu">
            <?php /*if(is_front_page()) { ?>
                <span class="menu-item"><?php _e( 'Home', 'academe-theme' );?></span>
            <?php } else { ?>
                <a href="/" class="menu-item link"><?php _e( 'Home', 'academe-theme' );?></a>
            <?php }*/ ?>
            <?php if(is_user_logged_in()) { ?>
                <span class="menu-item dropdown ui dark w-200 blue">
                    Me+
                    <?php icon('chevron-bold', 'icon-blue-stroke with-rotate'); ?>
                    <div class="menu">
                        <div class="menu-body">
                            <a href="/my-lessons" class="item <?php link_is_active('my-lessons'); ?>"><?php _e('My Lessons', 'academe-theme'); ?></a>
                            <a href="/my-movies" class="item <?php link_is_active('my-movies'); ?>"><?php _e('My Videos', 'academe-theme'); ?></a>
                        </div>
                    </div>
                </span>
            <?php } ?>

            <span class="menu-item dropdown ui dark w-200">
                <?php _e('Grade', 'academe-theme'); ?>
                <?php icon('chevron-bold', 'icon-gray-stroke with-rotate'); ?>
                <div class="menu">
                    <div class="menu-body">
                        <a href="/grade/elementary" class="item <?php link_is_active('elementary'); ?>">
                            <?php _e('Elementary', 'academe-theme'); ?>
                        </a>
                        <a href="/grade/middle-school" class="item <?php link_is_active('middle-school'); ?>">
                            <?php _e('Middle School', 'academe-theme'); ?>
                        </a>
                        <a href="/grade/high-school" class="item <?php link_is_active('high-school'); ?>">
                            <?php _e('High School', 'academe-theme'); ?>
                        </a>
                    </div>
                </div>
            </span>

            <span class="menu-item dropdown ui dark w-200">
                <?php _e('Movies', 'academe-theme'); ?>
                <?php icon('chevron-bold', 'icon-gray-stroke with-rotate'); ?>
                <div class="menu">
                    <div class="menu-body">
                        <a href="/movies" class="item <?php link_is_active('movies'); ?>">
                            <?php _e('Movies', 'academe-theme'); ?>
                        </a>
                        <a href="/clips" class="item <?php link_is_active('clips'); ?>">
                            <?php _e('Clips', 'academe-theme'); ?>
                        </a>
                    </div>
                </div>
            </span>

            <?php /*if(is_user_logged_in() && !is_user_in_role('student')) { ?>
                <a href="/topics" class="menu-item link <?php link_is_active('topics'); ?>"><?php _e('Topics', 'academe-theme'); ?></a>
            <?php }*/ ?>
            <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
                <a href="/guides" class="menu-item link <?php link_is_active('guides'); ?>"><?php _e('Teaching Guides', 'academe-theme'); ?></a>
                <a href="/courses" class="menu-item link <?php link_is_active('courses'); ?>"><?php _e('Lessons', 'academe-theme'); ?></a>
            <?php } ?>
            <span class="menu-item dropdown ui dark w-200">
                <?php _e('About', 'academe-theme'); ?>
                <?php icon('chevron-bold', 'icon-gray-stroke with-rotate'); ?>
                <div class="menu">
                    <div class="menu-body">
                        <a href="/about-us" class="item <?php link_is_active('about-us'); ?>">
                            <?php _e('About Us', 'academe-theme'); ?>
                        </a>
                        <a href="/what-we-do" class="item <?php link_is_active('what-we-do'); ?>">
                            <?php _e('What (we do)', 'academe-theme'); ?>
                        </a>
                        <a href="/why-we-do" class="item <?php link_is_active('why-we-do'); ?>">
                            <?php _e('Why (we do)', 'academe-theme'); ?>
                        </a>
                        <a href="/how-we-do" class="item <?php link_is_active('how-we-do'); ?>">
                            <?php _e('How (we do)', 'academe-theme'); ?>
                        </a>
                        <a href="/our-team" class="item <?php link_is_active('our-team'); ?>">
                            <?php _e('Our Team', 'academe-theme'); ?>
                        </a>
                    </div>
                </div>
            </span>
            
        </nav>
    </div>
    <div class="right-part">
    <?php if(is_user_logged_in() && is_user_in_role('student')) { ?>
                <span style='color: white; margin-right: 10px;'><b>Join a lesson</b></span>
                <form action="" method="POST" id="session-form">
                    <input type="text" style="padding: 9px 35px 9px 9px; width: 120px; border-radius: 0" class="form-control" name="post_name" />
                    <button type="submit" 
                    style="width: 20px;
                        height: 20px;
                        border: 0;
                        border-radius: 100%;
                        background: #51acfd;
                        position: absolute;
                        margin-top: 9px;
                        margin-left: -31px;
                        cursor: pointer;
                    ">
                        <b>></b>
                    </button>
                </form>
        <?php } ?>
        <?php if(is_user_logged_in() && !is_user_in_role('student')) { ?>
            <a href="/lesson-editor" class="create-lesson-btn">
                <?php icon('blue-plus'); ?>
                <span><?php _e( 'Create a Lesson', 'academe-theme' );?></span>
            </a>
        <?php } ?>
        <?php if(is_user_logged_in() && function_exists('get_learndash_notifications_modal_template')) { ?>
            
            <div class="notification-btn ui dropdown">
            <?php if ( $unread = learndash_notifications_unread_count_by_user() ) : ?>
            <div class="notify" ><?php //echo $unread; ?></div>
            <?php endif;?>
            <img src="<?php echo get_template_directory_uri() . '/assets/img/notification.svg'; ?>" class="unread"/>
            
                <div class="menu">
                    <div style="display: none" class="item"></div>
                    <?php get_learndash_notifications_modal_template(); ?>
                </div>
            </div>
        <?php } ?>
        <div class="account-btn ui dropdown">
            <?php $user = wp_get_current_user(); ?>
             <?php if ($user->exists()) { ?>
                <?php echo get_avatar( $user->ID, 30); ?>
                    <?php }else{?>    
                <img class="interactive" src="<?php echo get_template_directory_uri() . '/assets/img/user_avatar.svg'; ?>" />
            <?php }?>    
            <div class="menu">
                <?php if ($user->exists()) { ?>
                    <div class="menu-header">
                        <div class="user-name"><?php echo $user->display_name; ?></div>
                        <div class="user-email"><?php echo $user->user_email; ?></div>
                    </div>
                    <div class="menu-body">
                        <a href="/my-lessons" class="item" style="display: block"><?php _e('My Lessons', 'academe-theme'); ?></a>
                        <?php if (!is_user_in_role('student')) { ?>
                            <a href="/reports" target="_blank" class="item" style="display: block"><?php _e('Reports', 'academe-theme'); ?></a>
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
                        <a href="<?php echo wp_login_url(); ?>" class="item"><?php _e( 'Login', 'academe-theme' );?></a>
                    </div>
                <?php } ?>

            </div>

        </div>

    </div>

    <div class="menu-btn"><?php icon('burger-menu'); ?></div>

    <div class="ui modal mobile-menu">
        <?php icon('cross', 'close'); ?>
        <nav class="top-menu">
            <a href="/enter-lesson" class="menu-item link text-blue"><?php _e('Enter the lesson', 'academe-theme'); ?></a>
        </nav>
        <div class="separator"></div>
        <div class="account-actions">
            <?php if ($user->exists()) { ?>
                <div class="user-info">
                    <div class="user-name"><?php echo $user->display_name; ?></div>
                    <div class="user-email"><?php echo $user->user_email; ?></div>
                </div>
                <a href="<?php echo wp_logout_url(); ?>" class="item"><?php _e( 'Logout', 'academe-theme' );?></a>
            <?php } else { ?>
                <a href="<?php echo wp_login_url(); ?>" class="item"><?php _e( 'Login', 'academe-theme' );?></a>
            <?php } ?>
        </div>
    </div>

</header>

<?php if ( is_front_page() /* !is_search() && !is_singular('session') && !is_page_template( 'page-enter-lesson.php' ) */ ) { ?>
<section id="search">
    <form class="search-form" action="/">
        <div class="search-btn"><img src="<?php echo get_template_directory_uri() . '/assets/img/search.svg'; ?>" /></div>
        <input class="search-input" type="text" name="s" placeholder="Search" />
        <?php if (0) { ?>
            <div class="search-separator"></div>
            <div class="search-filter ui dropdown dark">
                <span class="text default"><?php _e( 'All', 'academe-theme' );?></span>
                <?php icon('chevron-bold', 'with-rotate'); ?>
                <div class="menu">
                    <div class="menu-body">
                        <div class="item" data-value="0"><?php _e( 'All', 'academe-theme' );?></div>
                        <div class="item" data-value="1"><?php _e( 'Movies', 'academe-theme' );?></div>
                        <div class="item" data-value="2"><?php _e( 'Lessons', 'academe-theme' );?></div>
                        <div class="item" data-value="3"><?php _e( 'Guides', 'academe-theme' );?></div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </form>
</section>
<?php } ?>

<style>
    .notify{
        width: 8px;
        height: 8px;
        background: red;
        border-radius: 100px;
        color: white;
        text-align: center;
        position: absolute;
        right: 0;
    }
    .avatar-default {
    border-radius: 30px;
}
</style>

<script>
    jQuery('#session-form').submit(function(e){
        e.preventDefault();
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType : 'json',
            data: jQuery('#session-form').serialize() + "&action=check_session",
            success: function (response) {
                console.log(response)
                if (!response.error) {
                    window.location.href = response.success;
                } 
                else {
                    showToast('Error!', response.error);
                }

            }
        });
    })

    function showToast(title, message) {
        jQuery('body').toast({
            title: title,
            message: message,
            displayTime: 3000,
            position: 'top center',
            class : 'dark',
            className: {
                toast: 'ui toast'
            }
        });
    }
</script>

<?php if ( empty($_SESSION['logged_in']) ) : ?>
<div id="container">
    <div id="passwordModal" style="text-align: center;" class="reveal-modal">
        <img src="<?php echo get_template_directory_uri() . '/assets/img/logo.svg'; ?>" style="margin-bottom: 10px;" class="h-6 logo-img" />

        <p style="color: red; text-align: left; display: none;" id="errorPasswordModal"></p>
        <form action="" method="post" name="passwordModal">
            <input type="text" name="password" placeholder="Password" style="
                width: 100%;
                padding: 10px;
                margin:top: 10px;
            "><br>
            <input type="hidden" name="action" value="passwordModal_form" style="display: none; visibility: hidden; opacity: 0;">
            <button type="submit" style="
                width: 100%;
                border: 0;
                background: #51ACFD;
                padding: 10px;
                margin-top: 10px;
                cursor: pointer;"
            >Log in</button>
        </form>
        
        <div class="line"></div>

        <a href="https://about.academe.plus" target="_blank"><button type="submit" style="
                width: 100%;
                border: 0;
                background: #F98C40;
                padding: 10px;
                margin-top: 20px;
                margin-bottom: -10px;
                cursor: pointer;"
        >Learn More about AcadeMe+</button></a>
    </div>
</div>

<style>
    body {
        overflow: hidden !important;
    }
    #container {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        visibility: visible;
        display: block;
        background-color: rgba(22,22,22,0.5);
        z-index: 9999;
    }

    .reveal-modal {
        background: #e1e1e1;
        margin: 0 auto;
        width: max-content; 
        position:relative; 
        z-index:41;
        top: 25%;
        padding:30px; 
        -webkit-box-shadow:0 0 10px rgba(0,0,0,0.4);
        -moz-box-shadow:0 0 10px rgba(0,0,0,0.4); 
        box-shadow:0 0 10px rgba(0,0,0,0.4);
    }

    .line {
        height: 0.5px;
        width: 125%;
        margin-left: -12.5%;
        margin-top: 20px;
        background-color: black;
    }
</style>

<script>
    jQuery( 'form[name="passwordModal"]' ).on( 'submit', function(event) {
    event.preventDefault()
    var form_data = jQuery( this ).serializeArray();
    form_data.push( { "name" : "security", "value" : ajax_nonce } );
 
    jQuery.ajax({
        url : ajax_url,
        type : 'post',
        data : form_data,
        success : function( response ) {
        if ( response == 301 || response == 302 )
            jQuery('#errorPasswordModal').css('display', 'block');
        if ( response == 301 )
            jQuery( "#errorPasswordModal" ).text('Insert password please');
        else if ( response == 302 )
            jQuery( "#errorPasswordModal" ).text('Incorrect password');
        else {
            jQuery('#container').hide();
            jQuery('body').attr('style', 'overflow: auto !important');
        }

        }
    });
     
    // This return prevents the submit event to refresh the page.
    return false;
});
</script>
<?php endif;?>