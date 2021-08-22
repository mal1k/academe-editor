<?php if (is_user_logged_in()) { ?>
    <?php
    update_post_meta(get_the_ID(), 'lti-code-ncf-ddlo-drx', get_the_ID());
    ?>
    <?php if (!is_user_in_role('student')) { ?>
        <div id="<?php echo $args['id']; ?>" class="modal ui start-session">
            <?php icon('cross', 'close'); ?>
            <form id="sessionForm" onsubmit="event.prevent.default">

                <input type="hidden" name="session_title" value="<?php the_title(); ?>" />
                <input type="hidden" name="based_on" value="<?php echo str_replace('sfwd-courses', 'lesson', $post->post_type); ?>" />
                <input type="hidden" name="related_item" value="<?php echo $post->ID; ?>" />

                <h3><?php the_title(); ?></h3>
                <div class="">
                    <div class="session-code">
                        Lesson code: <span class="code" id="lessonCode" style="font-size: 52px; padding: 20px;">xxx-xxxx-xxx</span>
                    </div>
                    <div class="session-url" style="display: none">
                        <span class="url"></span>
                    </div>
                    <div class="horisontal-line" style="margin-top: 15px;"></div>
                </div>
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
                        const formatDate = (input) => {
                            const day = input.getDate();
                            const month = input.getMonth() + 1;
                            const year = input.getFullYear();
                            const minutes = input.getMinutes();
                            const hours = input.getHours();
                            return `${day}/${month.toString().length === 1 ? "0" + month : month}/${year} ${hours}:${minutes}`
                        };
                        const asyncSessionHook = jQuery("#sessionAsync_<?php echo $args['id']; ?>");
                        const syncSessionHook = jQuery("#sessionSync_<?php echo $args['id']; ?>");
                        const scheduleHook = jQuery("#scheduleDate");
                        const scheduleInput = jQuery("#schedule");
                        asyncSessionHook.change(() => {
                            if (asyncSessionHook.is(":checked")) {
                                jQuery('#scheduleBtn').hide();
                                scheduleInput.val(formatDate(new Date()));
                                scheduleHook.hide();
                            };
                        });
                        syncSessionHook.change(() => {
                            if (syncSessionHook.is(":checked")) {
                                jQuery('#scheduleBtn').show();
                                scheduleInput.val("");
                                scheduleHook.show();
                            };
                        });
                    });
                </script>

                <div class="flex-row items-center" id="scheduleDate">
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
                                        <input type="text" placeholder="Date/Time" name="schedule" id="schedule"/>
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
                <div class="flex-row items-center space-between">
                    <div class="cancel secondary-btn">Cancel</div>

                    <div class="buttons">
                        <a class="schedule-now secondary-btn" id="scheduleBtn" style="margin-right: 10px!important;"><?php _e('Schedule', 'academe-theme'); ?></a>
                        <a class="start-now primary-btn"><?php _e('Start Now', 'academe-theme'); ?></a>
                    </div>
                </div>
                <div class="sessionShare" id="sessionShare">
                    <div class="shareList hidden">
                    <div class="horisontal-line"></div>
                        <div>
                            <span style="font-size: 14px; margin: -10px 0 20px;">Sharing options</span>
                        </div>
                        <div class="flex-row">
                            <div style="margin-left: 25px;">
                                <span class="shareLink" ss="shareModalLink" id="shareModalLink" href=""><?php icon('copy-modal', 'copy'); ?><br>
                                Copy link
                                </span>
                            </div>
                            <div style="margin-left: 25px;">
                                <span class="shareLink" id="whatsAppModalLink" href=""><?php icon('whatsapp-modal'); ?><br>
                                WhatsApp
                                </span>
                            </div>
                            <div style="margin-left: 25px;">
                                <span class="shareLink" id="emailModalLink" href=""><?php icon('email-modal'); ?><br>
                                Email
                                </span>
                            </div>
                            <div style="margin-left: 25px;">
                                <span class="shareLink" id="puzzleModalLink" href=""><?php icon('puzzle-modal'); ?><br>
                                LTI
                                </span>
                            </div>

                            <script>
                            jQuery('#puzzleModalLink').click(function(){
                                event.preventDefault();
                                jQuery.ajax('<?php bloginfo('template_directory'); ?>/lti_create_code.php?code=' + jQuery('#lessonCode').text() + '&id=<?php echo get_the_ID(); ?>&time=' + jQuery('#schedule').val(),{
                                type: 'POST',
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                    success: (data)=>{
                                        var copyText = "<?php echo get_home_url(); ?>/lti-movies?code=" + jQuery('#lessonCode').text();

                                        navigator.clipboard.writeText(copyText).then(function() {
                                            alert('Copied');
                                        });
                                        // showToast('Copied!', 'The LTI link was successfully copied to your clipboard.'); doesnt work
                                    }
                                })  
                            })
                            </script>

                            <div style="margin-left: 25px;">
                                <span class="shareLink" id="shareButtonLink" href=""><?php icon('share-modal'); ?><br>
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
        </style>

    <?php } ?>
<?php } ?>