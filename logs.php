<?php  
include("config.php"); 
if(!checkFirewall()) redirect('error.php');
if(!loggedin()) redirect('index.php');

if(isset($_POST["dateSend"])) {
	$dstart = $_POST["dstart"]." 00:00:00";
	$dend = $_POST["dstart"]." 23:59:59";
	$reportSend = true;
}


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Logi</title>
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
				<h2>Logi: <?php if(isset($reportSend)) echo $_POST["dstart"]; ?></h2>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<h4>Kryteria:</h4>
					<form class="form-horizontal" action="" method="POST">
						<div class="form-group">
							<label class="control-label col-sm-3" for="dstart">Data:</label>
							<div class="col-sm-9">          
								<input type="date" class="form-control" id="dstart" placeholder="" value="<?php if(isset($reportSend)) echo $_POST['dstart']; else echo date('Y-m-d');?>" name="dstart" required>
							</div>
						</div>
						<div class="form-group">        
							<div class="col-sm-offset-5 col-sm-2">
								<input type="submit" name="dateSend" class="btn btn-primary" value="Pokaż" />
							</div>
						</div>
					</form>

				</div>
			</div>
			<?php  
			if(isset($message)) {  
				echo $message;  
			}  
			if(isset($reportSend)) {
				$db = getDB();
				$users = [];
				$departments = [];
				$statement = $db->prepare("SELECT * FROM users");
				$statement->execute();
				foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$users[$row["id"]] = $row["name"];
				}
				$statement = $db->prepare("SELECT * FROM firewall");
				$statement->execute();
				foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$departments[$row["id"]] = $row["name"];
				}
				$showed = false;
				echo '<pre style="max-height: 500px;">';
				$statement = $db->prepare("SELECT * FROM logs WHERE date >= '$dstart' AND date <= '$dend' ORDER BY date DESC");
				$statement->execute();
				$action = [1=>"Skanowanie", 2=>"Dodanie/edycja urządzenia", 3=>"Zmiana statusu", 5=>"Dodanie komentarza"];
				foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
					echo $row["date"].' ('.$users[$row["uid"]].') Urządzenie: '.$row["did"].' : '.$action[$row["aid"]].' - '.$row["result"].' : '.$departments[$row["department"]].' : '.$row["ip"].'<br>';


					$showed = true;
				}
				if(!$showed) { 
					echo 'Brak danych do wyświetlenia.';
				} 
				echo '</pre>';
			}
			?>
		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>