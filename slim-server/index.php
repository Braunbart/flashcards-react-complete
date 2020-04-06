<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Middlewares\TrailingSlash;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->add(new TrailingSlash(false)); // remove trailing slashes

$mongoClient = new MongoDB\Client('mongodb://localhost:27017');
$cardsCollection = $mongoClient->selectCollection('flashcards-react-jerry', 'cards');

// GET /
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents('../client/build/index.html'));

    return $response;
});

// GET /cards
$app->get('/cards', function (Request $request, Response $response, $args) use ($cardsCollection) {
    $allCards = $cardsCollection->find()->toArray();
    foreach ($allCards as $card) {
        $card['_id'] = (string) $card['_id'];
    }
    $response->getBody()->write(json_encode($allCards));

    return $response->withHeader('Content-Type', 'application/json');
});

// GET /cards/{id}
$app->get('/cards/{id}', function (Request $request, Response $response, $args) use ($cardsCollection) {
    $card = $cardsCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $card['_id'] = (string) $card['_id'];
    $response->getBody()->write(json_encode($card));

    return $response->withHeader('Content-Type', 'application/json');
});

// POST /cards
$app->post('/cards', function (Request $request, Response $response, $args) use ($cardsCollection) {
    $body = json_decode($request->getBody(), true);
    $result = $cardsCollection->insertOne($body);
    $card = $cardsCollection->findOne(['_id' => $result->getInsertedId()]);
    $card['_id'] = (string) $card['_id'];
    $response->getBody()->write(json_encode($card));

    return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
});

// PATCH /cards/{id}
$app->patch('/cards/{id}', function (Request $request, Response $response, $args) use ($cardsCollection) {
    $body = json_decode($request->getBody(), true);
    $cardsCollection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectID($args['id'])],
        ['$set' => $body]
    );
    $card = $cardsCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $card['_id'] = (string) $card['_id'];
    $response->getBody()->write(json_encode($card));

    return $response->withHeader('Content-Type', 'application/json');
});

// DELETE /cards/all
$app->delete('/cards/all', function (Request $request, Response $response, $args) use ($cardsCollection) {
    $cardsCollection->drop();

    return $response;
});

// DELETE /cards/{id}
$app->delete('/cards/{id}', function (Request $request, Response $response, $args) use ($cardsCollection) {
    $cardsCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);

    return $response;
});

$app->addErrorMiddleware(true, false, false);
$app->run();
