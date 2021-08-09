<?php ob_start(); /* Template Name: LTI */ ?>
<?php if (!is_user_logged_in()) {
    wp_redirect(home_url());
} ?>

<?php get_header(); ?>

<script type='text/javascript' id='wpProQuiz_front_javascript-js-extra'>
    /* <![CDATA[ */
    var WpProQuizGlobal = {
        "ajaxurl":"<?php echo str_replace( array( 'http:', 'https:' ), array( '', '' ), admin_url( 'admin-ajax.php' ) ); ?>",
        "loadData":"Loading",
        "questionNotSolved":"You must answer this question.",
        "questionsNotSolved":"You must answer all questions before you can complete the quiz.",
        "fieldsNotFilled":"All fields have to be filled."
    };
    /* ]]> */
</script>
<script src="/wp-content/plugins/sfwd-lms/includes/lib/wp-pro-quiz/js/wpProQuiz_front.min.js"></script>
<script src="/wp-includes/js/jquery/ui/core.min.js?ver=1.12.1"></script>
<script src="/wp-includes/js/jquery/ui/mouse.min.js?ver=1.12.1"></script>
<script src="/wp-includes/js/jquery/ui/sortable.min.js?ver=1.12.1"></script>
<script src="/wp-content/plugins/sfwd-lms/includes/lib/wp-pro-quiz/js/jquery.ui.touch-punch.min.js?ver=0.2.2"></script>
<script src="https://app.annoto.net/annoto-bootstrap.js"></script>

<?php 
$code = $_GET['code']; 
$meta_key = 'lti-code-'.$code;

global $wpdb;
 $post_ID = $wpdb->get_var( $wpdb->prepare("SELECT post_ID FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1" , $meta_key) );
 if ( empty($post_ID) )
    header("Location: /");
 $time = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1" , $meta_key) );
 $custom_fields = get_fields($post_ID);
 $kalturaID = $custom_fields['kaltura_id'];
 $partner_id = get_field('partner_id', 'option');

 if ( get_field('kaltura_player_id', 'option') ) {
    $kalturaPlayerID = get_field('kaltura_player_id', 'option');
} else {
    $kalturaPlayerID = 46602743;
}
?>
<?php
if ( $time < current_time('d/m/Y H:i') ) { ?>
    <script src="https://cdnapisec.kaltura.com/p/<?php print $partner_id; ?>/sp/253884200/embedIframeJs/uiconf_id/<?php echo $kalturaPlayerID; ?>/partner_id/<?php print $partner_id; ?>"></script>                
    <div class="player">
        <div id="kaltura_player" style="width:600px;height:480px; margin:0 auto;">
    </div>
    
    </div>
    <?php $client = get_kaltura_session(); ?>
    <script>
            kWidget.embed({
            targetId: "kaltura_player",
            wid: "_<?php print $partner_id; ?>",
            uiconf_id: "<?php echo $kalturaPlayerID; ?>",
            entry_id: "<?php echo $kalturaID; ?>",
            flashvars: {
                "ks": "<?php echo $client->getKS(); ?>",
                "applicationName": "mediaspace",
                "playbackContext": "",
                "disableAlerts": "false",
                "externalInterfaceDisabled": "false",
                "autoPlay": "false",
                "streamerType": "auto",
                "localizationCode": "he_IL",
                "leadWithHTML5": "true",
                "sideBarContainer": {"plugin": "true", "position": "left", "clickToClose": "true"},
                "chapters": {"plugin": "true", "layout": "vertical", "thumbnailRotator": "false"},
                "streamSelector": {"plugin": "true"},
            }
        })
    </script>
<?php }
else
    echo '<span style="color:white;">Wait for this date: ' . $time . "</span>";
?>

<script>
  // Application configuration
  var config = {

    /* Below are the only Mandatory configuration options */
    clientId: '', /* set to your client id after registration. */
    widgets: [
      {
        player: {
          type:    'kaltura', /* type of the player */
          element: 'kaltura_player' /* DOM element or id of the player */
        },
        timeline: {
            overlayVideo: true
        }
      }
    ],
    demoMode: true, /* set to false when you have your clientId */

  };

  window.Annoto.boot(config);
</script>

<?php get_footer(); ?>