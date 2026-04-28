<?php
session_start();
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

$ids = $_POST['ids'] ?? [];

if (empty($ids)) {
    header("Location: admin_contenu.php");
    exit;
}

foreach ($ids as $id) {
    $id = (int)$id;

    /* -------- DOC / PDF -------- */
    $stmt = $pdo->prepare("SELECT content_file FROM contenu WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();

    if ($file) {
        $path = __DIR__ . '/../' . $file;
        if (file_exists($path)) unlink($path);
    }

    /* -------- IMAGES -------- */
    $stmt = $pdo->prepare("SELECT image_path FROM image WHERE contenu_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($images as $img) {
        $imgPath = __DIR__ . '/../' . ltrim($img, '/');
        if (file_exists($imgPath)) unlink($imgPath);
    }

    $pdo->prepare("DELETE FROM image WHERE contenu_id = ?")->execute([$id]);

    /* -------- TYPES -------- */
    $pdo->prepare("DELETE FROM contenu_type WHERE contenu_id = ?")->execute([$id]);

    /* -------- CONTENU -------- */
    $pdo->prepare("DELETE FROM contenu WHERE id = ?")->execute([$id]);
}

header("Location: admin_contenu.php");
exit;