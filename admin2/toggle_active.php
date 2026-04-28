<?php
require_once 'C:/wamp64/secure/db.php';

if(isset($_POST['id'], $_POST['active'])){
    $id = (int)$_POST['id'];
    $active = (int)$_POST['active']; // 0 ou 1

    $stmt = $pdo->prepare("UPDATE carousel SET active=? WHERE id=?");
    $stmt->execute([$active, $id]);

    echo "success";
} else {
    echo "error";
}