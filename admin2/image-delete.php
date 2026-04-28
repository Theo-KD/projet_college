<?php
$id = (int)$_POST['id'];
$image = basename($_POST['image']);

$path = "../col6/img/news/$id/$image";
if(file_exists($path)) unlink($path);

echo "success";