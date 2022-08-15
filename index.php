<?php
require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;

$app = new \Slim\App();
$app->add(new ChatterAuth());
$app->add(new ChatterLogging());

$app->get('/messages', function($request, $response, $args) {
    $_message = new Message();
    $messages = $_message->all();
    $payload = [];
    foreach($messages as $_msg) {
        $payload[$_msg->id] = [ 'body' => $_msg->body,
                                'user_id' => $_msg->user_id,
                                'created_at' => $_msg->created_at ];
    }
    return $response->withStatus(200)->withJson($payload);
});

$app->post('/message', function($request, $response, $args) {
    $_message = $request->getParsedBody();
    //upload images
    $imagePath = '';
    //end 
    $message = new Message();
    $message->body = isset($_message['message']) ? $_message['message'] : '';
    $message->user_id = -1;
    if ($message->save()) {
        $payload = ['message_id' => $message->id,
                    'message_uri' => '/messages/' . $message->id];
        return $response->withStatus(201)->withJson($payload);
    }
    return $response->withStatus(400);
});

$app->delete('/message/{message_id}', function($request, $response, $args) {
    $message = Message::find($args['message_id']);
    if (!$message) return $response->withStatus(404);
    if ($message->delete()) return $response->withStatus(204);
    return $response->withStatus(400);
});

$app->run();

