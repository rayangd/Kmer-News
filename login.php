<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

$body = jsonBody();
$email = trim(strtolower($body['email'] ?? ''));
$password = $body['password'] ?? '';

if ($email === '' || $password === '') {
    jsonResponse(['success' => false, 'message' => 'Merci de renseigner votre e-mail et votre mot de passe.'], 422);
}

$role = attemptLogin($email, $password);

if ($role === false) {
    jsonResponse(['success' => false, 'message' => 'Identifiants incorrects, ou trop de tentatives récentes. Réessayez dans quelques minutes.'], 401);
}

// === Détection automatique du rôle : le front redirige selon la valeur "role" ===
jsonResponse([
    'success' => true,
    'role' => $role, // 'admin' ou 'user'
    'redirect' => $role === 'admin' ? '../admin/index.html' : '../espace/profil.html',
]);
