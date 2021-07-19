<?php
//quiz-reporting-extension/assets/dist/css

add_filter( 'style_loader_src', 'academe_replace_stylesheet', 10, 2 );
function academe_replace_stylesheet( $stylesheet_src, $handle ){

    if( 'qre_public_css' == $handle ){
         $stylesheet_src = get_template_directory_uri() . '/assets/css/quiz-reporting-plugin.css';
    }
    return $stylesheet_src;
}