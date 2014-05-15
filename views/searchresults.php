<?php

$terms = isset($_GET['terms']) ? $_GET['terms'] : '';
$sortby = strtolower(isset($_GET['sortby']) ? $_GET['sortby'] : '');
$media = strtolower(isset($_GET['media']) ? $_GET['media'] : '');
/*$authorID = (int)(isset($_GET['authorid']) ? $_GET['authorid'] : '');*/                 $authorID = '';  //temporary, until this function is fixed
$sectionIDs = (isset($_GET['sectionid']) ? $_GET['sectionid'] : '');
$volumeID = (int)(isset($_GET['volumeid']) ? $_GET['volumeid'] : '');
$issueNumber = (int)(isset($_GET['issuenumber']) ? $_GET['issuenumber'] : '');
$onPage = (int)(isset($_GET['onpage']) ? $_GET['onpage'] : '');
$perPage = (int)(isset($_GET['perpage']) ? $_GET['perpage'] : '');

if (!$onPage)
	$onPage = 1;

if (!$perPage)
	$perPage = 10;

$articles = Model::getSearchArticles($terms, $media, $authorID, $sectionIDs, $volumeID, $issueNumber, $sortby);

$authors = Model::getSearchAuthors($terms, $media, $sectionIDs, $volumeID, $issueNumber);

