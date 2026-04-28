<?php
require_once 'C:/wamp64/secure/db.php';

$id = (int)$_POST['id'];

$stmt = $pdo->prepare("SELECT filename FROM news_images WHERE id=?");
$stmt->execute([$id]);
$img = $stmt->fetch();

if ($img) {
    unlink('../uploads/news/'.$img['filename']);
    $pdo->prepare("DELETE FROM news_images WHERE id=?")->execute([$id]);
}

echo "ok";