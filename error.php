<?php
	include("config.php"); 
	if(checkFirewall()) redirect('index.php');
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Document</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 
		<link rel="stylesheet" href="css/style.css"/> 
	</head>
	<body>
		<div class="container" style="margin-top:50px">
			<div class="starter-template">
				<h1>Błąd lokalizacji</h1>
				<p class="lead">Logujesz się ze złej lokalizacji. Upewnij się, że jesteś połączony z odpowiednią siecią wifi.</p>
				<p>W razie dalszych problemów skontaktuj się z działem technicznym.</p>
			</div>
		</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>