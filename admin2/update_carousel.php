<?php
require_once 'C:/wamp64/secure/db.php';

$id = (int)$_POST['id'];
$title = $_POST['title'] ?? '';
$subtitle = $_POST['subtitle'] ?? '';
$text = $_POST['text'] ?? '';

// Priorité : nouvelle image > image existante > rien
if(!empty($_FILES['image']['name'])){
    $filename = time().'_'.basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], '../col6/img/carousel/'.$filename);
} elseif(!empty($_POST['existing_image'])) {
    $filename = $_POST['existing_image'];
}

// Mise à jour BDD
if(isset($filename)){
    $stmt = $pdo->prepare("UPDATE carousel SET image=?, title=?, subtitle=?, text=? WHERE id=?");
    $stmt->execute([$filename, $title, $subtitle, $text, $id]);
} else {
    $stmt = $pdo->prepare("UPDATE carousel SET title=?, subtitle=?, text=? WHERE id=?");
    $stmt->execute([$title, $subtitle, $text, $id]);
}

echo "success";