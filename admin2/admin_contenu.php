<?php
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

$search = $_GET['search'] ?? '';
$sort   = $_GET['sort'] ?? '';

$sql = "
SELECT 
    c.id,
    c.titre,
    c.created_at,
    c.updated_at,
    GROUP_CONCAT(t.slug ORDER BY t.slug SEPARATOR ', ') AS slugs
FROM contenu c
LEFT JOIN contenu_type ct ON ct.contenu_id = c.id
LEFT JOIN type t ON t.id = ct.type_id
WHERE c.titre LIKE :search
GROUP BY c.id
";

switch ($sort) {
    case 'alpha':
        $sql .= " ORDER BY c.titre ASC";
        break;
    case 'date_asc':
        $sql .= " ORDER BY c.created_at ASC";
        break;
    case 'date_desc':
        $sql .= " ORDER BY c.created_at DESC";
        break;
    case 'update_desc':
        $sql .= " ORDER BY c.updated_at DESC";
        break;
    default:
        $sql .= " ORDER BY c.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin – Contenus</title>
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
    <a href="contenu_form.php" class="btn-create">+ Créer un contenu</a>

    <form method="get" class="search-form">
        <input type="text" name="search" placeholder="Recherche par titre"
               value="<?= htmlspecialchars($search) ?>">

        <select name="sort">
            <option value="">Aucun tri</option>
            <option value="alpha" <?= $sort === 'alpha' ? 'selected' : '' ?>>Ordre alphabétique</option>
            <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Date ↑</option>
            <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Date ↓</option>
            <option value="update_desc" <?= $sort === 'update_desc' ? 'selected' : '' ?>>Dernière modification</option>
        </select>

        <button type="submit">Rechercher</button>

        <button type="button" id="deleteSelected" class="btn-delete">
        Supprimer la sélection
    </button>
    </form>
</div>

<form id="deleteForm" method="post" action="contenu_delete.php">
<table class="articles-table">
<thead>
<tr>
    <th><input type="checkbox" id="selectAll"></th>
    <th>Titre</th>
    <th>Slug(s)</th>
    <th>Créé le</th>
    <th>Modifié le</th>
</tr>
</thead>
<tbody>
<?php if (empty($contenus)): ?>
<tr>
    <td colspan="5" style="text-align:center;">Aucun contenu trouvé</td>
</tr>
<?php else: ?>
<?php foreach ($contenus as $c): ?>
<tr>
    <td>
        <input type="checkbox" name="ids[]" value="<?= $c['id'] ?>">
    </td>

    <td class="clickable"
        onclick="location.href='contenu_form.php?id=<?= $c['id'] ?>'">
        <?= htmlspecialchars($c['titre']) ?>
    </td>

    <td><?= htmlspecialchars($c['slugs'] ?? '—') ?></td>
    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
    <td><?= $c['updated_at'] ? date('d/m/Y', strtotime($c['updated_at'])) : '—' ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</form>

<script>
// Sélectionner tout
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
        cb.checked = this.checked;
    });
});

// Supprimer sélection
document.getElementById('deleteSelected')?.addEventListener('click', function () {
    const checked = document.querySelectorAll('input[name="ids[]"]:checked');

    if (checked.length === 0) {
        alert("Aucun contenu sélectionné.");
        return;
    }

    if (confirm("Supprimer définitivement les contenus sélectionnés ?")) {
        document.getElementById('deleteForm').submit();
    }
});
</script>

</body>
</html>