<?php
require_once __DIR__ . '/../bootstrap.php';
apiRequireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $comments = $pdo->query(
        "SELECT cm.*, u.prenom, u.nom, a.titre AS article_titre, a.slug AS article_slug
         FROM comments cm JOIN users u ON u.id=cm.user_id JOIN articles a ON a.id=cm.article_id
         ORDER BY cm.created_at DESC"
    )->fetchAll();
    jsonResponse(['success' => true, 'comments' => $comments]);
}

if ($method === 'POST') {
    checkCsrfApi();
    $body = jsonBody();
    $commentId = (int)($body['id'] ?? 0);
    if (($body['action'] ?? '') === 'toggle') {
        $stmt = $pdo->prepare("SELECT statut FROM comments WHERE id=:id");
        $stmt->execute(['id' => $commentId]);
        $current = $stmt->fetchColumn();
        $new = $current === 'approuve' ? 'rejete' : 'approuve';
        $stmt = $pdo->prepare("UPDATE comments SET statut=:s WHERE id=:id");
        $stmt->execute(['s' => $new, 'id' => $commentId]);
        jsonResponse(['success' => true, 'new_status' => $new]);
    }
    jsonResponse(['success' => false, 'message' => 'Action inconnue.'], 400);
}

if ($method === 'DELETE') {
    checkCsrfApi();
    $body = jsonBody();
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id=:id");
    $stmt->execute(['id' => (int)($body['id'] ?? 0)]);
    jsonResponse(['success' => true, 'message' => 'Commentaire supprimé.']);
}

jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
