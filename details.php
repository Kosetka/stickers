<?php  
include("config.php"); 
if(!checkFirewall()) redirect('error.php');
if(!loggedin()) redirect('index.php');

if(isset($_POST['department'])) {
	$departName = getSingleValue("firewall","tag",$_POST['department'],"name");
}


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Raport szczegółowy</title>
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
			<h2 class="text-center">Raport szczegółowy <?php if(isset($_POST['department'])) echo ' - '.$departName; ?></h2>
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
								<div class="col-sm-offset-4 col-sm-2">
									<input type="submit" name="depSend" class="btn btn-primary" value="Wybierz" />
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-8">
						<h4>Skróty oddziałów:</h4>
						<table class="table table-bordered">
							<thead></thead>
							<tbody>
								<?php
								$db = getDB();
								$statement = $db->prepare("SELECT * FROM firewall");
								$statement->execute();
								$i = 1;
								foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $fw) {
									if($i==1) echo "<tr>";
									echo "<th>".$fw['tag']."</th>";
									echo "<td>".$fw['name']."</td>";
									if($i==5) echo "</tr>";
									$i++;
									if($i==5) $i = 1;
								}

								?>
							</tbody>
						</table>
						<p>Kolorem <span style="color: goldenrod; font-weight: bold;">żółtym</span> oznaczone są urządzenia, których status został zmieniony w ciągu ostatniego miesiąca.</p>
					</div>
				</div>
			<?php
				require('detailTables.php'); //brak POST i GET
			?>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>