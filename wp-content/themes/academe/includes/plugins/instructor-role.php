<?php
/**
 * Enable instructors access to custom post types
 * @author  Kumar Rajpurohit
 */
function ir_allow_cpt( $post_types_allowed ) {
    $post_types_allowed[] = 'movie';
    $post_types_allowed[] = 'teaching-guide';
    $post_types_allowed[] = 'session';
    $post_types_allowed[] = 'clip';

    return $post_types_allowed;
}
add_filter('wdmir_set_post_types', 'ir_allow_cpt');
add_filter('wdmir_exclude_post_types', 'ir_allow_cpt');

/**
 * Enable instructors access to CPT screens
 * @author  Kumar Rajpurohit
 */
add_filter('wdmir_add_dash_tabs', 'ir_show_cpt_in_admin_menu');
function ir_show_cpt_in_admin_menu($tabs) {
    $tabs[] = 'edit.php?post_type=clip';

    return $tabs;
}

/**
 * Allow access to CPT screens
 * @author  Kumar Rajpurohit
 */
add_filter('ir_filter_deny_page_access', 'ir_allow_cpt_screens_access', 10, 2);
function ir_allow_cpt_screens_access($restict_access, $page_data) {
    global $current_screen;
    if (! wdm_is_instructor()) {
        return $restict_access;
    }

    // Add the CPT slug and screen ID to allow access
    if ('clip' == $page_data['get_post_type'] || 'clip' == $current_screen->post_type) {
        $restict_access = false;
    }
    return $restict_access;
}

/**
 * Change path to profile template
 */
function add_instructor_profile_template_changed( $template ) {
    global $wp_query, $ld_course_grid_assets_needed;

    // Check if instructor profile template.
    if ( $wp_query->is_author() && get_query_var( 'ir_instructor_profile' ) ) {
        // Get current user details.
        $curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );

        // Check if current user is instructor.
        if ( in_array( 'wdm_instructor', $curauth->roles ) ) {
            $ld_course_grid_assets_needed = true;
            $template = get_template_directory() . '/templates/ir-instructor-profile.template.php';
        }
    }
    return $template;
}

function rewrite_profile_template() {
    add_filter( 'template_include', 'add_instructor_profile_template_changed', 999, 1 );
}
add_action( 'init', 'rewrite_profile_template' );

/**
 * Add rewrite rule for custom url (/teacher/<user_nicename>)
 */
function add_profile_rewrite_rule() {
    add_rewrite_rule(
        '^teacher/([^/]+)/?$',
        'index.php?author_name=$matches[1]&ir_instructor_profile=true',
        'top'
    );
}
add_action( 'init', 'add_profile_rewrite_rule', 10 );

/**
 * Remove rewrite for /instructor/<user_nicename>)
 */
remove_filters_with_method_name( 'init', 'add_profile_rewrite_rule', 10 );

/**
 * Change instructor's public title
 */
function change_designation( $designation, $userdata ) {
    $designation = '';

    $role = get_role( $userdata->roles[0] );
    switch ( $role->name ) {
        case 'wdm_instructor':
            $designation = __( 'Teacher', 'wdm_instructor_role' );
            break;

        case 'administrator':
            $designation = __( 'Administrator', 'wdm_instructor_role' );
            break;

        case 'editor':
            $designation = __( 'Editor', 'wdm_instructor_role' );
            break;

        case 'subscriber':
            $designation = __( 'Student', 'wdm_instructor_role' );
            break;
    }

    return $designation;
}
add_filter( 'ir_filter_profile_designation', 'change_designation', 10, 2 );

/**
 * Disable loading plugin's profile styles
 * Custom profile styles loads with webpack
 */
function override_ir_styles() {
    wp_dequeue_style( 'ir-style' );
}
add_action( 'wp_enqueue_scripts', 'override_ir_styles', 100 );

add_action("wp_ajax_edit_teacher_profile" , "edit_teacher_profile");
add_action('wp_ajax_nopriv_edit_teacher_profiles', 'edit_teacher_profile');
function edit_teacher_profile() {

    if ( array_key_exists( 'ir_profile_social_links', $_POST ) && !empty( $_POST['ir_profile_social_links'] ) ) {
        $social_links = array(
            'facebook' => empty( $_POST['ir_profile_social_links']['facebook'] ) ? '' : $_POST['ir_profile_social_links']['facebook'],
            'youtube'  => empty( $_POST['ir_profile_social_links']['youtube'] ) ? '' : $_POST['ir_profile_social_links']['youtube'],
        );
        // Update.
        update_user_meta(get_current_user_id(), 'ir_profile_social_links', $social_links);
    }

    if ( array_key_exists( 'ir_profile_education_list', $_POST ) && !empty( $_POST['ir_profile_education_list'] ) ) {
        update_user_meta(get_current_user_id(), 'ir_profile_education_list', $_POST['ir_profile_education_list']);
    }

    if ( isset( $_POST['description'] ) && !empty( $_POST['description'] ) ) {
        update_user_meta(get_current_user_id(), 'description', $_POST['description']);
    }

    echo json_encode([
        'success' => 'true'
    ]);

    wp_die();
}

add_filter('pre_get_posts', 'clips_for_current_teacher');
function clips_for_current_teacher($query) {
    global $pagenow;

    if( 'edit.php' != $pagenow || !$query->is_admin )
        return $query;

    if( !current_user_can( 'edit_others_posts' ) ) {
        global $user_ID;
        $query->set('author', $user_ID );
    }
    return $query;
}
