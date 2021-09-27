<?php get_header(); ?>
    <main class="main">
        <section id="movieFilters" class="filters-section">
            <h1>
                <?php post_type_archive_title( '', true ); ?>
                <?php if (isset($_GET['faculty']) || isset($_GET['grade']) || isset($_GET['subject']) || isset($_GET['topic']) || isset($_GET['genre'])) { ?>
                    <?php echo ': '; _e("Filtered results", "academe-theme"); ?>
                <?php } ?>
            </h1>
            <?php get_template_part('templates/partials/filter-button', 'null', [
                'filters' => ['faculty', 'grade', 'subject', 'topic', 'genre']
            ]); ?>
        </section>

        <?php if (!is_paged() && !get_query_var('taxonomy')) { //show sliders only on 1st page
            if(is_user_logged_in()) {
                continue_watching_list();
                my_list();
            }
        } ?>

        <section id="allMovies">
            <div class="filter-row-block" style="text-align: -webkit-right;">
                <div class="dropdown ui search movies-filter dark" data-post-type="movie" data-taxonomy="genre">
                    <input name="genre" type="hidden" value="<?php echo (isset($_GET['genre'])) ? strtolower(urlencode($_GET['genre'])) : ''; ?>" >
                    <span class="default text">All</span>
                    <?php icon('chevron-bold', 'with-rotate'); ?>
                    <div class="menu">
                        <div class="menu-body">
                            <?php $terms = get_terms( 'genre', [
                                'hide_empty' => true,
                            ] );
                            foreach ($terms as $term) { ?>
                                <a class="item" href="<?php global $wp;
                                            echo home_url($wp->request) . '?genre='.$term->slug; ?>"><div class="item" data-value="<?php echo $term->slug; ?>"><?php echo $term->name; ?></div></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="section-heading"><?php _e("All movies", "academe-theme"); ?></h2>
            <?php if ( !empty($_GET['genre']) ) : ?>
            <a href="<?php echo home_url('/movies/'); ?>"><div class="secondary-btn" style="width: 160px;">Clear filter</div></a>
            <?php endif; ?>
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