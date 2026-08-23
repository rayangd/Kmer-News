<?php
require_once __DIR__ . '/../bootstrap.php';
apiRequireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $categories = getCategories($pdo);
    foreach ($categories as &$c) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = :id");
        $stmt->execute(['id' => $c['id']]);
        $c['nb_articles'] = (int)$stmt->fetchColumn();
    }
    unset($c);
    jsonResponse(['success' => true, 'categories' => $categories]);
}

if ($method === 'POST') {
    checkCsrfApi();
    $body = jsonBody();
    $nom = trim($body['nom'] ?? '');
    $description = trim($body['description'] ?? '');
    $couleur = trim($body['couleur'] ?? '#0048D9');
    $editId = (int)($body['id'] ?? 0);

    $errors = [];
    if ($nom === '') $errors[] = 'Le nom de la rubrique est obligatoire.';
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $couleur)) $errors[] = 'Couleur invalide.';
    if (!empty($errors)) jsonResponse(['success' => false, 'message' => implode(' ', $errors)], 422);

    $slug = slugify($nom);
    if ($editId) {
        $stmt = $pdo->prepare("UPDATE categories SET nom=:n, slug=:s, description=:d, couleur=:c WHERE id=:id");
        $stmt->execute(['n'=>$nom,'s'=>$slug,'d'=>$description,'c'=>$couleur,'id'=>$editId]);
        jsonResponse(['success' => true, 'message' => 'Rubrique mise à jour.']);
    } else {
        $maxOrdre = (int)$pdo->query("SELECT COALESCE(MAX(ordre),0) FROM categories")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO categories (nom, slug, description, couleur, ordre) VALUES (:n,:s,:d,:c,:o)");
        $stmt->execute(['n'=>$nom,'s'=>$slug,'d'=>$description,'c'=>$couleur,'o'=>$maxOrdre+1]);
        jsonResponse(['success' => true, 'message' => 'Rubrique créée.']);
    }
}

if ($method === 'DELETE') {
    checkCsrfApi();
    $body = jsonBody();
    $catId = (int)($body['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = :id");
    $stmt->execute(['id' => $catId]);
    if ((int)$stmt->fetchColumn() > 0) {
        jsonResponse(['success' => false, 'message' => 'Impossible de supprimer : des articles utilisent encore cette rubrique.'], 422);
    }
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $catId]);
    jsonResponse(['success' => true, 'message' => 'Rubrique supprimée.']);
}

jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
