<?php  
include("config.php"); 
if(!checkFirewall()) redirect('error.php');
if(!loggedin()) redirect('index.php');

if(isset($_POST["dataSend"])) {
	$reportSend = true;
	$devTag = $_POST["device"];
}

?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Wolne numery naklejek</title>
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
				<h2>Wolne numery naklejek <?php if(isset($devTag)) echo '['.$devTag.']'; ?>:</h2>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<h4>Kryteria:</h4>
					<form class="form-horizontal" action="" method="POST">
						<div class="form-group">
							<label class="control-label col-sm-3" for="device">Urządzenie:</label>
							<div class="col-sm-9"> 
								<select class="form-control" name="device" id="device" required>
									<?php
									$db = getDB();
									$statement2 = $db->prepare("SELECT * FROM types");
									$statement2->execute();

									foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $value) {
										if(isset($_POST['device']) && $_POST['device']==$value['tag']) {
											echo '<option value="'.$value["tag"].'" selected>'.$value["name"].'</option>';
											$deviceName = $value["name"];
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
								<input type="submit" name="dataSend" class="btn btn-primary" value="Pokaż" />
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
				$statement = $db->prepare("SELECT * FROM status WHERE name like '$devTag%'");
				$statement->execute();
				foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$devices[] = $row["name"];
				}
				$showed = false;
				echo '<pre style="max-height: 500px;">';
				$sorted = [];
				foreach ($devices as $dev) {
					$sorted[] = substr($dev,2);
				}
				asort($sorted);
				$total = 0;
				foreach ($sorted as $dev) {
					//echo $devTag.$dev.'<br>';
					$showed = true;
					$total++;
				}
				echo 'Ostatnie dodane urządzenie: '.$devTag.$dev.'<br>';
				for($i=1;$i<=$dev;$i++) {
					if(!in_array($i,$sorted))
						if($i<10) {
							echo $devTag.'0'.$i.'<br>';
						} else {
							echo $devTag.$i.'<br>';
						}
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