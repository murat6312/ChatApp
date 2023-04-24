<?php
require __DIR__ . '/vendor/autoload.php';

$app = new \Slim\App();

$db = new PDO('sqlite:chat.db');

$app->get('/users', function($request, $response, $args) use ($db) {
    $stmt = $db->prepare('SELECT * FROM users');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $response->withJson($users);
});

$app->post('/users', function($request, $response, $args) use ($db) {
    $username = $request->getParam('username');
    $stmt = $db->prepare('INSERT INTO users (username) VALUES (:username)');
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $response->withJson(['success' => true]);
});

$app->get('/messages/{recipient}', function($request, $response, $args) use ($db) {
    $recipient = $args['recipient'];
    $stmt = $db->prepare('SELECT messages.*, users.username AS sender_name FROM messages JOIN users ON messages.sender = users.id WHERE messages.recipient = :recipient');
    $stmt->bindParam(':recipient', $recipient);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $response->withJson($messages);
});

$app->post('/messages', function($request, $response, $args) use ($db) {
    $sender = $request->getParam('sender');
    $recipient = $request->getParam('recipient');
    $content = $request->getParam('content');
    $stmt = $db->prepare('INSERT INTO messages (sender, recipient, content) VALUES (:sender, :recipient, :content)');
    $stmt->bindParam(':sender', $sender);
    $stmt->bindParam(':recipient', $recipient);
    $stmt->bindParam(':content', $content);
    $stmt->execute();
    return $response->withJson(['success' => true]);
});

$app->run();