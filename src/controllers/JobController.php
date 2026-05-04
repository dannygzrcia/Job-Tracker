<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../Database.php';

class JobController{

    public function create(Request $request, Response $response) {
        $db = Database::getInstance();
        $body = $request->getParsedBody();
        $auth = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $auth);

        $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $company = $body['company'] ?? null;
        $role = $body['role'] ?? null;
        $status = $body['status'] ?? 'applied';
        $notes = $body['notes'] ?? null;
        $salary_min = $body['salary_min'] ?? null;
        $salary_max = $body['salary_max'] ?? null;
        $applied_at = $body['applied_at'] ?? null;
        
        if (!$company || !$role ) {
        $response->getBody()->write(json_encode(["message" => "Error"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $stmt = $db->prepare("INSERT INTO jobs (user_id, company, role, status, notes, salary_min, salary_max, applied_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user['id'], $company,$role,$status,$notes,$salary_min,$salary_max,$applied_at]);

        $response->getBody()->write(json_encode(["message" => "Job Applied"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function index(Request $request, Response $response) {
    $db = Database::getInstance();
    $auth = $request->getHeaderLine('Authorization');
    $token = str_replace('Bearer ', '', $auth);

    $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM jobs WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($jobs));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

}
    public function update(Request $request, Response $response, array $args){
    $db = Database::getInstance();
    $auth = $request->getHeaderLine('Authorization');
    $token = str_replace('Bearer ', '', $auth);

    $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $jobId = $args['id'];
    $body = $request->getParsedBody();
    $status = $body['status'] ?? null;
    $notes = $body['notes'] ?? null;

    $stmt = $db->prepare("UPDATE jobs SET status = ?, notes = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$status, $notes, $jobId, $user['id']]);



    $response->getBody()->write(json_encode(["message" => "Job updated successfully"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);


}
public function delete(Request $request, Response $response, array $args){
    $db = Database::getInstance();
    $auth = $request->getHeaderLine('Authorization');
    $token = str_replace('Bearer ', '', $auth);

    $stmt = $db->prepare("SELECT id FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $jobId = $args['id'];
    
    $stmt = $db->prepare("DELETE FROM jobs WHERE id = ? AND user_id = ?");
    $stmt->execute([$jobId, $user['id']]);

    $response->getBody()->write(json_encode(["message" => "Job deleted successfully"]));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);


}

}