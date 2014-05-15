<?php

require_once '../library/autoloader.php';

$articleID = isset($_GET['articleid']) ? (int)$_GET['articleid'] : 0;

Database::incrementArticleHits($articleID);

header('Location: ../?page=article&articleid='.$articleID);

?>