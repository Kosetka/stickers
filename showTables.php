<?php 
	defined('inc') or header('Location: index.php');
	$db = getDB();
?>
<h2>Lista wszystkich urządzeń</h2>

<?php
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
			<th>Akcja</th>
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
			$i = 1;
			$count = $statement->rowCount();
			if($count<1)
				echo showMessage(1,' Brak danych do wyświetlenia.');
			else 
				echo showMessage(0,' Liczba urządzeń: '.$count);
			foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$rname = $row['name'];
				$rid = $typeName['id'];
		?>
		<tr>
			<td><?php echo $i++; ?></td>
			<td style="text-transform: uppercase;"><?php echo $rname; ?></td>
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
			?>
			<td>
				<?php 
					$q = $db->prepare("SELECT status FROM status WHERE name = :name ORDER BY date DESC LIMIT 1");
					$q->bindParam(':name',$rname); 
					$q->execute();
					$f = $q->fetch();
					$result = $f['status'];
					echo $statuses[$result];
				?>
			</td>
			<td><a title="Edytuj" href="device.php?id=<?php echo $rname; ?>"><span class="glyphicon glyphicon-pencil" style="color: black; font-size: 1em"></span></a></td>
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
?>
		