<?php

require 'entity.class.php';

class Issue extends Entity
{
	protected $issueID;
	protected $volumeID;
}

class Test extends Entity
{
	protected $id;
	protected $issues = array();
}


$test = new Test();
$test->issues[0] = new Issue();

$test->issues[0]->issueID = 10;
$test->issue[0]->volumeID = 90;
$test->id = 8;

echo $test->issues[0]->volumeID;

?>