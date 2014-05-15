<?php

$volumeID = (int)$_GET['volumeid'];
$issueNumber = (int)$_GET['issuenumber'];

$pages = Model::getIssuePages($volumeID, $issueNumber);

?>

<!DOCTYPE html Public "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>The Mainsheet</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link href="library/stylesheet.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="library/favicon.ico" />
	</head>
	<body>
		<?php include("views/header.php"); ?>

		<h2 class="focusTitle" style="padding-bottom: 10px">Volume <?php echo $volumeID; ?> Number <?php echo $issueNumber; ?></h2>
		<center><p><a href="?page=searchresults&volumeid=<?php echo $volumeID; ?>&issuenumber=<?php echo $issueNumber; ?>">
		View all articles from this issue.</a></p></center>
		
		<div style="padding-left: 150px">
		<ul>
		<?php
		foreach ($pages as $pageNumber => $pageID)
		{	
			?>
			<li><a href="pages/<?php echo $pageID; ?>.pdf" target="_blank">
			Page <?php echo $pageNumber; ?>
			</a></li>
		<?php
		}
		?>
		</ul>
		</div>
	</body>
	<?php include("views/footer.php"); ?>
</html>