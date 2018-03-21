<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	if(isset($_POST["dateSend"])) {
		$dstart = $_POST["dstart"]." 00:00:00";
		$dend = $_POST["dend"]." 23:59:59";
		$department = $_POST["department"];

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

			<div class="text-center">
				<h2>Raport rotacji: <?php if(isset ($reportSend)) echo $_POST["dstart"].' - '.$_POST["dend"]; ?></h2>
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
					<div class="col-sm-3"> 
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
						<th>Przyjechał z</th>
						<th>Data przyjazdu</th>
						<th>Wyjechał do</th>
						<th>Data wyjazdu</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
				try {
					$db = getDB();
					$statement = $db->prepare("SELECT DISTINCT * FROM scan WHERE date>='$dstart' AND date<='$dend' AND department = $depID");

					$statement->execute();
					foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
						$devName = $row['name'];
						$statement = $db->prepare("SELECT * FROM scan WHERE date>='$dstart' AND date<='$dend' AND name = '$devName' ORDER BY date DESC LIMIT 2 ");
						$statement->execute();
						$col = [];
						$date = [];
						$p = 0;
						foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $c) {
							$col[] = $c['department'];
							$p++;
							$date[] = $c['date'];
						}
						if($p>1) {
							if($col[0]==$depID || $col[1]==$depID) {
								echo "<tr>";
								echo "<th><a href='device.php?id=$devName'>".$devName."</a></th>";
								if($depID<>$col[1]) {
									echo "<td>".getSingleValue('firewall','id',$col[1],'name')."</td>";
									echo "<td>".$date[1]."</td>";
								} else {
									echo "<td></td>";
									echo "<td></td>";
								}
								if($depID<>$col[0]) {
									echo "<td>".getSingleValue('firewall','id',$col[0],'name')."</td>";
									echo "<td>".$date[0]."</td>";
								} else {
									echo "<td></td>";
									echo "<td></td>";
								}
								echo "</tr>";
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