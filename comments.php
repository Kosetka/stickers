<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');
	
	if(isset($_GET['id'])) {
		$did = secure($_GET['id']);
		if(!statusExists($did)) redirect('home.php');
	} else {
		redirect('home.php');
	}
?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Komentarze</title>
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
				<h2>Komentarze: <?php echo $_GET["id"]; ?></h2>
				<div class="col-sm-12" style="text-align: left;">
					<?php
						$db = getDB();
						$deviceID = secure($_GET["id"]);
						$stmt = $db->prepare("SELECT * FROM comments WHERE did = :did ORDER BY date DESC");
						$stmt->bindParam(':did',$deviceID); 
						$stmt->execute();
						$count = 0;
						foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $comm) {
							$count++;
							$user = getSingleValue("users","id",$comm["uid"],"name");
							echo '<div class="col-sm-12">
														<div class="panel panel-default">
															<div class="panel-heading">
																<strong>'.$user.'</strong> <span class="text-muted">Data: '.$comm["date"].'</span>
															</div>
															<div class="panel-body">
																'.secure($comm["content"]).'
															</div>
														</div>
													</div>';
						}
						if($count==0) {
							echo showMessage(1," Brak komentarzy do wyświetlenia.");
						}
					?>

				</div>
			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>