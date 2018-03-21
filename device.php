<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');
	
	


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Urządzenia</title>
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
				<?php
					if(isset($_GET['id']) && !empty($_GET['id'])) {
						$deviceID = secure($_GET['id']);
						$iID = substr($deviceID, 2);     // wycina dwie pierwsze
						$gID = substr($deviceID, 0, 2);  // zwraca dwie pierwsze
						if(tagExists($gID)) {
							if(!deviceExists($gID, true) && !isset($_POST['editSend'])) { 	 // nie ma takiego tagu sprzetu
								//sam tag gdy nie ma żadnego sprzętu
								require('deviceAdd.php'); 
								//tutaj lista sprzętu z danego tagu
								
							} else {													 // tag istnieje
								//if(deviceExists($deviceID, false)) {	//sprzęt z nazwą istnieje
									$today = date("Y-m-d H:i:s");
									if(isset($_POST['editSend'])) {
										//GET i POST
										foreach($_POST as $key => $value) {
											if($key<>"editSend") {
												$db = getDB();
												$statement = $db->prepare("INSERT INTO fieldvalue(name, fieldname, value, date) VALUES(:name, :fieldname, :value, :date)");
												$statement->execute(array(
													"name" => $deviceID,
													"fieldname" => $key,
													"value" => $value,
													"date" => $today
												));
											}
										}
										if(!statusExists($deviceID)) {
											$userID = getSingleValue("users","username",$_SESSION["username"],"id");
											$statement = $db->prepare("INSERT INTO status(name, status, uid, date) VALUES(:name, :status, :uid, :date)");
											$statement->execute(array(
												"name" => $deviceID,
												"status" => "1",
												"uid" => $userID,
												"date" => $today
											));
											
										}
										$message = showMessage(0,"Dane zostały zapisane.");
									} elseif(isset($_POST['statusSend'])) {
										$db = getDB();
										$userID = getSingleValue("users","username",$_SESSION["username"],"id");
										$statement = $db->prepare("INSERT INTO status(name, status, uid, date) VALUES(:name, :status, :uid, :date)");
										$statement->execute(array(
											"name" => $deviceID,
											"status" => $_POST['status'],
											"uid" => $userID,
											"date" => $today
										));
										$message = showMessage(0,"Status został zmieniony pomyślnie.");
									}
									if($iID=="") {
										echo "lista wszystkich";
									} else {
										require('deviceAdd.php'); 
									}
								/*} else {															//sprzęt z nazwą nie istnieje
									if($iID<>"") {
										echo "nie ma";
										//formularz dodawania
										require('deviceAdd.php'); 
									} else {
										redirect('index.php');	
									}
								}*/
							}
						} else {
							redirect('index.php');				
						}
					} else {
						require('showTables.php'); //brak POST i GET
					}
				?>
			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>