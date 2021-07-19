<?php

/**
 * Store notification content in DB
 */
add_action('learndash_notification_before_send_emails', 'learndash_notification_store', 10, 3);
function learndash_notification_store($emails, $model, $args) {
    foreach ( $emails as $email ) {
        global $wpdb;
        $model->populate_shortcode_data( $args );
        $user = get_user_by( 'email', $email );
        if ( is_object( $user ) ) {
            //check the subscription
//            $list = get_user_meta( $user->ID, 'learndash_notifications_subscription', true );
//            if ( isset( $list[ $this->trigger ] ) && absint( $list[ $this->trigger ] ) === 0 ) {
//
//            }

            $notification_link = create_notification_link($model, $args);
            $row = $wpdb->insert(
                $wpdb->prefix . ONSITE_NOTIFICATIONS_DB_TABLE,
                [
                    'user_id' => $user->ID,
                    'title' => do_shortcode($model->post->post_title),
                    'message' => do_shortcode($model->post->post_content),
                    'icon' => get_field('onsite_icon', $model->post->ID),
                    'link' => $notification_link,
                    'shortcode_data' => '',
                    'sent_on' => time(),
                    'sent_on_readable' => current_time('mysql'),
                    'read_on' => NULL,
                    'read_on_readable' => NULL,
                ],
                [ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );
        }

    }
}

/**
 * Generate notification link based on trigger type
 * @param $model
 * @param $args
 * @return string|WP_Error
 */
function create_notification_link($model, $args) {
    $url = '#';

    switch ($model->trigger) {
        case 'publish_course':
        case 'enroll_course':
        case 'complete_course':
        case 'course_expires':
        case 'course_expires_after':
            $url = get_post_permalink($args['course_id']);
            break;
        case 'complete_lesson':
        case 'lesson_available':
            $url = get_post_permalink($args['lesson_id']);
            break;
        case 'complete_topic':
            $url = get_post_permalink($args['topic_id']);
            break;
        case 'pass_quiz':
        case 'fail_quiz':
        case 'submit_quiz':
        case 'complete_quiz':
        case 'submit_essay':
        case 'essay_graded':
            $url = get_post_permalink($args['quiz_id']);
            break;
        case 'upload_assignment':
        case 'approve_assignment':
            $url = get_post_permalink($args['assignment_id']);
            break;
        case 'publish_movie':
            $url = get_post_permalink($args['movie_id']);
            break;
        case 'publish_teaching_guide':
            $url = get_post_permalink($args['teaching_guide_id']);
            break;
        case 'session_expires':
            $url = get_post_permalink($args['session_id']);
            break;
        case 'enroll_group':
        case 'not_logged_in':
        default :
            $url = '#';
            break;
    }

    return $url;
}

/**
 * Mark clicked notification as "read"
 */
add_action("wp_ajax_learndash_notification_read" , "learndash_notification_read");
add_action('wp_ajax_nopriv_learndash_notification_read', 'learndash_notification_read');
function learndash_notification_read () {
    global $wpdb;
    $wpdb->update( $wpdb->prefix . ONSITE_NOTIFICATIONS_DB_TABLE,
        [
            'read_on' => time(),
            'read_on_readable' => current_time('mysql')
        ],
        [ 'ID' => $_POST['notification_id'] ]
    );

    echo json_encode(['success' => true]);
    wp_die();
}

/**
 * Ajax load more notifications
 */
add_action("wp_ajax_learndash_notification_load" , "learndash_notification_load");
add_action('wp_ajax_nopriv_learndash_notification_load', 'learndash_notification_load');
function learndash_notification_load () {
    $notifications = learndash_notifications_by_user($_GET['offset']);
    ob_start();
    foreach ($notifications as $notification) {
        the_single_notification($notification);
    }
    $data = ob_get_clean();
    echo json_encode([
        'success' => true,
        'data' => $data,
        'offset' => $_GET['offset'] + count($notifications)
    ]);
    wp_die();
}

/**
 * Get first X notifications for current user
 * @param int $offset
 * @param int $limit
 * @return array|object|null
 */
function learndash_notifications_by_user($offset = 0, $limit = 10) {
    global $wpdb;

    $user_id = get_current_user_id();
    if (!$user_id) {
        return NULL;
    }

    $table_notifications = $wpdb->prefix . ONSITE_NOTIFICATIONS_DB_TABLE;

    return $wpdb->get_results( $wpdb->prepare(
        "
		SELECT *
		FROM $table_notifications as n
		WHERE n.user_id = %d
		ORDER BY id DESC
		LIMIT ${limit} OFFSET ${offset};
		", $user_id)
    );
}

/**
 * Get total number of notifications for current user
 * @return string|null
 */
function learndash_notifications_count_by_user() {
    global $wpdb;

    $user_id = get_current_user_id();
    if (!$user_id) {
        return NULL;
    }

    $table_notifications = $wpdb->prefix . ONSITE_NOTIFICATIONS_DB_TABLE;

    return $wpdb->get_var( $wpdb->prepare(
        "
		SELECT COUNT('id')
		FROM $table_notifications as n
		WHERE n.user_id = %d;
		", $user_id)
    );
}

/**
 * Get number of unread notifications for current user
 * @return string|null
 */
function learndash_notifications_unread_count_by_user() {
    global $wpdb;

    $user_id = get_current_user_id();
    if (!$user_id) {
        return NULL;
    }

    $table_notifications = $wpdb->prefix . ONSITE_NOTIFICATIONS_DB_TABLE;

    return $wpdb->get_var( $wpdb->prepare(
        "
		SELECT COUNT('id')
		FROM $table_notifications as n
		WHERE n.user_id = %d AND (n.read_on IS NULL OR n.read_on = '');
		", $user_id)
    );
}

/**
 * Render single notification in list
 * @param $notification
 */
function the_single_notification($notification) {
?>
    <div class="notification-item <?php echo $notification->read_on ? '' : 'unread'; ?>" data-id="<?php echo $notification->id; ?>" data-href="<?php echo $notification->link; ?>">
        <img class="icon icon-24" src="<?php echo wp_get_attachment_image_url($notification->icon); ?>" />
        <div class="notification-content-wrap">
            <div class="title"><?php echo $notification->title; ?></div>
            <div class="time"><?php echo human_time_diff($notification->sent_on, time()) . ' ' . __('ago', 'onsite-notifications-plugin'); ?></div>
        </div>
    </div>
<?php
}

/**
 * Get rendered list of notifications
 */
function get_learndash_notifications_modal_template() {
    include ONSITE_NOTIFICATIONS_PLUGIN_DIR . 'templates/modal.php';
}

/**
 * Modify recipient emails for new types
 */
add_filter( 'learndash_notification_recipients_emails', 'add_custom_recipients_emails', 10, 4 );
function add_custom_recipients_emails($emails, $recipients, $user_id, $course_id) {
    foreach ( $recipients as $recipient ) {
        switch ( $recipient ) {
            case 'teachers':
                $users = get_users(
                    array(
                        'role' => 'wdm_instructor',
                    )
                );
                foreach ( $users as $user ) {
                    $emails[] = $user->user_email;
                }
                break;
            case 'students':
                $users = get_users(
                    array(
                        'role' => 'subscriber',
                    )
                );
                foreach ( $users as $user ) {
                    $emails[] = $user->user_email;
                }
                break;
        }
    }
    return $emails;
}

/**
 * Modify recipients list in admin panel
 */
add_filter( 'learndash_notifications_recipients', 'add_custom_recipients_in_recipients_list', 10, 1 );
function add_custom_recipients_in_recipients_list( $recipients ) {
    // Add all teachers as a recipients.
    if ( ! in_array( 'teachers', $recipients ) ) {
        $recipients['teachers'] = __( 'All teachers', 'onsite-notifications-plugin' );
    }
    if ( ! in_array( 'students', $recipients ) ) {
        $recipients['students'] = __( 'All students', 'onsite-notifications-plugin' );
    }

    return $recipients;
}

/**
 * Modify notifications list in admin panel
 */
add_filter( 'learndash_notifications_triggers', 'add_custom_triggers_in_triggers_list', 10, 1 );
function add_custom_triggers_in_triggers_list( $triggers ) {
    if ( ! in_array( 'publish_movie', $triggers ) ) {
        $triggers['publish_movie'] = __( 'New movie was uploaded', 'onsite-notifications-plugin' );
    }
    if ( ! in_array( 'publish_course', $triggers ) ) {
        $triggers['publish_course'] = __( 'New lesson was publiched', 'onsite-notifications-plugin' );
    }
    if ( ! in_array( 'publish_teaching_guide', $triggers ) ) {
        $triggers['publish_teaching_guide'] = __( 'New TG was published', 'onsite-notifications-plugin' );
    }
    if ( ! in_array( 'session_expires', $triggers ) ) {
        $triggers['session_expires'] = __( '"X" days before session expires', 'onsite-notifications-plugin' );
    }

    return $triggers;
}

/**
 * Modify notifications settings fields in admin panel
 */
add_filter( 'learndash_notification_settings', 'override_notification_settings', 10, 1 );
function override_notification_settings( $settings ) {
    $settings['session_expires_days'] = array(
        'type'       => 'text',
        'title'      => __( 'Before how many days?', 'learndash-notifications' ),
        'help_text'  => __( 'Setting associated with the email trigger setting above.', 'learndash-notifications' ),
        'label'      => __( 'day(s)', 'learndash-notifications' ),
        'hide'       => 1,
        'hide_delay' => 1,
        'size'       => 2,
        'parent'     => 'session_expires',
    );

    $settings['delay']['hide_on'][] = 'session_expires';
    $settings['delay_unit']['hide_on'][] = 'session_expires';

    return $settings;
}

/**
 * Modify shortcodes instructions in admin panel
 */
add_filter( 'learndash_notifications_shortcodes_instructions', 'add_custom_shortcodes_instructions', 10, 1 );
function add_custom_shortcodes_instructions( $instructions ) {

    $user_shortcode = array(
        '[ld_notifications field="user" show="username"]'     => __( 'Display user\'s username.', 'learndash-notifications' ),
        '[ld_notifications field="user" show="email"]'        => __( 'Display user\'s email.', 'learndash-notifications' ),
        '[ld_notifications field="user" show="display_name"]' => __( 'Display user\'s display name.', 'learndash-notifications' ),
        '[ld_notifications field="user" show="first_name"]'   => __( 'Display user\'s first name.', 'learndash-notifications' ),
        '[ld_notifications field="user" show="last_name"]'    => __( 'Display user\'s last name.', 'learndash-notifications' ),
    );

    $course_basic_shortcode = array(
        '[ld_notifications field="course" show="title"]' => __( 'Display course title.', 'learndash-notifications' ),
        '[ld_notifications field="course" show="url"]'   => __( 'Display course URL.', 'learndash-notifications' ),
    );

    $movie_shortcode = array(
        '[ld_notifications field="movie" show="title"]' => __( 'Display movie title.', 'learndash-notifications' ),
        '[ld_notifications field="movie" show="url"]'   => __( 'Display movie URL.', 'learndash-notifications' ),
    );

    $teaching_guide_shortcode = array(
        '[ld_notifications field="teaching_guide" show="title"]' => __( 'Display teaching guide title.', 'learndash-notifications' ),
        '[ld_notifications field="teaching_guide" show="url"]'   => __( 'Display teaching guide URL.', 'learndash-notifications' ),
    );

    $session_shortcode = array(
        '[ld_notifications field="session" show="title"]' => __( 'Display session title.', 'learndash-notifications' ),
        '[ld_notifications field="session" show="url"]'   => __( 'Display session URL.', 'learndash-notifications' ),
    );

    $instructions['publish_movie'] = array_merge( $user_shortcode, $movie_shortcode );
    $instructions['publish_teaching_guide'] = array_merge( $user_shortcode, $teaching_guide_shortcode );
    $instructions['publish_course'] = array_merge( $user_shortcode, $course_basic_shortcode );
    $instructions['session_expires'] = array_merge( $user_shortcode, $session_shortcode );

    return $instructions;
}

/**
 * Add new hooks on first publish CPT
 */
add_action('publish_movie', 'add_hooks_on_post_published_first_time', 10, 2);
add_action('publish_teaching-guide', 'add_hooks_on_post_published_first_time', 10, 2);
add_action('publish_clip', 'add_hooks_on_post_published_first_time', 10, 2);
add_action('publish_sfwd-courses', 'add_hooks_on_post_published_first_time', 10, 2);
function add_hooks_on_post_published_first_time($post_id, $post) {
    if ( time() - strtotime($post->post_date_gmt) > 30 ) { //post published more than 30 seconds ago
        return NULL;
    }
    $already_published_meta = get_post_meta($post_id, 'already_published', true);
    if( empty( $already_published_meta ) && !wp_is_post_revision( $post_id ) ) {
        update_post_meta($post_id, 'already_published', '1');
        /**
         * Fires after post was published first time.
         */
        do_action($post->post_type . '_was_published', $post);
    }
}
