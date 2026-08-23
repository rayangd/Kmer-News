<?php
require_once __DIR__ . '/bootstrap.php';
jsonResponse(['success' => true, 'categories' => getCategories($pdo)]);
