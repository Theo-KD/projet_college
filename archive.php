<?php
$image = "img/college.jpg";
$logoCollege = "img/région.png";

// Connexion BDD
require_once 'C:/wamp64/secure/db.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;


// Total articles
$totalArticles = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalPages = ceil($totalArticles / $limit);

// Normalisation des images
function normalizeImages($imageField) {
    $imgs = json_decode($imageField, true);
    if (!is_array($imgs)) $imgs = $imageField ? [$imageField] : [];
    foreach ($imgs as &$img) {
        if ($img[0] !== '/') $img = '/' . ltrim($img, '/');
    }
    unset($img);
    return $imgs;
}

// Articles
$stmt = $pdo->prepare("
    SELECT * FROM news
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fonction pour extraire un extrait depuis content_file
function getArticleExcerpt($filePath, $length = 150) {
    if (!$filePath || !file_exists($filePath)) {
        return "Contenu indisponible.";
    }

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $content = "";

    if ($extension === 'docx') {
        require_once __DIR__ . '/vendor/autoload.php';
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                // TEXTE + LIENS
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $child) {

                        // Texte simple
                        if ($child instanceof \PhpOffice\PhpWord\Element\Text) {
                            $content .= htmlspecialchars($child->getText()) . ' ';
                        }

                        // Lien Word
                        if ($child instanceof \PhpOffice\PhpWord\Element\Link) {
                            $url = $child->getSource();
                            $text = $child->getText();
                            $content .= '<a href="'.htmlspecialchars($url).'">'.htmlspecialchars($text).'</a> ';
                        }
                    }
                }

                // Cas simple (paragraphes sans TextRun)
                elseif (method_exists($element, 'getText')) {
                    $content .= htmlspecialchars($element->getText()) . ' ';
                }
            }
        }
    }
    elseif ($extension === 'pdf') {
        $content = shell_exec("pdftotext " . escapeshellarg($filePath) . " -");
    }

    $content = preg_replace('/\s+/', ' ', trim($content));
    return mb_substr($content, 0, $length);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Archives des actualités</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/college.css">
<style>body{background-image:url('<?= htmlspecialchars($image) ?>')}</style>
</head>

<body>

<!-- NAV TOP -->
<nav class="top-nav">
    <a href="page.php?type=eleve-top">Élèves</a>
    <a href="page.php?type=parent-top">Parents</a>
    <a href="page.php?type=enseignant-top">Enseignants</a>
    <a href="page.php?type=autre-top">Autres</a>

    <a href="https://x.com/ClgLambourde971" class="x-link" target="_blank">
        <img src="img/X-black.png" class="x-logo" alt="X logo">
    </a>

    <div class="gouv">
        <a href="https://www.ac-guadeloupe.fr/calendriers/calendrier_vacances_scolaires" target="_blank">
            Calendriers
        </a>
    </div>
</nav>

<!-- HEADER -->
<header>
    <a href="college.php" class="header-link">
        <img src="<?= htmlspecialchars($logoCollege) ?>" class="logo" alt="Logo du collège">
        <h1>Collège Aurélie LAMBOURDE</h1>
    </a>


</header>

