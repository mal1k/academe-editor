<?php /* Template Name: Enter the lesson */ ?>

<?php get_header(); ?>
    <main class="main enter-lesson-page">
        <div class="m-login">
            <div class="container">
                <div class="m-login__top">
                    <h1 class="main-subtitle"><?php _e( 'Welcome to your lesson', 'academe-theme' );?></h1>
                    <span><?php _e( 'Please Enter your Lesson Code', 'academe-theme' );?></span>
                </div>
                <form action="" method="POST" id="enterSessionForm">
                    <div class="input-group">
                        <label for="session_code"><span><?php _e( 'Lesson Code', 'academe-theme' );?></span></label>
                        <input type="text" class="form-control" name="session_code" />
                        <span class="session-code-error"></span>
                    </div>
                    <button class="btn-blue-animated"><?php _e( 'Join Lesson', 'academe-theme' );?></button>
                </form>

                <div class="m-login__bottom">
                    <div class="m-login__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/conect-img.png" alt="Login">
                    </div>
                    <span>Copyright © <?php echo 'Y'?> AcadeMe+ Inc. All rights reserved. </span>
                    <nav class="footer-nav">
                        <ul class="footer-nav__list">
                            <li class="footer-nav__item">
                                <a href="#" class="footer-nav__link"><?php _e( 'Privacy Policy', 'academe-theme' );?></a>
                            </li>
                            <li class="footer-nav__item">
                                <a href="#" class="footer-nav__link"><?php _e( 'Terms of Use', 'academe-theme' );?></a>
                            </li>
                            <li class="footer-nav__item">
                                <a href="#" class="footer-nav__link"><?php _e( 'Support', 'academe-theme' );?></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>
<?php get_footer(); ?>