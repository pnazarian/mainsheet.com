<?php

$articleID = isset($_GET['articleid']) ? (int)$_GET['articleid'] : 0;

$article = Model::getArticle($articleID);

?>

<!DOCTYPE html Public "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>The Mainsheet</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link href="library/stylesheet.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="library/favicon.ico" />
	</head>
	<body onload="stepGallery(0)" >
		<?php include("views/header.php"); ?>
		<?php
			if (!$article)
			{
				?> <h3 style="text-align: center; color: red">Article not found. One of us made a mistake.</h3> <?php
			}
			else
			{					
				?> <table><tr><td width="750px" style="padding-left:75px; padding-right: 25px; vertical-align: top">
			
				<span class="titleData"><p style="float: left"><?php echo $article->section; ?></p>
				<p style="float: right">
				<?php echo ($article->isOnlineOnly ? 'Web Only' : 'Volume '.$article->volumeID.' Number '.$article->issueNumber); ?>
				</p></span>
				<div style="clear: both"></div>
				<h2 style="padding-top:3px; padding-bottom:3px; line-height: 110%"><?php echo $article->title; ?></h2>
				<span class="titleData"><p>by 
				
				<?php
				
				for ($j=0; $j<count($article->authors); $j++)
				{
					echo ($j==0 ? '' : ' and ').$article->authors[$j];
				}
				
				?>
				 - <?php echo $article->date; ?></p>
				</span>
				<?php
				
				if (count($article->images) > 0)
				{	?>
					<SCRIPT LANGUAGE="JavaScript">
						var galleryIndex = 0;
						var gallerySrcs = new Array(<?php echo count($article->images); ?>);
						var galleryCaptions = new Array(<?php echo count($article->images); ?>);
						var galleryCredits = new Array(<?php echo count($article->images); ?>);
						var galleryWidths = new Array(<?php echo count($article->images); ?>);
						var galleryHeights = new Array(<?php echo count($article->images); ?>);
						
						<?php
						for ($i=0; $i<count($article->images); $i++) {
						
							echo 'gallerySrcs['.$i.'] = "'.$article->images[$i]->path.'";';
							echo 'galleryCaptions['.$i.'] = "'.$article->images[$i]->caption.'";';
							echo 'galleryCredits['.$i.'] = "'.$article->images[$i]->credit.'";';
							echo 'galleryWidths['.$i.'] = "'.Helper::constrainedImageWidth(getimagesize($article->images[$i]->path), 300, 500).'";';
							echo 'galleryHeights['.$i.'] = "'.Helper::constrainedImageHeight(getimagesize($article->images[$i]->path), 300, 500).'";';
							
						}
						?>
						function stepGallery(n) {
							galleryIndex += n;
							galleryIndex = Math.max(0, galleryIndex);
							galleryIndex = Math.min(gallerySrcs.length - 1, galleryIndex);
							
							document.getElementById("galleryImg").src = gallerySrcs[galleryIndex];
							document.getElementById("galleryIndex").firstChild.nodeValue = galleryIndex+1;
							document.getElementById("galleryCaption").firstChild.nodeValue = galleryCaptions[galleryIndex];
							document.getElementById("galleryCredit").firstChild.nodeValue = galleryCredits[galleryIndex];
							document.getElementById("galleryImg").width = galleryWidths[galleryIndex];
							document.getElementById("galleryImg").height = galleryHeights[galleryIndex];
							
						}
					</SCRIPT>
				
					<center><table style="margin-top: 5px"><tr><td>
					<img id="galleryLeft" src="images/leftarrow.png" width="100px" style="border: none"
					onmouseover="this.src='images/leftarrow_dark.png'" 
					onmouseout="this.src='images/leftarrow.png'" 
					onclick="stepGallery(-1)"
					/>
					</td><td>
					<img id="galleryImg" class="imgWrapper" />
					</td><td>
					<img id="galleryRight" src="images/rightarrow.png" width="100px" style="border: none"
					onmouseover="this.src='images/rightarrow_dark.png'" 
					onmouseout="this.src='images/rightarrow.png'" 
					onclick="stepGallery(1)"
					/>
					
					</td></tr></table></center>
					
					<div class="imageData" style="border-bottom: solid thin black; margin-top: 5px">
					<p id="galleryCredit" style="float: left">Credit</p>
					<p style="float: right">Image <span id="galleryIndex">1</span> of <?php echo count($article->images); ?></p>
					<p id="galleryCaption" style="clear: both; font-weight: normal">Caption</p>
					</div>
				<?php
				}	?>
					
				<br />
				<p style="font-family: Georgia, serif"><?php echo $article->text; ?></p>
				<br />
				<p style="text-align: center" class="titleData"><?php echo $article->hits; ?> views</p>
				
				</td><td style="border-left: solid thin #777; padding-left: 10px; vertical-align: top; padding-top: 20px">
				<?php
				if ($article->pageID !== NULL)
				{
				?>
					<h3>Tools:</h3>
					<span id="toolbox">
					<p><a href="pages/<?php echo $article->pageID; ?>.pdf" target="_blank">View Original Page as PDF</a></p>
					<!--<p><a>Print Article</a></p>-->
					<p></p>
					</span>
				<?php } ?>
				<h3>Article Keywords:</h3>
				
				<p>
				<?php
				for ($i=0; $i<count($article->keywords); $i++)
				{?><a href="?page=searchresults&terms=<?php echo $article->keywords[$i]; ?>"><?php echo $article->keywords[$i]; ?></a><?php echo ($i<count($article->keywords)-1 ? ', ' : '');
				} ?>
				</p>
				
				<br /><h3>Related Articles:</h3>
				<table>
				
				<?php
				
				for ($i=0; $i<count($article->relatedArticles); $i++) {
					?>
					<tr><td style="padding-top: 10px"><div id="substandard">
					<h4><a href="models/article_model.php?articleid=<?php echo $article->relatedArticles[$i]->id; ?>"><?php echo $article->relatedArticles[$i]->title; ?></a></h4>
					</div>
					<p class="searchData"><?php echo $article->relatedArticles[$i]->date; ?> - by 
					
					<?php
					
					for ($j=0; $j<count($article->relatedArticles[$i]->authors); $j++)
					{
						echo ($j==0 ? '' : ' and ').$article->relatedArticles[$i]->authors[$j];
					} 
					?>
					 - in <?php echo $article->relatedArticles[$i]->section; ?> - 
					 <?php echo ($article->relatedArticles[$i]->isOnlineOnly ? 'Web Only' : 'Vol '.$article->relatedArticles[$i]->volumeID.' Num '.$article->relatedArticles[$i]->issueNumber); ?></p>
						  </td>
						  </tr>
					</td></tr>
				<?php } ?>
				</table>

				</td>
				</tr></table>
			<?php } ?>
	</body>
	<?php include("views/footer.php"); ?>
</html>