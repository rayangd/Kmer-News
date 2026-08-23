<?php
require_once __DIR__ . '/bootstrap.php';
logoutUser();
jsonResponse(['success' => true, 'redirect' => '../index.html']);
