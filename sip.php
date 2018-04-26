<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');
?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>SIP</title>
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
			<h2 class="text-center">Raport numerów kolejek:</h2>
			<?php
			if(isset($message)) {
				echo showMessage(0,$message);
			}
			?>
			<div class="row">
				<div class="col-sm-4">
					<h4>Kryteria:</h4>
					<form class="form-horizontal" action="" method="POST">
						<div class="form-group">
							<label class="control-label col-sm-3" for="department">Oddział:</label>
							<div class="col-sm-9"> 
								<select class="form-control" name="department" id="department" required>
									<option value="all" selected>Wszystkie</option>
									<?php
									$db = getDB();
									$statement2 = $db->prepare("SELECT * FROM firewall");
									$statement2->execute();

									foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $value) {
										if(isset($_POST['department']) && $_POST['department']==$value['tag']) {
											echo '<option value="'.$value["tag"].'" selected>'.$value["name"].'</option>';
											$departName = $value["name"];
										}
										else
											echo '<option value="'.$value["tag"].'">'.$value["name"].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">        
							<div class="col-sm-offset-5 col-sm-2">
								<input type="submit" name="depSend" class="btn btn-primary" value="Wybierz" />
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-8">
					<h4>Legenda:</h4>
					<?php
						if(isset($_POST['depSend'])) {
							$departID = $_POST['department'];
							if($departID<>"all") $departID = getSingleValue("firewall","tag",$departID,"id");
						} else { $departID = "all"; }
						if($departID<>"all") {
							?>
							<p style="text-align: center" class="takenFine">Zajęte numery kolejek przez odpowiedni oddział.</p>
							<p style="text-align: center" class="takenWrong">Zajęte numery kolejek przez zły oddział.</p>
							<p style="text-align: center" class="takenListening">Wolne numery kolejek do odsłuchu.</p>
							<p style="text-align: center" class="takenListeningFine">Zajęte numery kolejek do odsłuchu przez odpowiedni oddział.</p>
							<p style="text-align: center" class="takenListeningWrong">Zajęte numery kolejek do odsłuchu przez zły oddział.</p>
							<?php
						} else {
							?>
                    <p>Kolorem <span style="color: red; font-weight: bold;">czerwonym</span> oznaczone są urządzenia, których numer kolejki VNCS jest po za zakresem danego oddziału, <span style="color: goldenrod; font-weight: bold;">żółtym</span> te, które nie były jeszcze skanowane, a <span style="color: green; font-weight: bold;">zielonym</span> te bez numeru kolejki.</p>
							<?php
						}
					?>
					
				</div>
			</div>
			
			<?php
				require("sipTables.php");
			?>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>