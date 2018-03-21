<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');
?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Dodawanie urządzenia</title>
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
			<div class="text-center">
				<h2>Podaj nazwę urządzenia:</h2>
				<form class="form-horizontal" action="device.php" method="GET">
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="id">Nazwa:</label>
						<div class="col-sm-3">          
							<input type="text" class="form-control" id="id" placeholder="" name="id" maxlength="8" autofocus required>
						</div>
					</div>
					<div class="form-group">        
						<div class="col-sm-offset-5 col-sm-2">
							<input type="submit" class="btn btn-primary" value="Dodaj" />
						</div>
					</div>
				</form>
			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>