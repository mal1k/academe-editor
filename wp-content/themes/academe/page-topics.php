<?php get_header(); ?>
    <main class="main by-subject-page">
        <section id="bySubjectFilters" class="filters-section">
            <h1><?php the_title(); ?></h1>
            <?php get_template_part('templates/partials/filter-button', 'null', [
                'filters' => ['faculty', 'grade', 'subject', 'topic']
            ]); ?>
        </section>

        <?php if (is_user_logged_in() && !is_user_in_role('student')) { ?>
            <?php if (isset($_GET['faculty']) || isset($_GET['grade']) || isset($_GET['subject']) || isset($_GET['topic'])) {

                $filter_by = ['faculty', 'grade', 'subject', 'topic'];
                $tax_query = [
                    'relation' => 'OR',
                ];
                foreach ($filter_by as $taxonomy) {
                    if (isset($_GET[$taxonomy]) && !empty($_GET[$taxonomy])) {
                        $tax_query[] = [
                            'taxonomy' => $taxonomy,
                            'field' => 'slug',
                            'terms' => $_GET[$taxonomy],
                        ];
                    }
                }

                $query_string_url = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';

                // Lessons (courses):
                $args = [
                    'post_type' => 'sfwd-courses',
                    'orderby' => ['date' => 'DESC', 'ID' => 'DESC'],
                    'posts_per_page' => 20,
                    'tax_query' => $tax_query,
                ];
                $query = new WP_Query($args);

                if ( $query->posts) {
                    get_template_part('templates/partials/slider-strip', 'null', [
                        'title' => 'Lessons',
                        'show_all_link' => '/courses' .$query_string_url,
                        'posts' => $query->posts
                    ]);
                }

                // Teacher Guides:
                $args = [
                    'post_type' => 'teaching-guide',
                    'orderby' => ['date' => 'DESC', 'ID' => 'DESC'],
                    'posts_per_page' => 20,
                    'tax_query' => $tax_query,
                ];
                $query = new WP_Query($args);

                if ( $query->posts) {
                    get_template_part('templates/partials/slider-strip', 'null', [
                        'title' => 'Teacher Guides',
                        'show_all_link' => '/guides' . $query_string_url,
                        'posts' => $query->posts,
                    ]);
                }

                // Movies:
                $args = [
                    'post_type' => 'movie',
                    'orderby' => ['date' => 'DESC', 'ID' => 'DESC'],
                    'posts_per_page' => 20,
                    'tax_query' => $tax_query,
                ];
                $query = new WP_Query($args);

                if ( $query->posts) {
                    get_template_part('templates/partials/slider-strip', 'null', [
                        'title' => 'Movies',
                        'show_all_link' => '/movies' . $query_string_url,
                        'posts' => $query->posts,
                    ]);
                }

            } else {
                top_courses_list(false, __('Recommended lessons', 'academe-theme'));
                recent_teacher_guides_list(false, __('Recommended Teaching Guides'));
                recommended_movies_list(false, __('Recommended movies', 'academe-theme'));
            } ?>

        <?php } ?>

    </main>
<?php get_footer(); ?>