<!-- NAV PRINCIPALE -->
<nav class="main-nav">
    <ul class="main-menu">
        <li><a href="college.php">Accueil</a></li>
        <span class="separator" aria-hidden="true">|</span>

       <!-- <li><a href="#">Plan du site</a></li>
        <span class="separator" aria-hidden="true">|</span>-->

        <li class="has-submenu">
            <a href="page.php?type=etablissement,presentation,services-organigramme,projet-etablissement,conseil-ecoles-college,restauration,contact,examen,information">L'établissement<span class="arrow"></a>
            <ul class="submenu">
                <li><a href="page.php?type=presentation">Présentation</a></li>
                <li><a href="page.php?type=Services-organigramme">Services / organigramme</a></li>
                <li><a href="page.php?type=projet-etablissement">Projet d'établissement</a></li>
                <li><a href="page.php?type=conseil-ecoles-college">Conseil écoles collège</a></li>
                <li><a href="page.php?type=restauration">Restauration</a></li>
                <li><a href="page.php?type=contact">Contact</a></li>
                <li><a href="page.php?type=examen">Examens / formations</a></li>
                <li><a href="page.php?type=information">Informations de la direction</a></li>
                <li><a href="page.php?type=parent">Parents d'élèves</a></li>
            </ul>
        </li>
        <span class="separator" aria-hidden="true">|</span>

        <li>
            <a href="page.php?type=numerique-emi">Numérique EMI & innovation pédagogique</a>
        </li>
        <span class="separator" aria-hidden="true">|</span>

        <li class="has-submenu">
            <a href="page.php?type=vie-college,atelier-jeudi,peac,parcours-sante-citoyennete,assistante,association,cesc,cvc,infirmieres">Vie du collège et projets<span class="arrow"></a>
            <ul class="submenu">
                <li><a href="page.php?type=atelier-jeudi">Atelier du jeudi</a></li>
                <li><a href="page.php?type=peac">Parcours d'éducation culturelle et artistique (PEAC)</a></li>
                <li><a href="page.php?type=parcours-sante-citoyennete">Parcours santé & citoyenneté</a></li>
                <li><a href="page.php?type=assistante">Assistante sociale</a></li>
                <li><a href="page.php?type=association">Association sportive</a></li>
                <li><a href="page.php?type=cesc">CESC</a></li>
                <li><a href="page.php?type=cvc">CVC Conseil de vie collégienne</a></li>
                <li><a href="page.php?type=infirmieres">Infirmières</a></li>
            </ul>
        </li>
        <span class="separator" aria-hidden="true">|</span>

        <li class="has-submenu">
            <a href="page.php?type=parcours-avenir,cordees-reussite,liens-utiles,parcours-avenir-sites">Parcours Avenir / Orientation<span class="arrow"></a>
            <ul class="submenu">
                <li><a href="page.php?type=cordees-reussite">Cordées de la réussite</a></li>
                <li><a href="page.php?type=liens-utiles">Liens utiles pour accompagner son enfant</a></li>
                <li><a href="page.php?type=parcours-avenir-sites">Parcours Avenir : les sites indispensables</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- ARCHIVES -->
<section class="archive-container">
<h2>📰 Archives des actualités</h2>

<?php if (empty($articles)): ?>
    <p>Pas d’information pour le moment.</p>
<?php else: ?>
    <?php foreach ($articles as $article): ?>
        <article class="archive-article">
            <?php
                $images = normalizeImages($article['image']);
                if ($images && !empty($images[0])):
                ?>
                <div class="archive-image">
                <img src="<?= htmlspecialchars($images[0]) ?>" alt="">
                </div>
            <?php endif; ?>

            <div class="archive-content">
                <h3>
                    <a href="news.php?id=<?= $article['id'] ?>" class="article-title">
                        <?= htmlspecialchars($article['title']) ?>
                    </a>
                </h3>

                <span class="archive-date">
                    📅 <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                </span>

                <p>
                    <?= htmlspecialchars_decode(getArticleExcerpt($article['content_file'])) ?>
                    <a href="news.php?id=<?= $article['id'] ?>">Lire la suite…</a>
                </p>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">⬅ Précédent</a>
    <?php endif; ?>

    <span>Page <?= $page ?> / <?= $totalPages ?></span>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Suivant ➡</a>
    <?php endif; ?>
</div>
<?php endif; ?>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-logo">
      <img src="img/région.jpg" alt="Logo du site">
    </div>
    <div class="footer-text">
      <p>© 2026 - Tous droits réservés Mon Site</p> |
      <a href="mentions-legales.php">Mentions légales</a> |
      <a href="mailto:college@example.fr">college@example.fr</a> |
      <a href="tel:+590590123456">+590 590 12 34 56</a>
    </div>
  </div>
</footer>

</body>
</html>