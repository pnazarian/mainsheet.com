<?php

require '../library/autoloader.php';

//increase time limit from default 60s, because downloading the Google Docs takes a long time
set_time_limit(120);

//this page recieves POST data from views/finalizeIssueForm.php, and then retreives the associated data. then all of the data is inputed into the database.

$articleRowCount = (int)$_POST['articlerowcount'];
$pageRowCount = (int)$_POST['pagerowcount'];
$imageRowCount = (int)$_POST['imagerowcount'];
$volumeID = (int)$_POST['volumeid'];
$issueNumber = (int)$_POST['issuenumber'];
$issueDate = addslashes(stripslashes($_POST['issuedate']));

$online = false;
if ($volumeID==0 || $issueNumber==0) // then online article
{
	$volumeID = "NULL";
	$issueNumber = "NULL";
	$online = true;                 // use this later to decide whether it has to move the file
}

$numFilesAdded = 0;

$articleIDs = array();

for ($i=0; $i<$articleRowCount; $i++)
{
	$include = isset($_POST['include'.$i]) && $_POST['include'.$i]=='on' ? true : false;
	$title = isset($_POST['title'.$i]) ? addslashes(stripslashes($_POST['title'.$i])) : '';
	$include &= $title!='';
	$sectionid = (int)$_POST['sectionid'.$i];
	$promoted = isset($_POST['promoted'.$i]) && $_POST['promoted'.$i]=='on' ? 1 : 0;
	$googleID = stripslashes($_POST['id'.$i]);
	$pagenumber = (int)$_POST['articlepage'.$i];
	
	if ($pagenumber==0)
		$pagenumber = 'NULL';
	
	$authorids = isset($_POST['authorids'.$i]) ? addslashes(stripslashes($_POST['authorids'.$i])) : '';
	$authorids = explode(' ', $authorids);
	
	if ($include)
	{
		$body = Helper::escapes2html(GoogleDriveService::queryFileBody($googleID));
		
		Database::insertArticle($sectionid, $volumeID, $issueNumber, $pagenumber, $title, $body, $issueDate, $promoted);
		
		if ($online)
		{
			GoogleDriveService::archiveFile($googleID, true);
		}
		
		$currentArticleID = Database::queryLastArticle()['articleID'];
		
		$articleIDs[$i] = $currentArticleID;
		
		$numFilesAdded++;
		
		for ($j=0; $j<sizeof($authorids); $j++)
		{
			$currentAuthorID = (int)$authorids[$j];
			if ($currentAuthorID != 0)
			{
				Database::insertArticleAuthor($currentArticleID, $currentAuthorID);
			}
		}
	}
}

$numPagesAdded = 0;

for ($i=0; $i<$pageRowCount; $i++)
{
	$include = isset($_POST['pageinclude'.$i]) && $_POST['pageinclude'.$i]=='on' ? true : false;
	$googleID = stripslashes($_POST['pageid'.$i]);
	$number = (int)$_POST['pagenumber'.$i];
	
	if ($include)
	{
		$body = GoogleDriveService::queryFileBody($googleID, false);
		
		Database::insertPage($volumeID, $issueNumber, $number);
		
		$pageID = Database::queryLastPage()['pageID'];
		
		$pagefile = fopen('../pages/'.$pageID.'.pdf', "w");
		
		fputs($pagefile, $body);
		fclose($pagefile);
		
		$numPagesAdded++;
	}
}

$numImagesAdded = 0;

for ($i=0; $i<$imageRowCount; $i++)
{
	$include = isset($_POST['imageinclude'.$i]) && $_POST['imageinclude'.$i]=='on' ? true : false;
	$googleID = stripslashes($_POST['imageid'.$i]);
	$articleNum = (int)$_POST['imagearticle'.$i];
	$imageCredit = addslashes(stripslashes($_POST['imagecredit'.$i]));
	$imageCaption = addslashes(stripslashes($_POST['imagecaption'.$i]));
	
	if ($include)
	{
		$body = GoogleDriveService::queryFileBody($googleID, false);
		
		Database::insertImage($imageCaption, $imageCredit);
		
		$imageID = Database::queryLastImage()['imageID'];
		
		Database::insertArticleImage($articleIDs[$articleNum], $imageID);
		
		$imagefile = fopen('../images/userImages/'.$imageID.'.jpg', "w");
		
		fputs($imagefile, $body);
		fclose($imagefile);
		
		if ($online)
		{
			GoogleDriveService::archiveFile($googleID, false);
		}
		
		$numImagesAdded++;
	}
	
}

Database::updateIssue($volumeID, $issueNumber, $issueDate);

header('Location: ../?page=finalizeIssueNotify&articles='.$numFilesAdded.'&pages='.$numPagesAdded.'&images='.$numImagesAdded);

?>