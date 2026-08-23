<?php
require_once __DIR__ . '/../bootstrap.php';
apiRequireAdmin();

$totalArticles = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE statut='publie'")->fetchColumn();
$totalBrouillons = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE statut='brouillon'")->fetchColumn();
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalComments = (int)$pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$totalVues = (int)$pdo->query("SELECT COALESCE(SUM(vues),0) FROM articles")->fetchColumn();
$totalMessages = (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE lu=0")->fetchColumn();

$totalPropositions = (int)$pdo->query(
    "SELECT COUNT(*) FROM articles a JOIN users u ON u.id=a.auteur_id WHERE u.role='user' AND a.statut='brouillon'"
)->fetchColumn();

$categories = getCategories($pdo);
$catStats = [];
foreach ($categories as $c) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id=:id AND statut='publie'");
    $stmt->execute(['id' => $c['id']]);
    $catStats[] = ['nom' => $c['nom'], 'couleur' => $c['couleur'], 'count' => (int)$stmt->fetchColumn()];
}

$recentArticles = $pdo->query(
    "SELECT a.*, c.nom AS cat_nom, c.couleur AS cat_couleur FROM articles a
     JOIN categories c ON c.id = a.categorie_id ORDER BY a.created_at DESC LIMIT 6"
)->fetchAll();

$recentComments = $pdo->query(
    "SELECT cm.*, u.prenom, u.nom, a.titre AS article_titre, a.slug AS article_slug
     FROM comments cm JOIN users u ON u.id = cm.user_id JOIN articles a ON a.id = cm.article_id
     ORDER BY cm.created_at DESC LIMIT 6"
)->fetchAll();

jsonResponse([
    'success' => true,
    'stats' => [
        'articles' => $totalArticles, 'brouillons' => $totalBrouillons, 'users' => $totalUsers,
        'comments' => $totalComments, 'vues' => $totalVues, 'messages_non_lus' => $totalMessages,
        'propositions_lecteurs' => $totalPropositions,
    ],
    'cat_stats' => $catStats,
    'recent_articles' => $recentArticles,
    'recent_comments' => $recentComments,
]);
