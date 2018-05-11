<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	$q = "";
	if(isset($_GET["q"])) {
		$q = secure($_GET["q"]);
	}

	
	if($q<>"") {
		$devTag = [];
		$devTagUnique = [];
		$db = getDB();
		$query = "SELECT DISTINCT(name) FROM fieldvalue WHERE value LIKE :q";  
		$statement = $db->prepare($query); 
		$n = "%".$q."%";
		$statement->bindParam(':q',$n); 
		$statement->execute();  
		$count = $statement->rowCount();  
		if($count > 0) {
			foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$devTag[] = $row['name'];
				if(!in_array(substr(strtoupper($row['name']),0,2),$devTagUnique)) {
					$devTagUnique[] = substr(strtoupper($row['name']),0,2);
				}
			}
			
		}
		
		
	}

?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Wyszukiwanie zaawansowane</title>
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
				<h2>Wyszukiwanie zaawansowane</h2>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<h4>Kryteria:</h4>
					<form class="form-horizontal" action="" method="GET">
						<div class="form-group">
							<label class="control-label col-sm-3" for="q">Fraza:</label>
							<div class="col-sm-9"> 
								<input class="form-control" name="q" id="q" required>
							</div>
						</div>
						<div class="form-group">        
							<div class="col-sm-offset-5 col-sm-2">
								<input type="submit" class="btn btn-primary" value="Szukaj" />
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-8">
					<?php
						//print_r($devTag);
						//print_r($devTagUnique);
					?>
				</div>
			</div>
			<?php
				if($q<>"") {
			?>
			<div class="row">
				<div class="col-sm-12">
					<h4>Wyniki wyszukiwania:</h4>
					
					<?php
						$found = 0;
						foreach($devTagUnique as $dTU) {
							echo '<table class="table table-bordered">';
							echo '<thead>';
							echo '<tr>';
							echo '<th class="text-center" style="background-color: lightgrey;" colspan="20">['.$dTU.'] '.getSingleValue("types","tag",$dTU,"name").'</th>';
							echo '</tr>';
							echo '<tr>';
							echo '<th>Nazwa</th>';
							$dTUid = getSingleValue("types","tag",$dTU,"id");
							$statement2 = $db->prepare("SELECT * FROM fields WHERE type_id = :typeID");
							$statement2->bindParam(':typeID',$dTUid); 
							$statement2->execute();
							foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $thead) {
								echo '<th>'.$thead["title"].'</th>';
								$arr[] = $thead['name'];
							}
							echo '<th>Oddział</th>';
							echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							
							foreach($devTag as $dT) {
								if(strpos(strtoupper($dT), $dTU)!==false) {
									echo '<tr>';
									echo '<td><a href="device.php?id='.strtoupper($dT).'">'.strtoupper($dT).'</a></td>';
									foreach($arr as $a) {
										$statement2 = $db->prepare("SELECT value FROM fieldvalue WHERE name LIKE :name AND fieldname = :fname ORDER BY date DESC LIMIT 1");
										$statement2->bindParam(':name',$dT); 
										$statement2->bindParam(':fname',$a); 
										$statement2->execute();
										$f = $statement2->fetch();
										$result = $f['value'];
										$class = "";
										if(stripos($result, $q) !== false) {
											$class = 'class="less"';
										}
										echo '<td '.$class.'>'.$result.'</td>';
									}
									if(statusExists($dT)) {
										$statement3 = $db->prepare("SELECT department FROM scan WHERE name LIKE :name ORDER BY date DESC LIMIT 1");
										$statement3->bindParam(':name',$dT); 
										$statement3->execute();
										$fq = $statement3->fetch();
										$res = getDepartment($fq['department']);
									} else {
										$res = 'Brak skanowania';
									}
									echo '<td>'.$res.'</td>';
									echo '</tr>';
								}
							}
							$arr = [];
							echo '</tbody>';
							echo '</table>';
							$found = 1;
						}
					
					?>
				
				</div>
			</div>
				<?php 
					if($found == 0)
						echo showMessage(1," Brak sprzętu spełniającego kryteria.<br><br><b>Fraza: </b>".$q);	
				?>
			<?php
			}
			?>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>