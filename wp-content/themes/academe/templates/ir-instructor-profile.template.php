<?php
/**
 * Instructor Author Profile Template
 *
 * @since 3.5.0
 */

use InstructorRole\Modules\Classes\Instructor_Role_Profile;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Restrict access for students and guests
if (!is_user_logged_in() || is_user_in_role('student')) {
    wp_redirect(home_url());
}

$author_data                  = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
$instructor_course_statistics = Instructor_Role_Profile::get_instructor_course_statistics( $author_data->ID );
$instructor_social_links      = get_user_meta( $author_data->ID, 'ir_profile_social_links', true );
$is_own_profile = get_current_user_id() == $author_data->ID;

get_header();
?>
<?php if ($is_own_profile) { ?>
    <?php // AVATAR HANDLE START
    if ( isset( $_POST['avatar_manager_submit'] ) && $_POST['avatar_manager_submit'] ) {

        if ( ! function_exists( 'wp_handle_upload' ) )
            require_once( ABSPATH . 'wp-admin/includes/file.php' );

        if ( ! function_exists( 'wp_generate_attachment_metadata' ) )
            require_once( ABSPATH . 'wp-admin/includes/image.php' );

        // An associative array with allowed MIME types.
        $mimes = array(
            'bmp'  => 'image/bmp',
            'gif'  => 'image/gif',
            'jpe'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'tif'  => 'image/tiff',
            'tiff' => 'image/tiff'
        );

        // An associative array to override default variables.
        $overrides = array(
            'mimes'     => $mimes,
            'test_form' => false
        );

        // Handles PHP uploads in WordPress.
        $file_attr = wp_handle_upload( $_FILES['avatar_manager_import'], $overrides );

        if ( isset( $file_attr['error'] ) ) {
            // Kills WordPress execution and displays HTML error message.
            wp_die( $file_attr['error'],  __( 'Image Upload Error', 'avatar-manager' ) );
        }

        // An associative array about the attachment.
        $attachment = array(
            'guid'           => $file_attr['url'],
            'post_content'   => $file_attr['url'],
            'post_mime_type' => $file_attr['type'],
            'post_title'     => basename( $file_attr['file'] )
        );

        // Inserts the attachment into the media library.
        $attachment_id = wp_insert_attachment( $attachment, $file_attr['file'] );

        // Generates metadata for the attachment.
        $attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $file_attr['file'] );

        // Updates metadata for the attachment.
        wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

        // Sets user's avatar.
        avatar_manager_set_avatar( $author_data->ID, $attachment_id );
    }

    // AVATAR HANDLE END
    ?>
    <div class="modal ui overlay change-info">
        <?php icon('cross', 'close'); ?>

        <?php $education_list = get_user_meta( get_current_user_id(), 'ir_profile_education_list', true ); ?>
        <?php $social_links = get_user_meta( get_current_user_id(), 'ir_profile_social_links', true ); ?>
        <?php $description = get_user_meta( get_current_user_id(), 'description', true ); ?>

        <form id="editProfileForm" method="post" novalidate="novalidate" enctype="multipart/form-data">
            <div class="ir-profile-edit-sections">
                <h2><?php _e('Profile edit', 'academe-theme'); ?></h2>
                <input type="hidden" name="action" value="edit_teacher_profile" />
            </div>

            <div class="ir-profile-edit-sections">
                <p class="ir-subtitle">Introduction:</p>
                <textarea name="description"><?php echo $description; ?></textarea>
            </div>

            <div class="ir-profile-edit-sections">
                <p class="ir-subtitle">Education:</p>
                <div class="ir-profile-list-container">
                    <?php foreach ( $education_list as $single ) : ?>
                        <div class="ir-profile-list">
                            <div class="ir-profile-input">
                                <input type="text" name="ir_profile_education_list[]" class="ir-profile-input-list" size="50" value="<?php echo esc_attr( $single ); ?>"/>
                                <span class="ir-profile-remove-input dashicons dashicons-no"></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <span class="ir-profile-add-input dashicons dashicons-plus-alt"></span>
                </div>
            </div>

            <div class="ir-profile-edit-sections">
                <p class="ir-subtitle">Social links:</p>
                <div class="irp-social-type irp-fb">
                    <i class="ir-icon-Facebook"></i>
                    <input type="text" name="ir_profile_social_links[facebook]" id="ir_profile_social_links_facebook" value="<?php echo esc_attr( $social_links['facebook'] ); ?>">
                </div>
                <div class="irp-social-type irp-youtube">
                    <i class="ir-icon-YouTube"></i>
                    <input type="text" name="ir_profile_social_links[youtube]" id="ir_profile_social_links_youtube" value="<?php echo esc_attr( $social_links['youtube'] ); ?>">
                </div>
            </div>

            <input class="primary-btn" name="usermeta_manager_submit" type="submit" value="<?php _e('Update profile', 'academe-theme'); ?>">
        </form>
    </div>
