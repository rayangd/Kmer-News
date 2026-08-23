<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Méthode non autorisée.'], 405);
}

$body = jsonBody();
$nom = trim($body['nom'] ?? '');
$email = trim($body['email'] ?? '');
$sujet = trim($body['sujet'] ?? '');
$contenu = trim($body['contenu'] ?? '');

$errors = [];
if ($nom === '') $errors[] = 'Merci de renseigner votre nom.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Adresse e-mail invalide.';
if ($contenu === '') $errors[] = 'Le message ne peut pas être vide.';

if (!empty($errors)) {
    jsonResponse(['success' => false, 'message' => implode(' ', $errors)], 422);
}

$stmt = $pdo->prepare("INSERT INTO messages (nom, email, sujet, contenu) VALUES (:n, :e, :s, :c)");
$stmt->execute(['n' => $nom, 'e' => $email, 's' => $sujet, 'c' => $contenu]);

jsonResponse(['success' => true, 'message' => 'Votre message a bien été envoyé. Merci !']);
