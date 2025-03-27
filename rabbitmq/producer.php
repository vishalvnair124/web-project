<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connect to RabbitMQ (Default: localhost, guest/guest)
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare a queue
$channel->queue_declare('hello_queue', false, false, false, false);

// Message to send
$msg = new AMQPMessage('Hello, RabbitMQ!');
$channel->basic_publish($msg, '', 'hello_queue');

echo " [x] Sent 'Hello, RabbitMQ!'\n";

// Close the channel and connection
$channel->close();
$connection->close();
