<?php
/*
Plugin Name: Kaltura data
Plugin URI: /
Description: Kaltura data System
Version: 1.0
Author: NetGateComUa
Author URI: http://netgate.com.ua
License: GPL2
*/

?>
<?php
if (!defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_PLUGIN_URL'))
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
if (!defined('WP_PLUGIN_DIR'))
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
require_once ABSPATH . 'wp-admin/includes/file.php';


?>
<?php

function formatMilliseconds($milliseconds)
{
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $milliseconds = $milliseconds % 1000;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%u:%02u:%02u';
    $time = sprintf($format, $hours, $minutes, $seconds);
    return rtrim($time, '0');
}

function deactivate () {
    $args = array (
        'post_type' => 'movie',
        'nopaging' => true
    );
    $query = new WP_Query ($args);
    while ($query->have_posts ()) {
        $query->the_post ();
        $id = get_the_ID ();
        if($id!='2811')
        wp_delete_post ($id, true);
    }
    wp_reset_postdata ();
}


?>
<?php /* register menu item */
function msp_Kalturadata_admin_menu_setup()
{
    add_submenu_page(
        'edit.php?post_type=movie',
        'Import movies',
        'Import movies',
        'manage_options',
        'msp_Kalturadata',
        'msp_Kalturadata_admin_page_screen'
    );
}

add_action('admin_menu', 'msp_Kalturadata_admin_menu_setup');

function msp_Kalturadata_admin_page_screen()
{
    global $submenu;
    $page_data = array();

    //deactivate();
    //die();
    global $wpdb; ?>
    <link href="<?php echo plugin_dir_url(__FILE__) . 'css/bootstrap.css'; ?>" rel="stylesheet"/>
    <style>
    .content{
        margin:20px;
    }
    .button_group{
        margin:10px 0;
    }
    .csv_form{
        border:1px solid #ccc;
        padding: 10px;
        width:fit-content;
    }
    </style>

    <div class="content">
        <div class="button_group">
            <a href="/wp-content/uploads/kaltura-data/movies_template_Ks5p9fkwBl.csv" class="btn btn-primary download_template" download="movies_sample.csv">Download csv template</a>
            <!-- <a href="--><?php //print $_SERVER['REQUEST_URI']; ?><!--&action=deleteall" onclick="return confirm('Are you sure?')" class="btn btn-danger clear_all_movies">Delete all films</a> -->
        </div>
        <p>Send .csv file to import movies</p>
        <form action="" method="post" name="csv_form" class="csv_form" enctype="multipart/form-data">
            <input type="file" name="csvfile" value="" class="file"/>
            <input type="submit" value="Send" class="btn btn-primary"/>

        </form>
    </div>
    <?php if (!empty($_GET['action']) && ($_GET['action']=="deleteall")){
        deactivate(); ?>
        <div class="alert alert-success">All films was deleted</div>
    <?php }

    /**
     * CSV fields:
     * 0  => Movie title
     * 1  => Year
     * 2  => Kaltura ID
     * 3  => Provider
     * 4  => Duration
     * 5  => Time (not used)
     * 6  => Rate
     * 7  => Warning
     * 8  => Genre
     * 9  => Faculty/Grade
     * 10 => Subject
     * 11 => Topic
     * 12 => Tags
     * 13 => Origin country
     * 14 => Language
     * 15 => Dubbing Language
     * 16 => Subtitles
     * 17 => Synopsis
     * 18 => Director
     * 19 => Cast
     */

    // UPLOAD CSV AND IMPORT MOVIES:
    if (!empty($_FILES)){
        $table=array();
        $row = 0;
        if (($handle = fopen($_FILES["csvfile"]["tmp_name"], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                echo "<pre>".print_r($data,true)."</pre>";
                //echo "<p> $num полей в строке $row: <br /></p>\n";

                if ($row>0) {
                    //echo "<pre>".print_r($data,true)."</pre>";
                    $table[$row]['post_title'] = $data[0];
                    $table[$row]['year'] =  $data[1];
                    $table[$row]['kaltura_id'] = $data[2];
                    $table[$row]['provider'] = $data[3];
                    $table[$row]['duration'] = $data[4];
                    $table[$row]['time'] = $data[5];
                    $table[$row]['rate'] = $data[6];
                    $table[$row]['warning'] = $data[7];

                    $taxonomies_list = [ '8' => 'genre', '9' => 'faculty', '10' => 'subject', '11' => 'topic' ];
                    foreach ($taxonomies_list as $key => $taxonomy) {
                        if ($data[$key]) {
                            $term_names = explode(',', $data[$key]);
                            if ($term_names) {
                                foreach ($term_names as $term_name) {
                                    $term = get_term_by('name', trim($term_name), $taxonomy);
                                    if ($term) {
                                        $table[$row][$taxonomy][] = $term->term_id;
                                    } else {
                                        $new_term = wp_insert_category([
                                            'cat_name' => trim($term_name),
                                            'taxonomy' => $taxonomy
                                        ]);
                                        $table[$row][$taxonomy][] = $new_term;
                                    }
                                }
                            }
                        }
                    }

                    $table[$row]['post_tags'] = explode(',', $data[12]);
                    $table[$row]['country'] = $data[13];
                    $table[$row]['lang'] = $data[14];
                    $table[$row]['dubbing_lang'] = $data[15];
                    $table[$row]['subtitles'] = $data[16];
                    $table[$row]['post_content'] = $data[17];
                    $table[$row]['director'] = $data[18];
                    $table[$row]['actors'] = $data[19];

                }
                $row++;
            }
            fclose($handle);
        }

        foreach($table as $item) {
            $data = array(
                //'post_date'        => $post_date,
                //'post_date_gmt'    => $post_date,
                'post_title' => wp_strip_all_tags($item['post_title']),
                'post_content' => wp_strip_all_tags($item['post_content']),
                'post_status' => 'publish',
                'comment_status' => 'closed',
                //'post_name' => wp_strip_all_tags($item['post_title']),
                'post_type' => 'movie',
                'tags_input' => $item['post_tags'],
                'tax_input' =>[
                    'genre' => $item['genre'],
                    'faculty' => $item['faculty'],
                    'subject' => $item['subject'],
                    'topic' => $item['topic']
                ]
            );

            $post_id = wp_insert_post($data);
            update_field( "year", $item['year'], $post_id );
            update_field( "kaltura_id", $item['kaltura_id'], $post_id );
            update_field( "provider", $item['provider'], $post_id );
            update_field( "duration", $item['duration'], $post_id );
            update_field( "time", $item['time'], $post_id );
            update_field( "rate", $item['rate'], $post_id );
            update_field( "warning", $item['warning'], $post_id );
            update_field( "country", $item['country'], $post_id );
            update_field( "lang", $item['lang'], $post_id );
            update_field( "dubbing_lang", $item['dubbing_lang'], $post_id );
            update_field( "subtitles", $item['subtitles'], $post_id );
            update_field( "director", $item['director'], $post_id );
            update_field( "actors", $item['actors'], $post_id );

        } ?>
        <div class="alert alert-success"><?php print $row-1  ?> films have been successfully added</div>
    <?php
    }
}

//IMPORT MOVIE DATA (FROM KALTURA API) USING FORM ON MOVIE EDIT PADE:

function movie_data_import_from_kaltura_meta_box() {
    add_meta_box(
        'movieImportFromKaltura',
        'Import movie data from Kaltura',
        'movie_post_type_print_custom_form',
        'movie',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'movie_data_import_from_kaltura_meta_box');

function movie_post_type_print_custom_form() { ?>
    <div style="display: flex; justify-content: space-between; align-items: center">
        <input type="text" name="movie-id" id="movieId" placeholder="Kaltura movie ID" style="width: 150px" />
        <div style="display: flex; justify-content: flex-end;">
            <span class="spinner"></span>
            <input id="getMovieDataBtn" type="button" class="button" value="Import">
        </div>
    </div>

    <script type="text/javascript" >
        jQuery(document).ready(function($) {
            $('#getMovieDataBtn').on('click', function () {
                var _this = $(this);
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType : 'json',
                    data: {
                        'action': 'get_movie_data_ajax_action',
                        'movie_id': $('#movieId').val()
                    },
                    beforeSend: function() {
                        _this.addClass('disabled');
                        _this.siblings('.spinner').addClass('is-active');
                    },
                    success: function (response) {
                        _this.removeClass('disabled');
                        _this.siblings('.spinner').removeClass('is-active');

                        $('[data-name="kaltura_id"].acf-field .acf-input input').val(response.id);
                        $('[data-name="year"].acf-field .acf-input input').val(response.שנתהפקה);
                        $('[data-name="duration"].acf-field .acf-input input').val(response.msDuration);
                        $('[data-name="country"].acf-field .acf-input input').val(response.country);
                        $('[data-name="lang"].acf-field .acf-input input').val();
                        $('[data-name="director"].acf-field .acf-input input').val(response.במאי);
                        $('[data-name="actors"].acf-field .acf-input input').val(response.שחקנים);
                        //$('[data-name="title_en"].acf-field .acf-input input').val(response.שםהסרט);
                        //$('[data-name="count_views"].acf-field .acf-input input').val(response.views);
                        $('input#title').val(response.name).siblings('#title-prompt-text').attr('id', 'screen-reader-text'); //replace with screen-reader-text
                        tinymce.get('content').setContent(response.description);
                        $('input#new-tag-post_tag').val(response.tags).siblings('.button.tagadd').trigger('click');
                        $(window).scrollTop(0);
                        alert('Data successfully imported. Don\'t forget to save the post!');
                    }
                });
            });
        });
    </script>
<?php } // end movie_post_type_print_custom_form()

add_action("wp_ajax_get_movie_data_ajax_action" , "get_movie_data_from_kaltura");
function get_movie_data_from_kaltura() {

    $movie_id = $_POST['movie_id'] ?? NULL;
    $movie_data = [];
    if ($movie_id) {
        $client = get_kaltura_session();

        $partner_id = get_field('partner_id', 'option');

        //Get movie metadata:
        $metadata_filter = new KalturaMetadataFilter();
        $metadata_filter->objectIdEqual = $_POST['movie_id'] ?? 0;
        $metadata_result = $client->metadata->listAction($metadata_filter);
        $metadata = $metadata_result->objects ? xmlstring2array($metadata_result->objects[0]->xml) : [];


        //Get movie info:
        $movie_filter = new KalturaMediaEntryFilter();
        $movie_filter->idEqual = $_POST['movie_id'] ?? 0;
        $movie_result = $client->media->listAction($movie_filter);
        $movie = $movie_result->objects ? $movie_result->objects[0] : [];

        $movie_data = array_merge((array)$movie, $metadata);
        $movie_data['msDuration'] = formatMilliseconds($movie_data['msDuration']);
    }

    echo json_encode($movie_data);
    wp_die();
}

function xmlstring2array($string)
{
    $xml   = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);

    $array = json_decode(json_encode($xml), TRUE);

    return $array;
}

