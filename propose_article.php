<?php
require_once __DIR__ . '/bootstrap.php';
apiRequireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}
checkCsrfApi();

$user = currentUser();

$titre = trim($_POST['titre'] ?? '');
$chapo = trim($_POST['chapo'] ?? '');
$contenu = trim($_POST['contenu'] ?? '');
$categorieId = (int)($_POST['categorie_id'] ?? 0);
$tag = trim($_POST['tag'] ?? '');

$errors = [];
if ($titre === '' || mb_strlen($titre) < 8) $errors[] = 'Le titre doit contenir au moins 8 caractères.';
if ($contenu === '' || mb_strlen($contenu) < 100) $errors[] = 'Le contenu doit contenir au moins 100 caractères.';
if (!$categorieId) $errors[] = 'Merci de choisir une rubrique.';

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors de l'envoi de l'image.";
    } elseif (!isset($allowed[$mime])) {
        $errors[] = 'Format image non autorisé (JPG, PNG, WEBP uniquement).';
    } elseif ($file['size'] > 4 * 1024 * 1024) {
        $errors[] = "L'image dépasse la taille maximale (4 Mo).";
    } else {
        $uploadDir = __DIR__ . '/../assets/img/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $filename = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $imagePath = '../assets/img/uploads/' . $filename;
        } else {
            $errors[] = "Impossible d'enregistrer l'image.";
        }
    }
}

if (!empty($errors)) {
    jsonResponse(['success' => false, 'message' => implode(' ', $errors)], 422);
}

$slug = uniqueArticleSlug($pdo, $titre);

// Les propositions des lecteurs sont enregistrées en "brouillon" : elles doivent
// être validées et publiées par un administrateur avant d'apparaître sur le site.
$stmt = $pdo->prepare(
    "INSERT INTO articles (titre, slug, chapo, contenu, image, tag, categorie_id, auteur_id, statut, a_la_une)
     VALUES (:titre, :slug, :chapo, :contenu, :image, :tag, :cat, :auteur, 'brouillon', 0)"
);
$stmt->execute([
    'titre' => $titre, 'slug' => $slug, 'chapo' => $chapo, 'contenu' => $contenu,
    'image' => $imagePath, 'tag' => $tag, 'cat' => $categorieId, 'auteur' => $user['id'],
]);

jsonResponse([
    'success' => true,
    'message' => "Merci ! Votre article a été envoyé à la rédaction et sera publié après vérification.",
]);
