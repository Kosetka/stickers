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
		<link rel="stylesheet" href="css/jasny-bootstrap.min.css"/> 
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
						$deviceID = str_replace(' ','',$deviceID);
                        $deviceID = strtoupper($deviceID);
						$iID = substr($deviceID, 2);     // wycina dwie pierwsze
						$gID = substr($deviceID, 0, 2);  // zwraca dwie pierwsze
						if(tagExists($gID)) {
							if(isset($_POST["commSend"]) && $_POST["comment"]<>"") {
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
								$userID = getSingleValue("users","username",$_SESSION["username"],"id");
								$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
								$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");
								$statement->execute(array(
									"uid" => $userID,
									"aid" => 5,
									"result" => "success",
									"date" => $today,
									"ip" => $ip,
									"did" => $deviceID,
									"department" => $departmentSelected
								));
								$message2 = showMessage(0," Komentarz został dodany.");
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
												if(substr($key, 2)=="pracownik") {
													$value = strtoupper($value);
												}
												$statement = $db->prepare("INSERT INTO fieldvalue(name, fieldname, value, date) VALUES(:name, :fieldname, :value, :date)");
												$statement->execute(array(
													"name" => $deviceID,
													"fieldname" => $key,
													"value" => $value,
													"date" => $today
												));
											}
										}
										$userID = getSingleValue("users","username",$_SESSION["username"],"id");
										if(!statusExists($deviceID)) {
											$statement = $db->prepare("INSERT INTO status(name, status, uid, date) VALUES(:name, :status, :uid, :date)");
											$statement->execute(array(
												"name" => $deviceID,
												"status" => "1",
												"uid" => $userID,
												"date" => $today
											));
										}
										$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
										$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");
										$statement->execute(array(
											"uid" => $userID,
											"aid" => 2,
											"result" => "success",
											"date" => $today,
											"ip" => $ip,
											"did" => $deviceID,
											"department" => $departmentSelected
										));
										
										$message = showMessage(0," Dane zostały zapisane.");
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
										$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
										$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");
										$statement->execute(array(
											"uid" => $userID,
											"aid" => 3,
											"result" => $_POST['status'],
											"date" => $today,
											"ip" => $ip,
											"did" => $deviceID,
											"department" => $departmentSelected
										));
										$message = showMessage(0," Status został zmieniony pomyślnie.");
									}
									if($iID=="") {
										//redirect('home.php');
										$sAll = true;
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
					<?php
						if(!isset($sAll)) {	
							if(!statusExists($deviceID)) $dEx = true;
							if(!isset($dEx)) {
					?>
					<div class="col-sm-8">
						<?php 
							$db = getDB();
							if(isset($_GET["sid"])) {
								$tempsid = $_GET["sid"];
								$tempid = $_GET["id"];
								
								$q = $db->query("SELECT * FROM scan WHERE id = '$tempsid' AND name = '$tempid' LIMIT 1");
								$f = $q->fetch();
								$tempres = 'id=>'.$f["id"].';ip=>'.$f["ip"].';name=>'.$f["name"].';date=>'.$f["date"].';uid=>'.$f["uid"].';department=>'.$f["department"];
								$userID = getSingleValue("users","username",$_SESSION["username"],"id");
								$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
								$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");

								$del = $db->prepare("DELETE FROM scan WHERE id = '$tempsid' AND name = '$tempid' LIMIT 1");
								$del->execute();
								$tempcount = $del->rowCount();
								if($tempcount>0) {
									$statement->execute(array(
										"uid" => $userID,
										"aid" => 8,
										"result" => $tempres,
										"date" => $today,
										"ip" => $ip,
										"did" => $tempid,
										"department" => $departmentSelected
									));
								}
							}
							$q = $db->query("SELECT department FROM scan WHERE name='$deviceID' ORDER BY date DESC LIMIT 1");
							$f = $q->fetch();
							$res = $f["department"];
							$depName = getSingleValue("firewall","id",$res,"name");
							$q = $db->query("SELECT date FROM scan WHERE name='$deviceID' ORDER BY date DESC LIMIT 1");
							$f = $q->fetch();
							$res = $f["date"];
							$q = $db->query("SELECT uid FROM scan WHERE name='$deviceID' AND date = '$res' ORDER BY date DESC LIMIT 1");
							$f = $q->fetch();
							$res2 = getSingleValue("users","id",$f["uid"],"name");
						?>
						<h2>Ostatnie skanowanie: <?php echo $depName;?></h2>
						<h4>Data: <?php echo $res; ?></h4>
						<h4>Skanujący: <?php echo $res2; ?></h4>
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
								
								if(isset($_GET["kid"])) {
									$tempsid = $_GET["kid"];
									$tempid = $_GET["id"];

									$q = $db->query("SELECT * FROM comments WHERE id = '$tempsid' AND did = '$tempid' LIMIT 1");
									$f = $q->fetch();
									$tempres = 'id=>'.$f["id"].';did=>'.$f["did"].';uid=>'.$f["uid"].';date=>'.$f["date"].';content=>'.$f["content"].';link=>'.$f["link"];
									$userID = getSingleValue("users","username",$_SESSION["username"],"id");
									$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
									$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");

									$del = $db->prepare("DELETE FROM comments WHERE id = '$tempsid' AND did = '$tempid' LIMIT 1");
									$del->execute();
									$tempcount = $del->rowCount();
									if($tempcount>0) {
										$statement->execute(array(
											"uid" => $userID,
											"aid" => 8,
											"result" => $tempres,
											"date" => $today,
											"ip" => $ip,
											"did" => $tempid,
											"department" => $departmentSelected
										));
									}
								}
								if(isset($_GET["kid"])) {
									if ($tempcount == 0) {
										echo showMessage(1,' Usunięcie komentarza niepomyślne.'); 
									} elseif ($tempcount == 1) {
										echo showMessage(0,' Pomyślnie usunięto komentarz.');
									} else {
										echo showMessage(2,' Co tu się stało.');
									}
								}
								
								
								$stmt = $db->prepare("SELECT * FROM comments WHERE did = :did ORDER BY date DESC");
								$stmt->bindParam(':did',$deviceID); 
								$stmt->execute();
								$commentsCount = 0;
								foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $comm) {
									$user = getSingleValue("users","id",$comm["uid"],"name");
									echo '<div class="col-sm-12">
													<div class="panel panel-default">
														<div class="panel-heading" style="display: flex;">
															<div class="col-sm-8">
																<strong>'.$user.'</strong> <span class="text-muted">Data: '.$comm["date"].'</span>
															</div>';
									$access = getSingleValue("users","username",$_SESSION["username"],"access");
									$tempuid = getSingleValue("users","username",$_SESSION["username"],"id");
									if($comm["uid"]==$tempuid || $access==9) {
										echo "				<div class='col-sm-4' style='text-align: right;'>
																<a style='color: black !important;' href='device.php?id=".$deviceID."&kid=".$comm['id']."' title='Usuń komentarz'><span class='glyphicon glyphicon-trash'></span></a>
															</div>";
									}
									echo '					</div>
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
									$commentsCount++;
								}
								if($commentsCount<=0) {
									echo showMessage(2,' Brak komentarzy do wyświetlenia.');
								}
							?>
					
					</div>
					<div class="text-center">
						<h2>Historia skanowania:</h2>
					</div> 
					<div class="col-sm-12" style="text-align: left;">
					<?php 
						if(isset($_GET["sid"])) {
							if ($tempcount == 0) {
								echo showMessage(1,' Usunięcie skanowanie niepomyślne.'); 
							} elseif ($tempcount == 1) {
								echo showMessage(0,' Pomyślnie usunięto skanowanie.<br>');
							} else {
								echo showMessage(2,' Co tu się stało.');
							}
						}
					?>
					</div>
					<table class="table table-bordered custom-center">
						<thead>
							<tr>
								<th>Oddział</th>
								<th>Data</th>
								<th>Użytkownik</th>
								<th>Akcje</th> 
							</tr>
						</thead>
						<tbody>
							<?php

							try {
								$db = getDB();
								$statement = $db->prepare("SELECT * FROM scan WHERE name = '$deviceID' ORDER BY date ASC");
								$statement->execute();
								foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
									echo "<tr>";
									echo "<td>".getSingleValue('firewall','id',$row['department'],'name')."</td>";
									echo "<td>".$row['date']."</td>";
									echo "<td>".getSingleValue("users","id",$row['uid'],"name")."</td>";
									$access = getSingleValue("users","username",$_SESSION["username"],"access");
									if ($access >= 5) { 
										echo "<td><a href='device.php?id=".$deviceID."&sid=".$row['id']."' title='Usuń skanowanie'><span class='glyphicon glyphicon-trash'></span></a></td>"; 
									} else {
										echo "<td></td>";
									}
									echo "</tr>";
								}


							} catch(PDOException $e) {
								echo $e->getMessage();
							}
							?>
						</tbody>
					</table>
						<div class="text-center">
							<h2>Historia statusów:</h2>
						</div> 
						<table class="table table-bordered custom-center">
							<thead>
								<tr>
									<th>Data</th>
									<th>Status</th>
									<th>Użytkownik</th>
								</tr>
							</thead>
							<tbody>
								<?php

								try {
									$db = getDB();
									$statement = $db->prepare("SELECT * FROM status WHERE name = '$deviceID' ORDER BY date ASC");
									$statement->execute();
									$tempC = 1;
									foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
										echo "<tr>";
										echo "<td>".$row['date']."</td>";
										if ($tempC == 1) {
											echo "<td>Dodany/Sprawny</td>";
										} else {
											echo "<td>".$statuses[$row['status']]."</td>";
										}
										echo "<td>".getSingleValue("users","id",$row['uid'],"name")."</td>";
										echo "</tr>";
										$tempC++;
									}


								} catch(PDOException $e) {
									echo $e->getMessage();
								}
								?>
							</tbody>
						</table>
						<div class="text-center">
							<h2>Historia edycji urządzenia:</h2>
						</div> 
						<table class="table table-bordered custom-center">
							<thead>
								<tr>
									<th rowspan="2">Data edycji</th>
									<th colspan="3">Status</th>
									<th rowspan="2">Użytkownik</th>
								</tr>
								<tr>
									<td>Pole</td>
									<td>Przed zmianą</td>
									<td>Po zmianie</td>
								</tr>
							</thead>
							<tbody>
								<?php

								try {
									$db = getDB();
								
									$fieldNames = [];
									$fieldNamesFull = [];
									$dates = [];
									$data = [];
									
									$statement = $db->prepare("SELECT DISTINCT fieldname FROM fieldvalue WHERE name = '$deviceID'");
									$statement->execute();
									foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
										$fieldNames[] = $row["fieldname"];
									}
									$tag = substr($deviceID,0,2);
									$statement = $db->prepare("SELECT name, title FROM fields WHERE name LIKE '$tag%'");
									$statement->execute();
									foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
										$fieldNamesFull[$row["name"]] = $row["title"];
									}
									
									$statement = $db->prepare("SELECT DISTINCT date FROM fieldvalue WHERE name = '$deviceID' ORDER BY date DESC");
									$statement->execute();
									foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
										$dates[] = $row["date"];
									}
									$firstRow = true;
									
									$dateCount = 0;
									$totalFields = count($fieldNames);
									if(count($dates)>1) {
										foreach($dates as $date) {
											$firstRow = true;
											$tempRows = 0;
											$statement = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$deviceID' AND date = '$date'");
											$statement->execute();
											foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
												$data[$dateCount][$tempRows] = $row['value'];
												$tempRows++;
												/*echo "<tr>";
												if($firstRow) {
													echo "<td rowspan='$totalFields'>$date</td>";
												}
												echo "<td>".$fieldNamesFull[$fieldNames[$tempRows-1]]."</td>";
												echo "<td>".$row['value']."</td>";
												echo "<td>".$row['value']."</td>";
												if($firstRow) {
													echo "<td rowspan='$totalFields'>Użytkownik</td>";
													$firstRow = false;
												}
												echo "</tr>";*/
											}
											/*for($i=$tempRows;$tempRows<$totalFields;$i++) {
												echo "<tr><td>".$fieldNamesFull[$fieldNames[$i]]."</td><td></td><td></td></tr>";
												$tempRows++;
											}*/
											$dateCount++;
										}
										for($i=1;$i<count($data);$i++) {
											$firstRow = true;
											$tempRows = 0;
											$showed = false;
											for($j=0;$j<count($data[$i]);$j++) {
												echo "<tr>";
												$tempRows++;
												if($firstRow) {
													echo "<td rowspan='$totalFields'>".$dates[$i-1]."</td>";
												}
												if($data[$i-1][$j]<>$data[$i][$j]) {
													echo "<td>".$fieldNamesFull[$fieldNames[$j]]."</td>";
													echo "<td>".$data[$i][$j]."</td>";
													echo "<td>".$data[$i-1][$j]."</td>";
												} else {
													if(!$showed) {
														echo "<td style='margin: 0 !important; padding: 0 !important;'></td><td style='margin: 0 !important; padding: 0 !important;'></td><td style='margin: 0 !important; padding: 0 !important;'></td>";
														$showed = true;
													}
												}
												if($firstRow) {
													echo "<td rowspan='$totalFields'>".getSingleValue('users','id',getSingleValue('logs','date',$dates[$i-1],'uid'),'name')."</td>";
													$firstRow = false;
												}
												echo "</tr>";
												//echo $data[$i][$j];
												//echo $data[$i-1][$j];
											}
											for($k=$tempRows;$k<count($data[$i-1]);$k++) {
												echo "<tr><td>".$fieldNamesFull[$fieldNames[$k]]."</td><td></td><td>".$data[$i-1][$j]."</td></tr>";
												$tempRows++;
											}
											
										}
										//echo "<pre>";
										//print_r($data);
										//echo "</pre>";
										
										
									}
									
									

								} catch(PDOException $e) {
									echo $e->getMessage();
								}
								?>
							</tbody>
						</table>
				</div>
					<?php
							}
						} else {
							?>
							<div class="col-sm-12" style="text-align: left;">
								<h2>Lista wszystkich urządzeń - [<?php echo $gID;?>] <?php echo getSingleValue("types","tag",$gID,"name");?></h2>
								
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>lp.</th>
											<th>Nazwa</th>
											<?php
												$db = getDB();
												$typeID = getSingleValue("types","tag",$gID,"id");
												$statement2 = $db->prepare("SELECT * FROM fields WHERE type_id = :typeID");
												$statement2->bindParam(':typeID',$typeID); 
												$statement2->execute();
												foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $thead) {
													echo '<th>'.$thead["title"].'</th>';
													$arr[] = $thead['name'];
												}
											?>
										</tr>
									</thead>
									<tbody>
										<?php
										
										$ttname = "%".$gID."%";
										$statement = $db->prepare("SELECT DISTINCT name FROM fieldvalue WHERE name LIKE :name ORDER BY name ASC");
										$statement->bindParam(':name',$ttname); 
										$statement->execute();
										$j = 1;
										$count = $statement->rowCount();
										foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
											$rname = $row['name'];
											echo '<tr>';
											echo '<td>'.$j.'</td>';
											echo '<td style="text-transform: uppercase;"><a href="device.php?id='.$rname.'">'.$rname.'</a></td>';
											foreach ($arr as $a) {
												$statement2 = $db->prepare("SELECT value FROM fieldvalue WHERE name LIKE :name AND fieldname = :fname ORDER BY date DESC LIMIT 1");
												$statement2->bindParam(':name',$rname); 
												$statement2->bindParam(':fname',$a); 
												$statement2->execute();
												$f = $statement2->fetch();
												$result = $f['value'];
												echo '<td>'.$result.'</td>';
											}
											
											echo '</tr>';
											$j++;
										}
							
										?>
									
									</tbody>
								</table>
							</div>
							
							
							<?php
						}
					
					} else {
						echo '<div class="col-sm-12">';
						require('showTables.php'); //brak POST i GET
					}
				?>
			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="js/jasny-bootstrap.min.js"></script>
	</body>
</html>