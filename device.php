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
				<div class="row">
				<?php
					if(isset($_GET['id']) && !empty($_GET['id'])) {
					echo '<div class="col-sm-4">';
						$deviceID = secure($_GET['id']);
                        $deviceID = strtoupper($deviceID);
						$iID = substr($deviceID, 2);     // wycina dwie pierwsze
						$gID = substr($deviceID, 0, 2);  // zwraca dwie pierwsze
						if(tagExists($gID)) {
							if(isset($_POST["commSend"])) {
								$today = date("Y-m-d H:i:s");
								$uid = getSingleValue("users","username",$_SESSION["username"],"id");
								$content = secure($_POST["comment"]);
								$devLink = $_POST['link'];
								$db = getDB();
								$statement = $db->prepare("INSERT INTO comments(did, uid, date, content, link) VALUES(:did, :uid, :date, :content, :link)");
								$statement->execute(array(
									"did" => $deviceID,
									"uid" => $uid,
									"date" => $today,
									"link" => $devLink,
									"content" => $content
								));
								$message2 = showMessage(0,"Komentarz został dodany.");
								unset($_POST["commSend"]);
							}
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
										redirect('home.php');
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
					?>
					</div>
					<div class="col-sm-8">
						<h2>Dodaj komentarz:</h2>
						<?php 
							if(isset($message2)) {  
								echo $message2;  
							}  
						?>
						<form class="form-horizontal" action="" method="POST">
							<div class="form-group"> 
								<label class="control-label col-sm-2" for="comment">Treść:</label>
								<div class="col-sm-9" style="text-align: left;">
									<textarea class="form-control" name="comment" id="comment" required style="min-width: 100%; max-width: 100%; min-height: 60px;"></textarea>
								</div>
							</div>
							<div class="form-group"> 
								<label class="control-label col-sm-2" for="link">Link do zdjęcia:</label>
								<div class="col-sm-9" style="text-align: left;">
									<input class="form-control" name="link" id="link">
								</div>
							</div>
							<div class="form-group">        
								<div class="col-sm-offset-5 col-sm-2">
									<input type="submit" name="commSend" class="btn btn-primary" value="Wyślij" />
								</div>
							</div>
						</form>
						
						
						<h2>Komentarze:</h2>
						<div class="col-sm-12" style="text-align: left;">
							<?php
								$db = getDB();
								$stmt = $db->prepare("SELECT * FROM comments WHERE did = :did ORDER BY date DESC");
								$stmt->bindParam(':did',$deviceID); 
								$stmt->execute();
								foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $comm) {
									$user = getSingleValue("users","id",$comm["uid"],"name");
									echo '<div class="col-sm-12">
													<div class="panel panel-default">
														<div class="panel-heading">
															<strong>'.$user.'</strong> <span class="text-muted">Data: '.$comm["date"].'</span>
														</div>
														<div class="panel-body">
															'.secure($comm["content"]).'
														</div>';
									if($comm["link"]<>"") {
										echo '	<div class="panel-footer">
															<a href="'.$comm["link"].'">Zdjęcie</a>
														</div>';
									}
														
									echo '	</div>
												</div>';
								}
							?>
					
					</div>
				</div>
					<?php
						
					} else {
						echo '<div class="col-sm-12">';
						require('showTables.php'); //brak POST i GET
					}
				?>
			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>