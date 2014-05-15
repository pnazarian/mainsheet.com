<?php

$issues = Model::getIssues();

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
			<h2 class="focusTitle">Archived Issues:</h2>
			<div style="padding-left: 150px">
			<ul>
			<?php
			
			for ($i=0; $i<count($issues); $i++)
			{
				if ($i==0 || $issues[$i]->volumeID != $issues[$i-1]->volumeID)
				{
				?>
					</ul>
					<h3>Volume <?php echo $issues[$i]->volumeID; ?>:</h3>
					<ul>
				<?php
				}
				?>
				
				<li><a href="?page=searchresults&volumeid=<?php echo $issues[$i]->volumeID; ?>&issuenumber=<?php echo $issues[$i]->issueNumber; ?>">
				Volume <?php echo $issues[$i]->volumeID; ?> Number <?php echo $issues[$i]->issueNumber; ?> (<?php echo $issues[$i]->date; ?>)
				</a> -- <a href="?page=issue&volumeid=<?php echo $issues[$i]->volumeID; ?>&issuenumber=<?php echo $issues[$i]->issueNumber; ?>">[PDFs]</a></li>
				
				<?php
			}
			?>
			</ul>
			</div>
	</body>
	<?php include("views/footer.php"); ?>
</html>