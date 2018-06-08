<?php  
include("config.php"); 
if(!checkFirewall()) redirect('error.php');
if(!loggedin()) redirect('index.php');

if(isset($_GET['id'])) {
	$typeID = secure($_GET['id']);
	$ttag = $typeID;
	$query = "SELECT * FROM firewall WHERE tag = :tag"; 
	$db = getDB();
	$statement = $db->prepare($query); 
	$statement->bindParam(':tag',$typeID); 
	$statement->execute();
	$tID = $statement->fetch();
	$tID = $tID["id"];
	$count = $statement->rowCount();  
	if($count > 0 || isset($_POST["depSend2"])) {

		if(empty($_POST["name"]) || empty($_POST["tag"]) || empty($_POST["ip"]) || empty($_POST["stand"])) {  
			$message = showMessage(1,' Uzupełnij wymagane pola.');
		} else {
			$name = $_POST["name"];
			$tag = strtoupper($_POST["tag"]);
			$ip = $_POST["ip"];
			$stand = $_POST["stand"];
			$vncsfrom = $_POST["vncsfrom"];
			$vncsto = $_POST["vncsto"];
			$listeningfrom = $_POST["listeningfrom"];
			$listeningto = $_POST["listeningto"];
			$instance = $_POST["instance"];
			try {
				$db = getDB();
				$statement = $db->prepare("UPDATE firewall SET name = :name, tag = :tag, ip = :ip, range_from = :range_from, range_to = :range_to, listening_from = :listening_from, listening_to = :listening_to, instance = :instance, stand = :stand WHERE id = :id");
				$statement->execute(array(
					"name" => $name,
					"tag" => $tag,
					"ip" => $ip,
					"id" => $typeID,
					"range_from" => $vncsfrom,
					"range_to" => $vncsto,
					"listening_from" => $listeningfrom,
					"listening_to" => $listeningto,
					"instance" => $instance,
					"stand" => $stand
				));

				$message = showMessage(0,' Edycja oddziału pomyślna.');
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

	} 
	if($count==0) {
		$query = "SELECT * FROM firewall WHERE id = :id"; 
		$statement = $db->prepare($query); 
		$statement->bindParam(':id',$typeID); 
		$statement->execute();
		$count = $statement->rowCount();  
		if($count > 0) {
			$numeric = true;
			$fname = getSingleValue("firewall", "id", $typeID, "name");
			$ftag = getSingleValue("firewall", "id", $typeID, "tag");
			$fip = getSingleValue("firewall", "id", $typeID, "ip");
			$fstand = getSingleValue("firewall", "id", $typeID, "stand");
			$vncsfrom = getSingleValue("firewall", "id", $typeID, "range_from");
			$vncsto = getSingleValue("firewall", "id", $typeID, "range_to");
			$listeningfrom = getSingleValue("firewall", "id", $typeID, "listening_from");
			$listeningto = getSingleValue("firewall", "id", $typeID, "listening_to");
			$instance = getSingleValue("firewall", "id", $typeID, "instance");

		} else {
			redirect("department.php");
		}
	}
}


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Prywatne telefony</title>
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
			<div class="col-sm-12">
				<h2 class="text-center">Prywatne telefony</h2>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Skrót</th>
							<th>Pracownik</th>
							<th>VNCS</th>
							<th>Oddział</th>
							<th>Akcja</th>
						</tr>
					</thead>
					<tbody>
						<?php
						try {
							$db = getDB();
							
							$statement = $db->prepare("SELECT * FROM status WHERE name LIKE 'PT%'");
							$statement->execute();
							foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
						?>
						<tr>
							<td><?php echo '<a href=device.php?id='.$row["name"].'>'.$row["name"].'</a>'; ?></td>
							<?php
								$rname = $row["name"];
								$rns = ["PTpracownik", "PTvncs", "PToddzial"];
								foreach($rns as $rn) {
									$q = $db->query("SELECT value FROM fieldvalue WHERE name ='$rname' AND fieldname = '$rn' ORDER BY date DESC LIMIT 1");
									$f = $q->fetch();
									$result = $f["value"];
									echo '<td>'.$result.'</td>';
								}
							?>
							<td><a title="Szczegóły" href="device.php?id=<?php echo $row["name"]; ?>"><span class="glyphicon glyphicon-th-list" style="color: black; font-size: 1em"></span></a></td>
						</tr>

						<?php
							}
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
						?>
					</tbody>
				</table>
				<h4 class="text-center">Dodaj nowy:</h4>
				<form class="form-horizontal text-center" action="device.php" method="GET">
					<div class="form-group"> 
						<label class="control-label col-sm-5" for="status">Nazwa:</label>
						<div class="col-sm-3" style="text-align: left;">
							<input class="form-control" type="text" value="PT" name="id">
						</div>
					</div>
					<div class="form-group">        
						<div class="col-sm-offset-5 col-sm-2">
							<input type="submit" class="btn btn-primary" value="Dalej" />
						</div>
					</div>
				</form>

			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>