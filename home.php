<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

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

		<div class="starter-template">
			<h1>Twoje IP:</h1>
			<p class="lead"><?php echo $ip = $_SERVER['REMOTE_ADDR']; ?></p>
			<h2>Oddział:</h2>
			<p class="lead"><?php echo $dNameShow = getSingleValue("firewall", "id", $departmentSelected, "name"); ?></p>
		</div>
		
		<?php
		$ccom = "0";
		$rname = "pc35";
		?>
		<table>
		<tr>
			<td>
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#<?php echo $rname;?>">
					<span class="glyphicon glyphicon-comment"></span><?php echo $ccom; ?>
				</button>
			</td>
			
		</tr>
			
		</table>
		<!-- Modal -->
		<div class="modal fade bd-example-modal-lg" id="<?php echo $rname;?>" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title" id="<?php echo 'ml'.$rname;?>">Komentarze</h3>
					</div>
					<div class="modal-body">
						...
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
					</div>
				</div>
			</div>
		</div>
		
		
	</div><!-- /.container -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>