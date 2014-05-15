<?php header('Content-type: text/xml'); ?>

<?php
$prefix = '../';
require '../includes/authorization.php';
include('../library/database.php');
?>
<xmlresponse>
<?php
	$firstName = $_POST['firstname'];
	$lastName = $_POST['lastname'];

	$db = new Database();
		
	if (mysqli_connect_errno()) {
		echo 'Error: Could not connect to database';
	} else {
		
		$db->insertAuthor($firstName, $lastName);
		
		$authorID = $db->queryLastAuthor()['authorID'];
		
		?><firstname><?php
		echo $firstName;
		?></firstname>
		
		<lastname>
		<?php echo $lastName; ?>
		</lastname>
		
		<authorid>
		<?php echo $authorID; ?>
		</authorid>
	<?php
	}
?>
</xmlresponse>