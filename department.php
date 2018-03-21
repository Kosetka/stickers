<?php  
include("config.php"); 
if(!checkFirewall()) redirect('error.php');
if(!loggedin()) redirect('index.php');

if(isset($_POST["depSend"])) {
	if(empty($_POST["name"]) || empty($_POST["tag"]) || empty($_POST["ip"]) || empty($_POST["stand"])) {  
		$message = showMessage(1,' Wszystkie pola są wymagane'); 
	} else {
		$name = $_POST["name"];
		$tag = strtoupper($_POST["tag"]);
		$ip = $_POST["ip"];
		$stand = $_POST["stand"];
		$query = "SELECT * FROM firewall WHERE tag = :tag"; 
		$db = getDB();
		$statement = $db->prepare($query); 
		$statement->bindParam(':tag',$tag); 
		$statement->execute();  
		$count = $statement->rowCount();  
		if($count <= 0) {
			try {
				$statement = $db->prepare("INSERT INTO firewall(name, tag, ip, stand) VALUES(:name, :tag, :ip, :stand)");
				$statement->execute(array(
					"name" => $name,
					"ip" => $ip,
					"stand" => $stand,
					"tag" => $tag
				));
				$message = showMessage(0,' Oddział został dodany.');
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		} else {
			$message = showMessage(1,' Ten skrót jest już zajęty.');
		}
	}
}
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
				try {
					$db = getDB();
					$statement = $db->prepare("UPDATE firewall SET name = :name, tag = :tag, ip = :ip, stand = :stand WHERE id = :id");
					$statement->execute(array(
						"name" => $name,
						"tag" => $tag,
						"ip" => $ip,
						"id" => $typeID,
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

		} else {
			redirect("department.php");
		}
	}
}


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Oddziały</title>
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
			<div class="col-sm-6 text-center">

				<h2 style="margin-bottom: 50px">Dodawanie/edycja oddziału</h2>
				<?php  
					if(isset($message)) {  
						echo $message;  
					}  
				?> 
				<form class="form-horizontal" action="" method="POST">
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="name">Pełna nazwa:</label>
						<div class="col-sm-6">          
							<input type="text" class="form-control" id="name" placeholder="" name="name" maxlength="32" <?php if(isset($numeric)) echo "value='$fname'"; ?> required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="tag">Skrót (3 znaki):</label>
						<div class="col-sm-2">          
							<input type="text" class="form-control" id="tag" placeholder="" name="tag" maxlength="3" <?php if(isset($numeric)) echo "value='$ftag'"; ?> style="text-transform: uppercase" required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="ip">Adres IP:</label>
						<div class="col-sm-4">          
							<input type="text" class="form-control" id="ip" name="ip" <?php if(isset($numeric)) echo "value='$fip'"; ?> required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="stand">Liczba stanowisk:</label>
						<div class="col-sm-2">          
							<input type="number" class="form-control" id="stand" placeholder="" name="stand" <?php if(isset($numeric)) echo "value='$fstand'"; ?> required>
						</div>
					</div>
					<div class="form-group">        
						<div class="col-sm-offset-5 col-sm-2">
							<input type="submit" name="<?php if(isset($numeric)) echo 'depSend2'; else echo 'depSend';?>" class="btn btn-primary" value="Zapisz" />
						</div>
					</div>
				</form>
			</div>





			<div class="col-sm-6">
				<h2 style="margin-bottom: 50px" class="text-center">Oddziały</h2>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Pełna nazwa</th>
							<th>Skrót</th>
							<th>IP</th>
							<th>Stanowiska</th>
							<th>Akcja</th>
						</tr>
					</thead>
					<tbody>
						<?php
						try {
							$db = getDB();
							$statement = $db->prepare("SELECT * FROM firewall");
							$statement->execute();
							foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
						?>
						<tr>
							<td><?php echo $row["name"]; ?></td>
							<td><?php echo $row["tag"]; ?></td>
							<td><?php echo $row["ip"]; ?></td>
							<td><?php echo $row["stand"]; ?></td>
							<td><a title="Szczegóły" href="department.php?id=<?php echo $row['id']; ?>"><span class="glyphicon glyphicon-th-list" style="color: black; font-size: 1em"></span></a></td>
						</tr>

						<?php
							}
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
						?>
					</tbody>
				</table>


			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$('#type').change(function(){
					if(this.value == 1)
						$('#valHide').fadeIn('fast');
					else
						$('#valHide').fadeOut('fast');

				});
			});
		</script>
	</body>
</html>