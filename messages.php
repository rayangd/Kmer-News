<?php
require_once __DIR__ . '/../bootstrap.php';
apiRequireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $messages = $pdo->query("SELECT * FROM messages ORDER BY lu ASC, created_at DESC")->fetchAll();
    jsonResponse(['success' => true, 'messages' => $messages]);
}

if ($method === 'POST') {
    checkCsrfApi();
    $body = jsonBody();
    $msgId = (int)($body['id'] ?? 0);
    if (($body['action'] ?? '') === 'read') {
        $stmt = $pdo->prepare("UPDATE messages SET lu=1 WHERE id=:id");
        $stmt->execute(['id' => $msgId]);
        jsonResponse(['success' => true]);
    }
    jsonResponse(['success' => false, 'message' => 'Action inconnue.'], 400);
}

if ($method === 'DELETE') {
    checkCsrfApi();
    $body = jsonBody();
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id=:id");
    $stmt->execute(['id' => (int)($body['id'] ?? 0)]);
    jsonResponse(['success' => true, 'message' => 'Message supprimé.']);
}

jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
