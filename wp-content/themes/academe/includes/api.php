<?php

function get_next_lesson_id( $object ) {
    return learndash_next_post_link('', 'id', get_post($object['id']));
}

function get_prev_lesson_id( $object ) {
    return learndash_previous_post_link('', 'id', get_post($object['id']));
}

add_action( 'rest_api_init', function() {

    register_rest_field('sfwd-lessons', 'next_lesson', [
            'get_callback' => 'get_next_lesson_id',
            'schema' => null,
        ]
    );
    register_rest_field('sfwd-lessons', 'prev_lesson', [
            'get_callback' => 'get_prev_lesson_id',
            'schema' => null,
        ]
    );

    require_once ACADEME_THEME_DIR . 'includes/lesson-slide-templates.php';
    register_rest_route( 'academe/v1', '/slide-templates', [
        'methods'  => 'GET',
        'callback' => 'get_lesson_slide_templates',
    ] );

    register_rest_route('academe/v1', '/kaltura-config',[
        'methods' => 'GET',
        'callback' => function() {
            $client = get_kaltura_session();

            return [
                'wid' => get_field('partner_id', 'option'),
                'ks' => $client->getKS(),
                'uiconf_id' => 46602743,
            ];
        }
    ]);

    register_rest_route( 'academe/v1', '/get-movie-meta-by-kaltura', [
        'methods'  => 'GET',
        'callback' => function($data) {
            $kaltura_id = $data->get_param( 'id' );

            $args = array(
                'post_type' => 'movie',
                'meta_key' => 'kaltura_id',
                'meta_value' => $kaltura_id
            );

            $post = get_posts($args);
            return getMovieDataFromPost($post[0]);
        }
    ]);

    register_rest_route( 'academe/v1', '/movies/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => function() {
            $movie_id = $request->get_param( 'id' );
            $post = get_post($movie_id);
            return getMovieDataFromPost($post);
        }
    ]);

    register_rest_route( 'academe/v1', '/my-movies', [
        'methods'  => 'GET',
        'callback' => function() {
            $array = [];
            $user_id = get_current_user_id();
            $my_list = get_my_list($user_id, 'ids');
            $query_posts = [];
            
            if ($my_list) {
                $query = new WP_Query(['post_type' => 'movie', 'post__in' => $my_list, 'orderby' => 'post__in', 'suppress_filters' => true]);
                $query_posts = $query->posts;
            }

            foreach ($query_posts as $post) {
                array_push($array, getMovieDataFromPost($post));
            }
            
            return $array;
        }
    ] );

    register_rest_route( 'academe/v1', '/create_new_step', [
        'methods'  => 'POST',
        'callback' => function($data) {

            /**
             * <This is how LearnDash save entities from admin side>
             * The code partially from: wp-content/plugins/sfwd-lms/includes/admin/classes-builders/class-learndash-admin-course-builder-metabox.php
             * Method name: learndash_builder_selector_step_new()
             */

            $parameters = $data->get_params();

            if ($parameters['course_id']) {
                $title = 'Course ' . $parameters['course_id'];
                if ($parameters['post_type'] === 'sfwd-lessons') {
                    $title .= ' / Lesson ' . time();
                } else if ($parameters['post_type'] === 'sfwd-quiz') {
                    $title .= ' / Lesson ' . $parameters['lesson_id'] . ' / Quiz ' . time();
                } else if ($parameters['post_type'] === 'sfwd-question') {
                    $title .= ' / Lesson ' . $parameters['lesson_id'] . ' / Quiz ' . $parameters['quiz_id'] . ' / Question ' . time();
                }
                $post_args = array(
                    'post_type'    => esc_attr( $parameters['post_type'] ),
                    'post_status'  => 'publish',
                    'post_title'   => $title,
                    'post_content' => '',
                );
                if ( 'sfwd-question' === $parameters['post_type'] ) {
                    $post_args['action'] = 'new_step';
                }

                $new_post_id = wp_insert_post( $post_args );

                if ( $new_post_id ) {
                    global $wpdb;
                    /**
                     * We have to set the guid manually because the one assigned within wp_insert_post is non-unique.
                     * See LEARNDASH-3853
                     */
                    $wpdb->update(
                        $wpdb->posts,
                        array(
                            'guid' => add_query_arg(
                                array(
                                    'post_type' => $parameters['post_type'],
                                    'p'         => $new_post_id,
                                ),
                                home_url()
                            ),
                        ),
                        array( 'ID' => $new_post_id )
                    );

                    if ( 'sfwd-quiz' === $parameters['post_type'] ) {

                        $quiz_mapper = new WpProQuiz_Model_QuizMapper();
                        $quiz_pro    = new WpProQuiz_Model_Quiz();
                        $quiz_pro->setName( $post_args['post_title'] );
                        $quiz_pro->setText( 'AAZZAAZZ' );
                        $quiz_pro    = $quiz_mapper->save( $quiz_pro );
                        $quiz_pro_id = $quiz_pro->getId();
                        $quiz_pro_id = absint( $quiz_pro_id );
                        learndash_update_setting( $new_post_id, 'quiz_pro', $quiz_pro_id );

                        // Set the 'View Statistics on Profile' for the new quiz.
                        update_post_meta( $new_post_id, '_viewProfileStatistics', 1 );

                        // Associate quiz with lesson
                        learndash_update_setting( $new_post_id, 'lesson', absint( $parameters['lesson_id'] ) );
                    }

                    if ( 'sfwd-question' === $parameters['post_type'] ) {

                        $question_pro_id = learndash_update_pro_question( 0, $post_args );
                        if ( ! empty( $question_pro_id ) ) {
                            update_post_meta( $new_post_id, 'question_pro_id', absint( $question_pro_id ) );
                            learndash_proquiz_sync_question_fields( $new_post_id, $question_pro_id );
                        }

                        // Associate question with quiz
                        learndash_update_setting( $new_post_id, 'quiz', absint( $parameters['quiz_id'] ) );
                        update_post_meta( $new_post_id, 'quiz_id', absint( $parameters['quiz_id'] ) );

                        update_post_meta( $parameters['quiz_id'], 'ld_quiz_questions', [$new_post_id => $question_pro_id] );
                    }

                    // Associate current post type with course
                    learndash_update_setting( $new_post_id, 'course', absint( $parameters['course_id'] ) );
                }

            }

            return $new_post_id ?? null;
        }
    ]);

    register_rest_route( 'academe/v1', '/default-quiz-settings', [
        'methods'  => 'POST',
        'callback' => function($data) {
            $parameters = $data->get_params();

            if ($parameters['quiz_id']) {
                global $wpdb;

                $pro_quiz_id = get_post_meta( $parameters['quiz_id'], 'quiz_pro_id', true );

                $wpdb->update( 'wp_learndash_pro_quiz_master',
                    [
                        'btn_restart_quiz_hidden'       => true,
                        'btn_view_question_hidden'      => true,
                        'autostart'                     => true,
                        'quiz_run_once'                 => true,
                        'quiz_run_once_type'            => 1,
                    ],
                    [ 'ID' => $pro_quiz_id ],
                    [ '%d', '%d',  '%d', '%d', '%d' ],
                    [ '%d' ]
                );

            }

            return json_encode(['status' => 'success']);
        }
    ] );

    register_rest_route( 'academe/v1', '/get-movie-images/(?P<id>[a-zA-Z0-9-_]+)', array(
		'methods'  => 'GET',
		'callback' => 'movieImagesFromKaltura',
    ) );

    register_rest_route( 'academe/v1', '/get-movie-list', array(
		'methods'  => 'GET',
		'callback' => function() {
            $array = [];
            $taxonomyInfo = [];
            $query = new WP_Query(['post_type' => 'movie', 'posts_per_page' => -1, 'orderby' => 'id', 'suppress_filters' => true]);
            $query_posts = $query->posts;
            $i = 0;

            foreach ($query_posts as $post) {
                $info = [];
                $info[$i] = getMovieDataFromPost($post);

                $faculty = wp_get_post_terms($post->ID, 'faculty');
                $n = 0;
                foreach ($faculty as $term) {
                    $info[$i]['faculty'][$n]['id'] = $term->term_id;
                    $info[$i]['faculty'][$n]['name'] = $term->name;
                    $n += 1;
                }

                $grade = wp_get_post_terms($post->ID, 'grade');
                $n = 0;
                foreach ($grade as $term) {
                    $info[$i]['grade'][$n]['id'] = $term->term_id;
                    $info[$i]['grade'][$n]['name'] = $term->name;
                    $n += 1;
                }

                $subject = wp_get_post_terms($post->ID, 'subject');
                $n = 0;
                foreach ($subject as $term) {
                    $info[$i]['subject'][$n]['id'] = $term->term_id;
                    $info[$i]['subject'][$n]['name'] = $term->name;
                    $n += 1;
                }

                $topic = wp_get_post_terms($post->ID, 'topic');
                $n = 0;
                foreach ($topic as $term) {
                    $info[$i]['topic'][$n]['id'] = $term->term_id;
                    $info[$i]['topic'][$n]['name'] = $term->name;
                    $n += 1;
                }
                    
                array_push($array, $info[$i]);

                $i += 1;
            }
            
            return $array;
        }
    ) );

});

