<?php
require_once __DIR__ . '/../bootstrap.php';
apiRequireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$admin = currentUser();

if ($method === 'GET') {
    $search = trim($_GET['q'] ?? '');
    $sql = "SELECT u.*, (SELECT COUNT(*) FROM comments WHERE user_id=u.id) AS nb_comments,
            (SELECT COUNT(*) FROM article_likes WHERE user_id=u.id) AS nb_likes
            FROM users u WHERE u.role='user'";
    $params = [];
    if ($search !== '') { $sql .= " AND (u.prenom LIKE :q OR u.nom LIKE :q OR u.email LIKE :q)"; $params['q'] = "%$search%"; }
    $sql .= " ORDER BY u.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    jsonResponse(['success' => true, 'users' => $stmt->fetchAll()]);
}

if ($method === 'POST') {
    checkCsrfApi();
    $body = jsonBody();
    $userId = (int)($body['id'] ?? 0);

    if ($userId === (int)$admin['id']) {
        jsonResponse(['success' => false, 'message' => 'Vous ne pouvez pas modifier votre propre statut ici.'], 422);
    }

    if (($body['action'] ?? '') === 'toggle') {
        $stmt = $pdo->prepare("SELECT statut FROM users WHERE id=:id");
        $stmt->execute(['id' => $userId]);
        $current = $stmt->fetchColumn();
        $new = $current === 'actif' ? 'suspendu' : 'actif';
        $stmt = $pdo->prepare("UPDATE users SET statut=:s WHERE id=:id AND role='user'");
        $stmt->execute(['s' => $new, 'id' => $userId]);
        jsonResponse(['success' => true, 'message' => 'Statut mis à jour.', 'new_status' => $new]);
    }
    jsonResponse(['success' => false, 'message' => 'Action inconnue.'], 400);
}

if ($method === 'DELETE') {
    checkCsrfApi();
    $body = jsonBody();
    $userId = (int)($body['id'] ?? 0);
    if ($userId === (int)$admin['id']) {
        jsonResponse(['success' => false, 'message' => 'Action impossible sur votre propre compte.'], 422);
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id AND role='user'");
    $stmt->execute(['id' => $userId]);
    jsonResponse(['success' => true, 'message' => 'Utilisateur supprimé.']);
}

jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
