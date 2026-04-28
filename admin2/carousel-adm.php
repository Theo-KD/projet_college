<?php
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php'; // connexion PDO

$imagePath = '../col6/img/carousel/';

// Récupérer toutes les slides, triées par position
$stmt = $pdo->query("SELECT * FROM carousel ORDER BY position ASC");
$slides = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Carrousel</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Gestion du site</h1>
    <nav class="admin-nav">
        <a href="admin_contenu.php">Contenus</a> |
        <a href="admin_news.php">News</a> |
        <a href="carousel-adm.php">Carrousel</a> |
    </nav>
    <a href="logout.php" class="logout-btn">Déconnexion</a>
</header>

<div class="container">

    <div class="carousel-admin">
        <?php foreach ($slides as $slide): ?>
        <div class="slide">
            <img src="<?= $imagePath . htmlspecialchars($slide['image']) ?>" alt="Slide <?= $slide['position'] ?>">
            <div class="options">
                <label>
                    <input type="radio" name="edit_slide"
                           class="edit-radio"
                           data-id="<?= $slide['id'] ?>"
                           data-title="<?= htmlspecialchars($slide['title']) ?>"
                           data-subtitle="<?= htmlspecialchars($slide['subtitle']) ?>"
                           data-text="<?= htmlspecialchars($slide['text']) ?>"
                           data-image="<?= htmlspecialchars($slide['image']) ?>">
                    Modification
                </label>
                <br>
                <label>
                    <input type="checkbox"
                           class="active-checkbox"
                           <?= $slide['active'] ? 'checked' : '' ?>
                           onchange="toggleActive(<?= $slide['id'] ?>, this.checked)">
                    Activation / Désactivation
                </label>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Formulaire de modification -->
    <form id="editForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="slide_id">
        <div class="editor hidden" id="editor">

            <div class="upload-zone">
                <h3>Image du carousel</h3>
                
                <!-- Afficher l'image actuelle -->
                <img id="currentImage" src="" alt="Image actuelle" style="max-width:200px; display:block; margin-bottom:10px;">

                <!-- Choisir une image existante -->
                <label>Utiliser une image existante :</label>
                <select id="existingImage" name="existing_image">
                    <option value="">-- Aucune --</option>
                    <?php
                    $files = glob('../col6/img/carousel/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                    foreach($files as $file){
                        $filename = basename($file);
                        echo "<option value=\"$filename\">$filename</option>";
                    }
                    ?>
                </select>

                <p>Ou téléverser une nouvelle image :</p>
                <input type="file" name="image">
            </div>

            <div class="text-zone">
                <h3>Modification du texte du carousel</h3>

                <label>Titre</label>
                <input type="text" name="title" id="title" value="">

                <label>Sous-titre</label>
                <input type="text" name="subtitle" id="subtitle" value="">

                <label>Texte</label>
                <textarea name="text" rows="5" id="text"></textarea>

                <button type="submit">Enregistrer</button>
                <p id="successMsg" style="color:green; display:none;">Modification effectuée !</p>
            </div>

        </div>
    </form>
</div>

<script src="carousel.js"></script>
</body>
</html>