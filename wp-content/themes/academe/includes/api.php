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
    register_rest_field('sfwd-quiz', 'questions', [
            'get_callback' => function($object) {
                return learndash_get_quiz_questions( $object['id'] );
            },
            'schema' => null,
        ]
    );
    register_rest_field('sfwd-courses', 'default_cover', [
            'get_callback' => function() {
                return get_site_url(null, '/wp-content/themes/academe/assets/img/lesson-cover.jpg');
            },
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
                'uiconf_id' => get_field('kaltura_player_id', 'option'),
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
        'callback' => function($request) {
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
                $query = new WP_Query(['post_type' => ['movie', 'clip'], 'post__in' => $my_list, 'orderby' => 'post__in', 'suppress_filters' => true]);
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
            $query = new WP_Query(['post_type' => ['movie', 'clip'], 'posts_per_page' => -1, 'orderby' => 'id', 'suppress_filters' => true]);
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


    register_rest_route( 'academe/v1', '/get-lesson-quizzes', [
        'methods'  => 'GET',
        'callback' => function($data) {
            $parameters = $data->get_params();
            if ($parameters['course'] && $parameters['lesson']) {
                $quizzes = learndash_course_get_children_of_step( $parameters['course'], $parameters['lesson'], 'sfwd-quiz' );

                $result = [];
                foreach ($quizzes as $quiz) {
                    $result[] = [
                        'id' => $quiz,
                        'time' => intval(get_field('show_at', $quiz))
                    ];
                }

                return $result;
            }

        }
    ]);

    register_rest_route( 'academe/v1', '/get-lesson-last-session-url', [
        'methods'  => 'GET',
        'callback' => function($data) {
            $parameters = $data->get_params();
            if ($parameters['lesson']) {

                $wp_query = new WP_Query([
                    'post_type' => 'session',
                    'meta_query' => [
                        array(
                            'key' => 'related_lesson',
                            'value' => $parameters['lesson'],
                            'compare' => '=',
                        )
                    ]
                ]);

                $session_url = '/sessions/' . $wp_query->posts[0]->post_name;

                return $session_url;
            }

        }
    ]);

    register_rest_route( 'academe/v1', '/save-image', [
        'methods'  => 'POST',
        'callback' => function($data) {
            $parameters = $data->get_params();

            $old_url = $parameters['url'];

            // Save pixabay images locally:
            if (parse_url($old_url, PHP_URL_HOST) === 'pixabay.com') {
                $get = wp_remote_get( $old_url );
                $type = wp_remote_retrieve_header( $get, 'content-type' );

                if (!$type)
                    return false;

                $mirror = wp_upload_bits( basename( $old_url ), '', wp_remote_retrieve_body( $get ) );

                $attachment = array(
                    'post_mime_type' => $type
                );

                $attach_id = wp_insert_attachment( $attachment, $mirror['file']);

                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );

                wp_update_attachment_metadata( $attach_id, $attach_data );

                $new_url = wp_get_attachment_image_url($attach_id, 'full');

            } else {
                $new_url = $old_url;
            }
            update_field( $parameters['field'], $new_url, $parameters['post_id'] );

            return json_encode(['status' => 'success']);
        }
    ] );

    register_rest_route( 'academe/v1', '/get-lesson-meta-terms', [
        'methods'  => 'GET',
        'callback' => function($data) {
            $lesson_id = $data->get_param( 'lesson_id' );

            $subjects = wp_get_post_terms($lesson_id, 'subject', ['fields' => 'names']);
            $topics = wp_get_post_terms($lesson_id, 'topic', ['fields' => 'names']);
            $grades = wp_get_post_terms($lesson_id, 'grade', ['fields' => 'names']);
            $tags = wp_get_post_tags($lesson_id, ['fields' => 'names']);

            $tags_hash = array_map(function ($tag) {
                return '#' . $tag;
            }, $tags);

            return [
                'subjects' => $subjects,
                'topics' => $topics,
                'grades' => $grades,
                'tags' => $tags_hash,
            ];
        }
    ]);

    $steps_controller = new LD_REST_Courses_Steps_Controller_V2();
    register_rest_route( 'academe/v1', '/course-steps/(?P<id>[\d]+)', [
        'methods'             => 'GET',
        'callback'            => array( $steps_controller, 'get_course_steps' ),
        'permission_callback' => 'get_course_steps_permissions_check_custom',
        'args'                => $steps_controller->get_collection_params(),
    ]);

    $courses_controller = new LD_REST_Courses_Controller_V1();
    register_rest_route( 'academe/v1', '/sfwd-courses/(?P<id>[\d]+)', [
        'methods'             => 'GET',
        'callback'            => array( $courses_controller, 'get_item' ),
        'permission_callback' => 'get_course_permissions_check_custom',
        'args'                => $courses_controller->get_collection_params(),
    ]);

    $lessons_controller = new LD_REST_Lessons_Controller_V1();
    register_rest_route( 'academe/v1', '/sfwd-lessons/(?P<id>[\d]+)', [
        'methods'             => 'GET',
        'callback'            => array( $lessons_controller, 'get_item' ),
        'permission_callback' => 'get_course_permissions_check_custom',
        'args'                => $lessons_controller->get_collection_params(),
    ]);

    register_rest_route( 'academe/v1', '/update-lessons-order-params', [
        'methods'  => 'POST',
        'callback' => function($data) {
            $parameters = $data->get_params();

            $meta_values = get_post_meta( $parameters['post_id'], '_sfwd-courses', false );

            $meta_values_new = $meta_values;
            $meta_values_new[0]['sfwd-courses_course_lesson_orderby'] = 'menu_order';
            $meta_values_new[0]['sfwd-courses_course_lesson_order'] = 'ASC';

            update_post_meta($parameters['post_id'], '_sfwd-courses', $meta_values_new[0]);

            return json_encode(['status' => 'success']);
        }
    ] );
});

