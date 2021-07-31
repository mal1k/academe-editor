<?php get_header(); ?>
<main class="main">
    <?php if ( have_posts() ) :
        if ( is_home() && ! is_front_page() ) :
            ?>
            <header>
                <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
            </header>
        <?php
        endif;

        /* Start the Loop */
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
    else :
        the_content();
    endif;
    ?>

</main>

<?php get_footer(); ?>