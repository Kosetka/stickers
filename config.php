<?php
	session_start();
	/* DATABASE CONFIGURATION */
	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');
	define('DB_DATABASE', 'stickers');
	define('inc', 'true');
	define('testVersion', 'false'); //false

    require("functions.php");
?>
