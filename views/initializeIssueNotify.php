<?php

$volumeID = isset($_GET['volumeid']) ? $_GET['volumeid'] : NULL;
$issueNumber = isset($_GET['issuenumber']) ? $_GET['issuenumber'] : NULL;

GoogleClient::authenticate();

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

		<div style="padding-left:150px">
		
		<p>Volume <?php echo $volumeID; ?> Number <?php echo $issueNumber; ?> has been successfully initialized!</p>
		<p>Once the contents of the Google Folder are final, come back and finalize the issue.</p>
		<br /><p><a href="?page=management">Back to Management Page</a></p>
		</div>
			
	</body>
	<?php include("views/footer.php"); ?>
</html>