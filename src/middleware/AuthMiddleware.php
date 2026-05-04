<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;


require_once __DIR__ . '/../Database.php';

class AuthMiddleware{
    public function __invoke(Request $request, RequestHandler $handler): Response{
        $auth = $request->getHeaderLine('Authorization');
    if (!$auth) {
    $response = new SlimResponse();
    $response->getBody()->write(json_encode(["error" => "Unauthorized"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
}

    $token = str_replace('Bearer ', '', $auth);

        // Check token in DB
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
    $response = new SlimResponse();
    $response->getBody()->write(json_encode(["error" => "Invalid token"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
}

        return $handler->handle($request);
    }
}