<?php
$image = "img/college.jpg";
$logoCollege = "img/région.png";

/* GESTION DU TITRE DE PAGE */

$type = isset($_GET['type']) ? $_GET['type'] : 'mentions-legales';
$typeParts = explode(',', $type);
$mainType = strtolower(trim($typeParts[0]));

$pageTitle = $navTitles[$mainType] ?? "Mentions légales";

/* LECTURE DU FICHIER WORD */

// Chemin fichier mentions légales
$filePath = __DIR__ . '/documents/mention-legal.docx';

require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;

$content = '';
if (!file_exists($filePath)) {
    $content = "Mentions légales indisponibles.";
} else {
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    if ($extension === 'docx') {
        $phpWord = IOFactory::load($filePath);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $content .= $element->getText() . "\n\n";
                }
            }
        }
    } elseif ($extension === 'pdf') {
        $content = shell_exec("pdftotext " . escapeshellarg($filePath) . " -");
    } else {
        $content = "Format de fichier non supporté.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($pageTitle) ?> - Collège Aurélie LAMBOURDE</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/college.css">
<style>body{background-image:url('<?= htmlspecialchars($image) ?>');}</style>
</head>

<body>
<div class="main-content">
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

<!-- MENTIONS LÉGALES -->
<div class="news-container">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <div class="news-content">
        <?= nl2br(htmlspecialchars_decode($content)) ?>
    </div>
</div>

<script src="js/carousel.js"></script>
<script src="js/wheather.js"></script>

</div>
<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-logo">
      <img src="img/région.jpg" alt="Logo du site">
    </div>
    <div class="footer-text">
      <p>© 2026 - Tous droits réservés Mon Site</p> |
      <a href="mentions-legales.php?type=mentions-legales">Mentions légales</a> |
      <a href="mailto:college@example.fr">college@example.fr</a> |
      <a href="tel:+590590123456">+590 590 12 34 56</a>
    </div>
  </div>
</footer>

</body>
</html>