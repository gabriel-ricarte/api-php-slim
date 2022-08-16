<?php
require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;
use Chatter\Middleware\FileFilter;
use Chatter\Middleware\FileMove;
use Chatter\Middleware\ImageRemoveExif;

$app = new \Slim\App();
$app->add(new ChatterAuth());
$app->add(new ChatterLogging());

$app->get('/messages', function($request, $response, $args) {
    $_message = new Message();
    $messages = $_message->all();
    $payload = [];
    foreach($messages as $_msg) {
        $payload[$_msg->id] = [ 
                                'body'      => $_msg->body,
                                'user_id'   => $_msg->user_id,
                                'user_uri'  => '/user/' . $_msg->user_id,
                                'created_at' => $_msg->created_at,
                                'image_url' => $_msg->image_url,
                                'message_id' => $_msg->id,
                                'message_uri' => '/messages/' . $_msg->id
                              ];
    }
    return $response->withStatus(200)->withJson($payload);
});

$filter = new FileFilter();
$removeExif = new ImageRemoveExif();
$move = new FileMove();
$app->post('/message', function($request, $response, $args) {
    $_message = $request->getParsedBody();
    $message = new Message();
    $message->body = isset($_message['message']) ? $_message['message'] : '';
    $message->user_id = -1;
    $message->image_url = $request->getAttribute('png_filename');
    if ($message->save()) {
        $payload = [
                    'message_id'  => $message->id,
                    'message_uri' => '/messages/' . $message->id,
                    'message_url' => $message->image_url
                   ];
        return $response->withStatus(201)->withJson($payload);
    }
    return $response->withStatus(400);
})->add($filter)->add($removeExif)->add($move);

$app->delete('/message/{message_id}', function($request, $response, $args) {
    $message = Message::find($args['message_id']);
    if (!$message) return $response->withStatus(404);
    if ($message->delete()) return $response->withStatus(204);
    return $response->withStatus(400);
});

$app->run();

