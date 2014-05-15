<?php

$sections = Model::getAllSections();

?>

<div class='wrapper'>

<div class='header'>
	<center><a href="?page=index" style="color: black"><span style="font-size: 40px; font-family: Arial, sans-sarif; position: relative; bottom: 7px">THE</span><span style="font-size: 125px; font-family: Georgia, sarif; font-weight:bold">mainsheet</span></a><span style="font-family: Georgia, sarif; font-size: small; position: relative; bottom: 25px; font-weight: bold">
	<?php
	echo strtolower(date('l'));
	echo strtoupper(date(' F j, Y'));
	?>
	</span></center>
	<center><span style="font-family: Georgia, sarif; position: relative; bottom: 10px; font-weight: bold; font-size: small">Chadwick School - 26800 S Academy Drive, Palos Verdes Peninsula, CA - (310) 377-1543</span></center>
	<?php	
		$topnav_cell_percent = 100/(count($sections) + 3.5);
	?>		
	<div class="topnav"><table width="100%"><tr>
	<td style="border: none"><a href="?page=archive">Archived Issues</a></td>
	<td><a href="?page=searchresults&media=web&sortby=dateDesc">Non-Print Content</a></td>
	<?php	
	
		foreach ($sections as $sectionID => $sectionName)
		{ ?>
			<td><a href="?page=searchresults&sectionid=<?php echo $sectionID;?>&sortby=dateDesc">
			<?php echo $sectionName; ?>
			</a></td>
		<?php } ?>
		
		<td><form action="" method="GET">
		<input type="hidden" name="page" value="searchresults" />
		
		<input type="text" name="terms" value="Search" size="15" onload="var defaultValue = terms.value"
		onfocus="if (this.value==defaultValue) { this.value = ''; }"
		onblur="if (this.value=='') { this.value = defaultValue; }"
		/>
		
		<input type="submit" value="Go" />
		</form></td>
		
		</div></tr></table>
		<?php

		$isLoggedIn = false;
		$prefix = '';
		?>
</div></div>
<div class='content'>