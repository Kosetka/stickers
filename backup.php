<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	defined('inc') or header('Location: index.php');

	if(!checkAccess(9)) redirect('deny.php');


	if(isset($_POST["doBackup"])) {
		//$file = "stickers_".date("d.m.Y_H.i").".sql";
		//$SQLDump   = '/*'.chr(13);
		//$SQLDump  .= '# Kopia bazy stickers: '.(BdServer_Base).chr(13); 
		//$SQLDump  .= '# Wygenerowano: '.date("d.m.Y H:i:s").chr(13);
		//$SQLDump  .= '*/'.chr(13); 
		//try { 
		//	$db = getDB();
		//	$db->exec("set names utf8");
		//	foreach ($db->query('SHOW TABLES;') as $Table) {
		//		$TableStructure = $db->query('SHOW CREATE TABLE `'.$Table[0].'`')->fetch(); 
		//		$SQLDump .= '/*Struktura tabeli `'.$Table[0].' */ '.chr(13).chr(13); 
		//		$SQLDump .= $TableStructure[1].';'.chr(13);  
		//		$SQLDump .= chr(13).'/*Rekordy tabeli `'.$Table[0].' */ '.chr(13).chr(13); 
		//		foreach($db->query('SELECT * FROM `'.$Table[0].'`;')->fetchAll(PDO::FETCH_ASSOC) as $SelectRow) 
		//			$SQLDump .= sprintf('INSERT INTO `'.$Table[0].'` (%s) VALUES (%s);', implode(', ', array_map(function($n){ return '`'.$n.'`';} ,array_keys($SelectRow))), implode(', ',array_map(array($db, 'quote'), $SelectRow))).chr(13); 
		//		$SQLDump .= chr(13).chr(13); 
		//	}
		//} catch (PDOException $e) { 
		//	exit(sprintf("Błędy: %s", ($e->getMessage()))); 
		//}
		//file_put_contents($file, $SQLDump);
		//header("Content-Description: File Transfer"); 
		//header("Content-Type: application/octet-stream"); 
		//header("Content-Disposition: attachment; filename='" . basename($file) . "'"); 
		//readfile ($file);
		//fclose($file);
		//unlink($file);
		$tables = array();
		backup_tables($tables);
	}

?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Strona główna</title>
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
			<div class="col-sm-12 text-center">
				<h2>Kopia bazy danych:</h2>
				<form class="form-horizontal" action="" method="POST">
					<div class="form-group">        
						<div class="col-sm-offset-5 col-sm-2">
							<input type="submit" name="doBackup" class="btn btn-primary" value="Pobierz kopię bazy" />
						</div>
					</div>
				</form>
			</div>
		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>