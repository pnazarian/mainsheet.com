<?php

GoogleClient::authenticate();

$unpublishedIssue = Model::getLastUnpublishedIssue();

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
		
		<div style="padding-left: 150px"><?php
		
		if ($unpublishedIssue) {
		
			//finalize
			
			?>
			<SCRIPT langauge="JavaScript">
			
			function SubmitForm()
			{
				var theForm = document.forms["finalizeForm"];
				
				var day = +theForm.elements["day"].value;
				var month = +theForm.elements["month"].value;
				var year = +theForm.elements["year"].value;
				
				var output = "";
				
				if (isNaN(day) || isNaN(month) || isNaN(year) || day < 1 || day > 31 || month < 1 || month > 12 || year < 1000 || year > 9999)
				{
					document.getElementById("invalidInput").style.display = "";
				} else {
					output += year.toString() + "-";
					
					if (month < 10)
						output += "0";
					
					output += month.toString() + "-";
					
					if (day < 10)
						output += "0";
						
					output += day.toString();
					theForm.elements["issuedate"].value = output;
					
					theForm.submit();
				}
				
			}
			</SCRIPT>
			
			<form method="POST" action="?page=finalizeIssueForm" name="finalizeForm">
			<h3 style="padding-bottom: 10px">Finalize Volume <?php echo $unpublishedIssue->volumeID; ?> Number <?php echo $unpublishedIssue->issueNumber; ?>:</h3>
								
			<p>Date: <input type="text" name="month" size="1" /> / 
			<input type="text" name="day" size="1" /> / 
			<input type="text" name="year" size="4" /></p>
			
			<input type="hidden" name="issuedate" />
			
			<input type="button" value="Finalize" onclick="SubmitForm()" style="margin: 5px"/>
			
			<span id="invalidInput" style="color: red; display: none"><p>Invalid Input.</p></span>
			</form>
		<?php
		} else {
			//initialize result
			?>
			<form id="initializeForm" method="POST" action="models/initializeIssue_model.php">
			<h3 style="padding-bottom: 10px">Initialize Issue</h3>
			<p>Volume: <input type="text" name="volumeid" size="2" /> Number: <input type="text" name="issuenumber" size="2" /></p>
			<input type="hidden" name="secret" value="" />
			<input type="submit" value="Initialize" /> <input type="button" value="Initialize without Sharing"
			onclick="document.forms['initializeForm'].elements['secret'].value = 'true'; document.forms['initializeForm'].submit();" />
			</form>
			<?php
		}
		?>
		
		<br />
		
		<form method="POST" action="models/finalizeIssue_model.php" name="finalizeOnlineForm">
		<h3 style="padding-bottom: 10px">Upload Online Articles:</h3>
		
		<input type="submit" value="Upload" />
		</form>
		<br />
		
		<?php
		include ("views/addAuthor.php");
		?>
		
		</div>

	</body>
	<?php include("views/footer.php"); ?>
</html>