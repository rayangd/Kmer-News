<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

$body = jsonBody();
$prenom = trim($body['prenom'] ?? '');
$nom = trim($body['nom'] ?? '');
$email = trim(strtolower($body['email'] ?? ''));
$password = $body['password'] ?? '';

$errors = [];
if ($prenom === '' || $nom === '') $errors[] = 'Merci de renseigner votre prénom et votre nom.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Adresse e-mail invalide.';
if (!isPasswordValid($password)) $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';

if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Un compte existe déjà avec cette adresse e-mail.';
    }
}

if (!empty($errors)) {
    jsonResponse(['success' => false, 'message' => implode(' ', $errors)], 422);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, password_hash, role) VALUES (:p, :n, :e, :h, 'user')");
$stmt->execute(['p' => $prenom, 'n' => $nom, 'e' => $email, 'h' => $hash]);

attemptLogin($email, $password);

jsonResponse([
    'success' => true,
    'message' => 'Bienvenue sur Kmer News, ' . $prenom . ' !',
    'redirect' => '../espace/profil.html',
]);
