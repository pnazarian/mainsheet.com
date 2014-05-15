<?php

//increases time limit from default 60s, because downloading the Google docs can take a long time
set_time_limit(120);

GoogleClient::authenticate();

$issueDate = isset($_POST['issuedate']) ? addslashes(stripslashes($_POST['issuedate'])) : NULL;
$issueDate = preg_match('#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#', $issueDate) ? $issueDate : NULL;

$online = false;
if ($issueDate===NULL) // online article upload, then
{
	$issueDate = date('Y-m-d');
	$online = true;
}

if (!$online)
{
	$unpublishedIssue = Model::getLastUnpublishedIssue();
	
	$volumeID = $unpublishedIssue->volumeID;
	$issueNumber = $unpublishedIssue->issueNumber;
	$folderID = $unpublishedIssue->folderSystem->articlesFolderID;
	$pageFolderID = $unpublishedIssue->folderSystem->pagesFolderID;
	$imageFolderID = $unpublishedIssue->folderSystem->imagesFolderID;
	
	$pageFiles = Model::getPageFiles($pageFolderID);
}
else 
{
	$folderID = '0B99bsttQVKIeRFlFTVcwOHhGX2c'; 				//these must be updated, possibly
	$imageFolderID = '0B99bsttQVKIeZC1mcmh3NWNieGM';
}

$articleFiles = Model::getArticleFiles($folderID);
$imageFiles = Model::getImageFiles($imageFolderID);

