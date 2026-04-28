<?php
$image = "img/college.jpg";
$logoCollege = "img/région.png";

require_once 'C:/wamp64/secure/db.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

/* TYPES / SLUGS */
$typeSlugs = $_GET['type'] ?? 'inconnu';
$typeSlugsArray = array_map('trim', explode(',', $typeSlugs));
$mainSlug = $typeSlugsArray[0];

/* TITRES NAV */
$navTitles = [
    'eleve-top' => "Élèves",
    'parent-top' => "Parents d'élèves",
    'enseignant-top' => "Enseignants",
    'autre-top' => "Autres",
    'presentation' => "Présentation",
    'etablissement' => "L'établissement",
    'numerique-emi' => "Numérique EMI & innovation pédagogique",
    'vie-college' => "Vie du collège et projets",
    'parcours-avenir' => "Parcours Avenir / Orientation",
    'eleve' => "Élèves",
    'parent' => "Parents d'élèves",
    'enseignant' => "Enseignants",
    'autre' => "Autres",
    'Services-organigramme' => "Services / organigramme",
    'projet-etablissement' => "Projet d'établissement",
    'conseil-ecoles-college' => "Conseil écoles collège",
    'restauration' => "Restauration",
    'contact' => "Contact",
    'examen' => "Examens / formations",
    'information' => "Informations de la direction",
    'atelier-jeudi' => "Atelier du jeudi",
    'peac' => "PEAC",
    'parcours-sante-citoyennete' => "Parcours santé & citoyenneté",
    'assistante' => "Assistante sociale",
    'association' => "Association sportive",
    'cesc' => "CESC",
    'cvc' => "CVC",
    'infirmieres' => "Infirmières",
    'cordees-reussite' => "Cordées de la réussite",
    'liens-utiles' => "Liens utiles",
    'parcours-avenir-sites' => "Parcours Avenir : sites",
    'inconnu' => "Pas d’information pour le moment"
];

$pageTitle = $navTitles[$mainSlug] ?? "Pas d’information pour le moment";

/*TYPES BDD */
$placeholders = implode(',', array_fill(0, count($typeSlugsArray), '?'));
$stmt = $pdo->prepare("SELECT id FROM type WHERE slug IN ($placeholders)");
$stmt->execute($typeSlugsArray);
$typeIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$typeIds) $typeIds = [0];

/* PAGINATION */
$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/* CONTENUS */
$placeholdersIds = implode(',', array_fill(0, count($typeIds), '?'));

$sql = "
SELECT DISTINCT c.*
FROM contenu c
JOIN contenu_type ct ON ct.contenu_id = c.id
WHERE ct.type_id IN ($placeholdersIds)
ORDER BY c.created_at DESC
LIMIT ? OFFSET ?
";

$stmt = $pdo->prepare($sql);
$i = 1;
foreach ($typeIds as $tid) {
    $stmt->bindValue($i++, $tid, PDO::PARAM_INT);
}
$stmt->bindValue($i++, $limit, PDO::PARAM_INT);
$stmt->bindValue($i++, $offset, PDO::PARAM_INT);
$stmt->execute();

$contenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* EXTRAIT DOCX / PDF */
function getArticleExcerpt(?string $filePath, int $length = 150): string
{
    if (!$filePath) return "Contenu indisponible.";

    $fullPath = __DIR__ . '/../' . ltrim($filePath, '/');
    if (!file_exists($fullPath)) return "Contenu indisponible.";

    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $content = '';

    if ($ext === 'docx') {
        $phpWord = IOFactory::load($fullPath);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text = $element->getText();
                    if (is_array($text)) $text = implode(' ', $text);
                    $content .= $text . ' ';
                }
            }
        }
    }

    $content = preg_replace('/\s+/', ' ', trim($content));
    return mb_substr($content, 0, $length) . '…';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($pageTitle) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/college.css">
<style>body{background-image:url('<?= htmlspecialchars($image) ?>')}</style>
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

<!-- CONTENU -->
<section class="archive-container">
<h2><?= htmlspecialchars($pageTitle) ?></h2>

<?php if (!$contenus): ?>
    <p>Aucun contenu disponible.</p>
<?php else: ?>
    <?php foreach ($contenus as $contenu): ?>
        <?php
        $stmt = $pdo->prepare("SELECT image_path FROM image WHERE contenu_id = ? ORDER BY position");
        $stmt->execute([$contenu['id']]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($images as &$img) {
            if ($img[0] !== '/') {
            $img = '/' . ltrim($img, '/');
    }
}
unset($img);
        ?>
        <article class="archive-article">
            <?php if (!empty($images[0])): ?>
            <div class="archive-image">
                <img src="<?= htmlspecialchars($images[0]) ?>" alt="">
            </div>
            <?php endif; ?>

            <div class="archive-content">
                <h3>
                    <a href="site.php?id=<?= $contenu['id'] ?>">
                        <?= htmlspecialchars($contenu['titre']) ?>
                    </a>
                </h3>

                <span class="archive-date">
                    📅 <?= date('d/m/Y', strtotime($contenu['created_at'])) ?>
                </span>

                <p>
                    <?= getArticleExcerpt($contenu['content_file']) ?>
                    <a href="site.php?id=<?= $contenu['id'] ?>">Lire la suite…</a>
                </p>
            </div>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
</section>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-logo">
      <img src="img/région.jpg" alt="Logo">
    </div>
    <div class="footer-text">
      <p>© 2026 - Tous droits réservés</p> |
      <a href="mentions-legales.php">Mentions légales</a> |
      <a href="mailto:college@example.fr">college@example.fr</a> |
      <a href="tel:+590590123456">+590 590 12 34 56</a>
    </div>
  </div>
</footer>

</body>
</html>