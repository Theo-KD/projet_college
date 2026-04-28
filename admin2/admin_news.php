<?php
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

// Recherche
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "SELECT * FROM news WHERE title LIKE :search";

// Tri
switch ($sort) {
    case 'alpha':
        $sql .= " ORDER BY title ASC";
        break;
    case 'date_asc':
        $sql .= " ORDER BY created_at ASC";
        break;
    case 'date_desc':
        $sql .= " ORDER BY created_at DESC";
        break;
    case 'update_desc':
        $sql .= " ORDER BY updated_at DESC";
        break;
    default:
        $sql .= " ORDER BY created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin News</title>
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

<div class="actions">
    <a href="article_form.php" class="btn-create">+ Créer un article</a>

    <form method="get" class="search-form">
        <input type="text" name="search" placeholder="Recherche par titre" value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
            <option value="">Aucun tri</option>
            <option value="alpha">Ordre alphabétique</option>
            <option value="date_asc">Date ↑</option>
            <option value="date_desc">Date ↓</option>
            <option value="update_desc">Dernière modification</option>
        </select>
        <button type="submit">Rechercher</button>
        <button type="button" id="deleteSelected">Supprimer la sélection</button>
    </form>
</div>

<form id="deleteForm">
<table class="articles-table">
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>Titre</th>
            <th>Créé le</th>
            <th>Modifié le</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $a): ?>
        <tr>
            <td><input type="checkbox" name="ids[]" value="<?= $a['id'] ?>"></td>
            <td class="clickable"
                onclick="location.href='article_form.php?id=<?= $a['id'] ?>'">
                <?= htmlspecialchars($a['title']) ?>
            </td>
            <td><?= $a['created_at'] ?></td>
            <td><?= $a['updated_at'] ?? '—' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</form>

<script src="articles.js"></script>
</body>
</html>