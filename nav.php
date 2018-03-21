<?php 
	defined('inc') or header('Location: index.php');
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="home.php">VC</a>
		</div>
		<ul class="nav navbar-nav">
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">Dodaj
					<span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="add.php">Urządzenie</a></li>
					<li><a href="type.php">Typ urządzeń</a></li>
					<li><a href="department.php">Oddział</a></li>
				</ul>
			</li>
			<li><a href="scan.php">Skanowanie</a></li>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">Raporty
					<span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="report.php">Ewidencji</a></li>
					<li><a href="details.php">Szczegółowy</a></li>
					<li><a href="rotate.php">Rotacji sprzętu</a></li>
				</ul>
			</li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<form class="navbar-form navbar-left" action="device.php">
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Szukaj..." name="id">
					<div class="input-group-btn">
						<button class="btn btn-default" type="submit">
							<i class="glyphicon glyphicon-search"></i>
						</button>
					</div>
				</div>
			</form>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo getDepartment($departmentSelected); ?>
					<span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="depchange.php">Zmień oddział</a></li>
				</ul>
			</li>
			<li><p class="navbar-text glyphicon glyphicon-user"> <?php echo $_SESSION["username"]; ?></p></li>
			<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Wyloguj</a></li>
		</ul>
	</div>
</nav>