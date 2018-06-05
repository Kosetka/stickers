<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	if(isset($_POST["typeSend"])) {
		if(empty($_POST["name"]) || empty($_POST["tag"]) || strlen($_POST["tag"])<>2 || is_numeric($_POST["tag"])) {  
			$message = showMessage(1,' Wszystkie pola są wymagane'); 
		} else {
			$name = $_POST["name"];
			$tag = strtoupper($_POST["tag"]);
			$query = "SELECT * FROM types WHERE tag = :tag"; 
			$db = getDB();
			$statement = $db->prepare($query); 
			$statement->bindParam(':tag',$tag); 
			$statement->execute();  
			$count = $statement->rowCount();  
			if($count <= 0) {
				try {
					$statement = $db->prepare("INSERT INTO types(name, tag) VALUES(:name, :tag)");
					$statement->execute(array(
						"name" => $name,
						"tag" => $tag
					));
					
					$userID = getSingleValue("users","username",$_SESSION["username"],"id");
					$today = date("Y-m-d H:i:s");
					$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
					$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");
					$statement->execute(array(
						"uid" => $userID,
						"aid" => 4,
						"result" => "success",
						"date" => $today,
						"ip" => $ip,
						"did" => $tag,
						"department" => $departmentSelected
					));
					
					
					$message = showMessage(0,' Typ urządzenia został dodany.');
				} catch(PDOException $e) {
					echo $e->getMessage();
				}
			} else {
				$userID = getSingleValue("users","username",$_SESSION["username"],"id");
				$today = date("Y-m-d H:i:s");
				$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
				$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");
				$statement->execute(array(
					"uid" => $userID,
					"aid" => 4,
					"result" => "error",
					"date" => $today,
					"ip" => $ip,
					"did" => $tag,
					"department" => $departmentSelected
				));
				$message = showMessage(1,' Ten skrót jest już zajęty.');
			}
		}
	}
	if(isset($_GET['id'])) {
		$typeID = secure($_GET['id']);
		$ttag = $typeID;
		$query = "SELECT * FROM types WHERE tag = :tag"; 
		$db = getDB();
		$statement = $db->prepare($query); 
		$statement->bindParam(':tag',$typeID); 
		$statement->execute();
		$tID = $statement->fetch();
		$tID = $tID["id"];
		$count = $statement->rowCount(); 

		if($count > 0 || isset($_POST["editSend"]) || isset($_POST["fieldSend"])) {
			if(isset($_POST["fieldSend"])) {
				if(empty($_POST["name"])) {  
					$message = showMessage(1,' Uzupełnij wymagane pola.'); 
				} else {
					$name = $_POST["name"];
					$fname = $_POST["fname"];
					$req = isset($_POST["req"])?1:0;
					$type = $_POST["type"];
					$val = $_POST["val"];
					$query = "SELECT * FROM fields WHERE name = :name"; 
					$db = getDB();
					$statement = $db->prepare($query); 
					$statement->bindParam(':name',$fname); 
					$statement->execute();  
					$count = $statement->rowCount();  
					if($count > 0) {
						$message = showMessage(1,' Nazwa pola jest już zajęta.'); 
					} else {
						try {
							$statement = $db->prepare("INSERT INTO fields(type_id, name, title, required, field_type) VALUES(:type_id, :name, :title, :required, :field_type)");
							$statement->execute(array(
								"type_id" => $tID,
								"title" => $name,
								"name" => $fname,
								"required" => $req,
								"field_type" => $type
							));
							
							$values = explode(";", $val);
							$fid = $db->lastInsertId();
							foreach($values as $value) {
								if($value<>"") {
									$statement = $db->prepare("INSERT INTO fieldselect(fid, name) VALUES(:fid, :name)");
									$statement->execute(array(
										"fid" => $fid,
										"name" => $value
									));
								}
							}
							
							$userID = getSingleValue("users","username",$_SESSION["username"],"id");
							$today = date("Y-m-d H:i:s");
							$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
							$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");
							$statement->execute(array(
								"uid" => $userID,
								"aid" => 6,
								"result" => "success",
								"date" => $today,
								"ip" => $ip,
								"did" => $fid,
								"department" => $departmentSelected
							));
							
							$message = showMessage(0,' Pole zostało dodane.');
						} catch(PDOException $e) {
							echo $e->getMessage();
						}
					}
					
				}
			} elseif(isset($_POST["editSend"])) {
				if(empty($_POST["name"])) {  
					$message = showMessage(1,' Uzupełnij wymagane pola.');
				} else {
					$title = $_POST["name"];
					$req = isset($_POST["req"])?1:0;
					$type = $_POST["type"];
					$val = $_POST["val"];
					try {
						$db = getDB();
						$statement = $db->prepare("UPDATE fields SET title = :title, required = :required, field_type = :field_type WHERE id = :id");
						$statement->execute(array(
							"title" => $title,
							"required" => $req,
							"id" => $typeID,
							"field_type" => $type
						));
						$statement = $db->prepare("DELETE FROM fieldselect WHERE fid = :fid");
						$statement->execute(array(
							"fid" => $typeID
						));
						$values = explode(";", $val);
						foreach($values as $value) {
							if($value<>"") {
								$statement = $db->prepare("INSERT INTO fieldselect(fid, name) VALUES(:fid, :name)");
								$statement->execute(array(
									"fid" => $typeID,
									"name" => $value
								));
							}
						}
						$userID = getSingleValue("users","username",$_SESSION["username"],"id");
						$today = date("Y-m-d H:i:s");
						$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
						$statement = $db->prepare("INSERT INTO logs(uid, aid, result, date, ip, did, department) VALUES(:uid, :aid, :result, :date, :ip, :did, :department)");
						$statement->execute(array(
							"uid" => $userID,
							"aid" => 7,
							"result" => "success",
							"date" => $today,
							"ip" => $ip,
							"did" => $fid,
							"department" => $departmentSelected
						));
						$message = showMessage(0,' Edycja pomyślna.');
					} catch(PDOException $e) {
						echo $e->getMessage();
					}
				}
			}
		} 
		if($count==0) {
			$query = "SELECT * FROM fields WHERE id = :id"; 
			$statement = $db->prepare($query); 
			$statement->bindParam(':id',$typeID); 
			$statement->execute();
			$count = $statement->rowCount();  
			if($count > 0) {
				$numeric = true;
				$ftag = getSingleValue("fields", "id", $typeID, "type_id");
				$tname = getSingleValue("types", "id", $ftag, "name");
				$ttag = getSingleValue("types", "id", $ftag, "tag");
				
				$fvalues = $statement->fetch();
				
			} else {
				//$message = showMessage(1,'Taki typ urządzeń nie istnieje.');
				//$typeID = null;
			}
		}
	}
	
	
