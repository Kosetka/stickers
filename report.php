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
	$csvStr = '';

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
				<h2>Raport ewidencji: <?php if(isset($reportSend)) echo $_POST["dstart"].' - '.$_POST["dend"]; ?></h2>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<h4>Kryteria:</h4>
					<form class="form-horizontal" action="" method="POST">
						<div class="form-group">
							<label class="control-label col-sm-3" for="dstart">Data od:</label>
							<div class="col-sm-9">          
								<input type="date" class="form-control" id="dstart" placeholder="" value="<?php if(isset($reportSend)) echo $_POST['dstart']; else echo date('Y-m-').'01';?>" name="dstart" required>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="dend">Data do:</label>
							<div class="col-sm-9">          
								<input type="date" class="form-control" id="dend" placeholder="" value="<?php if(isset($reportSend)) echo $_POST['dend'];else echo date('Y-m-d');?>" name="dend" required>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="department">Oddział:</label>
							<div class="col-sm-9"> 
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
							<div class="col-sm-offset-5 col-sm-2">
								<input type="submit" name="dateSend" class="btn btn-primary" value="Pokaż" />
							</div>
						</div>
					</form>
				
				</div>
				<div class="col-sm-8">
					<h4>Skróty oddziałów:</h4>
					<table class="table table-bordered">
						<thead></thead>
						<tbody> 
						<?php
							$db = getDB();
							$statement = $db->prepare("SELECT * FROM firewall");
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
					<p>Pierwsza liczba przy oddziale oznacza ilość urządzeń, druga ilość stanowisk.</p>
					<p>Jeżeli komputerów jest więcej niż stanowisk tło jest <span style="color: green; font-weight: bold;">zielone</span>, tyle samo - <span style="color: goldenrod; font-weight: bold;">żółte</span>, komputerów jest za mało - <span style="color: red; font-weight: bold;">czerwone</span>.</p>
					<p>W przypadku Magazynu <b>MGN</b> kolor <span style="color: green; font-weight: bold;">zielony</span> oznacza liczbę urządzeń sprawnych, a <span style="color: red; font-weight: bold;">czerwony</span> zepsutych.</p>
				</div>
			
			</div>
			<?php  
				if(isset($message)) {  
					echo $message;  
				}  
				if(isset($reportSend)) {

			?> 
			<p><a href="csv/report-csv.php">Pobierz raport csv</a></p>
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Urządzenie</th>
							<?php
								if($department=="all") 
									$statement = $db->prepare("SELECT * FROM firewall");
								else {
									$statement = $db->prepare("SELECT * FROM firewall WHERE tag = :tag");
									$statement->bindParam(':tag',$department); 
								}
								$statement->execute();
								$depart = [];

								$csvStr = '[["Urządzenie"';
								foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $fw) {
									echo "<th colspan='2' title='".$fw['name']."'>".$fw['tag']."</th>";
									$depart[] = $fw['id'];
									$temp = $fw['name'];
									$csvStr .= ',"'.$temp.'",""';
								}
								$csvStr .= ']';
							?>

						</tr>
					</thead>
					<tbody>
						<?php
						try {
							$statement = $db->prepare("SELECT * FROM types");

							$statement->execute();
							foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
								$tempName = $row['name'];
								$csvStr .= ',["'.$tempName.'"';
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
										$stmt = $db->prepare("SELECT DISTINCT name FROM scan WHERE department = 16 AND name LIKE :name AND date>= :dstart AND date<= :dend;");
										$stmt->bindParam(':name',$name); 
										$stmt->bindParam(':dstart',$dstart); 
										$stmt->bindParam(':dend',$dend); 
										$stmt->execute();
										$working = 0;
										$nWorking = 0;
										foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ddd) {
											$statement2 = $db->prepare("SELECT DISTINCT name FROM status WHERE name LIKE :name");
											$statement2->bindParam(':name',$ddd["name"]); 
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
											$csvStr .= ',"'.$count.'","'.$stand.'"';
										} else {
											echo "<td colspan='2'>".$count."</td>";
											$csvStr .= ',"'.$count.'","0"';
										}
									}
								}
								echo "</tr>";
								$csvStr .= ']';
							}
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
						$csvStr .= '];';
						?>
					</tbody>
				</table>
			</div>
			<?php
				}
			//echo $csvStr;
			?>
		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			var data = <?php echo $csvStr; ?>

			var csvContent = '';
			data.forEach(function(infoArray, index) {
				dataString = infoArray.join(';');
				csvContent += index < data.length ? dataString + '\n' : dataString;
			});

			var download = function(content, fileName, mimeType) {
				var a = document.createElement('a');
				mimeType = mimeType || 'application/octet-stream';

				if (navigator.msSaveBlob) { // IE10
					navigator.msSaveBlob(new Blob([content], {
						type: mimeType
					}), fileName);
				} else if (URL && 'download' in a) { //html5 A[download]
					a.href = URL.createObjectURL(new Blob([content], {
						type: mimeType
					}));
					a.setAttribute('download', fileName);
					document.body.appendChild(a);
					a.click();
					document.body.removeChild(a);
				} else {
					location.href = 'data:application/octet-stream,' + encodeURIComponent(content); // only this mime type is supported
				}
			}

			//download(csvContent, 'dowload.csv', 'text/csv;encoding:utf-8');
		</script>
	</body>
</html>