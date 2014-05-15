<?php

require_once '../library/autoloader.php';

$volumeID = isset($_POST['volumeid']) ? (int)$_POST['volumeid'] : NULL;
$issueNumber = isset($_POST['issuenumber']) ? (int)$_POST['issuenumber'] : NULL;
$secret = isset($_POST['secret']) ? (boolean)$_POST['secret'] : false;

$folderSystem = GoogleDriveService::createIssueFolderSystem($volumeID, $issueNumber, $secret);

Database::insertIssue($volumeID, $issueNumber, $folderSystem->articlesFolderID, $folderSystem->pagesFolderID, $folderSystem->imagesFolderID);

header('Location: ../?page=initializeIssueNotify&volumeid='.$volumeID.'&issuenumber='.$issueNumber);

?>