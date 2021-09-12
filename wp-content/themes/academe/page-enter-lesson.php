<?php /* Template Name: Enter the lesson */ ?>

<?php get_header(); ?>
    <main class="main enter-lesson-page">
        <div class="m-login">
            <div class="container">
                <div class="m-login__top">
                    <h1 class="main-subtitle">Welcome to your lesson</h1>
                    <span>Please Enter your Lesson Code</span>
                </div>
                <form action="" method="POST" id="enterSessionForm">
                    <div class="input-group">
                        <label for="session_code"><span>Lesson Code</span></label>
                        <input type="text" class="form-control" name="session_code" />
                        <span class="session-code-error"></span>
                    </div>
                    <button class="btn-blue-animated">Join Lesson</button>
                </form>

                <div class="m-login__bottom">
                    <div class="m-login__img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/conect-img.png" alt="Login">
                    </div>
                    <span>Copyright © 2021 AcadeMe+ Inc. All rights reserved. </span>
                    <nav class="footer-nav">
                        <ul class="footer-nav__list">
                            <li class="footer-nav__item">
                                <a href="#" class="footer-nav__link">Privacy Policy</a>
                            </li>
                            <li class="footer-nav__item">
                                <a href="#" class="footer-nav__link">Terms of Use</a>
                            </li>
                            <li class="footer-nav__item">
                                <a href="#" class="footer-nav__link">Support</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </main>
<?php get_footer(); ?>