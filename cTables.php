<?php 
defined('inc') or header('Location: index.php');
$db = getDB();

if(isset($_POST['depSend'])) {
	$departID = $_POST['department'];
	if($departID<>"all") $departID = getSingleValue("firewall","tag",$departID,"id");
} else { $departID = null; }
?>

<?php
if($departID<>null) {
	

	$statement = $db->prepare("SELECT * FROM types");
	$statement->execute();
	foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $typeName) {
		$arr = [];
	?>
	<p class="lead tname"><?php echo "[".$typeName['tag']."] ".$typeName['name'];?></p>
<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>lp.</th>
				<th>Model</th>
				<th>Ilość</th>
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
			$statement3 = $db->prepare("SELECT DISTINCT name FROM fieldselect");
			$statement3->bindParam(':name',$ttname); 
			$statement3->execute();
			$total = [];
			foreach ($statement3->fetchAll(PDO::FETCH_ASSOC) as $row2) {
				$total[$row2['name']] = 0;
			}
			foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$rname = $row['name'];
				$rid = $typeName['id'];
				if($departID<>"all") { // raport oddział
					$stmt = $db->prepare("SELECT * FROM scan WHERE name = :name ORDER BY date DESC LIMIT 1");
					$stmt->bindParam(':name',$rname); 
					$stmt->execute();
					$depid = $stmt->fetch();
					$depid = $depid['department'];
					if($departID==$depid) {
						$statement2 = $db->prepare("SELECT value FROM fieldvalue WHERE name LIKE :name AND fieldname LIKE '%model%' ORDER BY date DESC LIMIT 1");
						$statement2->bindParam(':name',$rname); 
						$statement2->execute();
						$f = $statement2->fetch();
						$result = $f['value'];
						$total[$result]++;
					}
				} else {
					$statement2 = $db->prepare("SELECT value FROM fieldvalue WHERE name LIKE :name AND fieldname LIKE '%model%' ORDER BY date DESC LIMIT 1");
					$statement2->bindParam(':name',$rname); 
					$statement2->execute();
					$f = $statement2->fetch();
					$result = $f['value'];
					$total[$result]++;
				}
			}
			$j = 1;
			//$total = asort($total);
			ksort($total);
			$totalnumber = 0;
			foreach($total as $t=>$v) {
				if($v>0) {
					echo '<tr>';
					echo '<td>'.$j++.'</td>';
					echo '<td>'.$t.'</td>';
					echo '<td>'.$v.'</td>';
					echo '</tr>';
				}
				$totalnumber+=$v;
			}
			echo '<tr>';
			echo '<th colspan="2">Łącznie: </th>';
			echo '<th>'.$totalnumber.'</th>';
			
			echo '</tr>';
			
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
			?>
		</tbody>
	</table>
</div>
<?php 
	}
}
?>
