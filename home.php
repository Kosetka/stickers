<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

?>  
<!DOCTYPE html>
<html lang="pl">
<head>
	<title>Strona główna</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 
	<link rel="stylesheet" href="css/style.css"/> 
</head>
<body>
	<?php
		require('nav.php');
	?>

	<div class="container" style="margin-top:50px">

		<div class="starter-template">
			<h1></h1>
			<p class="lead"></p>
		</div>

	</div><!-- /.container -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>