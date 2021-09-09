<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php wp_get_document_title() ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Roboto&display=swap" rel="stylesheet">

    <link href="<?php echo get_template_directory_uri() . '/build/styles.css'; ?>" rel="stylesheet">
	<?php wp_head() ?>
</head>
<body <?php body_class('font-montserrat') ?>>
    <?php wp_body_open(); ?>