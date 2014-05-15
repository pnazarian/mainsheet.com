<?php
	$pageCode = isset($_GET['page']) ? $_GET['page'] : '';
	
	if (is_file('views/'.$pageCode.'.php'))
	{	
		require 'views/'.$pageCode.'.php';
	}
	else
	{
		require 'views/index.php';
	}
?>