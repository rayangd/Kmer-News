<?php
require_once __DIR__ . '/bootstrap.php';
apiRequireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}
checkCsrfApi();

$body = jsonBody();
$articleId = (int)($body['article_id'] ?? 0);
$contenu = trim($body['contenu'] ?? '');
$user = currentUser();

if ($contenu === '' || mb_strlen($contenu) > 1000) {
    jsonResponse(['success' => false, 'message' => 'Commentaire vide ou trop long (1000 caractères max).'], 422);
}
if (!getArticleById($pdo, $articleId)) {
    jsonResponse(['success' => false, 'message' => 'Article introuvable.'], 404);
}

$stmt = $pdo->prepare("INSERT INTO comments (article_id, user_id, contenu, statut) VALUES (:a, :u, :c, 'approuve')");
$stmt->execute(['a' => $articleId, 'u' => $user['id'], 'c' => $contenu]);

jsonResponse([
    'success' => true,
    'comment' => [
        'prenom' => $user['prenom'],
        'nom' => $user['nom'],
        'contenu' => $contenu,
        'created_at' => date('Y-m-d H:i:s'),
    ],
]);