?>  
<!DOCTYPE html>
<html lang="pl">
	<head>
		<title>Typy urządzeń</title>
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
			<div class="col-sm-6 text-center">
			
			
			
			
				
					<?php
						if(!isset($typeID)) {
					?>
					<h2 style="margin-bottom: 50px">Dodawanie nowego typu urządzenia</h2>
					<?php  
						if(isset($message)) {  
							echo $message;  
						}  
					?> 
					<form class="form-horizontal" action="" method="POST">
						<div class="form-group">
							<label class="control-label col-sm-offset-1 col-sm-4" for="name">Pełna nazwa:</label>
							<div class="col-sm-5">          
								<input type="text" class="form-control" id="name" placeholder="" name="name" maxlength="32" required>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-offset-1 col-sm-4" for="tag">Skrót (2 znaki):</label>
							<div class="col-sm-2">          
								<input type="text" class="form-control" id="tag" placeholder="" name="tag" maxlength="2" style="text-transform: uppercase" required>
							</div>
						</div>
						<div class="form-group">        
							<div class="col-sm-offset-5 col-sm-2">
								<input type="submit" name="typeSend" class="btn btn-primary" value="Dodaj" />
							</div>
						</div>
					</form>
					<?php
						} else {
							if(isset($numeric)) {
								echo '<h2 style="margin-bottom: 50px">Edycja pola</h2>';
							} else {
								echo '<h2 style="margin-bottom: 50px">Dodawanie pól: '.getSingleValue("types", "tag", $typeID, "name").'</h2>';
							}
							if(isset($message)) {  
								echo $message;  
							}  
					?>
					<form class="form-horizontal" action="" method="POST">
						<div class="form-group">
							<label class="control-label col-sm-offset-1 col-sm-4" for="name">Nazwa:</label>
							<div class="col-sm-5">          
								<input type="text" class="form-control" id="name" placeholder="" name="name" maxlength="32" required 
											 <?php echo 'value="'.(isset($numeric)?$fvalues['title']:'').'"'?>>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-offset-1 col-sm-4" for="fname">Nazwa pola:</label>
							<div class="col-sm-5">          
								<input type="text" class="form-control" id="fname" placeholder="" name="fname" maxlength="32" required
											 <?php echo 'value="'.(isset($numeric)?$fvalues['name'].'" disabled':'"')?>>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-offset-1 col-sm-4" for="req">Wymagane:</label>
							<div class="col-sm-5" style="text-align: left;">          
								<input type="checkbox" name="req" id="req" <?php if(isset($numeric)) if($fvalues['required']==1) echo 'checked';?>>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-offset-1 col-sm-4" for="type">Typ:</label>
							<div class="col-sm-5" style="text-align: left;">          
								<select class="form-control" name="type" id="type">
									<option value="0" <?php echo (isset($numeric)?($fvalues['field_type']==0)?'selected':'':'')?>>Input</option>
									<option value="1" <?php echo (isset($numeric)?($fvalues['field_type']==1)?'selected':'':'')?>>Select</option>
									<option value="2" <?php echo (isset($numeric)?($fvalues['field_type']==2)?'selected':'':'')?>>Textarea</option>
								</select>
							</div>
						</div>
						<div class="form-group" id="valHide" 
						<?php 
							if(isset($numeric)) { 
								if($fvalues['field_type']<>1) 
									echo 'style="display: none;"';
							} else {
								echo 'style="display: none;"';
							}
						?>>
							<label class="control-label col-sm-offset-1 col-sm-4" for="val">Wartości:</label>
							<div class="col-sm-5">          
								
									<?php
										if(isset($numeric)) {
											$result = '';
											if($fvalues['field_type']==1) {
												$statement = $db->prepare("SELECT * FROM fieldselect WHERE fid = :fid");
												$statement->bindParam(':fid',$typeID); 
												$statement->execute();
												foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
													$result .= $row['name'].';';
												}
											}
										}
									?>
								<textarea class="form-control" name="val" id="val" style="min-width: 100%; max-width: 100%; min-height: 60px;"><?php if(isset($numeric)) echo $result; ?></textarea>
							</div>
						</div>
						
						<div class="form-group">        
							<div class="col-sm-offset-5 col-sm-2">
								<input type="submit" <?php if(isset($numeric)) echo 'name="editSend"'; else echo 'name="fieldSend"'; ?> class="btn btn-primary" value="Zapisz" />
							</div>
						</div>
					</form>	
					<?php
						}
					?>
			</div>
			
			
			
			
			
			<div class="col-sm-6">
			
				<?php
					if(isset($typeID)) {
				?>
				<h2 style="margin-bottom: 50px" class="text-center">Szczegóły: <?php echo (isset($numeric)?$tname:getSingleValue('types', 'tag', $typeID, 'name'));?></h2>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Nazwa</th>
							<th>Typ</th>
							<th>Wymagane</th>
							<th>Akcja</th>
						</tr>
					</thead>
					<tbody>
						<?php
							try {
								$type_id = getSingleValue('types', 'tag', $ttag, 'id');
								$db = getDB();
								$statement = $db->prepare("SELECT * FROM fields WHERE type_id = :type_id");
								$statement->bindParam(':type_id',$type_id); 
								$statement->execute();
								$selVal = ["input", "select", "textarea"];
								foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
						?>
									<tr>
										<td><?php echo $row["title"]; ?></td>
										<td><?php echo $selVal[$row["field_type"]]; ?></td>
										<td><?php echo ($row["required"]==1?"TAK":"NIE"); ?></td>
										<td><a title="Edytuj" href="type.php?id=<?php echo $row['id']; ?>"><span class="glyphicon glyphicon-pencil" style="color: black; font-size: 2em"></span></a></td>
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
				
			
				<h2 style="margin-bottom: 50px" class="text-center">Typy urządzeń</h2>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Pełna nazwa</th>
							<th>Skrót</th>
							<th>Akcja</th>
						</tr>
					</thead>
					<tbody>
						<?php
							try {
								$db = getDB();
								$statement = $db->prepare("SELECT * FROM types");
								$statement->execute();
								foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
									?>
										<tr>
											<td><?php echo $row["name"]; ?></td>
											<td><?php echo $row["tag"]; ?></td>
											<td><a title="Szczegóły" href="type.php?id=<?php echo $row['tag']; ?>"><span class="glyphicon glyphicon-th-list" style="color: black; font-size: 2em"></span></a></td>
										</tr>
										
									<?php
								}
							} catch(PDOException $e) {
								echo $e->getMessage();
							}
						?>
					</tbody>
				</table>
				
				
			</div>

		</div><!-- /.container -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				$('#type').change(function(){
					if(this.value == 1)
						$('#valHide').fadeIn('fast');
					else
						$('#valHide').fadeOut('fast');

				});
			});
		</script>
	</body>
</html>