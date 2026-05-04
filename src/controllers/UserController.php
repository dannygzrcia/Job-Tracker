<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../Database.php';

class UserController {

    public function register(Request $request, Response $response) {
        $db = Database::getInstance();
        $body = $request->getParsedBody();

        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        // Validate inputs
        if (!$email || !$password) {
            $response->getBody()->write(json_encode(["error" => "Email and password are required"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode(["error" => "Invalid email format"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $response->getBody()->write(json_encode(["error" => "Email already exists"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        // Hash password and insert
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
        $stmt->execute([$email, $hash]);

        $response->getBody()->write(json_encode(["message" => "User registered successfully"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
        public function login(Request $request, Response $response) {
        $db = Database::getInstance();
        $body = $request->getParsedBody();

        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$user){
            $response->getBody()->write(json_encode(["error" => "User not found"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        if(!password_verify($password, $user['password_hash'])){
            $response->getBody()->write(json_encode(["error" => "Invalid Password"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $token = bin2hex(random_bytes(32));
        $stmt = $db->prepare("UPDATE users SET token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);
        $response->getBody()->write(json_encode(["token" => $token]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        }
}