$sections = Model::getAllSections();

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
		<?php
			//TODO: consider using AJAX to update search results from sidebar without reloading page
		
			?><table><tr><td width="825px" style="border-right: solid thin #777; vertical-align: top; padding-left: 25px"><?php
			
			?><table><tr><td width="125px"></td><td style="width: 675px">

			<h2 class="focusTitle">
			Matching Articles: <i><?php echo $terms; ?></i>
			</h2></td>
			<?php
			
			if (count($articles)>0)
			{
				for ($i = $perPage*($onPage-1); $i<count($articles) && $i<$perPage*$onPage; $i++) {
					?>
					<tr>
					
					  <td><center><a href="models/article_model.php?articleid=<?php echo $articles[$i]->id; ?>">
					  <?php echo ($articles[$i]->image!==NULL ?
					  '<div '.Helper::croppedDivSizeString(getimagesize($articles[$i]->image->path), 70, 115).' >'.
					  '<img '.Helper::croppedImageSizeString(getimagesize($articles[$i]->image->path), 70, 115).' src="'.$articles[$i]->image->path.'" />'
					  : ''); ?>
					  </a></center></td>
					  <td style="padding-left: 15px"><div id="promoted">
					  <h4 style="margin-top: 15px"><a href="models/article_model.php?articleid=<?php echo $articles[$i]->id; ?>"><?php echo $articles[$i]->title; ?></a></h4>
					  <p style="line-height: 125%">
					  
					<?php echo $articles[$i]->blurb; ?>
					
					</p></div>
						   <p class="searchData"><?php echo $articles[$i]->date; ?> - by 
					
					<?php
					
					for ($j=0; $j<count($articles[$i]->authors); $j++)
					{
						echo ($j==0 ? '' : ' and ').$articles[$i]->authors[$j];
					}
					?>  
					 - in <?php echo $articles[$i]->section; ?> - <?php echo ($articles[$i]->isOnlineOnly ? 'Online' : 'Print - Vol. '.$articles[$i]->volumeID.', Num. '.$articles[$i]->issueNumber); ?></p>
						  </td>
						  </tr>
				<?php
				}
				?>
				
				<tr><td></td><td style="text-align: center; padding-top: 20px"><form id="pageChanger" action="" method="GET">
				<input type="hidden" name="page" value="searchresults" />
				
				<SCRIPT language="JavaScript">
				function updatePageNumber( n , isCustom)
						 {
							var newVal = +document.forms["pageChanger"].elements["onpage"].value + n;
							
							newVal = Math.max(1, Math.min(<?php echo (int)((count($articles)-1)/$perPage)+1; ?>, newVal));
							
							if (newVal != document.forms["pageChanger"].elements["onpage"].value)
							{
								document.forms["pageChanger"].elements["onpage"].value = newVal;
								document.forms["pageChanger"].submit();
							}
						 }
				</SCRIPT>
				
				<input type="hidden" name="terms" value="<?php echo $terms; ?>" />
				<input type="hidden" name="sortBy" value="<?php echo $sortby; ?>" />
				<input type="hidden" name="media" value="<?php echo $media; ?>" />
				<input type="hidden" name="authorid" value="<?php echo $authorID; ?>" /> 
				<input type="hidden" name="sectionid" value="<?php echo $sectionIDs; ?>" />
				<input type="hidden" name="volumeid" value="<?php echo $volumeID; ?>" />
				<input type="hidden" name="issuenumber" value="<?php echo $issueNumber; ?>" />
				<input type="hidden" name="perpage" value="<?php echo $perPage; ?>" />
				<a href="javascript:updatePageNumber(-1, false)">&lt </a>
				<input type="text" name="onpage" size="1" value="<?php echo $onPage; ?>" onchange="updatePageNumber(0, true)" /> of <?php echo (int)((count($articles)-1)/$perPage)+1; ?>
				<a href="javascript:updatePageNumber(1, false)"> &gt</a>
				
				</form></td></tr>
			<?php
			} else {
			?>
				<tr><td></td><td style="color: red; text-align: center">Sorry, no matches were found.</td></tr>
			<?php
			}
			?>
			</table></td>
			
			<td style="vertical-align: top; padding-left: 15px; padding-top: 10px">
			
			<form name="advancedSearch" action="" method="GET">
			<input type="hidden" name="page" value="searchresults" />
			
			<h3 class="focusTitle">Advanced Search:</h3>
			<br />
			<h4>Sort by:</h4>
			
			<p>
			<input type="radio" name="sortby" value="relevance" <?php echo ($sortby!='datedesc' && $sortby!='dateasc' ? 'CHECKED' : ''); ?> >Relevance</input>&nbsp&nbsp
			<input type="radio" name="sortby" value="dateDesc" <?php echo ($sortby=='datedesc' ? 'CHECKED' : ''); ?> >Newest</input>&nbsp&nbsp
			<input type="radio" name="sortby" value="dateAsc" <?php echo ($sortby=='dateasc' ? 'CHECKED' : ''); ?> >Oldest</input>
			</p>
			
			<br /><h4>Media:</h4>
			
			<p>
			<input type="radio" name="media" value="both" <?php echo ($media!='print' && $media!='web' ? 'CHECKED' : ''); ?> >Both</input>&nbsp&nbsp
			<input type="radio" name="media" value="print" <?php echo ($media=='print' ? 'CHECKED' : ''); ?> >Print</input>&nbsp&nbsp
			<input type="radio" name="media" value="web" <?php echo ($media=='web' ? 'CHECKED' : ''); ?> >Web</input>
			</p>
			<!--
			<br /><h4>Author:</h4>
			<p>
			
			<input type="hidden" name="authorid" value="<?php echo $authorID; ?>" />
			
			<select onchange="authorid.value = this.value">
			
			<option value="0">Any</option>
			<?php
			foreach ($authors as $currentAuthorID => $currentAuthorName)
			{
				echo '<option value="'.$currentAuthorID.'" '.($currentAuthorID==$authorID ? 'SELECTED' : '').' >'.$currentAuthorName.'</option>';
			}
			?>
			</select>
			</p>
			-->
			<br /><h4>Items per page:&nbsp&nbsp
			<input type="text" name="perpage" value="<?php echo $perPage; ?>" size="3" /></h4>
			
			<br /><h4>Sections: </h4>
			
			<SCRIPT language="JavaScript">
				function updateSectionCheckboxes()
					{	
						var checkboxes = document.getElementsByClassName("sectionCheckbox");
						var newSectionIDs = "";
				
						for (var i=0; i<checkboxes.length; i++)
						{
							if (checkboxes[i].checked)
							{
								newSectionIDs += checkboxes[i].value + ",";
							}
						}
					
						if (newSectionIDs.length > 0)
							newSectionIDs = newSectionIDs.substr(0, newSectionIDs.length-1);
						
						document.getElementsByName("sectionid")[1].value = newSectionIDs;
					}
					
					function checkAll()
					{
						var checkboxes = document.getElementsByClassName("sectionCheckbox");
						for (i=0; i<checkboxes.length; i++)
						{
							var thisBox = checkboxes[i];
							if (thisBox.checked===false)
							{
								updateSectionCheckboxes(thisBox.value);
								thisBox.checked = true;
							}
						}
					}
			
			</SCRIPT>
			<p>
			<?php
			$allSectionIDs = '';
			foreach ($sections as $currentSectionID => $currentSectionName)
			{	
				$allSectionIDs .= $currentSectionID.',';
				?>
				<input type="checkbox" class="sectionCheckbox" value="<?php echo $currentSectionID; ?>"
				onchange="updateSectionCheckboxes()"
				<?php
				$explosion = explode(",", $sectionIDs);
				$isChecked = $sectionIDs=='';
				foreach ($explosion as $current)
				{
					$isChecked |= $current==$currentSectionID;
				}
									
				echo $isChecked ? 'CHECKED' : '';
				?>
				> <?php echo $currentSectionName; ?></input><br />
			<?php
			}
			?>
			<input type="hidden" name="sectionid" value="<?php echo ($sectionIDs!='' ? $sectionIDs : substr($allSectionIDs, 0, strlen($allSectionIDs)-1)); ?>" />
			</p>
			
			<br /><h4>Volume:&nbsp <input type="text" name="volumeid" size="2" value="<?php echo $volumeID; ?>" />
			&nbspIssue:&nbsp <input type="text" name="issuenumber" size="2" value="<?php echo $issueNumber; ?>" /></h4>
			
			<br /><h4>Query: </h4><p><input type="text" name="terms" value="<?php echo $terms ?>" /></p>
			
			<br /><input type="submit" value="Update"/>
			
			</form>
			
			</td>
			</tr></table>

	</body>
	<?php include("views/footer.php"); ?>
</html>