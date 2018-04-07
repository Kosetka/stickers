<?php 
defined('inc') or header('Location: index.php');
$db = getDB();

if(isset($_POST['depSend'])) {
	$departID = $_POST['department'];
	if($departID<>"all") $departID = getSingleValue("firewall","tag",$departID,"id");
} else { $departID = "all"; }

function getDep($dname) {
	$db = getDB();
	$q = $db->query("SELECT * FROM scan WHERE name='$dname' ORDER BY date DESC LIMIT 1");
	$f = $q->fetch();
	$result = $f["department"];
	return $result;
}
$departmentsName = [];
$statement = $db->prepare("SELECT * FROM firewall");
$statement->execute();
foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $dN) {
	$departmentsName[$dN["id"]] = $dN["name"];
}
?>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>lp.</th>
			<th>Urządzenie</th>
			<th>Oddział</th>
			<th>Login</th>
			<th>Hasło</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 0;
		$total = [];
		if($departID=="all") {
			$statement = $db->prepare("SELECT DISTINCT(name) FROM fieldvalue WHERE fieldname LIKE '%wifi%' ORDER BY name ASC");
			$statement->execute();
			foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $uN) {
				$uniqueName = $uN['name'];
				$statement2 = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$uniqueName' AND fieldname LIKE '%wifilogin%' ORDER BY date DESC LIMIT 1");
				$statement2->execute();
				$total[$i]["device"] = $uniqueName;
				foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$total[$i]["login"] = $row["value"];
					$total[$i]["department"] = $departmentsName[getDep($row["name"])];
				}
				$statement2 = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$uniqueName' AND fieldname LIKE '%wifipass%' ORDER BY date DESC LIMIT 1");
				$statement2->execute();
				foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$total[$i]["pass"] = $row["value"];
				}
				$i++;
			}
		} else {
			
			
			$statement = $db->prepare("SELECT DISTINCT(name) FROM fieldvalue WHERE fieldname LIKE '%wifi%' ORDER BY name ASC");
			$statement->execute();
			foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $uN) {
				$uniqueName = $uN['name'];
				$statement2 = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$uniqueName' AND fieldname LIKE '%wifilogin%' ORDER BY date DESC LIMIT 1");
				$statement2->execute();
				foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $row) {
					if($departID == getDep($row["name"])) {
						$total[$i]["device"] = $uniqueName;
						$total[$i]["login"] = $row["value"];
						$total[$i]["department"] = $departmentsName[getDep($row["name"])];
					}
				}
				$statement2 = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$uniqueName' AND fieldname LIKE '%wifipass%' ORDER BY date DESC LIMIT 1");
				$statement2->execute();
				foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $row) {
					if($departID == getDep($row["name"])) {
						$total[$i]["pass"] = $row["value"];
					}
				}
				$i++;
			}
		}
		$i=1;
		foreach($total as $t) {
			echo '<tr>';
			echo '<td>'.$i.'</td>';
			echo '<td><a href="device.php?id='.$t["device"].'">'.$t["device"].'</a></td>';
			echo '<td>'.$t["department"].'</td>';
			echo '<td>'.$t["login"].'</td>';
			echo '<td>'.$t["pass"].'</td>';
			echo '</tr>';
			$i++;
		}
		?>
	</tbody>
</table>