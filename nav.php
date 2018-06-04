<?php 
	defined('inc') or header('Location: index.php');
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
			
			
				<a class="navbar-brand" href="home.php">VC</a>
				<form class="searchRWD" action="device.php">
					<div class="input-group">
						<input type="text" style="text-transform: uppercase;" class="form-control" placeholder="Szukaj..." name="id">
						<div class="input-group-btn">
							<button class="btn btn-default" type="submit">
								<i class="glyphicon glyphicon-search"></i>
							</button>
						</div>
					</div>
				</form>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Dodaj
						<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="type.php">Typ urządzeń</a></li>
						<li><a href="department.php">Oddział</a></li>
						<li><a href="privphone.php">Prywatny telefon</a></li>
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
						<li><a href="count.php">Ilość sprzętu</a></li>
						<li><a href="sip.php">Numery kolejek</a></li>
						<li><a href="routers.php">Dane sieci wifi</a></li>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Funkcje
						<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="test.php">Test</a></li>
						<li><a href="backup.php">Kopia bazy</a></li>
						<li><a href="mikrotik.php">Dostępy MikroTik</a></li>
						<li><a href="employees.php">Zarządzanie kontami</a></li>
						<li><a href="search.php">Wyszukiwanie zaawansownane</a></li>
						<li><a href="scanlogs.php">Logi skanowania</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo getDepartment($departmentSelected); ?>
						<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="depchange.php">Zmień oddział</a></li>
					</ul>
				</li>
				<li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo getSingleValue("users","username",$_SESSION["username"],"name"); ?></a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Wyloguj</a></li>
			</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>
