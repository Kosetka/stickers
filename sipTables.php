<?php 
	defined('inc') or header('Location: index.php');
	$db = getDB();

	if(isset($_POST['depSend'])) {
		$departID = $_POST['department'];
		if($departID<>"all") $departID = getSingleValue("firewall","tag",$departID,"id");
	} else { $departID = null; }

	function getDep($dname) {
		$db = getDB();
		$q = $db->query("SELECT * FROM scan WHERE name='$dname' ORDER BY date DESC LIMIT 1");
		$f = $q->fetch();
		$result = $f["department"];
		return $result;
	}
if($departID<>null) {
	$departmentsName = [];
	$statement = $db->prepare("SELECT * FROM firewall");
	$statement->execute();
	foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $dN) {
		$departmentsName[$dN["id"]] = $dN["name"];
	}
?>
<div class="table-responsive">
<table class="table table-bordered">
	<thead>
		<tr>
			<th>lp.</th>
			<th>Urządzenie</th>
			<th>Oddział</th>
			<th>VNCS</th>
			<th>Odsłuch</th>
			<th>Zakres z</th>
		</tr>
	</thead>
	<tbody>
<?php
	$i = 1;
	$total = [];
	if($departID=="all") {
		$statement = $db->prepare("SELECT DISTINCT(name) FROM fieldvalue WHERE fieldname LIKE '%vncs%' ORDER BY name ASC");
		$statement->execute();
		foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $uN) {
			$uniqueName = $uN['name'];
			$statement2 = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$uniqueName' AND fieldname LIKE '%vncs%' ORDER BY date DESC LIMIT 1");
			$statement2->execute();
			foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$val = $row["value"];
				$q = $db->prepare("SELECT * FROM firewall WHERE range_from <= '$val' AND listening_to >= '$val'");
				$q->execute();
				$f = $q->fetch();
				$result = $f["id"];

				$total[$i]["department"] = $departmentsName[$result];
				$total[$i]["device"] = $row["name"];
				$devType = substr($row["name"], 0, 2); 
				if(getDep($row["name"])<>"") {
					$total[$i]["place"] = $departmentsName[getDep($row["name"])];
				} else {
					$total[$i]["place"] = "Brak skanowania";
				}
				$total[$i]["vncs"] = $row["value"];
				if($total[$i]["vncs"]=="") {
					$total[$i]["department"] = "";
				}
				$q = $db->prepare("SELECT * FROM firewall WHERE listening_from <= '$val' AND listening_to >= '$val'");
				$q->execute();
				$f = $q->rowCount();
				if($f>0 AND $total[$i]["vncs"]<>"") 
					$result = "TAK";
				else
					$result = "";
				if($devType == "TB" OR $devType == "KA") {
					if($total[$i]["vncs"]<>"")
						$result = "TAK";
				}
				$total[$i]["listening"] = $result;
				$i++;
			}
		}
	} else {
		$statement = $db->prepare("SELECT DISTINCT(name) FROM fieldvalue WHERE fieldname LIKE '%vncs%' ORDER BY name ASC");
		$statement->execute();
		foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $uN) {
			$uniqueName = $uN['name'];
			$statement2 = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$uniqueName' AND fieldname LIKE '%vncs%' ORDER BY date DESC LIMIT 1");
			$statement2->execute();
			foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $row) {
				if($departID == getDep($row["name"])) {
					$val = $row["value"];
					if($val<>"") {
						$q = $db->prepare("SELECT * FROM firewall WHERE range_from <= '$val' AND listening_to >= '$val'");
						$q->execute();
						$f = $q->fetch();
						$result = $f["id"];
						$total[$i]["department"] = $departmentsName[$result];
						$total[$i]["device"] = $row["name"];
						$devType = substr($row["name"], 0, 2); 
						if(getDep($row["name"])<>"") {
							$total[$i]["place"] = $departmentsName[getDep($row["name"])];
						} else {
							$total[$i]["place"] = "Brak skanowania";
						}
						$total[$i]["vncs"] = $row["value"];

						$q = $db->prepare("SELECT * FROM firewall WHERE listening_from <= '$val' AND listening_to >= '$val'");
						$q->execute();
						$f = $q->rowCount();
						if($f>0) 
							$result = "TAK";
						else
							$result = "";
						if($devType == "TB" OR $devType == "KA") {
							if($total[$i]["vncs"]<>"")
								$result = "TAK";
						}
						$total[$i]["listening"] = $result;
						$i++;
					}
				}
			}
		}
	}
	$i = 1;
	usort($total, function($a, $b) {
		return $a['vncs'] <=> $b['vncs'];
	});
	foreach($total as $t) {
        if($t["vncs"]=="") {
            $class = "more";
        } else {
            if($t["place"] == "Brak skanowania") {
                $class = "equal";
            } elseif($t["place"]<>$t["department"]) {
                $class = "less";
            } else {
                $class = "";
            }
        }
        echo '<tr class="'.$class.'">';
        echo '<td>'.$i.'</td>';
        echo '<td style="text-transform: uppercase;"><a href="device.php?id='.$t["device"].'">'.$t["device"].'</a></td>';
        echo '<td>'.$t["place"].'</td>';
        echo '<td>'.$t["vncs"].'</td>';
        echo '<td>'.$t["listening"].'</td>';
        echo '<td>'.$t["department"].'</td>';
        echo '</tr>';
        $i++;
        
	}
