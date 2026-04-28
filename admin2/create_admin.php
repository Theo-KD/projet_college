<?php
require_once 'C:/wamp64/secure/db.php';

$hash = password_hash('', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO admin_users (username, password)
    VALUES (:username, :password)
");

$stmt->execute([
    'username' => '',
    'password' => $hash
]);

echo "Admin créé avec succès";