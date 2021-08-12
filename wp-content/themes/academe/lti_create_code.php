<?php 
    require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
    require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/image.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/file.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/media.php');
    require_once(ABSPATH.'wp-admin/includes/user.php');

$id = $_GET['id'];
$time = $_GET['time'];
$code = $_GET['code'];

update_post_meta($id, 'lti-code-'.$code, $time);

echo json_encode(array(
    'status'  => true,
    'msg'     => ''
));
exit;
?>