<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(loggedin()) redirect('home.php');

	if(isset($_POST["loginSend"])) {  
		if(empty($_POST["username"]) || empty($_POST["password"])) {  
			$message = '<label>All fields are required</label>';  
		} else {
			$db = getDB();
			$query = "SELECT * FROM users WHERE username = :username AND password = :password";  
			$statement = $db->prepare($query);  
			$statement->execute(  
				array(  
					'username'     =>     $_POST["username"],  
					'password'     =>     $_POST["password"]  
				)  
			);  
			$count = $statement->rowCount();  
			if($count > 0) {  
				$_SESSION["username"] = $_POST["username"];  
				redirect('home.php'); 
			} else {  
				$message = '<label>Błędne dane logowania.</label>';  
			}
		}
	}
?>  

<!DOCTYPE html>  
<html>  
	<head>  
		<title>Logowanie</title>   
		<link rel="stylesheet" href="css/bootstrap.min.css"/>  
		<link rel="stylesheet" href="css/style.css"/>  
	</head>  
	<body>   
		<div class="container" style="width:400px;padding-top: 30px;">  
			<?php  
				if(isset($message)) {  
					echo '<label class="text-danger">'.$message.'</label>';  
				}  
			?>  
			<h3 align="">Panel logowania</h3><br />  
			<form method="post">  
				<label for="username">Nazwa użytkownika:</label>  
				<input id="username" type="text" name="username" class="form-control" required/>  
				<br />  
				<label for="password">Hasło:</label>  
				<input id="password" type="password" name="password" class="form-control" required/>  
				<br />  
				<input type="submit" name="loginSend" class="btn btn-primary" value="Zaloguj" />  
			</form>  
		</div>  
		<br /> 
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> 
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>   
	</body>  
</html>  