$sections = Model::getAllSections();
$authors = Model::getAllAuthors();

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
			//this should retrieve the metadata for the Google Docs from the folder associated with the specified issue, and display it where
			//it can be modified. Next, the modified metadata will be passed via POST to finalizeAction.php
			
			if (!$online) {
				?><h2 class="focusTitle">Articles Retrieved for Volume <?php echo $volumeID; ?> Number <?php echo $issueNumber; ?></h2>
			<?php } else { ?>
				<h2 class="focusTitle">Articles Retrieved for Online Upload</h2>
			<?php } ?>
			
			<form action="models/finalizeIssue_model.php" method="POST" id="finalizeForm">
			
			<table class="fullTable" style="margin: 0 auto; width:1050px; margin-top: 25px; margin-bottom: 25px">
			
			<tr style="font-weight: bold"><td>Include:</td><td>Title:</td><td>Section:</td>
			<?php
			if (!$online)
			{
			?>
				<td>Page:</td>
			<?php
			}
			?>
			<td>Authors:</td><td>Promoted:</td></tr>
			
			<SCRIPT language="JavaScript">
					var authorIDs = new Array(<?php echo count($authors); ?>);
					var authorNames = new Array(<?php echo count($authors); ?>);
					var authorDisplayNames = new Array(<?php count($authors); ?>);
			<?php		
			for ($j=0; $j<count($authors); $j++)
			{
				echo 'authorIDs['.$j.'] = "'.$authors[$j]->id.'";';
				echo 'authorNames['.$j.'] = "'.$authors[$j]->name.'";';
				echo 'authorDisplayNames['.$j.'] = "'.$authors[$j]->displayName.'";';
			}
			?>
			
			function addAuthor(n, selection)
			  {
				var div = document.getElementById("additionalAuthors"+n);
				div.appendChild(document.createElement("p"));
				
				var select = document.createElement("select");
				select.setAttribute("name", "authorSelect"+n);
				select.setAttribute("class", "authorSelect");
				
				div.lastChild.appendChild(select);
				
				var option = document.createElement("option");
				option.setAttribute("value", "0");
				option.appendChild(document.createTextNode("---"));
				div.lastElementChild.firstElementChild.appendChild(option);
				
				for (i=0; i<authorIDs.length; i++)
				{
					var option = document.createElement("option");
					option.setAttribute("value", authorIDs[i]);
					
					if (selection !== undefined && selection==authorNames[i])
					{
						option.setAttribute("SELECTED", "true");
					}
					
					option.appendChild(document.createTextNode(authorDisplayNames[i]));
					div.lastElementChild.firstElementChild.appendChild(option);
				}
				
			  }
			  
			  function removeAuthor(n)
			  {
				var div = document.getElementById("additionalAuthors"+n);
				if (div.children.length > 1)
					div.removeChild(div.lastChild);
			  }
			</SCRIPT>
			<?php
			
			for ($i=0; $i<sizeof($articleFiles); $i++)
			{
				$file = $articleFiles[$i];
				?>
				<tr>
				
				<?php $articleFilenames[$i] = $file->title; ?>
				
				<td><center><input type="checkbox" name="include<?php echo $i; ?>" CHECKED/></center>
				
				<input type="hidden" name="id<?php echo $i; ?>" value="<?php echo $file->id; ?>" />
				
				</td>
				<td><p style="padding-bottom: 2px"><?php echo $file->title; ?></p>
				<span id="invalidTitle<?php echo $i; ?>" style="display: none; color: red">*</span>
				<input type="text" name="title<?php echo $i; ?>" value="<?php echo $file->title; ?>" size="50"></input></td>
				<td>
				<input type="hidden" name="sectionid<?php echo $i; ?>" value="1" />
				<select onchange="document.forms['finalizeForm'].elements['sectionid<?php echo $i; ?>'].value = this.value">
				<?php
				foreach ($sections as $sectionID => $sectionName)
				{
					?><option value="<?php echo $sectionID; ?>"><?php echo $sectionName; ?></option><?php
				}
				?>
				</select>
				</td>
				
				<?php
				if (!$online)
				{
				?>
					<td>
					<span id="invalidPageNumber<?php echo $i; ?>" style="display: none; color: red">*</span>
					<input type="text" size="2" name="articlepage<?php echo $i; ?>" />
					</td>
				<?php
				} else {
				?>
					<span id="invalidPageNumber<?php echo $i; ?>" style="display: none; color: red">*</span>
					<input type="hidden" value="0" name="articlepage<?php echo $i; ?>" />
				<?php
				}
				?>
				
				<td>
			
				<div id="additionalAuthors<?php echo $i; ?>">
				</div>
				
				<SCRIPT language="JavaScript">
				<?php
				for ($j=0; $j<sizeof($file->ownerNames); $j++)
				{
				?>
					addAuthor("<?php echo $i; ?>", "<?php echo $file->ownerNames[$j]; ?>");
				<?php
				}
				?>
				
				</SCRIPT>
				
				<span id="invalidAuthors<?php echo $i; ?>" style="display: none; color: red">*</span>
				<span style="font-size: small">
				<a href="javascript:addAuthor('<?php echo $i; ?>')">Add Author</a> - <a href="javascript:removeAuthor('<?php echo $i; ?>')">Remove Author</a>
				</span>
				
				<input type="hidden" name="authorids<?php echo $i; ?>" value="" />
									
				</td>
				<td><center><input type="checkbox" name="promoted<?php echo $i; ?>" /></center></td>
				
				</tr>
			<?php
			}
			?>
			
			</table>
			
			<input type="hidden" name="articlerowcount" value="<?php echo sizeof($articleFiles); ?>" />
			<?php
			/////////
			//PAGES//
			/////////
			if (!$online)
			{
				?>
				<input type="hidden" name="pagerowcount" value="<?php echo sizeof($pageFiles); ?>" />
				
				<h2 class="focusTitle">Pages Retrieved for Volume <?php echo $volumeID; ?> Number <?php echo $issueNumber; ?></h2>
				
				<table class="fullTable" style="margin: 0 auto; width:1050px; margin-top: 25px; margin-bottom: 25px">
				
				<tr style="font-weight: bold"><td>Include:</td><td>Filename:</td><td>Page Number:</td></tr>
				<?php
				for ($i=0; $i<sizeof($pageFiles); $i++)
				{
					$file = $pageFiles[$i];
					$title = $file->title;
					?>
					<tr>
					
					<td><center><input type="checkbox" name="pageinclude<?php echo $i; ?>" CHECKED/></center></td>
					
					<input type="hidden" name="pageid<?php echo $i; ?>" value="<?php echo $file->id; ?>" />
					
					<td><p><?php echo $title; ?></p></td>
					
					<td>
					<span id="invalidPage<?php echo $i; ?>" style="display: none; color: red">*</span>
					<input type="text" name="pagenumber<?php echo $i; ?>" value="<?php echo Helper::firstInt($title); ?>" size="2"/>
					
					</tr>
				<?php
				}
				?>
				
				</table>
			<?php
			} else {
			?>
				<input type="hidden" name="pagerowcount" value="0" /><div></div><div></div>
			<?php
			}
			
			//////////
			//IMAGES//
			//////////
			?>
			
			<input type="hidden" name="imagerowcount" value="<?php echo sizeof($imageFiles); ?>" />
			<?php
			if (!$online)
			{
			?>
				<h2 class="focusTitle">Images Retrieved for Volume <?php echo $volumeID; ?> Number <?php echo $issueNumber; ?></h2>
			<?php } else { ?>
				<h2 class="focusTitle">Images Retrieved for Online Upload</h2>
			<?php } ?>
			
			<table class="fullTable" style="margin: 0 auto; width:1050px; margin-top: 25px; margin-bottom: 25px">
			
			<tr style="font-weight: bold"><td>Include:</td><td>Filename:</td><td>Article:</td><td>Credit:</td><td>Caption:</td></tr>
			
			<?php
			for ($i=0; $i<sizeof($imageFiles); $i++)
			{
				$file = $imageFiles[$i];
				$title = $file->title;
				?>
				<tr>
				
				<td><center><input type="checkbox" name="imageinclude<?php echo $i; ?>" CHECKED/></center></td>
				
				<input type="hidden" name="imageid<?php echo $i; ?>" value="<?php echo $file->id; ?>" />
				
				<td><p><?php echo $title; ?></p></td>
				
				<td>
				
				<span id="invalidImageArticle<?php echo $i; ?>" style="display: none; color: red">*</span>
				<input type="hidden" name="imagearticle<?php echo $i; ?>" id="imagearticle<?php echo $i; ?>" value="0"/>
				<select onChange="document.getElementById('imagearticle<?php echo $i; ?>').value = this.value">
				<?php
				for ($j=0; $j<sizeof($articleFilenames); $j++)
				{
					?>
					<option value="<?php echo $j; ?>">
					<?php echo $articleFilenames[$j]; ?>
					</option>
				<?php
				}
				?>
				</select>
				
				</td><td>
				<span id="invalidImageCredit<?php echo $i; ?>" style="display: none; color: red">*</span>
				<input type="text" name="imagecredit<?php echo $i; ?>" value="Courtesy of" size="30" />
				</td>
				
				<td>
				<textarea name="imagecaption<?php echo $i; ?>" cols="50" rows="2"><?php echo $title; ?></textarea>
				</td>
				</tr>
			<?php
			}
			?>
			
			</table>
			
			<input type="hidden" name="volumeid" value="<?php echo $volumeID; ?>" />
			<input type="hidden" name="issuenumber" value="<?php echo $issueNumber; ?>" />
			<input type="hidden" name="issuedate" value="<?php echo $issueDate; ?>" />
			</form>
			
			<SCRIPT language="JavaScript">
			function updateAndSubmit()
				  {
				  
					var theForm = document.forms["finalizeForm"];
					
					var articleRows = document.getElementById("finalizeForm").firstElementChild.firstElementChild.children.length-2;
					
			<?php
			if (!$online)
			{
				?>	var pageRows = document.getElementById("finalizeForm").children[4].firstElementChild.children.length-1;<?php
			} else {
				?>	var pageRows = 0;<?php
			}
			?>
					
					var imageRows = document.getElementById("finalizeForm").children[7].firstElementChild.children.length-1;
					
					var pageNumbers = new Array();
					var index = 0;
					
					var allValid = true;
					
					for (i=0; i<pageRows; i++)
					{
						if (theForm.elements["pageinclude"+i.toString()].checked)
						{
							var value = +(theForm.elements["pagenumber"+i.toString()].value);
							if (isNaN(value) || value <= 0 || pageNumbers.indexOf(value)>-1)
							{
								allValid = false;
								document.getElementById("invalidPage"+i.toString()).style.display = "";
							} else {
								pageNumbers[index] = value;
								index++;
								document.getElementById("invalidPage"+i.toString()).style.display = "none";
							}
						} else {
							document.getElementById("invalidPage"+i.toString()).style.display = "none";
						}
					}
					
					for (i=0; i<imageRows; i++)
					{
						if (theForm.elements["imageinclude"+i.toString()].checked)
						{
							var value = +(theForm.elements["imagearticle"+i.toString()].value);
							if (isNaN(value) || !theForm.elements["include"+value.toString()].checked)
							{
								allValid = false;
								document.getElementById("invalidImageArticle"+i.toString()).style.display = "";
							} else {
								document.getElementById("invalidImageArticle"+i.toString()).style.display = "none";
							}
							
							if (theForm.elements["imagecredit"+i.toString()].value=="Courtesy of")
							{
								allValid = false;
								document.getElementById("invalidImageCredit"+i.toString()).style.display = "";
							} else {
								document.getElementById("invalidImageCredit"+i.toString()).style.display = "none";
							}
							
						} else {
							document.getElementById("invalidImageArticle"+i.toString()).style.display = "none";
							document.getElementById("invalidImageCredit"+i.toString()).style.display = "none";
						}
					}
					
					for (i=0; i<articleRows; i++)
					{
						var ps = document.getElementById("additionalAuthors"+i.toString()).children;
						
						theForm.elements["authorids"+i.toString()].value = "";
						for (j=0; j<ps.length; j++)
						{
							var select = ps[j].firstElementChild;
							
							if (select.value != "0")
								theForm.elements["authorids"+i.toString()].value += " "+select.value;
						}
						
						if (theForm.elements["include"+i.toString()].checked && theForm.elements["authorids"+i.toString()].value.match(/^$/))
						{
							allValid = false;
							document.getElementById("invalidAuthors"+i.toString()).style.display = "";
						} else {
							document.getElementById("invalidAuthors"+i.toString()).style.display = "none";
						}
						
						if (theForm.elements["include"+i.toString()].checked && theForm.elements["title"+i.toString()].value.match(/^$/))
						{
							allValid = false;
							document.getElementById("invalidTitle"+i.toString()).style.display = "";
						} else {
							document.getElementById("invalidTitle"+i.toString()).style.display = "none";
						}
			
						if (theForm.elements["include"+i.toString()].checked && (isNaN(+theForm.elements["articlepage"+i.toString()].value) || pageNumbers.indexOf(+theForm.elements["articlepage"+i.toString()].value)==-1) && theForm.elements["articlepage"+i.toString()].value!="" && theForm.elements["articlepage"+i.toString()].value!="0")
						{
							allValid = false;
							document.getElementById("invalidPageNumber"+i.toString()).style.display = "";
						} else {
							
							if (theForm.elements["articlepage"+i.toString()].value=="")
								theForm.elements["articlepage"+i.toString()].value = "0";
													
							document.getElementById("invalidPageNumber"+i.toString()).style.display = "none";
						}
						
						
					}
					
					if (allValid)
						theForm.submit();			
				  }
			</SCRIPT>
			
			<SCRIPT language="JavaScript">
				function authorAdded (firstName, lastName, authorID)
				{
					authorIDs[authorIDs.length] = authorID;
					authorDisplayNames[authorDisplayNames.length] = lastName + ", " + firstName;
					authorNames[authorNames.length] = firstName + " " + lastName;
					
					var selects = document.getElementsByClassName("authorSelect");

					for (var i=0; i<selects.length; i++)
					{
						var select = selects[i];
						
						var option = document.createElement("option");
						option.setAttribute("value", authorID);
						
						option.appendChild(document.createTextNode(lastName + ", " + firstName));
						select.appendChild(option);
						
					}
				}
			</SCRIPT>
			
			<br />
		
			<center><div style="background-color: #EEE; width: 1050px">
			<?php include ("views/addAuthor.php"); ?>
			
			</div><br /><input type="button" value="Submit" onclick="updateAndSubmit()"/></center>
	</body>
	<?php include("views/footer.php"); ?>
</html>