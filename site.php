<?php
require_once 'C:/wamp64/secure/db.php';
require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// CONTENU
$stmt = $pdo->prepare("SELECT * FROM contenu WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    $article = [
        'titre' => 'Pas d’information pour le moment',
        'created_at' => date('Y-m-d'),
        'content_file' => null
    ];
    $content = "Aucun contenu disponible actuellement.";
} else {
    $contentFile = $article['content_file'] ?? null;
    $content = '';

    if ($contentFile && file_exists(__DIR__ . '/../' . $contentFile)) {
        $ext = strtolower(pathinfo($contentFile, PATHINFO_EXTENSION));
        $fullPath = __DIR__ . '/../' . $contentFile;

        if ($ext === 'docx') {
            $phpWord = IOFactory::load($fullPath);
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                        $content .= htmlspecialchars($element->getText()) . "<br><br>";
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        foreach ($element->getElements() as $child) {
                            if ($child instanceof \PhpOffice\PhpWord\Element\Text) {
                                $content .= htmlspecialchars($child->getText());
                            }
                        }
                        $content .= "<br><br>";
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\Link) {
                        $content .= "<a href=\"" . htmlspecialchars($element->getSource()) . "\">" . htmlspecialchars($element->getText()) . "</a><br><br>";
                    }
                }
            }
        } elseif ($ext === 'pdf') {
            $content = shell_exec("pdftotext " . escapeshellarg($fullPath) . " -");
        } else {
            $content = "Format de fichier non supporté.";
        }
    }
}

// IMAGES
$stmt = $pdo->prepare("SELECT image_path FROM image WHERE contenu_id = ? ORDER BY position ASC");
$stmt->execute([$id]);
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Normalisation chemin
foreach ($images as &$img) {
    if ($img[0] !== '/') $img = '/' . ltrim($img, '/');
}
unset($img);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($article['titre']) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/college.css">
<style>
body {background-image: url('img/college.jpg');}
.news-images { display:flex; flex-wrap: wrap; gap:10px; margin:15px 0; }
.news-images .image-box { position: relative; cursor:pointer; }
.news-images img { max-width: 250px; height:auto; border:1px solid #ccc; }
.lightbox { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:9999; flex-wrap: wrap; overflow:auto; }
.lightbox img { max-width:90%; max-height:80%; margin:5px; }
.lightbox .close { position:absolute; top:10px; right:20px; font-size:40px; color:white; cursor:pointer; }
</style>
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
        <img src="img/région.png" class="logo" alt="Logo du collège">
        <h1>Collège Aurélie LAMBOURDE</h1>
    </a>
</header>

<!-- NAV PRINCIPALE -->
<nav class="main-nav">
    <ul class="main-menu">
        <li><a href="college.php">Accueil</a></li>
        <span class="separator" aria-hidden="true">|</span>
        <li class="has-submenu">
            <a href="page.php?type=etablissement,presentation,services-organigramme,projet-etablissement,conseil-ecoles-college,restauration,contact,examen,information">L'établissement<span class="arrow"></span></a>
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
        <li><a href="page.php?type=numerique-emi">Numérique EMI & innovation pédagogique</a></li>
        <span class="separator" aria-hidden="true">|</span>
        <li class="has-submenu">
            <a href="page.php?type=vie-college,atelier-jeudi,peac,parcours-sante-citoyennete,assistante,association,cesc,cvc,infirmieres">Vie du collège et projets<span class="arrow"></span></a>
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
            <a href="page.php?type=parcours-avenir,cordees-reussite,liens-utiles,parcours-avenir-sites">Parcours Avenir / Orientation<span class="arrow"></span></a>
            <ul class="submenu">
                <li><a href="page.php?type=cordees-reussite">Cordées de la réussite</a></li>
                <li><a href="page.php?type=liens-utiles">Liens utiles pour accompagner son enfant</a></li>
                <li><a href="page.php?type=parcours-avenir-sites">Parcours Avenir : les sites indispensables</a></li>
            </ul>
        </li>
    </ul>
</nav>
<div class="main-content">
    <!-- CONTENU -->
    <div class="news-container">
        <h1><?= htmlspecialchars($article['titre']) ?></h1>
        <p class="date"><?= date('d/m/Y', strtotime($article['created_at'])) ?></p>

        <?php if (!empty($images)): ?>
        <div class="news-images">
            <?php foreach ($images as $img): ?>
                <div class="image-box" onclick="openLightbox()">
                    <img src="<?= htmlspecialchars($img) ?>" alt="">
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="news-content"><?= nl2br(htmlspecialchars_decode($content)) ?></div>
    </div>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox">
        <span class="close" onclick="closeLightbox()">&times;</span>
        <div class="lightbox-images">
            <?php foreach ($images as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="">
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-logo"><img src="img/région.jpg" alt="Logo"></div>
    <div class="footer-text">
      <p>© 2026 - Tous droits réservés Collège Aurélie LAMBOURDE</p> |
      <a href="mentions-legales.php">Mentions légales</a> |
      <a href="mailto:college@example.fr">college@example.fr</a> |
      <a href="tel:+590590123456">+590 590 12 34 56</a>
    </div>
  </div>
</footer>
</div>
</body>
</html>