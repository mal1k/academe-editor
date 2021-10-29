<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Session implements MessageComponentInterface {

    protected $clients;
    private $subscriptions;
    private $users;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $data = json_decode($msg, true);

        switch ($data['command']) {
            case "subscribe":
                $msg = $data['message'];
                $this->subscriptions[$from->resourceId] = $msg;
                if($msg['role'] == 'student') {
                    $teacher_status = $this->checkTeacherStatus($msg['channel']);
                    $this->users[$from->resourceId]->send(
                        json_encode([
                            "message" => [
                                'teacher_online' => $teacher_status,
                            ]
                        ])
                    );
                }
                if($msg['role'] == 'teacher') {
                    foreach ($this->subscriptions as $id => $subscription) {
                        if ($subscription['channel'] == $msg['channel'] && $id != $from->resourceId) {
                            $this->users[$id]->send(
                                json_encode([
                                    "message" => [
                                        'teacher_online' => true,
                                    ]
                                ])
                            );
                        }
                    }
                }

                break;
            case "message":
                if (isset($this->subscriptions[$from->resourceId])) {
                    $target = $this->subscriptions[$from->resourceId];
                    foreach ($this->subscriptions as $id => $subscription) {
                        if ($subscription['channel'] == $target['channel'] && $id != $from->resourceId) {
                            $this->users[$id]->send(
                                json_encode([
                                    "message" => $data['message']
                                ])
                            );
                        }
                    }
                }
                break;
        }

//        foreach ($this->clients as $client) {
//            if ($from !== $client) {
//                // The sender is not the receiver, send to each client connected
//                $client->send($data['msg']);
//            }
//        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $user = $this->subscriptions[$conn->resourceId];
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);

        if ($user['role'] == 'teacher') {
            $teacher_status = $this->checkTeacherStatus($user['channel']);
            foreach ($this->subscriptions as $id => $subscription) {
                if ($subscription['channel'] == $user['channel'] && $id != $conn->resourceId) {
                    $this->users[$id]->send(
                        json_encode([
                            "message" => [
                                'teacher_online' => $teacher_status,
                            ]
                        ])
                    );
                }
            }
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function checkTeacherStatus($channel) {
        $teacher_online = false;
        //filter subscriptions in current channel
        $subscriptions = array_filter($this->subscriptions, function($subscription) use ($channel) {
            return $subscription['channel'] === $channel;
        });
        foreach ($subscriptions as $subscription) {
            if ($subscription['role'] == 'teacher') {
                $teacher_online = true;
                break;
            }
        }
        return $teacher_online;
    }
}