function get_movie_thumbnail($movie_id, $width = 1920, $height = 1080, $quality = 45, $vid_sec = 0) {
    $partner_id = get_field('partner_id', 'option');
    $link = "https://cdnapisec.kaltura.com/p/$partner_id/thumbnail/entry_id/$movie_id/width/$width/height/$height/type/1/quality/$quality";
    if ($vid_sec) {
        $link .= '/vid_sec/' . $vid_sec;
    }
    return $link;
}

function get_kaltura_session() {
    require_once dirname(__FILE__) . '/KalturaClient.php';

    $partner_id = get_field('partner_id', 'option');
    $config = new KalturaConfiguration();
    $config->setServiceUrl('https://www.kaltura.com');
    $client = new KalturaClient($config);
    $ks = $client->session->start(
        "e0ea1beaae9d9aa8407f9e34331ed690",
        "MovielandAcademy",
        KalturaSessionType::ADMIN,
        $partner_id
    );
    $client->setKS($ks);

    return $client;
}

function get_kaltura_ks() {
    require_once dirname(__FILE__) . '/KalturaClient.php';

    $partner_id = get_field('partner_id', 'option');
    $config = new KalturaConfiguration();
    $config->setServiceUrl('https://www.kaltura.com');
    $client = new KalturaClient($config);
    $ks = $client->session->start(
        "e0ea1beaae9d9aa8407f9e34331ed690",
        "MovielandAcademy",
        KalturaSessionType::ADMIN,
        $partner_id
    );
    $client->setKS($ks);

    return $ks;
}

