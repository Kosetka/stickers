<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');


	if(isset($_POST["scanSend"])) {
		$name = strtoupper($_POST["name"]);
		
		if(statusExists($name)) {
			$userID = getSingleValue("users","username",$_SESSION["username"],"id");
			$today = date("Y-m-d H:i:s");
			$day = date("Y-m-d");
			$db = getDB();
			
			$q = $db->query("SELECT date FROM scan WHERE name='$name' ORDER BY date DESC LIMIT 1");
			$f = $q->fetch();
			$dayDB = $f["date"];
			$dayDB = substr($dayDB, 0, 10);
            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
			if($day <> $dayDB) {
				$statement = $db->prepare("INSERT INTO scan(name, ip, uid, date, department) VALUES(:name, :ip, :uid, :date, :department)");
				$statement->execute(array(
					"name" => $name,
					"ip" => $ip,
					"uid" => $userID,
					"department" => $departmentSelected,
					"date" => $today
				));
				$message = showMessage(0," ".$name." - Urządzenie zeskanowane pomyślnie.");
				echo "<script>
							audio = new Audio('sound/success.wav');
							audio.play();
						</script>";
			} else {
				$message = showMessage(1," ".$name." - Urządzenie było już dzisiaj skanowane.");
				echo "<script>
							audio = new Audio('sound/warning.wav');
							audio.play();
						</script>";
			}
		} else {
			$message = showMessage(1," ".$name." - Sprzęt nie został jeszcze dodany.");
			echo "<script>
							audio = new Audio('sound/error.wav');
							audio.play();
						</script>";
		}
	}

?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Skanowanie</title>
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
				<h2>Skanowanie sprzętu</h2>
				<?php
					if(isset($message)) {  
						echo $message;  
					}
				?>
				<form class="form-horizontal" action="" method="POST">
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="name">Nazwa:</label>
						<div class="col-sm-3">          
							<input type="text" class="form-control" style="text-transform: uppercase;" id="name" placeholder="" name="name" maxlength="32" required autofocus>
						</div>
					</div>
					<div class="form-group">        
						<div class="col-sm-offset-5 col-sm-2">
							<input type="submit" name="scanSend" class="btn btn-primary" value="Wyślij" />
						</div>
					</div>
				</form>
			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>