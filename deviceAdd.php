<?php 
	defined('inc') or header('Location: index.php');
	$db = getDB();
?>
<h2>Urządzenie: <?php echo $deviceID; ?></h2>

<div class="col-sm-12">
<?php 
	if(isset($message)) {  
		echo $message;  
	}  
?>
	<form class="form-horizontal" action="" method="POST">
<?php
	$type_id = getSingleValue("types","tag",$gID,"id");
	$statement = $db->prepare("SELECT * FROM fields WHERE type_id = :type_id");
	$statement->bindParam(':type_id',$type_id); 
	$statement->execute();
	foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
		?>
		<div class="form-group">
			<label class="control-label col-sm-3" for="<?php echo $row['name']?>"><?php echo $row['title'];?>:</label>
			<div class="col-sm-9" style="text-align: left;">
			<?php
				$result = getFieldValue($deviceID,$row['name']);
				switch ($row['field_type']) {
					case 0:	
                        $testUpper = $row['name'];
                        if(strpos($testUpper, 'pass') !== false || strpos($testUpper, 'log') !== false || strpos($testUpper, 'LOG') !== false) {
                            $testUpper = '';
                        } else {
							$testUpper = 'text-transform: uppercase;';
                        }
					?>
                <input type="text" class="form-control" style="<?php echo $testUpper; ?>" id="<?php echo $row['name']?>" value="<?php echo $result; ?>" name="<?php echo $row['name']?>"<?php echo $row['required']==1?' required':''; ?>>
					<?php
						break;
					case 1:
						$fid = $row['id'];
					?>
						<select class="form-control" name="<?php echo $row['name']?>" id="<?php echo $row['name']?>">
						<?php
							$statement2 = $db->prepare("SELECT * FROM fieldselect WHERE fid = :fid");
							$statement2->bindParam(':fid',$fid); 
							$statement2->execute();
							foreach ($statement2->fetchAll(PDO::FETCH_ASSOC) as $value) {
								if($result==$value["name"]) 
									echo '<option value="'.$value["name"].'" selected>'.$value["name"].'</option>';
								else
									echo '<option value="'.$value["name"].'">'.$value["name"].'</option>';

							}
						?>
						</select>
					<?php
						break;
					case 2:
					?>
						<textarea class="form-control" name="<?php echo $row['name']?>" id="<?php echo $row['name']?>" <?php echo $row['required']==1?' required':''; ?> style="min-width: 100%; max-width: 100%; min-height: 60px;"><?php echo $result; ?></textarea>
					<?php
						break;
				}
				?>
				
			</div>
		</div>
		<?php
	}
?>
		<div class="form-group">        
			<div class="col-sm-offset-5 col-sm-2">
				<input type="submit" name="editSend" class="btn btn-primary" value="Zapisz" />
			</div>
		</div>
	</form>
	<h2>Zmiana statusu:</h2>
	<form class="form-horizontal" action="" method="POST">
		<div class="form-group"> 
			<label class="control-label col-sm-3" for="status">Status:</label>
			<div class="col-sm-9" style="text-align: left;">
				<select class="form-control" name="status" id="status">
					<?php
						$statement2 = $db->prepare("SELECT * FROM status WHERE name = :name ORDER BY date DESC LIMIT 1");
						$statement2->bindParam(':name',$deviceID); 
						$statement2->execute();
						$f = $statement2->fetch();
						$result = $f['status'];
						
						$i = 1;
						foreach($statuses as $status) {
							if($result==$i) 
								echo '<option value="'.$i.'" selected>'.$status.'</option>';
							else
								echo '<option value="'.$i.'">'.$status.'</option>';
							$i++;
						}

					
					?>
				</select>
			</div>
		</div>
		<div class="form-group">        
			<div class="col-sm-offset-5 col-sm-2">
				<input type="submit" name="statusSend" class="btn btn-primary" value="Zapisz" />
			</div>
		</div>
	</form>
</div>


