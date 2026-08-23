<?php
require_once __DIR__ . '/../bootstrap.php';
apiRequireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

// --- Liste / recherche ---
if ($method === 'GET' && !isset($_GET['id'])) {
    $search = trim($_GET['q'] ?? '');
    $catFilter = (int)($_GET['cat'] ?? 0);
    $statutFilter = $_GET['statut'] ?? '';

    $sql = "SELECT a.*, c.nom AS cat_nom, c.couleur AS cat_couleur, u.prenom, u.nom, u.role AS auteur_role
            FROM articles a JOIN categories c ON c.id=a.categorie_id JOIN users u ON u.id=a.auteur_id WHERE 1=1";
    $params = [];
    if ($search !== '') { $sql .= " AND a.titre LIKE :q"; $params['q'] = "%$search%"; }
    if ($catFilter) { $sql .= " AND a.categorie_id = :cat"; $params['cat'] = $catFilter; }
    if ($statutFilter) { $sql .= " AND a.statut = :statut"; $params['statut'] = $statutFilter; }
    $sql .= " ORDER BY a.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    jsonResponse(['success' => true, 'articles' => $stmt->fetchAll()]);
}

// --- Détail d'un article (pour pré-remplir le formulaire d'édition) ---
if ($method === 'GET' && isset($_GET['id'])) {
    $article = getArticleById($pdo, (int)$_GET['id']);
    if (!$article) jsonResponse(['success' => false, 'message' => 'Article introuvable.'], 404);
    jsonResponse(['success' => true, 'article' => $article]);
}

// --- Création / modification (multipart/form-data car upload d'image possible) ---
if ($method === 'POST') {
    checkCsrfApi();

    $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
    $existing = $id ? getArticleById($pdo, $id) : null;
    if ($id && !$existing) jsonResponse(['success' => false, 'message' => 'Article introuvable.'], 404);

    $titre = trim($_POST['titre'] ?? '');
    $sousTitre = trim($_POST['sous_titre'] ?? '');
    $chapo = trim($_POST['chapo'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $tag = trim($_POST['tag'] ?? '');
    $categorieId = (int)($_POST['categorie_id'] ?? 0);
    $statut = in_array($_POST['statut'] ?? '', ['publie', 'brouillon']) ? $_POST['statut'] : 'brouillon';
    $aLaUne = !empty($_POST['a_la_une']) ? 1 : 0;

    $errors = [];
    if ($titre === '') $errors[] = 'Le titre est obligatoire.';
    if ($contenu === '') $errors[] = 'Le contenu est obligatoire.';
    if (!$categorieId) $errors[] = 'Merci de choisir une rubrique.';

    $imagePath = $existing['image'] ?? null;
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
            $uploadDir = __DIR__ . '/../../assets/img/uploads/';
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

    $admin = currentUser();
    $slug = ($existing && $existing['titre'] === $titre) ? $existing['slug'] : uniqueArticleSlug($pdo, $titre, $id);
    $publishedAt = $existing['published_at'] ?? null;
    if ($statut === 'publie' && !$publishedAt) {
        $publishedAt = date('Y-m-d H:i:s');
    }

    if ($existing) {
        $stmt = $pdo->prepare(
            "UPDATE articles SET titre=:titre, slug=:slug, sous_titre=:st, chapo=:chapo, contenu=:contenu,
             image=:image, tag=:tag, categorie_id=:cat, statut=:statut, a_la_une=:une, published_at=:pub
             WHERE id=:id"
        );
        $stmt->execute(['titre'=>$titre,'slug'=>$slug,'st'=>$sousTitre,'chapo'=>$chapo,'contenu'=>$contenu,
            'image'=>$imagePath,'tag'=>$tag,'cat'=>$categorieId,'statut'=>$statut,'une'=>$aLaUne,'pub'=>$publishedAt,'id'=>$id]);
        jsonResponse(['success' => true, 'message' => 'Article mis à jour avec succès.']);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO articles (titre, slug, sous_titre, chapo, contenu, image, tag, categorie_id, auteur_id, statut, a_la_une, published_at)
             VALUES (:titre,:slug,:st,:chapo,:contenu,:image,:tag,:cat,:auteur,:statut,:une,:pub)"
        );
        $stmt->execute(['titre'=>$titre,'slug'=>$slug,'st'=>$sousTitre,'chapo'=>$chapo,'contenu'=>$contenu,
            'image'=>$imagePath,'tag'=>$tag,'cat'=>$categorieId,'auteur'=>$admin['id'],'statut'=>$statut,'une'=>$aLaUne,'pub'=>$publishedAt]);
        jsonResponse(['success' => true, 'message' => 'Article créé avec succès.']);
    }
}

// --- Suppression ---
if ($method === 'DELETE') {
    checkCsrfApi();
    $body = jsonBody();
    $id = (int)($body['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->execute(['id' => $id]);
    jsonResponse(['success' => true, 'message' => 'Article supprimé.']);
}

jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