function get_course_steps_permissions_check_custom() {
    return true;
}
function get_course_permissions_check_custom($request) {
    $allow = false;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $course = get_post($request['id']);
        if (learndash_is_admin_user() || is_user_in_role('student') || is_user_in_role('teacher') && $user_id == $course->post_author) {
            $allow = true;
        }
    }
    return $allow;
}

function getMovieDataFromPost($movie_post) {
    $array = Array();
    $custom_fields = get_fields($movie_post->ID);

    $array['title'] = $movie_post->post_title;
    $array['link'] = $movie_post->guid;

    //duration in seconds
    $array['duration'] = format_time_to_seconds($custom_fields['duration']);

    if ($movie_post->post_type === 'clip') {
        $movie_kaltura_id = get_field('kaltura_id', $custom_fields['movie_id']->ID);
        $array['kaltura_id'] = $movie_kaltura_id;

        $array['play_from'] = $custom_fields['play_from'] ?? '00:00:00';
        $array['play_to'] = $custom_fields['play_to'] ?? '00:00:00';

        $clip_duration = format_time_to_seconds($array['play_to']) - format_time_to_seconds($array['play_from']); // in seconds
        $array['duration'] = gmdate("H:i:s", $clip_duration); //in timestring

        $duration_arr = explode(":", $array['duration']);
        if ((int)$duration_arr[0] == 0) {
            $array['time'] = (int)$duration_arr[1].'m '. (int)$duration_arr[2].'s';
        } else {
            $array['time'] = (int)$duration_arr[0].'h '. (int)$duration_arr[1].'m';
        }

        if(has_post_thumbnail($movie_post->ID)) {
            $array['image'] = get_the_post_thumbnail_url($movie_post->ID, 'medium');
        } else {
            $play_from = format_time_to_seconds($custom_fields['play_from']);
            $array['image'] = get_movie_thumbnail(get_field('kaltura_id', $custom_fields['movie_id']->ID), 280, 175, 45, $play_from);
        }
    } else if ($movie_post->post_type === 'movie') {
        $array['image'] = get_movie_thumbnail($custom_fields['kaltura_id'], 280, 175);
        $array['kaltura_id'] = $custom_fields['kaltura_id'];
        // duration
        if (isset($custom_fields['duration'])) {
            //duration human friendly
            $duration = explode(":", $custom_fields['duration']);
            $durationTime = (int)$duration[0].'h '. (int)$duration[1].'m';
            $array['time'] = $durationTime;
        }
    }
    
        // tags
        $tags = wp_get_post_tags($movie_post->ID);
        if ($tags) {
            $tags_counter = -1;
            $tags_string = '';
            foreach ($tags as $tag) {
                if ($tags_counter) {
                    $tags_string .= '#' . $tag->name . ', ';
                    $tags_counter--;
//                    $array['tags'][] = array('tag'=>$tag->name);
                }
            }
            $array['tags'] = $tags;
        }

        // year
            $array['year'] = $custom_fields['year'];
        // views
            $array['views'] = pvc_get_post_views();
        // genres
            $genres = wp_get_post_terms($movie_post->ID, 'genre');
            if ($genres) {
                $array['genres'] = $genres;
//                foreach ($genres as $genre) {
//                    $array['genres'][] = array('genre'=>$genre);
//                }
            }
        // topics
        $topics = wp_get_post_terms($movie_post->ID, 'topic');
        if ($topics) {
            $array['topics'] = $topics;
//            foreach ($topics as $topic) {
//                $array['topics'][] = array('topic'=>$topic);
//            }
        }
        // grades
        $grades = wp_get_post_terms($movie_post->ID, 'grade');
        if ($grades) {
            $array['grades'] = $grades;
        }
        // subjects
        $subjects = wp_get_post_terms($movie_post->ID, 'subject');
        if ($subjects) {
            $array['subjects'] = $subjects;
        }
        // director
            $array['director'] = $custom_fields['director'];
        // content
            $content = get_separated_read_more_content($movie_post->post_content);
            $array['description'] = $content;
            $array['post_type'] = $movie_post->post_type;
            
        return $array;
}

        function movieImagesFromKaltura( WP_REST_Request $request ) {
            $kaltura_id = $request->get_param( 'id' );
            $count = 14; // images amount
            $time_step = 182; // in seconds

            $images[] = get_movie_thumbnail($kaltura_id, 1920, 1080, 50, 0);

            $vid_sec = $time_step;
            for ( $i = 1; $i <= $count; $i++ ) {
                $images[] = get_movie_thumbnail($kaltura_id, 1920, 1080, 50, $vid_sec);
                $vid_sec += $time_step;
            }

            return $images;
        }