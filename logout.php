<?php   
	session_start();  
	include("config.php");
	logout();
	redirect('index.php');
?>  