?>
	</tbody>
</table>
</div>


<?php
	if($departID<>"all") {
		$taken = [];
		$i = 0;
		$statement = $db->prepare("SELECT DISTINCT(name) FROM fieldvalue WHERE fieldname LIKE '%vncs%' ORDER BY name ASC");
		$statement->execute();
		foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $uN) {
			$uniqueName = $uN['name'];
			$statement2 = $db->prepare("SELECT * FROM fieldvalue WHERE name = '$uniqueName' AND fieldname LIKE '%vncs%' ORDER BY date DESC LIMIT 1");
			$statement2->execute();
			foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$val = $row["value"];
					
				$q = $db->prepare("SELECT * FROM firewall WHERE range_from <= '$val' AND listening_to >= '$val'");
				$q->execute();
				$f = $q->fetch();
				$result = $f["id"];
				echo $result."->".$val."->";
				if(getDep($row["name"])<>$result) {
					$taken[$i]["wrong"] = true;
				} else {
					$taken[$i]["wrong"] = false;
				}
				echo $row["name"]."->".getDep($row["name"])."<br>";
				$taken[$i]["vncs"] = $row["value"];
				$q = $db->prepare("SELECT * FROM firewall WHERE listening_from <= '$val' AND listening_to >= '$val'");
				$q->execute();
				$f = $q->rowCount();
				if($f>0) 
					$result = "TAK";
				else
					$result = "";
				$taken[$i]["listening"] = $result;

				$i++;
			}
		}
		$query = "SELECT * FROM firewall WHERE id = :id";  
		$statement = $db->prepare($query); 
		$statement->bindParam(':id',$departID); 
		$statement->execute();  
		$result = $statement->fetch(); 
		$n = $result["range_from"];
		$to = $result["listening_to"];
		$lf = $result["listening_from"];
		$ch = ($to - $n)/20; //może robić problem z przecinkami
		//print_r($taken);
		
		echo '<h2 class="text-center">Lista kolejek:</h2>';
		$instan = getSingleValue("firewall","id",$departID,"instance");
		if($instan==1) {
			$instan = "";
		}
		echo '<h4>Domena: sip.vc'.$instan.'.e-pbx.pl</h4>';
		echo '<h4>Hasło: '.getSingleValue("firewall","id",$departID,"password").'</h4>';
		echo '<div class="table-responsive"><table class="table table-bordered">';
		for($j=0;$j<=$ch;$j++) {
			echo '<tr>';
			for($i=0;$i<=19;$i++) {
				if($n > $to) {
					break;
				}
				foreach($taken as $t) {
					$class = "";
					if($n >= $lf){
						$class = "takenListening";
						if($t["vncs"]==$n) {
							if($t["wrong"] == true) {
								$class = "takenListeningWrong";
							} elseif($t["wrong"] == false) {
								$class = "takenListeningFine";
							}
							break;
						} 
					} else {
						if($t["vncs"]==$n) {
							if($t["wrong"] == true) {
								$class = "takenWrong";
							} else {
								$class = "takenFine";
							}
							break;
						} 
					}
				}
				echo '<td class="'.$class.'">'.$n++.'</td>';
			}
			echo '</tr>';
		}
		echo '</table></div>';
	}
}
?>