<?php
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');

if ($title === '') {
    die("Titre manquant");
}

/* ==========================
   DOSSIERS
========================== */
$docDir   = __DIR__ . '/../col6/documents/';
$imgDir   = __DIR__ . '/../col6/img/news/';

if (!is_dir($docDir)) mkdir($docDir, 0777, true);
if (!is_dir($imgDir)) mkdir($imgDir, 0777, true);

/* ==========================
   ARTICLE EXISTANT ?
========================== */
$article = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
}

/* ==========================
   DOCUMENT
========================== */
$docPath = $article['content_file'] ?? null;

if (!empty($_FILES['content_file']['name'])) {
    $ext = pathinfo($_FILES['content_file']['name'], PATHINFO_EXTENSION);
    $docName = uniqid('doc_') . '.' . $ext;
    move_uploaded_file($_FILES['content_file']['tmp_name'], $docDir . $docName);
    $docPath = 'col6/docs/news/' . $docName;
}

/* ==========================
   IMAGE (UNE SEULE)
========================== */
$imagePath = $article['image'] ?? null;

if (!empty($_FILES['images']['name'][0])) {
    $ext = pathinfo($_FILES['images']['name'][0], PATHINFO_EXTENSION);
    $imgName = uniqid('img_') . '.' . $ext;
    move_uploaded_file($_FILES['images']['tmp_name'][0], $imgDir . $imgName);
    $imagePath = 'col6/img/news/' . $imgName;
}

/* ==========================
   INSERT / UPDATE
========================== */
if ($id) {
    $stmt = $pdo->prepare("
        UPDATE news
        SET title = ?, content_file = ?, image = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$title, $docPath, $imagePath, $id]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO news (title, content_file, image, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$title, $docPath, $imagePath]);
}

/* ==========================
   REDIRECTION
========================== */
header('Location: admin_news.php');
exit; 