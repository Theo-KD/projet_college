<?php
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

$id = $_GET['id'] ?? null;

// Récupération de l'article si modification
$article = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Dossiers pour les fichiers
$docDir = __DIR__ . '/../col6/documents/';
$imgDir = __DIR__ . '/../col6/img/news/';

if (!is_dir($docDir)) mkdir($docDir, 0777, true);
if (!is_dir($imgDir)) mkdir($imgDir, 0777, true);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    
    // Document
    $docPath = $article['content_file'] ?? null;
    if (!empty($_FILES['content_file']['name'])) {
        $ext = pathinfo($_FILES['content_file']['name'], PATHINFO_EXTENSION);
        $docName = uniqid('doc_') . '.' . $ext;
        move_uploaded_file($_FILES['content_file']['tmp_name'], $docDir . $docName);
        $docPath = 'col6/documents/' . $docName;
    }

    // Images
    $existingImages = $article['image'] ? json_decode($article['image'], true) : [];
    $newImages = [];

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $i => $name) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $imgName = uniqid('img_') . '.' . $ext;
            move_uploaded_file($_FILES['images']['tmp_name'][$i], $imgDir . $imgName);
            $newImages[] = 'col6/img/news/' . $imgName;
        }
    }

    // Fusion des images existantes et nouvelles
    $allImages = array_merge($existingImages, $newImages);
    $imageJson = json_encode($allImages, JSON_UNESCAPED_SLASHES);

    // Insert / Update
    if ($id) {
        $stmt = $pdo->prepare("UPDATE news SET title = ?, content_file = ?, image = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $docPath, $imageJson, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO news (title, content_file, image, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$title, $docPath, $imageJson]);
    }

    header('Location: admin_news.php');
    exit;
}

// Pour affichage des images existantes
$existingImages = ($article && $article['image']) ? json_decode($article['image'], true) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= $id ? "Modifier l'article" : "Créer un article" ?></title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1><?= $id ? "Modifier l'article" : "Créer un article" ?></h1>
    <a href="admin_news.php" class="btn-back">Retour</a>
</header>

<form method="post" enctype="multipart/form-data" class="article-form">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

    <label>Titre :</label>
    <input type="text" name="title" value="<?= htmlspecialchars($article['title'] ?? '') ?>" required>

    <label>Fichier contenu (DOCX ou PDF) :</label>
    <?php if (!empty($article['content_file'])): ?>
        <p>Fichier actuel : <a href="<?= htmlspecialchars($article['content_file']) ?>" target="_blank"><?= basename($article['content_file']) ?></a></p>
    <?php endif; ?>
    <input type="file" name="content_file" accept=".docx,.pdf">

    <label>Images de l'article :</label>
    <?php if (!empty($existingImages)): ?>
        <div class="existing-images">
            <?php foreach ($existingImages as $img): ?>
                <div class="image-preview">
                    <img src="<?= htmlspecialchars($img) ?>" alt="">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <input type="file" name="images[]" multiple accept="image/*">

    <button type="submit"><?= $id ? "Mettre à jour" : "Créer l'article" ?></button>
</form>

</body>
</html>