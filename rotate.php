<?php  
include("config.php"); 
if(!checkFirewall()) redirect('error.php');
if(!loggedin()) redirect('index.php');

if(isset($_POST["dateSend"]) || isset($_POST["dateSend2"])) {
	$department = $_POST["department"];
	if(isset($_POST["dateSend2"])) {
		$details = true;
	}
	$reportSend = true;

	$depID = getSingleValue("firewall","tag",$department,"id");
}

?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Raport rotacji sprzętu</title>
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
			<h2 class="text-center">Raport rotacji:</h2>
			<div class="row">
				<div class="col-sm-4">
					<h4>Kryteria:</h4>
					<form class="form-horizontal" action="" method="POST">
						<div class="form-group">
							<label class="control-label col-sm-3" for="department">Oddział:</label>
							<div class="col-sm-9"> 
								<select class="form-control" name="department" id="department" required>
									<?php
									$db = getDB();
									$statement2 = $db->prepare("SELECT * FROM firewall");
									$statement2->execute();

									foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $value) {
										if(isset($department) && $department==$value['tag'])
											echo '<option value="'.$value["tag"].'" selected>'.$value["name"].'</option>';
										else
											echo '<option value="'.$value["tag"].'">'.$value["name"].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">        
							<div class="col-sm-offset-3 col-sm-2">
								<input type="submit" name="dateSend" class="btn btn-primary" value="Pokaż" />
							</div>       
							<div class="col-sm-offset-2 col-sm-2">
								<input type="submit" name="dateSend2" class="btn btn-primary" value="Pokaż szczegóły" />
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-8">
					<h4>Legenda:</h4>
					<table class="table table-bordered">
						<thead></thead>
						<tbody>
							<?php
							$db = getDB();
							$statement = $db->prepare("SELECT * FROM types");
							$statement->execute();
							$i = 1;
							foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $fw) {
								if($i==1) echo "<tr>";
								echo "<th>".$fw['tag']."</th>";
								echo "<td>".$fw['name']."</td>";
								if($i==5) echo "</tr>";
								$i++;
								if($i==5) $i = 1;
							}

							?>
						</tbody>
					</table>
					<p>W wersji szczegółowej kolorem <span style="color: green; font-weight: bold;">zielonym</span> oznaczone są urządzenia, które pojawiły się w oddziale od ostatniego skanowania, a <span style="color: red; font-weight: bold;">czerwonym</span> te które wyjechały.</p>
				</div>
			</div>
			<?php  
			if(isset($message)) {  
				echo $message;  
			}  
			if(isset($reportSend)) {

			?> 
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Data skanowania</th>
						<?php
							$stmt = $db->prepare("SELECT * FROM types");
							$stmt->execute();
							$dname = [];
							foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $nrow) {
								echo '<th title="'.$nrow["name"].'">'.$nrow["tag"].'</th>';
								$dname[] = $nrow["tag"];
							}
				
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					$stmt2 = $db->prepare("SELECT DISTINCT date(date_format(date, '%Y-%m-%d')) as uniquedates from scan");
					$stmt2->execute();
					$date = [];
					foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $drow) {
						$date[] = $drow["uniquedates"];
					}
				try {
					$n = 0;
					foreach($date as $d) {
						$check = $db->prepare("SELECT COUNT(*) FROM scan WHERE date LIKE '$d%' AND department = $depID");
						$check->execute();
						$ch = $check->fetchColumn(); 
						if($ch>0) {
							echo '<tr>';
							echo '<th>'.$d.'</th>';
							$devNames[$n] = [];
							$sts = $db->prepare("SELECT * FROM scan WHERE date LIKE '$d%' AND department = $depID");
							$sts->execute();
							foreach ($sts->fetchAll(PDO::FETCH_ASSOC) as $devN) {
								$devNames[$n][] = $devN["name"];
							}
							foreach($dname as $dn) {
								$statement = $db->prepare("SELECT COUNT(*) FROM scan WHERE date LIKE '$d%' AND department = $depID AND name LIKE '%$dn%'");
								$statement->execute();
								$ccom = $statement->fetchColumn(); 
								echo '<td>'.$ccom.'</td>';
							}
							echo '</tr>';
							if(isset($details)) {
								
								if($n>0) {
									$cte = 1;
									$diff = array_diff($devNames[$n], $devNames[$n-1]);
									foreach($diff as $dev) {
										if ($cte<=1) {
											echo '<tr>';
											echo '<td></td>';
										}
										echo '<td class="more"><a href="device.php?id='.$dev.'">'.$dev.'</a></td>';
										if($cte>=19) {
											echo '</tr>';
											$cte = 0;
										}
										$cte++;
									}
									$cte = 1;
									$diff = array_diff($devNames[$n-1], $devNames[$n]);
									foreach($diff as $dev) {
										if ($cte<=1) {
											echo '<tr>';
											echo '<td></td>';
										}
										echo '<td class="less"><a href="device.php?id='.$dev.'">'.$dev.'</a></td>';
										if($cte>=19) {
											echo '</tr>';
											$cte = 0;
										}
										$cte++;
									}
								}
								$n++;
							}
						}
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