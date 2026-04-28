<?php
$image = "img/college.jpg";
$logoCollege = "img/région.png";

// Connexion
require_once 'C:/wamp64/secure/db.php';
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;

// Carrousel BDD
$slides = $pdo->query("SELECT * FROM carousel WHERE active=1 ORDER BY position ASC")->fetchAll();

//"À la une"
$articles = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 6")->fetchAll();
$totalArticles = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();

// Infos
$infos = $pdo->query("SELECT * FROM infos_etablissement LIMIT 1")->fetch();

// Fonction pour lire le fichier content_file (DOCX ou PDF)
function getContentFromFile($filePath) {
    if (!$filePath || !file_exists($filePath)) return "Contenu indisponible.";

    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $content = '';

    if ($ext === 'docx') {
        $phpWord = IOFactory::load($filePath);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                    $text = is_array($element->getText()) ? implode(' ', $element->getText()) : $element->getText();
                    $content .= htmlspecialchars($text) . "<br><br>";
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Link) {
                    $url  = htmlspecialchars((string) $element->getSource());
                    $text = is_array($element->getText()) ? implode(' ', $element->getText()) : $element->getText();
                    $content .= "<a href=\"$url\">".htmlspecialchars($text)."</a><br><br>";
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $child) {
                        if ($child instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text = is_array($child->getText()) ? implode(' ', $child->getText()) : $child->getText();
                            $content .= htmlspecialchars($text);
                        } elseif ($child instanceof \PhpOffice\PhpWord\Element\Link) {
                            $url  = htmlspecialchars((string) $child->getSource());
                            $text = is_array($child->getText()) ? implode(' ', $child->getText()) : $child->getText();
                            $content .= "<a href=\"$url\">".htmlspecialchars($text)."</a>";
                        }
                    }
                    $content .= "<br><br>";
                }
            }
        }
    } elseif ($ext === 'pdf') {
        $content = shell_exec("pdftotext " . escapeshellarg($filePath) . " -");
    } else {
        $content = "Format de fichier non supporté.";
    }
    return $content;
}

//images
function normalizeImages($imageField) {
    $imgs = json_decode($imageField, true);
    if (!is_array($imgs)) $imgs = $imageField ? [$imageField] : [];
    foreach ($imgs as &$img) {
        if ($img[0] !== '/') $img = '/' . ltrim($img, '/');
    }
    unset($img);
    return $imgs;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Collège Aurélie LAMBOURDE</title>
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
    <a href="#" class="header-link">
        <img src="<?= htmlspecialchars($logoCollege) ?>" class="logo" alt="Logo du collège">
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
                <li><a href="page.php?type=peac">PEAC</a></li>
                <li><a href="page.php?type=parcours-sante-citoyennete">Parcours santé & citoyenneté</a></li>
                <li><a href="page.php?type=assistante">Assistante sociale</a></li>
                <li><a href="page.php?type=association">Association sportive</a></li>
                <li><a href="page.php?type=cesc">CESC</a></li>
                <li><a href="page.php?type=cvc">CVC</a></li>
                <li><a href="page.php?type=infirmieres">Infirmières</a></li>
            </ul>
        </li>
        <span class="separator" aria-hidden="true">|</span>

        <li class="has-submenu">
            <a href="page.php?type=parcours-avenir,cordees-reussite,liens-utiles,parcours-avenir-sites">Parcours Avenir / Orientation<span class="arrow"></span></a>
            <ul class="submenu">
                <li><a href="page.php?type=cordees-reussite">Cordées de la réussite</a></li>
                <li><a href="page.php?type=liens-utiles">Liens utiles</a></li>
                <li><a href="page.php?type=parcours-avenir-sites">Sites indispensables</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- CARROUSEL + INFOS + MÉTÉO -->
<div class="carousel-weather-container">

    <!-- CARROUSEL -->
    <div class="carousel">
        <?php foreach($slides as $i => $s):
            $imgPath = "img/carousel/" . $s['image'];
            if (!file_exists($imgPath)) $imgPath = "img/carousel/default.png";
        ?>
        <div class="slide <?= $i===0 ? 'active' : '' ?>">
            <img src="<?= htmlspecialchars($imgPath) ?>" alt="Slide <?= $i+1 ?>">
            <div class="slide-text">
                <h2><?= htmlspecialchars($s['title']) ?></h2>
                <h4><?= htmlspecialchars($s['subtitle']) ?></h4>
                <p><?= htmlspecialchars($s['text']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <button class="prev">&#10094;</button>
        <button class="next">&#10095;</button>
    </div>

    <!-- INFOS ÉTABLISSEMENT -->
    <div class="infos-etab-box">
        <h3>🏫 Établissement</h3>
        <p><strong>Nombre d'élèves :</strong><br><?= htmlspecialchars($infos['nb_eleves']) ?></p>
        <p><strong>Chef d'établissement :</strong><br><?= htmlspecialchars($infos['chef_etablissement']) ?></p>
        <p><strong>Adjoint :</strong><br><?= htmlspecialchars($infos['adjoint']) ?></p>
    </div>

    <!-- MÉTÉO -->
    <div class="weather-box">
        <h2>Météo</h2>
        <p class="temp">-- °C</p>
        <p class="desc">Chargement...</p>
        <img class="weather-icon" alt="Icône météo">
    </div>

</div>

<!-- À LA UNE -->
<section class="a-la-une">
<h2>📰 À la une</h2>
<div class="articles-grid">
<?php foreach($articles as $a):
    $imgs = normalizeImages($a['image']);
    $content = getContentFromFile($a['content_file'] ? __DIR__ . '/../' . $a['content_file'] : null);
?>
<article class="article">
    <a href="news.php?id=<?= $a['id'] ?>" class="article-title"><?= htmlspecialchars($a['title']) ?></a>
    <?php if($imgs): ?>
        <div class="article-images">
            <img src="<?= htmlspecialchars($imgs[0]) ?>" alt="">
        </div>
    <?php endif; ?>
    <p class="article-excerpt">
        <?= htmlspecialchars_decode($content) ?>
        <a href="news.php?id=<?= $a['id'] ?>">Lire la suite...</a>
    </p>
</article>
<?php endforeach; ?>
</div>
<?php if($totalArticles > 6): ?>
<div class="archive-link"><a href="archive.php">Archives</a></div>
<?php endif; ?>
</section>

<!-- INFOS PRATIQUES -->
<section class="infos-pratiques">
<h2>📍 Accès & horaires d’ouverture</h2>
<div class="infos-grid">
    <div class="horaires-box">
        <table>
            <thead>
                <tr><th></th><th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th></tr>
            </thead>
            <tbody>
                <tr><td>Matin</td><td>7h30-11h30</td><td>7h30-11h30</td><td>7h30-12h30</td><td>7h30-11h30</td><td>7h30-12h30</td></tr>
                <tr><td>Après-midi</td><td>13h-17h</td><td>13h-17h</td><td>—</td><td>13h-17h</td><td>—</td></tr>
            </tbody>
        </table>
    </div>

    <div class="map-box">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3830.02019644519!2d-61.5072432250155!3d16.270735384438364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8c13488507fcf5fb%3A0x584c8f8580ff9ae3!2sColl%C3%A8ge%20Aur%C3%A9lie%20Lambourde!5e0!3m2!1sfr!2sfr!4v1766080567081!5m2!1sfr!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>
</section>

</div>

<script src="js/carousel.js"></script>
<script src="js/wheather.js"></script>

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