function format_time_to_seconds($time_string) {
    return strtotime("1970-01-01 $time_string UTC");
}

// CLIPS:
add_action('admin_init', 'add_metabox_clips_js_handler');
function add_metabox_clips_js_handler(){
    $screens = array( 'clip' );
    add_meta_box( 'jsHandlerSection', 'JS handler', 'handle_import_button_click', $screens );
}

// HTML код блока
function handle_import_button_click() { ?>
    <script type="text/javascript" >
        jQuery(document).ready(function($) {
            $( '#jsHandlerSection' ).hide();
            // updating by user
            $('#movieSelectedName [name="acf[field_60ad1e9cc509e]"]').on('click', function(e){
                $('.the-tags').val('');
                $('.tagchecklist').empty();
                $('input#new-tag-post_tag').val('test1, test2');
                var selectedMovie = $('[data-name="movie_id"] .acf-input select').val();
                if (!selectedMovie) {
                    alert('The movie is not selected');
                } else {
                    //Clear old values
                    $('.tagchecklist').empty();
                    $('#topicchecklist input[type="checkbox"]').prop( "checked", false);

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            'action': 'import_clip_data',
                            'movie_id': selectedMovie,
                        },
                        success: function (data) {
                            //Update tags
                            $('input#new-tag-post_tag').val(data.tags).siblings('.button.tagadd').trigger('click');

                            //Update topics
                            data.topics.forEach(function(topic) {
                                $( "#in-topic-"+topic ).prop( "checked", true );
                            });

                            $(window).scrollTop(0);
                            alert('Tags and topics successfully imported. Don\'t forget to save the post!');
                        }
                    });
                }
            })
        });
    </script>
