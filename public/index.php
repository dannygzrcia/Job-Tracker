<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..'); 
$dotenv->load();

require __DIR__ . '/../src/Database.php';
require __DIR__ . '/../src/controllers/UserController.php';
require __DIR__ . '/../src/middleware/AuthMiddleware.php';
require __DIR__ . '/../src/controllers/JobController.php';


$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$pdo = Database::getInstance();

// Test route
$app->get('/', function ($req, $res) {
    $res->getBody()->write("API works");
    return $res;
});

// Users route
$app->get('/users', function ($request, $response) use ($pdo) {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});

// Register route
$app->post('/api/register', [UserController::class, 'register']);
$app->post('/api/login', [UserController::class, 'login']);
$app->get('/api/protected', function($request, $response) {
    $response->getBody()->write(json_encode(["message" => "You are in!"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
})->add(new AuthMiddleware());
$app->post('/api/jobs', [JobController::class, 'create'])->add(new AuthMiddleware());
$app->get('/api/jobs', [JobController::class, 'index'])->add(new AuthMiddleware());
$app->put('/api/jobs/{id}', [JobController::class, 'update'])->add(new AuthMiddleware());
$app->delete('/api/jobs/{id}', [JobController::class, 'delete'])->add(new AuthMiddleware());

$app->run();