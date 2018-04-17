<?php 
defined('inc') or header('Location: index.php');
$db = getDB();

if(isset($_POST['depSend'])) {
	$departID = $_POST['department'];
	if($departID<>"all") $departID = getSingleValue("firewall","tag",$departID,"id");
} else {
	$departID = null;
}
if($departID <> null) {
	$statement = $db->prepare("SELECT * FROM types");
	$statement->execute();
	foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $typeName) {
		$arr = [];
	?>
	<p class="lead tname"><?php echo "[".$typeName['tag']."] ".$typeName['name'];?></p>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>lp.</th>
				<th>Nazwa</th>
				<?php
					if($departID=="all") echo'<th>Oddział</th>';
		try {
			$typeID = $typeName['id'];
			$statement2 = $db->prepare("SELECT * FROM fields WHERE type_id = :typeID");
			$statement2->bindParam(':typeID',$typeID); 
			$statement2->execute();
			foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $thead) {
				echo '<th>'.$thead["title"].'</th>';
				$arr[] = $thead['name'];
			}
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
				?>
				<th>Status</th>
				<th>Komentarze</th>
				<th>Historia</th>
			</tr>
		</thead>
		<tbody>
			<?php
		try {
			$ttname = "%".$typeName['tag']."%";
			$db = getDB();
			$statement = $db->prepare("SELECT DISTINCT name FROM fieldvalue WHERE name LIKE :name");
			$statement->bindParam(':name',$ttname); 
			$statement->execute();
			$j = 1;
			$count = $statement->rowCount();
			foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$rname = $row['name'];
				$rid = $typeName['id'];
			?>
			<tr>

					<?php 
				if($departID<>"all") { // raport oddział
					$stmt = $db->prepare("SELECT * FROM scan WHERE name = :name ORDER BY date DESC LIMIT 1");
					$stmt->bindParam(':name',$rname); 
					$stmt->execute();
					$depid = $stmt->fetch();
					$depid = $depid['department'];
					if($departID==$depid) {
						?>
						<td><?php echo $j++; ?></td>
						<td style="text-transform: uppercase;"><a href="device.php?id=<?php echo $rname; ?>"><?php echo $rname; ?></a></td>
					<?php

						foreach($arr as $a) {
							$statement2 = $db->prepare("SELECT value FROM fieldvalue WHERE name LIKE :name AND fieldname = :fname ORDER BY date DESC LIMIT 1");
							$statement2->bindParam(':name',$rname); 
							$statement2->bindParam(':fname',$a); 
							$statement2->execute();
							$f = $statement2->fetch();
							$result = $f['value'];
							echo '<td>'.$result.'</td>';
						}
						$q = $db->prepare("SELECT status FROM status WHERE name = :name ORDER BY date DESC LIMIT 1");
						$q->bindParam(':name',$rname); 
						$q->execute();
						$f = $q->fetch();
						$result = $f['status'];
						$statement3 = $db->prepare("SELECT * FROM status WHERE name LIKE :name ORDER BY date DESC LIMIT 2");
						$statement3->bindParam(':name',$rname);
						$statement3->execute();
						$i = 1;
						$d = [];
						$s = [];
						$class = "";
						foreach ($statement3->fetchAll(PDO::FETCH_ASSOC) as $row2) {
							$d[$i] = $row2['date'];
							$s[$i] = $row2['status'];
							$i++;
						}
						if(isset($s[2]) && $s[2]<>$s[1]) {
							if($i>2) {
								$datetime1 = new DateTime($d[2]);
								$datetime2  = new DateTime($d[1]);
								$interval = $datetime1->diff($datetime2);
								if($interval->format('%a days')<=30) $class='class="equal2"';
							}
						}
						echo '<td '.$class.'>'.$statuses[$result].'</td>';
						$stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE did = '$rname'");
						$stmt->execute();
						$ccom = $stmt->fetchColumn(); 
						$ccom = " (".$ccom.")";
						echo '<td><a title="Komentarze" href="comments.php?id='.$rname.'"><span class="glyphicon glyphicon-comment"></span>'.$ccom.'</a></td>';
						echo '<td><a title="Historia" href="history.php?id='.$rname.'"><span class="glyphicon glyphicon-calendar"></span></a></td>';


					}

				} else {
					$stmt = $db->prepare("SELECT * FROM scan WHERE name = :name ORDER BY date DESC LIMIT 1");
					$stmt->bindParam(':name',$rname); 
					$stmt->execute();
					$depid = $stmt->fetch();
					$depid = $depid['department'];	
				?>
				<td><?php echo $j++; ?></td>
				<td style="text-transform: uppercase;"><a href="device.php?id=<?php echo $rname; ?>"><?php echo $rname; ?></a></td>
				<?php
					echo '<td title="'.getSingleValue("firewall","id",$depid,"name").'">'.getSingleValue("firewall","id",$depid,"tag").'</td>';
					foreach($arr as $a) {
						$statement2 = $db->prepare("SELECT value FROM fieldvalue WHERE name LIKE :name AND fieldname = :fname ORDER BY date DESC LIMIT 1");
						$statement2->bindParam(':name',$rname); 
						$statement2->bindParam(':fname',$a); 
						$statement2->execute();
						$f = $statement2->fetch();
						$result = $f['value'];
						echo '<td>'.$result.'</td>';
					}
					$q = $db->prepare("SELECT status FROM status WHERE name = :name ORDER BY date DESC LIMIT 1");
					$q->bindParam(':name',$rname); 
					$q->execute();
					$f = $q->fetch();
					$result = $f['status'];
					$statement3 = $db->prepare("SELECT * FROM status WHERE name LIKE :name ORDER BY date DESC LIMIT 2");
					$statement3->bindParam(':name',$rname);
					$statement3->execute();
					$i = 1;
					$d = [];
					$s = [];
					$class = "";
					foreach ($statement3->fetchAll(PDO::FETCH_ASSOC) as $row2) {
						$d[$i] = $row2['date'];
						$s[$i] = $row2['status'];
						$i++;
					}
					if(isset($s[2]) && $s[2]<>$s[1]) {
						if($i>2) {
							$datetime1 = new DateTime($d[2]);
							$datetime2  = new DateTime($d[1]);
							$interval = $datetime1->diff($datetime2);
							if($interval->format('%a days')<=30) $class='class="equal2"';
						}
					}
					echo '<td '.$class.'>'.$statuses[$result].'</td>';
					$stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE did = '$rname'");
					$stmt->execute();
					$ccom = $stmt->fetchColumn(); 
					$ccom = " (".$ccom.")";
					//echo '<td><a title="Komentarze" href="comments.php?id='.$rname.'"><span class="glyphicon glyphicon-comment"></span>'.$ccom.'</a></td>';
?>
				<!-- Button trigger modal -->
				<td>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#<?php echo $rname;?>">
						<span class="glyphicon glyphicon-comment"></span><?php echo $ccom; ?>
					</button>
				</td>
				<!-- Modal -->
				<div class="modal fade bd-example-modal-lg" id="<?php echo $rname;?>" tabindex="-1" role="dialog">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h3 class="modal-title" id="<?php echo 'ml'.$rname;?>">Komentarze - <?php echo $rname;?></h3>
							</div>
							<div class="modal-body">
								<?php
									$t2deviceID = $rname;
									$t2stmt = $db->prepare("SELECT * FROM comments WHERE did = :did ORDER BY date DESC");
									$t2stmt->bindParam(':did',$t2deviceID); 
									$t2stmt->execute();
									$t2count = 0;
									foreach ($t2stmt->fetchAll(PDO::FETCH_ASSOC) as $t2comm) {
										$t2count++;
										$t2user = getSingleValue("users","id",$t2comm["uid"],"name");
										echo '<div>
														<div class="panel panel-default">
															<div class="panel-heading">
																<strong>'.$t2user.'</strong> <span class="text-muted">Data: '.$t2comm["date"].'</span>
															</div>
															<div class="panel-body">
																'.secure($t2comm["content"]).'
															</div>
														</div>
													</div>';
									}
									if($t2count==0) {
										echo "Brak komentarzy do wyświetlenia.";
									}
								?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
							</div>
						</div>
					</div>
				</div>

<?php
					echo '<td><a title="Historia" href="history.php?id='.$rname.'"><span class="glyphicon glyphicon-calendar"></span></a></td>';

				}

				?>
			</tr>
			<?php
			}
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
			?>
		</tbody>
	</table>
	<?php 
	}
}
	?>
