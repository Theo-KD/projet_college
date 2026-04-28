<?php
require_once 'auth.php';
require_once 'C:/wamp64/secure/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM news WHERE id=?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    die("Article introuvable");
}

$imagesDir = __DIR__ . "/../col6/img/news/$id/";
$images = glob($imagesDir . "*");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier article</title>
<link rel="stylesheet" href="articles.css">
</head>
<body>

<header class="admin-header">
    <h2><?= htmlspecialchars($article['title']) ?></h2>
    <a href="/admin/articles.php">⬅ Retour</a>
</header>

<h3>Images</h3>
<div class="images">
<?php foreach ($images as $img): ?>
    <div class="image-box">
        <img src="/col6/img/news/<?= $id ?>/<?= basename($img) ?>" width="120">
        <button onclick="deleteImage('<?= basename($img) ?>')">Supprimer</button>
    </div>
<?php endforeach; ?>
</div>

<h3>Document</h3>
<p><?= htmlspecialchars($article['content_file']) ?></p>

<form method="post" action="/admin/article-update.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="file" name="document" accept=".pdf,.doc,.docx">
    <button type="submit">Remplacer le document</button>
</form>

<script>
function deleteImage(img){
    if(!confirm("Supprimer cette image ?")) return;

    fetch('/admin/image-delete.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `id=<?= $id ?>&image=${encodeURIComponent(img)}`
    })
    .then(() => location.reload());
}
</script>

</body>
</html>