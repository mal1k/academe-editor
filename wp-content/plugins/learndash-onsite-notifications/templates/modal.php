<?php
$notifications = learndash_notifications_by_user();
$total = learndash_notifications_count_by_user();
$unread = learndash_notifications_unread_count_by_user();
?>

<div class="notifications-section">
    <div class="notifications-header">
        <span class="title"><?php _e('Notifications'); ?></span>
        <span class="unread">(<?php echo $unread . ' ' . __('Unread'); ?>)</span>
    </div>
    <div class="notifications-body">
        <div class="notification-list">
            <?php foreach ($notifications as $notification) {
                the_single_notification($notification);
            } ?>
        </div>
        <?php if (count($notifications) != $total) { ?>
            <div id="loadMoreNotifications" class="primary-btn" data-offset="<?php echo count($notifications); ?>" data-total="<?php echo $total; ?>">
                <?php _e('Load more notifications', 'academe-theme'); ?>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('.notification-item').on('click', function (e) {
            if ($(this).hasClass('unread')) {
                var _this = $(this);
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType : 'json',
                    data: {
                        'action': 'learndash_notification_read',
                        'notification_id': $(this).data('id'),
                    },
                    success: function (response) {
                        if (response.success) {
                            window.location.href = _this.data('href');
                        }
                    }
                });
            } else {
                window.location.href = $(this).data('href');
            }

        });

        $('#loadMoreNotifications').on('click', function () {
            var _this = $(this);
            $.ajax({
                url: ajaxurl,
                type: 'GET',
                dataType : 'json',
                data: {
                    'action': 'learndash_notification_load',
                    'offset': $(this).attr('data-offset'),
                },
                success: function (response) {
                    if (response.success) {
                        $('.notification-list').append(response.data);
                        _this.attr('data-offset', response.offset);
                        if (_this.attr('data-offset') === _this.attr('data-total')) {
                            _this.hide();
                        }
                    }
                }
            });
        });
    });
</script>