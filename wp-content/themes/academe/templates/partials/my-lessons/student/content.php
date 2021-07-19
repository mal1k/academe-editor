<section id="movieFilters" class="filters-section">
    <h1><?php the_title(); ?></h1>
    <?php get_template_part('templates/partials/filter-button', 'null', [
        'filters' => ['session_teacher', 'session_status']
    ]); ?>
</section>
<?php
$all_posts = [];
$limit_per_page = 5;
$args = [
    'post_type' => 'session',
    'orderby' => 'date',
    'order' => 'DESC' ,
];

if (isset($_GET['teacher'])) {
    $args['author_name'] = $_GET['teacher'];
}

// Synchronous live (now) sessions:
if (!isset($_GET['status']) || isset($_GET['status']) && $_GET['status'] == 'live') {
    $args['meta_query'] = [
        'relation' => 'AND',
        array(
            'key' => 'session_starts',
            'value' => time(),
            'compare' => '<=',
        ),
        array(
            'key' => 'session_ends',
            'value' => time(),
            'compare' => '>=',
        ),
        array(
            'key' => 'session_type',
            'value' => 'sync',
            'compare' => '=',
        ),
    ];
    $wp_query = new WP_Query($args);
    $all_posts = array_merge($all_posts, $wp_query->posts);
}

// Upcoming sessions:
if (!isset($_GET['status']) || isset($_GET['status']) && $_GET['status'] == 'upcoming') {
    $args['meta_query'] = [
        'relation' => 'AND',
        array(
            'key' => 'session_starts',
            'value' => time(),
            'compare' => '>',
        )
    ];
    $wp_query = new WP_Query($args);
    $all_posts = array_merge($all_posts, $wp_query->posts);
}

// Active sessions:
if (!isset($_GET['status']) || isset($_GET['status']) && $_GET['status'] == 'active') {
    $args['meta_query'] = [
        'relation' => 'AND',
        array(
            'key' => 'session_starts',
            'value' => time(),
            'compare' => '<=',
        ),
        array(
            'key' => 'session_ends',
            'value' => time(),
            'compare' => '>=',
        ),
        array(
            'key' => 'session_type',
            'value' => 'async',
            'compare' => '=',
        ),
    ];
    $wp_query = new WP_Query($args);
    $all_posts = array_merge($all_posts, $wp_query->posts);
}

// Expired sessions:
if (!isset($_GET['status']) || isset($_GET['status']) && $_GET['status'] == 'expired') {
    if (count($all_posts) < $limit_per_page) {

        $args['meta_query'] = [
            'relation' => 'AND',
            array(
                'key' => 'session_ends',
                'value' => time(),
                'compare' => '<',
            )
        ];
        $args['posts_per_page'] = $limit_per_page - count($all_posts);
        $wp_query = new WP_Query($args);

        $loaded_expired = (($wp_query->post_count == $limit_per_page - count($all_posts)) && ($wp_query->post_count != $wp_query->found_posts))
            ? $wp_query->post_count : 0;
        $all_posts = array_merge($all_posts, $wp_query->posts);

    }
}?>

<?php if($all_posts) { ?>
    <div class="lesson-rows">
        <?php foreach ($all_posts as $post) { ?>
            <?php setup_postdata($post);
            get_template_part( 'templates/partials/my-lessons/student/session-row-block', 'null', []); ?>
        <?php } ?>
        <?php wp_reset_query(); ?>
    </div>
    <div class="container">
        <?php if ($loaded_expired) { ?>
            <span class="primary-btn load-more-sessions" data-offset="<?php echo $loaded_expired; ?>">Load more</span>
        <?php } ?>
    </div>
<?php } ?>