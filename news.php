<?php
$image = "img/college.jpg";
$logoCollege = "img/région.png";

// Connexion à la BDD
require_once 'C:/wamp64/secure/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    $article = [
        'title' => 'Pas d’information pour le moment',
        'created_at' => date('Y-m-d'),
        'image' => json_encode([]),
        'content_file' => null
    ];
    $content = 'Aucun article disponible actuellement.';
}

// Infos établissement
$infos = $pdo->query("SELECT * FROM infos_etablissement LIMIT 1")->fetch();

// Images
$images = [];

if (!empty($article['image'])) {
    $decoded = json_decode($article['image'], true);

    if (is_array($decoded)) {
        $images = $decoded;
    } else {
        $images = [$article['image']];
    }
}

// Normalisation DES URL POUR LE NAVIGATEUR
foreach ($images as &$img) {
    // on force un chemin ABSOLU web
    if ($img[0] !== '/') {
        $img = '/' . ltrim($img, '/');
    }
}
unset($img);

// Lecture du contenu depuis le fichier
require __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;

$content = '';
$filePath = $article['content_file'] ?? null;
$filePathServer = $filePath ? __DIR__ . '/../' . $filePath : null;

if (!$filePathServer || !file_exists($filePathServer)) {
    $content = "Contenu indisponible.";
} else {
    $extension = strtolower(pathinfo($filePathServer, PATHINFO_EXTENSION));

    if ($extension === 'docx') {
        $phpWord = IOFactory::load($filePathServer);

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                    $text = $element->getText();
                    if (is_array($text)) $text = implode(' ', $text);
                    $content .= htmlspecialchars($text) . "<br><br>";
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Link) {
                    $url  = htmlspecialchars((string) $element->getSource());
                    $text = $element->getText();
                    if (is_array($text)) $text = implode(' ', $text);
                    $content .= "<a href=\"$url\">".htmlspecialchars($text)."</a><br><br>";
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $child) {
                        if ($child instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text = $child->getText();
                            if (is_array($text)) $text = implode(' ', $text);
                            $content .= htmlspecialchars($text);
                        } elseif ($child instanceof \PhpOffice\PhpWord\Element\Link) {
                            $url  = htmlspecialchars((string) $child->getSource());
                            $text = $child->getText();
                            if (is_array($text)) $text = implode(' ', $text);
                            $content .= "<a href=\"$url\">".htmlspecialchars($text)."</a>";
                        }
                    }
                    $content .= "<br><br>";
                }
            }
        }
    } elseif ($extension === 'pdf') {
        $content = shell_exec("pdftotext " . escapeshellarg($filePathServer) . " -");
    } else {
        $content = "Format de fichier non supporté.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($article['title']) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/college.css">
<style>body{background-image:url('<?= htmlspecialchars($image) ?>');}</style>
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

        <li><a href="page.php?type=numerique-emi">Numérique EMI & innovation pédagogique</a></li>
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

<!-- CONTENU ARTICLE -->
<div class="main-content">
<div class="news-container">
    <h1><?= htmlspecialchars($article['title']) ?></h1>
    <p class="date"><?= date('d/m/Y', strtotime($article['created_at'])) ?></p>

    <!-- IMAGES -->
    <div class="news-images">
        <?php foreach ($images as $index => $img): ?>
            <?php if ($index == 1 && count($images) > 2): ?>
                <div class="image-box" onclick="openLightbox()">
                    <img src="<?= htmlspecialchars($img) ?>" alt="">
                    <div class="overlay see-more">Voir plus</div>
                </div>
            <?php else: ?>
                <div class="image-box">
                    <img src="<?= htmlspecialchars($img) ?>" alt="" onclick="openLightbox()">
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- TEXTE ARTICLE -->
    <div class="news-content">
        <?= nl2br(htmlspecialchars_decode($content)) ?>
    </div>
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

<script>
function openLightbox() {
    document.getElementById('lightbox').style.display = 'flex';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}
</script>
</div>
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