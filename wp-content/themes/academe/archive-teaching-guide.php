<?php get_header(); ?>
    <main class="main">
        <section id="guidesFilters" class="filters-section">
            <h1>
                <?php post_type_archive_title( '', true ); ?>
            </h1>
            <?php get_template_part('templates/partials/filter-button', 'null', [
                'filters' => ['faculty', 'grade', 'subject', 'topic']
            ]); ?>
            <?php if (0) { //works incorrect after adding search.php template ?>
            <div class="free-search-wrap">
                <form class="free-search">
                    <?php icon('search'); ?>
                    <input type="search" name="s" placeholder="<?php _e('Search', 'academe-theme'); ?>" value="<?php echo isset($_GET['s']) ? $_GET['s'] : ''; ?>" />
                </form>
            </div>
            <?php } ?>
        </section>
        <?php $slides = get_field('teacher_guide_slides', 'option');
        if ($slides) {
            get_template_part( 'templates/partials/big-slider', 'null', [
                'slides' => $slides,
            ]);
        } ?>
        <?php recent_teacher_guides_list($filter = false); ?>

        <section id="allMovies">
            <h2 class="section-heading"><?php _e("All teaching guides", "academe-theme"); ?></h2>
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
                    <?php _e("No teaching guides found. Please try to use other filter parameters.", "academe-theme"); ?>
                </div>
            <?php } ?>
        </section>

    </main>

<?php get_footer(); ?>