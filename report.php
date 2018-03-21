<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	if(isset($_POST["dateSend"])) {
		$dstart = $_POST["dstart"]." 00:00:00";
		$dend = $_POST["dend"]." 23:59:59";
		$ip = $_SERVER['REMOTE_ADDR'];
		$department = $_POST["department"];
		
		$reportSend = true;

	}


?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Raport ewidencji</title>
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
				<h2>Raport ewidencji: <?php if(isset ($reportSend)) echo $_POST["dstart"].' - '.$_POST["dend"]; ?></h2>
			</div>
			<form class="form-horizontal" action="" method="POST">
				<div class="form-group">
					<label class="control-label col-sm-2" for="dstart">Data od:</label>
					<div class="col-sm-2">          
						<input type="date" class="form-control" id="dstart" placeholder="" value="<?php if(isset($reportSend)) echo $_POST['dstart']; else echo date('Y-m-').'01';?>" name="dstart" required>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="dend">Data do:</label>
					<div class="col-sm-2">          
						<input type="date" class="form-control" id="dend" placeholder="" value="<?php if(isset($reportSend)) echo $_POST['dend'];else echo date('Y-m-d');?>" name="dend" required>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-2" for="department">Oddział:</label>
					<div class="col-sm-2"> 
						<select class="form-control" name="department" id="department" required>
							<option value="all" selected>Wszystkie</option>
							<?php
								$db = getDB();
								$statement2 = $db->prepare("SELECT * FROM firewall");
								$statement2->execute();
							
								foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $value) {
									echo '<option value="'.$value["tag"].'">'.$value["name"].'</option>';
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">        
					<div class="col-sm-offset-2 col-sm-2">
						<input type="submit" name="dateSend" class="btn btn-primary" value="Pokaż" />
					</div>
				</div>
			</form>
			<?php  
				if(isset($message)) {  
					echo $message;  
				}  
				if(isset($reportSend)) {

			?> 
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Urządzenie</th>
						<?php
							$db = getDB();
							if($department=="all") 
								$statement = $db->prepare("SELECT * FROM firewall");
							else {
								$statement = $db->prepare("SELECT * FROM firewall WHERE tag = :tag");
								$statement->bindParam(':tag',$department); 
							}
							$statement->execute();
							$depart = [];
							foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $fw) {
								echo "<th colspan='2' title='".$fw['name']."'>".$fw['tag']."</th>";
								$depart[] = $fw['id'];
							}
						?>
						
					</tr>
				</thead>
				<tbody>
					<?php
					try {
						$statement = $db->prepare("SELECT * FROM types");

						$statement->execute();
						foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
							echo "<tr>";
							echo "<th title='".$row['name']."'>".$row['name']."</th>";
							$name = '%'.$row["tag"].'%';
							
							
							
							foreach ($depart as $dep) {
								
								$statement = $db->prepare("SELECT COUNT(DISTINCT name) FROM scan WHERE department = :dep AND name LIKE :name AND date>= :dstart AND date<= :dend;");
								$statement->bindParam(':dep',$dep); 
								$statement->bindParam(':name',$name); 
								$statement->bindParam(':dstart',$dstart); 
								$statement->bindParam(':dend',$dend); 
								$statement->execute();
								$count = $statement->fetchColumn(); 
								if($dep==16) { // 16 to ID magazynu
									$working = 0;
									$nWorking = 0;
									$statement2 = $db->prepare("SELECT DISTINCT name FROM status WHERE name LIKE :name");
									$statement2->bindParam(':name',$name); 
									$statement2->execute();
									foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $device) {
										$dd = $device['name'];
										$statement3 = $db->prepare("SELECT * FROM status WHERE name = :name ORDER BY date DESC LIMIT 1");
										$statement3->bindParam(':name',$dd); 
										$statement3->execute();
										$f = $statement3->fetch();
										if($f['status']==2) {
											$nWorking += 1;

										} elseif($f['status']==1) {
											$working +=1;
										}
									}
									echo "<td class='more' title='Sprawne'>".$working."</td>";
									echo "<td class='less' title='Zepsute'>".$nWorking."</td>";
								} else {
									if($row["tag"]=="PC") {
										$stand = getSingleValue('firewall','id',$dep,'stand');
										$class = "";
										if($count>$stand) $class = "more";
										elseif($count<$stand) $class = "less";
										else $class = "equal";
										echo "<td class='".$class."' title='Liczba urządzeń'>".$count."</td>";
										echo "<td class='".$class."' title='Liczba stanowisk'>".$stand."</td>";
									} else {
										echo "<td colspan='2'>".$count."</td>";
									}
								}
							}
							echo "</tr>";
						}
					} catch(PDOException $e) {
						echo $e->getMessage();
					}
					?>
				</tbody>
			</table>
			<?php
				}
			?>
		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>