function getMovieDataFromPost($movie_post) {
    $array = Array();
    $custom_fields = get_fields($movie_post->ID);

    $array['kaltura_id'] = $custom_fields['kaltura_id'];
    $array['image'] = get_movie_thumbnail($custom_fields['kaltura_id'], 280, 175);
    $array['title'] = $movie_post->post_title;
    $array['link'] = $movie_post->guid;

    // duration
    if (isset($custom_fields['duration'])) {
        $duration = explode(":", $custom_fields['duration']);
        $durationTime = (int)$duration[0].'h '. (int)$duration[1].'m';
    }
    $array['time'] = $durationTime;
    
        // tags
        if( in_array($movie_post->post_type, ['movie', 'teaching-guide']) ) {
            $tags = wp_get_post_tags($movie_post->ID);
            if ($tags) {
                $tags_counter = -1;
                $tags_string = '';
                foreach ($tags as $tag) {
                    if ($tags_counter) {
                        $tags_string .= '#' . $tag->name . ', ';
                        $tags_counter--;
                        $array['tags'][] = array('tag'=>$tag->name);
                    }
                }
            } 
        }
        // year
            $array['year'] = $custom_fields['year'];
        // views
            $array['views'] = pvc_get_post_views();
        // genres
            $genres = wp_get_post_terms($movie_post->ID, 'genre', ['fields' => 'names']);
            if ($genres) {
                foreach ($genres as $genre) {
                    $array['genres'][] = array('genre'=>$genre);
                }
            }
        // director
            $array['director'] = $custom_fields['director'];
        // content
            $content = get_separated_read_more_content($movie_post->post_content);
            $array['description'] = $content;
            
        return $array;
}

        function movieImagesFromKaltura( WP_REST_Request $request ) {
            $kaltura_id = $request->get_param( 'id' );
            $count = 3; // images amount
            $time_step = 120; // in seconds

            for ( $i = 1; $i <= $count; $i++ ) {
                $images[] = get_movie_thumbnail($kaltura_id, 1920, 1080, 50, $time_step);
                $time_step += $time_step;
            }

            return $images;
        }