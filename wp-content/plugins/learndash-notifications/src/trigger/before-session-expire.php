<?php

namespace LearnDash_Notification\Trigger;

use LearnDash_Notification\Notification;
use LearnDash_Notification\Trigger;

class Before_Session_Expire extends Trigger {

    protected $trigger = 'session_expires';

    /**
     * @var array
     */
    protected $all_sessions = [];

    public function maybe_send_reminder() {
        foreach ( $this->get_notifications( $this->trigger ) as $model ) {
            if ( $model->before_session_expiry <= 0 ) {
                continue;
            }

            $session_ids = $this->get_all_async_sessions();

            foreach ( $session_ids as $session_id ) {

                //$user_ids = get_users(['fields' => ['ID']]);
                $user_id = $author_id = get_post_field( 'post_author', $session_id );

                //foreach ( $user_ids as $user_id ) {
                if ( $model->is_sent( $user_id, $this->trigger, $model->post->ID, $session_id ) ) {
                    continue;
                }
                $timestamp = get_post_meta( $session_id, 'session_ends', true );
                if ( !$timestamp ) {
                    continue;
                }
                $this->log( sprintf( esc_html__( 'The session will be expired at %s', 'learndash-notifications' ), $this->get_current_time_from( $timestamp ) ) );
                $init_time = get_option( 'ld_notifications_init' );
                if ( $init_time && $init_time > $timestamp ) {
                    //prevent duplicate email
                    continue;
                }
                if ( strtotime( '+ ' . $model->before_session_expiry . ' days', $this->get_timestamp() ) >= $timestamp ) {
                    //send emails
                    $emails = $model->gather_emails( $user_id );
                    $args   = array(
                        'user_id'   => $user_id,
                        'session_id' => $session_id
                    );
                    $this->send( $emails, $model, $args );
                    $model->mark_sent( $user_id, $this->trigger, $model->post->ID, $session_id );
                }
                //}
            }
        }
    }

    /**
     * @param $id
     *
     * @return array
     */
    protected function get_users_from_a_session( $id ) {
//        $query = learndash_get_users_for_course( $id );
//        if ( ! $query instanceof \WP_User_Query ) {
//            //something was wrong
//            return [];
//        }
//
//        return $query->get_results();
    }

    /**
     * Get all Course IDS
     * @return int[]
     */
    protected function get_all_async_sessions() {
        if ( ! empty( $this->all_sessions ) ) {
            return $this->all_sessions;
        }
        $query_args = array(
            'post_type'      => 'session',
            'fields'         => 'ids',
            'posts_per_page' => - 1,
            'post_status'    => 'publish',
            'meta_query'     => [
                'relation' => 'AND',
                array(
                    'key' => 'session_ends',
                    'value' => time(),
                    'compare' => '>=',
                ),
                array(
                    'key' => 'session_type',
                    'value' => 'async',
                    'compare' => '=',
                ),
            ]
        );

        $query = new \WP_Query( $query_args );

        $this->all_sessions = $query->get_posts();

        return $this->all_sessions;
    }

    /**
     * A base point for monitoring the events
     * @return void
     */
    function listen() {
        add_action( 'learndash_notifications_cron', [ &$this, 'maybe_send_reminder' ] );
    }

    /**
     * @param Notification $model
     * @param $args
     *
     * @return bool
     */
    protected function can_send_delayed_email( Notification $model, $args ) {
        //should never here
        return false;
    }
}