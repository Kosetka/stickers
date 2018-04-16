<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	if(isset($_POST['depSend'])) {
		$departmentSelected = $_POST['dep'];
		$_SESSION["department"] = $departmentSelected;
		$message = showMessage(0,"Oddział został zmieniony pomyslnie.");
	}


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Zmiana oddziału</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 
		<link rel="stylesheet" href="css/style.css"/> 
	</head>
	<body>
		<?php
			require('nav.php');
		?>
		<div class="container" style="margin-top: 50px">
			<div class="text-center">
				<h2 style="margin-bottom: 50px">Zmiana oddziału</h2>
				<?php  
					if(isset($message)) {  
						echo $message;  
					}  
				?> 
				<form class="form-horizontal" action="" method="POST">
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="dep">Wybierz oddział:</label>
						<div class="col-sm-3">          
							<select class="form-control" id="dep"name="dep" required>
								<?php
									$db = getDB();
									$statement2 = $db->prepare("SELECT * FROM firewall"); 
									$statement2->execute();
									foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $value) {
										if($departmentSelected==$value["id"]) 
											echo '<option value="'.$value["id"].'" selected>'.$value["name"].'</option>';
										else
											echo '<option value="'.$value["id"].'">'.$value["name"].'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">        
						<div class="col-sm-offset-5 col-sm-2">
							<input type="submit" name="depSend" class="btn btn-primary" value="Zmień" />
						</div>
					</div>
				</form>
			</div>
		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>