<?php } // end movie_post_type_print_custom_form()

add_action("wp_ajax_import_clip_data" , "import_clip_data");
add_action('wp_ajax_nopriv_import_clip_data', 'import_clip_data');
function import_clip_data() {
    $postTags = wp_get_post_tags($_POST['movie_id']);
    foreach ( $postTags as $postTag ) {
        $tagsArr[] = $postTag->name;
    }

    $tags = implode(', ', $tagsArr);
    $topics = wp_get_post_terms( $_POST['movie_id'], 'topic', array('fields' => 'ids') );

    echo json_encode(
        array(
            'status' => true,
            'tags' => $tags,
            'topics' => $topics,
        )
    );
    wp_die();
}

add_action('add_meta_boxes', 'add_movie_clip_list');
function add_movie_clip_list(){
    $screens = array( 'movie' );
    add_meta_box( 'movieClipsListSection', 'Related Clips', 'movie_clip_list_func', $screens );
}

function movie_clip_list_func() { ?>
    <style>
        ol {
            margin-left: 0;
            max-height: 220px;
            overflow-y: auto;
            counter-reset: li;
            list-style: none;
            padding: 0;
            text-shadow: 0 1px 0 rgba(255,255,255,.5);
        }

        ol a {
            position: relative;
            display: block;
            padding: .4em .4em .4em .8em;
            margin: .5em 0 .5em 2.5em;
            background: #f1f1f1;
            color: #444;
            text-decoration: none;
            transition: all .3s ease-out;
        }

        ol a:hover {background: #DCDDE1;}
        ol a:before {
            content: counter(li);
            counter-increment: li;
            position: absolute;
            left: -2.5em;
            top: 50%;
            margin-top: -1em;
            background: #007cba;
            color: white;
            height: 2em;
            width: 2em;
            line-height: 2em;
            text-align: center;
            font-weight: bold;
        }

        ol a:after {
            position: absolute;
            content: "";
            border: .5em solid transparent;
            left: -1em;
            top: 50%;
            margin-top: -.5em;
            transition: all .3s ease-out;
        }

        ol a:hover:after {
            left: -.5em;
            border-left-color: #007cba;
        }
        ol .new-clip a {
            background: #007cba;
            color: #ffffff;
        }
    </style>

    <ol>
        <li class="new-clip"><a target='_blank' href="post-new.php?post_type=clip">Create new clip</a></li>
        <?php
        $args = array(
            'post_type' => 'clip',
            'posts_per_page' => -1,
            'meta_key'		=> 'movie_id',
            'meta_value'	=> get_the_ID()
        );

        $query = new WP_Query($args);

        if ($query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post();
                echo "<li class=''><a target='_blank' href='post.php?post=" . get_the_ID() . "&action=edit'> " . get_the_title() . "</a></li>";
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </ol>

<?php }

add_action('admin_menu', 'add_import_clips');

function add_import_clips(){
    add_submenu_page(
        'edit.php?post_type=clip',
        'Import clips',
        'Import clips',
        'manage_options',
        'importClips',
        'import_clips_func'
    );
}

function import_clips_func()
{ ?>
    <link href="<?php echo plugin_dir_url(__FILE__) . 'css/bootstrap.css'; ?>" rel="stylesheet"/>
    <style>
        .content{
            margin:20px;
        }
        .button_group{
            margin:10px 0;
        }
        .csv_form{
            border:1px solid #ccc;
            padding: 10px;
            width:fit-content;
        }
    </style>

    <div class="content">
        <div class="button_group">
            <a href="/wp-content/uploads/kaltura-data/clips_template_Ks5p9fkwBl.csv" class="btn btn-primary download_template" download="clips_sample.csv">Download csv template</a>
        </div>
        <p>Send .csv file to import clips</p>
        <form action="" method="post" name="csv_form" class="csv_form" enctype="multipart/form-data">
            <input type="file" name="csvfile" value="" class="file"/>
            <input type="submit" value="Send" class="btn btn-primary"/>
        </form>

    </div>

    <?php

    if (!empty($_FILES)){
        $row = 0;
        if (($handle = fopen($_FILES["csvfile"]["tmp_name"], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row>0) {
                    $csv_kaltura_id = $data[0];
                    $csv_clip_title = $data[1];
                    $csv_clip_description = $data[2];
                    $csv_play_from = $data[3];
                    $csv_play_to = $data[4];

                    // create post
                    $id = wp_insert_post(array(
                        'post_title' => $csv_clip_title,
                        'post_content' => $csv_clip_description,
                        'post_type' => 'clip',
                        'post_status' => 'publish'
                    ));

                    $parent_movie = get_posts(array('numberposts' => 1, 'post_type'	=> 'movie', 'meta_key' => 'kaltura_id', 'meta_value' => $csv_kaltura_id));
                    $movie_id = $parent_movie[0]->ID;

                    update_field('movie_id', $movie_id, $id);
                    update_field('play_from', $csv_play_from, $id);
                    update_field('play_to', $csv_play_to, $id);

                    // Get terms from parent movie and set to the current clip
                    $topics = wp_get_post_terms( $movie_id, 'topic', array('fields' => 'ids') );
                    wp_set_post_terms( $id, $topics, 'topic' );

                    // Get tags from parent movie and set to the current clip
                    $tags = wp_get_post_tags($movie_id);
                    if ($tags) {
                        foreach ( $tags as $tag ) {
                            $tags_arr[] = $tag->name;
                        }
                        $tags = implode(', ', $tags_arr);
                        wp_add_post_tags( $id, $tags );
                    }

                }
                $row++;
            }
            fclose($handle);
        }
        ?>

        <div class="alert alert-success"><?php print $row-1 ?> clips have been successfully added</div>

        <?php

    } // end of !empty files

    ?>

<?php } // end of import clips func

?>
    