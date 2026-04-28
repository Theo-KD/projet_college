<?php
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* CONTENU PAR DÉFAUT */
$contenu = [
    'titre' => '',
    'content_file' => ''
];

/* CHARGEMENT CONTENU (MODIFICATION) */
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM contenu WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $contenu = $result;
    }
}

/* TOUS LES TYPES */
$types = $pdo->query("SELECT id, slug, nom FROM type ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

/* TYPES DÉJÀ LIÉS */
$selectedTypes = [];
if ($id) {
    $stmt = $pdo->prepare("SELECT type_id FROM contenu_type WHERE contenu_id = ?");
    $stmt->execute([$id]);
    $selectedTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/* IMAGES EXISTANTES */
$existingImages = [];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM image WHERE contenu_id = ? ORDER BY position ASC");
    $stmt->execute([$id]);
    $existingImages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* DOSSIER DE STOCKAGE */
$imgDir = __DIR__ . '/../col6/img/contenu/';
if (!is_dir($imgDir)) mkdir($imgDir, 0777, true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= $id ? 'Modifier' : 'Créer' ?> un contenu</title>
<link rel="stylesheet" href="admin.css">
<style>
.existing-images { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
.existing-images img { width:120px; height:auto; border:1px solid #ccc; }
.checkbox-group { margin-bottom:15px; }
</style>
</head>
<body>

<header class="admin-header">
    <h1><?= $id ? 'Modifier' : 'Créer' ?> un contenu</h1>
    <a href="admin_contenu.php" class="btn-back">← Retour</a>
</header>

<form action="contenu_save.php" method="post" enctype="multipart/form-data" class="article-form">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- TITRE -->
    <label for="titre">Titre</label>
    <input type="text" id="titre" name="titre"
           value="<?= htmlspecialchars($contenu['titre'] ?? '') ?>"
           required>

    <!-- FICHIER -->
    <label for="content_file">Fichier (DOCX / PDF)</label>
    <input type="file" id="content_file" name="content_file" accept=".docx,.pdf">
    <?php if (!empty($contenu['content_file'])): ?>
        <p>Fichier actuel :
            <a href="<?= htmlspecialchars($contenu['content_file']) ?>" target="_blank">
                <?= basename($contenu['content_file']) ?>
            </a>
        </p>
    <?php endif; ?>
<!-- IMAGES -->
    <label>Images associées</label>
    <?php if (!empty($existingImages)): ?>
        <div class="existing-images">
            <?php foreach ($existingImages as $img): ?>
                <div class="image-item">
                    <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="">
                    <input type="hidden" name="existing_images[]" value="<?= $img['id'] ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <input type="file" name="images[]" multiple accept="image/*">

    <!-- TYPES / SLUGS -->
    <label>Pages associées</label>
    <div class="checkbox-group">
        <?php foreach ($types as $t): ?>
            <label>
                <input type="checkbox"
                       name="types[]"
                       value="<?= $t['id'] ?>"
                       <?= in_array($t['id'], $selectedTypes) ? 'checked' : '' ?>>
                <?= htmlspecialchars($t['nom']) ?> <small>(<?= $t['slug'] ?>)</small>
            </label><br>
        <?php endforeach; ?>
    </div>

    <button type="submit"><?= $id ? "Mettre à jour" : "Créer le contenu" ?></button>

</form>

</body>
</html>