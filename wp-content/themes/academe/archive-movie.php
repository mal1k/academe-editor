<?php get_header(); ?>
    <main class="main">
        <section id="movieFilters" class="filters-section">
            <h1>
                <?php post_type_archive_title( '', true ); ?>
                <?php if (isset($_GET['faculty']) || isset($_GET['subject']) || isset($_GET['topic']) || isset($_GET['genre'])) { ?>
                    <?php echo ': '; _e("Filtered results", "academe-theme"); ?>
                <?php } ?>
            </h1>
            <?php get_template_part('templates/partials/filter-button', 'null', [
                'filters' => ['faculty', 'subject', 'topic', 'genre']
            ]); ?>
        </section>
        <?php $slides = get_field('movie_slides', 'option');
        if ($slides) {
            get_template_part( 'templates/partials/big-slider', 'null', [
                'slides' => $slides,
            ]);
        } ?>
        <?php if (!is_paged() && !get_query_var('taxonomy')) { //show sliders only on 1st page
            if(is_user_logged_in()) {
                continue_watching_list();
                my_list();
            }
        } ?>

        <section id="allMovies">
            <h2 class="section-heading"><?php _e("All movies", "academe-theme"); ?></h2>
            <?php if ( have_posts() ) { ?>
                <div class="movie-blocks">
                    <?php while ( have_posts() ) : ?>
                        <?php the_post(); ?>
                        <?php get_template_part('templates/partials/movie-block', 'null'); ?>
                    <?php endwhile; ?>
                </div>
                <?php numeric_posts_nav(); ?>
            <?php } else { ?>
                <div class="no-posts-found">
                    <?php _e("No movies found. Please try to use other filter parameters.", "academe-theme"); ?>
                </div>
            <?php } ?>
        </section>

    </main>

<?php get_footer(); ?>