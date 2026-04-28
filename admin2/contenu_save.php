<?php
session_start();
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

$id = $_POST['id'] ?? 0;
$titre = trim($_POST['titre'] ?? '');
$types = $_POST['types'] ?? [];
$existingImagesIds = $_POST['existing_images'] ?? [];

// --------------------
// DOSSIERS
// --------------------
$imgDir = __DIR__ . '/../col6/img/contenu/';
if (!is_dir($imgDir)) mkdir($imgDir, 0777, true);

$fileDir = __DIR__ . '/../col6/documents/';
if (!is_dir($fileDir)) mkdir($fileDir, 0777, true);

// --------------------
// 1️⃣ FICHIER DOCX / PDF
// --------------------
$contentFilePath = null;

if (!empty($_FILES['content_file']['name'])) {
    $ext = pathinfo($_FILES['content_file']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('doc_') . '.' . $ext;
    move_uploaded_file($_FILES['content_file']['tmp_name'], $fileDir . $fileName);
    $contentFilePath = 'col6/documents/' . $fileName; // chemin relatif
}

// --------------------
// 2️⃣ INSERT / UPDATE CONTENU
// --------------------
if ($id) {
    // Si un nouveau fichier est uploadé, on remplace
    if ($contentFilePath) {
        $stmt = $pdo->prepare("UPDATE contenu SET titre = ?, content_file = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$titre, $contentFilePath, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE contenu SET titre = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$titre, $id]);
    }
} else {
    $stmt = $pdo->prepare("INSERT INTO contenu (titre, content_file, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$titre, $contentFilePath]);
    $id = $pdo->lastInsertId();
}

// --------------------
// 3️⃣ TYPES LIÉS
// --------------------
$pdo->prepare("DELETE FROM contenu_type WHERE contenu_id = ?")->execute([$id]);

if (!empty($types)) {
    $stmt = $pdo->prepare("INSERT INTO contenu_type (contenu_id, type_id) VALUES (?, ?)");
    foreach ($types as $typeId) {
        $stmt->execute([$id, $typeId]);
    }
}

// --------------------
// 4️⃣ IMAGES
// --------------------

// Supprime les images non conservées
if (!empty($existingImagesIds)) {
    $placeholders = implode(',', array_fill(0, count($existingImagesIds), '?'));
    $pdo->prepare("DELETE FROM image WHERE contenu_id = ? AND id NOT IN ($placeholders)")
        ->execute(array_merge([$id], $existingImagesIds));
} else {
    $pdo->prepare("DELETE FROM image WHERE contenu_id = ?")->execute([$id]);
}

// Upload nouvelles images
if (!empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['name'] as $i => $name) {
        if (empty($name)) continue;

        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $imgName = uniqid('img_') . '.' . $ext;
        move_uploaded_file($_FILES['images']['tmp_name'][$i], $imgDir . $imgName);

        // Déterminer la position
        $stmt = $pdo->prepare("SELECT MAX(position) FROM image WHERE contenu_id = ?");
        $stmt->execute([$id]);
        $position = $stmt->fetchColumn();
        $position = $position !== null ? $position + 1 : 1;

        // Insert image
        $stmt = $pdo->prepare("INSERT INTO image (contenu_id, image_path, position) VALUES (?, ?, ?)");
        $stmt->execute([$id, 'col6/img/contenu/' . $imgName, $position]);
    }
}

header("Location: admin_contenu.php");
exit;