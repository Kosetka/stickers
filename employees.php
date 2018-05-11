<?php  
include("config.php"); 
if(!checkFirewall()) redirect('error.php');
if(!loggedin()) redirect('index.php');

if(!checkAccess(9)) redirect('deny.php');

if(isset($_POST["depSend"])) {
	if(empty($_POST["name"]) || empty($_POST["login"]) || empty($_POST["password"]) || empty($_POST["access"])) {  
		$message = showMessage(1,' Wszystkie pola są wymagane'); 
	} else {
		$name = $_POST["name"];
		$login = $_POST["login"];
		$password = $_POST["password"];
		$access = $_POST["access"];

		$query = "SELECT * FROM users WHERE username = :username"; 
		$db = getDB();
		$statement = $db->prepare($query); 
		$statement->bindParam(':username',$login); 
		$statement->execute();  
		$count = $statement->rowCount();  
		try {
			$statement = $db->prepare("INSERT INTO users(name, username, password, access) VALUES(:name, :username, :password, :access)");
			$statement->execute(array(
				"name" => $name,
				"username" => $login,
				"password" => $password,
				"access" => $access
			));
			$message = showMessage(0,' Konto zostało dodane.');
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
}
if(isset($_GET['id'])) {
	$typeID = secure($_GET['id']);
	$ttag = $typeID;
	$query = "SELECT * FROM users WHERE id = :id"; 
	$db = getDB();
	$statement = $db->prepare($query); 
	$statement->bindParam(':id',$ttag); 
	$statement->execute();
	$tID = $statement->fetch();
	$tID = $tID["id"];
	$count = $statement->rowCount();  
	if(isset($_POST["depSend2"])) {
		if(empty($_POST["name"]) || empty($_POST["login"]) || empty($_POST["password"]) || empty($_POST["access"])) {  
			$message = showMessage(1,' Uzupełnij wymagane pola.');
		} else {
			$name = $_POST["name"];
			$login = $_POST["login"];
			$password = $_POST["password"];
			$access = $_POST["access"];
			try {
				$db = getDB();
				$statement = $db->prepare("UPDATE users SET name = :name, username = :login, password = :password, access = :access WHERE id = :id");
				$statement->execute(array(
					"name" => $name,
					"login" => $login,
					"password" => $password,
					"access" => $access,
					"id" => $tID
				));

				$message = showMessage(0,' Edycja konta pomyślna.');
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

	} 
	$query = "SELECT * FROM users WHERE id = :id"; 
	$statement = $db->prepare($query); 
	$statement->bindParam(':id',$typeID); 
	$statement->execute();
	$count = $statement->rowCount();  
	if($count > 0) {
		$numeric = true;
		$fname = getSingleValue("users", "id", $typeID, "name");
		$ftag = getSingleValue("users", "id", $typeID, "username");
		$fip = getSingleValue("users", "id", $typeID, "password");
		$fstand = getSingleValue("users", "id", $typeID, "access");

	} else {
		redirect("employees.php");
	}
}


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Konta użytkowników</title>
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

				<h2 style="margin-bottom: 50px">Dodawanie/edycja kont</h2>
				<?php  
				if(isset($message)) {  
					echo $message;  
				}  
				?> 
				<form class="form-horizontal" action="" method="POST">
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="name">Imię i nazwisko:</label>
						<div class="col-sm-6">          
							<input type="text" class="form-control" id="name" placeholder="" name="name" maxlength="32" <?php if(isset($numeric)) echo "value='$fname'"; ?> required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="login">Login:</label>
						<div class="col-sm-6">          
							<input type="text" class="form-control" id="login" placeholder="" name="login" <?php if(isset($numeric)) echo "value='$ftag'"; ?> required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="password">Hasło:</label>
						<div class="col-sm-6">          
							<input type="text" class="form-control" id="password" name="password" <?php if(isset($numeric)) echo "value='$fip'"; ?> required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-offset-1 col-sm-4" for="access">Uprawnienia:</label>
						<div class="col-sm-2">          
							<input type="number" class="form-control" id="access" placeholder="" name="access" <?php if(isset($numeric)) echo "value='$fstand'"; ?> required>
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
				<h2 style="margin-bottom: 50px" class="text-center">Konta</h2>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Imię i Nazwisko</th>
							<th>Login</th>
							<th>Hasło</th>
							<th>Uprawnienia</th>
							<th>Akcja</th>
						</tr>
					</thead>
					<tbody>
						<?php
						try {
							$db = getDB();
							$statement = $db->prepare("SELECT * FROM users");
							$statement->execute();
							foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
						?>
						<tr>
							<td><?php echo $row["name"]; ?></td>
							<td><?php echo $row["username"]; ?></td>
							<td><?php echo $row["password"]; ?></td>
							<td><?php echo $row["access"]; ?></td>
							<td><a title="Szczegóły" href="employees.php?id=<?php echo $row['id']; ?>"><span class="glyphicon glyphicon-th-list" style="color: black; font-size: 1em"></span></a></td>
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