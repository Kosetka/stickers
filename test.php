<?php  
	include("config.php"); 
	if(!checkFirewall()) redirect('error.php');
	if(!loggedin()) redirect('index.php');

	if(!checkAccess(9)) redirect('deny.php');
?>  
<!DOCTYPE html>
<html lang="pl">
    <head>
        <title>Test</title>
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
                <p class="lead"><?php echo $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']; ?></p>
                <h2>Oddział:</h2>
                <p class="lead"><?php echo $dNameShow = getSingleValue("firewall", "id", $departmentSelected, "name"); ?></p>
                <?php 
                echo '<pre>';
                print_r($_SERVER);
                echo '</pre>';
                ?>

            </div>


        </div><!-- /.container -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
</html>