<?php } ?>

<?php
/**
 * Instructor Profile Start
 *
 * @since 3.5.2
 *
 * @param array $author_data    Instructor user data.
 */
do_action( 'ir_action_profile_start', $author_data );
?>

    <div class="ir-profile">
        <?php
        /**
         * Instructor Profile Header Start
         *
         * @since 3.5.2
         *
         * @param array $author_data    Instructor user data.
         */
        do_action( 'ir_action_profile_header_start', $author_data );
        ?>

        <div class="irp-top">
            <div class="irp-container">
                <div class="irp-image <?php echo ($is_own_profile) ? 'change-photo' : ''; ?>">
                    <?php
                    echo get_avatar(
                        $author_data->ID,
                        220,
                        '',
                        '',
                        array(
                            'height' => 220,
                            'width'  => 220,
                        )
                    );
                    ?>
                    <form method="post" novalidate="novalidate" enctype="multipart/form-data" style="display:none;">
                        <input id="avatar-manager-upload" name="avatar_manager_import" type="file">
                        <input class="button" name="avatar_manager_submit" type="submit" value="true">
                    </form>
                </div>
            </div>
        </div>

        <?php
        /**
         * Instructor Profile Header End
         *
         * @since 3.5.2
         *
         * @param array $author_data    Instructor user data.
         */
        do_action( 'ir_action_profile_header_end', $author_data );
        ?>

        <?php
        /**
         * Instructor Profile Content Start
         *
         * @since 3.5.2
         *
         * @param array $author_data    Instructor user data.
         */
        do_action( 'ir_action_profile_content_start', $author_data );
        ?>

        <div class="irp-content">
            <div class="irp-container irp-flex">
                <div class="irp-left">
                    <div class="irp-info">
                        <h1>
                            <?php the_author_meta( 'display_name', $author_data->ID ); ?>
                        </h1>
                        <div class="irp-designation">
                            <?php echo esc_html( ir_get_profile_designation( $author_data ) ); ?>
                        </div>
                        <?php if ($is_own_profile) { ?>
                            <div class="change-profile-info secondary-btn">Edit profile</div>
                        <?php } ?>
                        <?php if (0 && defined( 'WDM_LD_COURSE_VERSION' ) && ! empty( $instructor_course_statistics['instructor_reviews_count'] ) ) : ?>
                            <div class="irp-rating">
                                <div class="">
								<span class="irp-avg-rating">
									<i class="ir-icon-Star"></i>
									<span><?php echo esc_attr( $instructor_course_statistics['avg_instructor_rating'] ); ?></span>
								</span>
                                    <span class="irp-total-rating">
									(<?php echo esc_attr( $instructor_course_statistics['instructor_reviews_count'] ); ?>)
								</span>
                                </div>
                            </div>
                        
                       
                        
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $instructor_course_statistics ) ) : ?>
                        <div class="irp-courses-info">
                            <?php if ( ! empty( $instructor_course_statistics['courses_offered'] ) ) : ?>
                                <div class="irp-courses-offered">
                                    <i class="ir-icon-Courses"></i>
                                    <label>
                                        <span><?php echo esc_html( $instructor_course_statistics['courses_offered'] ); ?></span> <?php esc_html_e( sprintf( '%s offered', \LearnDash_Custom_Label::get_label( 'lessons' ) ), 'wdm_instructor_role' ); ?>
                                    </label>
                                </div>
                                    
                            <?php endif; ?>
                            
                            <div style="color:white">
                                <style>
                                    .gamipress-user-points-description { margin: auto 10px;}
                                </style>
                             <?php echo do_shortcode( '[gamipress_points type="all" columns="1" thumbnail="yes" label="yes" current_user="yes" layout="left" align="none"]' ); ?>
                            </div>
                            
                            <?php if ( ! empty( $instructor_course_statistics['students_count'] ) ) : ?>
                                <div class="irp-enrolled-courses">
                                    <i class="ir-icon-Students"></i>
                                    <label>
                                        <span><?php echo esc_html( $instructor_course_statistics['students_count'] ); ?></span> <?php esc_html_e( 'Enrolled Students', 'wdm_instructor_role' ); ?>
                                    </label>
                                </div>
                            <?php endif; ?>
                            <?php if ( ! empty( $instructor_course_statistics['completed_course_per'] ) ) : ?>
                                <div class="irp-completed-courses">
                                    <i class="ir-icon-Percentage"></i>
                                    <label><span><?php echo esc_html( $instructor_course_statistics['completed_course_per'] ) . '%'; ?></span> <?php esc_html_e( sprintf( 'Students completed %s', \LearnDash_Custom_Label::get_label( 'courses' ) ), 'wdm_instructor_role' ); ?></label>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $instructor_social_links ) ) : ?>
                        <div class="irp-social">
                            <h2>
                                <?php esc_html_e( 'CONNECT WITH ME', 'wdm_instructor_role' ); ?>
                            </h2>
                            <?php if ( ! empty( $instructor_social_links['facebook'] ) ) : ?>
                                <div class="irp-social-type irp-fb">
                                    <i class="ir-icon-Facebook"></i>
                                    <a href="<?php echo sprintf( '//%s', esc_attr( $instructor_social_links['facebook'] ) ); ?>" target="_blank"><?php echo esc_attr( $instructor_social_links['facebook'] ); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if ( 0 && ! empty( $instructor_social_links['twitter'] ) ) : ?>
                                <div class="irp-social-type irp-twitter">
                                    <i class="ir-icon-Twitter"></i>
                                    <a href="<?php echo sprintf( '//%s', esc_attr( $instructor_social_links['twitter'] ) ); ?>" target="_blank"><?php echo esc_attr( $instructor_social_links['twitter'] ); ?></a>
                                </div>
                            <?php endif; ?>
                            <?php if ( ! empty( $instructor_social_links['youtube'] ) ) : ?>
                                <div class="irp-social-type irp-youtube">
                                    <i class="ir-icon-YouTube"></i>
                                    <a href="<?php echo sprintf( '//%s', esc_attr( $instructor_social_links['youtube'] ) ); ?>" target="_blank"><?php echo esc_attr( $instructor_social_links['youtube'] ); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="irp-right">
                    <div class="irp-right-content">
                        <span class="irp-tabs">
                            <span class="irp-active" data-id="irp-intro">
                                <?php esc_html_e( 'Introduction', 'wdm_instructor_role' ); ?>
                            </span>
                            <span data-id="irp-courses">
                                <?php
                                echo esc_html_x(
                                    \LearnDash_Custom_Label::get_label( 'lessons' ),
                                    'placeholder: Lessons',
                                    'wdm_instructor_role'
                                );
                                ?>
                            </span>
                            <?php if ( 0 && defined( 'WDM_LD_COURSE_VERSION' ) ) : ?>
                                <span data-id="irp-rrf">
                                    <?php esc_html_e( 'RATINGS & REVIEWS', 'wdm_instructor_role' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="irp-tabs-content">
                            <div class="irp-tab-content" data-id="irp-intro" style="display: block;">
                                <h3 class="irp-hidden-lg irp-title"><?php esc_html_e( 'INTRODUCTION', 'wdm_instructor_role' ); ?></h3>
                                <p>
                                    <?php the_author_meta( 'description', $author_data->ID ); ?>
                                </p>

                                <?php Instructor_Role_Profile::display_instructor_sections( $author_data->ID ); ?>

                            </div>

                            <div class="irp-tab-content" data-id="irp-courses">
                                <h3 class="irp-hidden-lg irp-title">
                                    <?php
                                    echo esc_html_x(
                                        \LearnDash_Custom_Label::get_label( 'lessons' ),
                                        'placeholder: Lessons',
                                        'wdm_instructor_role'
                                    );
                                    ?>
                                </h3>
                                <?php // echo do_shortcode( "[ld_course_list col=2 instructor={$author_data->ID}]" ); ?>
                                <?php teachers_courses_list($author_data->ID); ?>
                            </div>
                            <?php if ( 0 && defined( 'WDM_LD_COURSE_VERSION' ) ) : ?>
                                <div class="irp-tab-content" data-id="irp-rrf">
                                    <h3 class="irp-hidden-lg irp-title"><?php esc_html_e( 'RATINGS & REVIEWS', 'wdm_instructor_role' ); ?></h3>
                                    <?php Instructor_Role_Profile::display_instructor_ratings_graph_section( $author_data->ID ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        /**
         * Instructor Profile Content End
         *
         * @since 3.5.2
         *
         * @param array $author_data    Instructor user data.
         */
        do_action( 'ir_action_profile_content_end', $author_data );
        ?>

    </div>

<?php
/**
 * Instructor Profile End
 *
 * @since 3.5.2
 *
 * @param array $author_data    Instructor user data.
 */
do_action( 'ir_action_profile_end', $author_data );
?>

<?php
get_footer();
