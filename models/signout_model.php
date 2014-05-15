<?php

require '../library/autoloader.php';

GoogleClient::signout();

header('Location: ../');

?>