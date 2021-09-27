<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Session;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/wp-config.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Session()
        )
    ),
    WEBSOCKET_PORT
);

$server->run();