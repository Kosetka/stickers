<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	if(!isset($_GET["id"])) 
		redirect('home.php');
	else {
		$did = secure($_GET["id"]);
	}

?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Historia rotacji sprzętu</title>
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
				<h2>Historia skanowania: <?php echo $did; ?></h2>
			</div> 
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Oddział</th>
						<th>Data</th>
					</tr>
				</thead>
				<tbody>
					<?php

				try {
					$db = getDB();
					$statement = $db->prepare("SELECT * FROM scan WHERE name = '$did' ORDER BY date DESC");
					$statement->execute();
					foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
						echo "<tr>";
						echo "<td>".getSingleValue('firewall','id',$row['department'],'name')."</td>";
						echo "<td>".$row['date']."</td>";
						echo "</tr>";
					}

					
				} catch(PDOException $e) {
					echo $e->getMessage();
				}
					?>
				</tbody>
			</table>



		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>