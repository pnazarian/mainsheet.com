<?php

$articles = Model::getLastIssueArticles();
$webArticles = Model::getLastOnlineOnlyArticles(15);

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
			<table><tr><td width="825px" style="border-right: solid thin #777; vertical-align: top; padding-left: 25px">
			
			<table><tr>
						
			<SCRIPT language="JavaScript">
			
			function expandSection(x)
			  {
				var rows = document.getElementsByName("expansionSection"+x);
				
				for (var i=0; i<rows.length; i++)
				{
					rows[i].style.display = "";
				}
				
				document.getElementById("expandTag"+x).style.display = "none";
			  
			  }
		
			</SCRIPT>
			
			<?php
			
			$lastSectionStart;
	
			for ($i=0; $i<count($articles); $i++) {
				
				if ($i == 0)
				{ ?>
					<td width="125px"></td><td width="675px">
					
					<h2 class="focusTitle">
					Latest Issue: Volume <?php echo $articles[$i]->volumeID; ?> Number <?php echo $articles[$i]->issueNumber; ?> (<?php echo $articles[$i]->date; ?>)
					</h2></td>
				<?php }
			
				if ($i==0 || $articles[$i]->sectionID != $articles[$i-1]->sectionID)
				{
					$lastSectionStart = $i;
					?><tr style="background-color: #DDD"><td><h4><?php echo $articles[$i]->section; ?>:</h4></td><td></td></tr>
				<?php } ?>

				<tr <?php
				
				if ($i - $lastSectionStart >= 3)
				{
					?> name="expansionSection<?php echo $articles[$i]->sectionID; ?>" style="display: none" <?php
				}
				?>
				
				>
				
				  <td width="125"><center><a href="models/article_model.php?articleid=<?php echo $articles[$i]->id; ?>">
				  <?php if ($articles[$i]->image !== NULL)
				  {						  
					  ?><div <?php echo Helper::croppedDivSizeString(getimagesize($articles[$i]->image->path), 70, 115); ?> >
					  <img <?php echo Helper::croppedImageSizeString(getimagesize($articles[$i]->image->path), 70, 115); ?>
					  src="<?php echo $articles[$i]->image->path; ?>" /></div>
				  <?php } ?>
				  
				  </a></center></td>
				  <td style="padding-left: 15px"><div id="<?php echo ($articles[$i]->promotion > 0 ? 'promoted' : 'standard'); ?>">
				  <h4 style="margin-top: 15px"><a href="models/article_model.php?articleid=<?php echo $articles[$i]->id; ?>">
				  <?php echo $articles[$i]->title; ?></a></h4>
				  <p>
				  
				<?php echo $articles[$i]->blurb; ?>
				</p></div>
					   <p class="searchData"><?php echo $articles[$i]->date; ?> - by 
				
				<?php
				
				for ($j=0; $j<count($articles[$i]->authors); $j++)
				{
					echo ($j==0 ? '' : ' and ').$articles[$i]->authors[$j];
				}
				?>  
				- in <?php echo $articles[$i]->section; ?> - <?php echo ($articles[$i]->isOnlineOnly ? 'Online' : 'Print'); ?></p>
					  </td>
					  </tr>
				<?php
				if ($i - $lastSectionStart == 3)
				{
				?>
					<tr id="expandTag<?php echo $articles[$i]->sectionID; ?>"><td></td><td>
					<a href="javascript:expandSection(<?php echo $articles[$i]->sectionID; ?>)">More</a>
					</td></tr>
				<?php
				}
			}
			?>
			</table></td>
			
			<td style="vertical-align: top; padding-left: 10px; padding-top:10px">
			
			<h3 class="focusTitle">Latest Web Only Stories</h3><table>
			
			<?php
			for ($i=0; $i<count($webArticles); $i++) {
				?>
				<tr><td style="padding-top: 10px"><div id="<?php echo ($webArticles[$i]->promotion > 0 ? 'standard' : 'substandard'); ?>">
				<h4><a href="models/article_model.php?articleid=<?php echo $webArticles[$i]->id; ?>"><?php echo $webArticles[$i]->title; ?></a></h4>
				</div>
				<p class="searchData"><?php echo $webArticles[$i]->date; ?> - by 
				
				<?php
				
				for ($j=0; $j<count($webArticles[$i]->authors); $j++)
				{
					echo ($j==0 ? '' : ' and ').$webArticles[$i]->authors[$j];
				}
				?>
				 - in <?php echo $webArticles[$i]->section; ?> - <?php echo ($webArticles[$i]->isOnlineOnly ? 'Online' : 'Print'); ?></p>
				</td>
				</tr>
				</td></tr>
			
			<?php } ?>
			
			</table>
			</td>
			</tr></table>
	</body>
	<?php include("views/footer.php"); ?>
</html>