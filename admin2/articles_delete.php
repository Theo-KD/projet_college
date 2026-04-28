<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'C:/wamp64/secure/db.php';

if (!isset($_POST['ids'])) {
    die("ERREUR : ids non reçus");
}

$ids = $_POST['ids'];

if (!is_array($ids) || empty($ids)) {
    die("ERREUR : ids invalides");
}

$ids = array_map('intval', $ids);

$placeholders = implode(',', array_fill(0, count($ids), '?'));

$sql = "DELETE FROM news WHERE id IN ($placeholders)";
$stmt = $pdo->prepare($sql);

if (!$stmt->execute($ids)) {
    die("ERREUR SQL");
}

echo "success";