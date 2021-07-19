<?php

namespace LearnDash_Notification\Trigger;

use LearnDash_Notification\Notification;
use LearnDash_Notification\Trigger;

class Teaching_Guide_Published extends Trigger
{
    protected $trigger = 'publish_teaching_guide';

    public function monitor( $post ) {

        $user_id = $post->post_author;

        $models = $this->get_notifications( $this->trigger );
        if ( empty( $models ) ) {
            return;
        }
        $this->log( '==========Job start========' );
        $this->log( sprintf( 'Process %d notifications', count( $models ) ) );
        foreach ( $models as $model ) {

            $emails = $model->gather_emails( $user_id );
            $args   = [
                'user_id'   => $user_id,
                'teaching_guide_id' => $post->ID,
            ];
            if ( absint( $model->delay ) ) {
                $this->queue_use_db( $emails, $model, $args );
            } else {
                $this->send( $emails, $model, $args );
                $this->log( 'Done, moving next if any' );
            }
        }
        $this->log( '==========Job end========' );
    }

    /**
     * A base point for monitoring the events
     * @return void
     */
    function listen() {
        add_action( 'teaching-guide_was_published', [ &$this, 'monitor' ], 10, 2 );
        add_action( 'leanrdash_notifications_send_delayed_email', [ &$this, 'send_db_delayed_email' ] );
    }

    /**
     * @param Notification $model
     * @param $args
     *
     * @return bool
     */
    protected function can_send_delayed_email( Notification $model, $args ) {
        return true;
    }
}