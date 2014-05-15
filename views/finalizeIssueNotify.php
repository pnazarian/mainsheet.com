<?php

GoogleClient::authenticate();

$numFilesAdded = isset($_GET['articles']) ? $_GET['articles'] : 0;
$numImagesAdded = isset($_GET['images']) ? $_GET['images'] : 0;
$numPagesAdded = isset($_GET['pages']) ? $_GET['pages'] : 0;


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
	
		<p><?php echo $numFilesAdded; ?> article<?php echo ($numFilesAdded==1 ? ' has' : 's have'); ?> been added.</p>
		<p><?php echo $numPagesAdded; ?> page<?php echo ($numPagesAdded==1 ? ' has' : 's have'); ?> been added.</p>
		<p><?php echo $numImagesAdded; ?> image<?php echo ($numImagesAdded==1 ? ' has' : 's have'); ?> been added.</p>
		<br /><p><a href="?page=management">Back to Management Page</a></p>
		
		<?php include("views/footer.php"); ?>
	</body>
</html>