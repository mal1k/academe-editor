<?php if (is_user_logged_in()) { ?>

<?php if (!is_user_in_role('student')) { ?>
    <div id="<?php echo $args['id']; ?>" class="modal ui start-session">
        <?php icon('cross', 'close'); ?>
        <form id="sessionForm" class="sessionForm" onsubmit="event.prevent.default">

            <input type="hidden" name="session_title" value="<?php the_title(); ?>" />
            <input type="hidden" name="based_on" class="based_on" value="<?php echo str_replace('sfwd-courses', 'lesson', $post->post_type); ?>" />
            <?php 
            $meta_key = $post->post_name; 
            global $wpdb;
            $post_ID = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'movie' LIMIT 1" , $meta_key) );
            ?>
            <input type="hidden" name="related_item" class="related_item" value="<?php echo $post_ID; ?>" />
            <div class="sessionForm__top">
                <h3 class="sessionForm__title"><?php the_title(); ?></h3>
                <span class="sessionForm__subtitle">A Lesson by Jurneys in Film</span>
            </div>
            <div class="sessionForm__code">
                <span class="sessionForm__title">Join with this lesson cODE</span>
                <div class="session-code">
                    <span class="code lessonCode" id="lessonCode">xxxxx</span>
                </div>
                <span class="note-text sessionTime hidden">Content Avail from Sun, July 21st until Tue, July 23rd</span>
                <div class="session-url" style="display: none">
                    <span class="url"></span>
                </div>
                <div class="flex-row items-center flex-center" style="margin-top: 60px;">
                    <div class="buttons">
                        <a class="nextScreen primary-btn "style="margin: 0 10px;"><?php _e('Schedule', 'academe-theme'); ?></a>
                        <a class="start-now primary-btn"><?php _e('Start Now', 'academe-theme'); ?></a>
                    </div>
                </div>
            </div>
            <div class="sessionForm__description hidden">
                <div class="flex-row items-center">
                    <div class="row-title"><?php _e('Type:', 'academe-theme'); ?></div>
                    <div class="row-data">
                        <input 
                            class="primary-radio" 
                            type="radio" name="session_type" 
                            id="sessionSync_<?php echo $args['id']; ?>" 
                            value="sync" checked
                        />
                        <label for="sessionSync_<?php echo $args['id']; ?>">
                            <?php _e('Synchronous', 'academe-theme'); ?>
                        </label>
                        <input 
                            class="primary-radio" 
                            type="radio" 
                            name="session_type" 
                            id="sessionAsync_<?php echo $args['id']; ?>" 
                            value="async" 
                        />
                        <label 
                            class="session-type-label" 
                            for="sessionAsync_<?php echo $args['id']; ?>">
                            <?php _e('A-synchronous', 'academe-theme'); ?>
                        </label>
                    </div>
                </div>

                <script type="text/javascript">
                    jQuery(document).ready((jQuery) => {
                        jQuery(document).on('click', '.nextScreen:not(".disabled")', function(e) {
                            e.preventDefault;
                            console.log(jQuery(this).closest('.sessionForm'))
                            const form = jQuery(this).closest('.sessionForm');
                            form.find('.sessionForm__code').addClass('hidden');
                            form.find('.sessionForm__description').removeClass('hidden');
                            form.find('.shareList').addClass('hidden');
                        })

                        const asyncSessionHook = jQuery(".sessionAsync");
                        const syncSessionHook = jQuery(".sessionSync");
                        jQuery('input[type=radio][name=session_type]').change(function() {
                            const form = jQuery(this).closest('.sessionForm');
                            if (this.value == 'sync') {
                                form.find('.scheduleDate').show();
                                form.find('.schedule').val("");
                            }
                            else if (this.value == 'async') {
                                form.find('.scheduleDate').hide();
                                form.find('.schedule').val(formatDate(new Date()));
                            }
                        });

                        const formatDate = (input) => {
                            const day = input.getDate();
                            const month = input.getMonth() + 1;
                            const year = input.getFullYear();
                            const minutes = input.getMinutes();
                            const hours = input.getHours();
                            return `${day}/${month.toString().length === 1 ? "0" + month : month}/${year} ${hours}:${minutes}`
                        };
                    });
                </script>

                <div class="flex-row items-center scheduleDate" id="scheduleDate">
                    <div class="row-title condition async"><?php _e('Limit access to:', 'academe-theme'); ?></div>
                    <div class="row-title condition sync"><?php _e('Schedule:', 'academe-theme'); ?></div>
                    <div class="row-data flex-row space-between">
                        <div class="flex-column condition async">
                            <div class="row-data">
                                <div class="dropdown ui dark duration-selector">
                                    <input name="access_duration" type="hidden" value="48" />
                                    <span class="default text">48h</span>
                                    <?php icon('chevron-bold', 'with-rotate'); ?>
                                    <div class="menu">
                                        <div class="menu-body">
                                            <div class="item" data-value="48">72h</div>
                                            <div class="item" data-value="48">48h</div>
                                            <div class="item" data-value="24">24h</div>
                                            <div class="item" data-value="12">12h</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex-column from items-center">
                            <div class="column-title condition async"><?php _e('From:', 'academe-theme'); ?></div>
                            <div class="row-data">
                                <div class="ui calendar datetime-selector">
                                    <div class="ui input right icon">
                                        <?php icon('calendar'); ?>
                                        <input type="text" placeholder="Date/Time" name="schedule" id="schedule" class="schedule"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-row">
                    <div class="row-title"><?php _e('Note:', 'academe-theme'); ?></div>
                    <div class="row-data">
                        <textarea name="notes"></textarea>
                    </div>
                </div>
                <div class="flex-row items-center flex-end">
                    <div class="cancel secondary-btn">Cancel</div>

                    <div class="buttons">
                        <a class="schedule-now primary-btn scheduleBtn" id="scheduleBtn" style="margin:0 10px;"><?php _e('Confirm', 'academe-theme'); ?></a>
                    </div>
                </div>
            </div>


            <div class="sessionShare" id="sessionShare">
                <div class="shareList">
                <div class="horisontal-line"></div>
                    <div>
                        <span class="note-text" style="margin-top:-10px">Sharing options</span>
                    </div>
                    <div class="flex-row space-between">
                        <div style="margin: 0 10px; text-align:center">
                            <span class="shareLink shareModalLink disabled" id="shareModalLink" href=""><?php icon('copy-modal', 'copy'); ?><br>
                            Copy link
                            </span>
                        </div>
                        <div style="margin: 0 10px; text-align:center">
                            <span class="shareLink disabled" id="whatsAppModalLink" href=""><?php icon('whatsapp-modal'); ?><br>
                            WhatsApp
                            </span>
                        </div>
                        <div style="margin: 0 10px; text-align:center">
                            <span class="shareLink disabled" id="emailModalLink" href=""><?php icon('email-modal'); ?><br>
                            Email
                            </span>
                        </div>
                        <div style="margin: 0 10px; text-align:center">
                            <span class="shareLink disabled puzzleModalLink" id="puzzleModalLink" href=""><?php icon('puzzle-modal'); ?><br>
                            LTI
                            </span>
                        </div>

                        <script>
                        jQuery('.puzzleModalLink:not(".disabled")').click(function(){
                            event.preventDefault();                              
                            const form = jQuery(this).closest('.sessionForm');
                            const code = form.find('.lessonCode').text();
                            const time = form.find('.schedule').val();
                            const postID = form.find('.related_item').val();
                            jQuery.ajax('<?php bloginfo('template_directory'); ?>/lti_create_code.php?code=' + code + '&id=' + postID + '&time=' + time,{
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                                success: (data)=>{
                                    var copyText = "<?php echo get_home_url(); ?>/lti-movies?code=" + code;

                                    navigator.clipboard.writeText(copyText).then(function() {
                                        alert('Copied');
                                    });

                                }
                            })  
                        })
                        </script>

                        <div style="margin-left: 25px;">
                            <span class="shareLink disabled" id="shareButtonLink" href=""><?php icon('share-modal'); ?><br>
                            Share
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .session-code {
            position: relative;
            width: 100%;
        }
        .session-code__label {
            position: absolute;
            top: -25px;
            font-size: 9px;
            width: 100%;
            text-align: center;
        }
        .session-code .code {
            display: block;
            font-size: 42px;
            line-height: 44px;
            text-align: center;
        }
        .social-items {
            justify-content: space-around;
            text-align: center;
        }
        .shareLink svg {
            max-width: 45px;
        }
        /*additional code*/
        .hidden {
            display: none;
        }
        .shareLink {
            cursor: pointer;
        }
        .nextScreen.disabled {
            background-color: #626262;
            color: #FFFFFF;
            opacity: 1;
        }
        .shareLink.disabled {
            opacity: 0.5;
        }
        .flex-row.flex-end {
            justify-content: flex-end;
        }
        .flex-row.flex-center {
            justify-content: center;
        }
        .sessionForm__title { 
            text-align: center;
            padding: 0 30px;
        }
        .sessionForm__top {
            margin-bottom: 40px;
        }
        .sessionForm__code {
            text-align: center;
            padding: 20px 0 50px;
        }
        .sessionForm__subtitle {
            display: block;
            font-family: Montserrat;
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 24px;
            color: #FFFFFF;
            opacity: 0.65;
            text-align: center;
        }
        .note-text {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #FFFFFF;
            opacity: 0.65;
        }
        .note-text.hidden {
            display: none;
        }
    </style>

<?php } ?>
<?php } ?>