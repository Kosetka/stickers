<?php
    $statuses = [1=>"Sprawny", 2=>"Zepsuty", 3=>"Serwis"];

    if(testVersion=='false') {
        if(!isset($_SESSION["department"])) {
            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
            $departmentSelected = getSingleValue("firewall", "ip", $ip, "id");
            if($departmentSelected=="") {
                $departmentSelected = 16;
            }
            $_SESSION["department"] = $departmentSelected;
        } else {
            $departmentSelected = $_SESSION["department"];
        }
    } else {
        if(!isset($_SESSION["department"])) {
            $departmentSelected = 16;
        } else {
            $departmentSelected = $_SESSION["department"];
        }
    }

    function getDB() {
        $dbhost=DB_SERVER;
        $dbuser=DB_USERNAME;
        $dbpass=DB_PASSWORD;
        $dbname=DB_DATABASE;
        try {
            $dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
            $dbConnection->exec("set names utf8");
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbConnection;
        }
        catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    function loggedin() {
        if(isset($_SESSION["username"])) {  
            return true; 
        } else {  
            return false;
        }  
    }

    function logout() {
        session_destroy();
        unset($_SESSION['username']);
        return true;
    }

    function redirect($url) {
        header("Location: $url");
    }

    function checkFirewall() {
        $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
        $db = getDB();
        $query = "SELECT * FROM firewall WHERE ip = :ip";  
        $statement = $db->prepare($query); 
        $statement->bindParam(':ip',$ip); 
        $statement->execute();  
        $count = $statement->rowCount();  
        if($count > 0) { 
            return true;
            //echo 'Twoje ip: '.$ip; 
        } else {
            if(testVersion==true) {
                return true;	
            } else {
                return false; //false
            }
        }
    }

    function secure($value) {
        return stripslashes($value);
    }

    function getDepartment($id) {
        $db = getDB();
        $query = "SELECT * FROM firewall WHERE id = :id";  
        $statement = $db->prepare($query); 
        $statement->bindParam(':id',$id); 
        $statement->execute();  
        $count = $statement->rowCount();  
        if($count > 0) {
            $result = $statement->fetch(); 
            return $result['name'];
        } else {  
            return true;
            //die('Zła lokalizacja.'); 
        }
    }

    function deviceExists($n, $like = false) {
        $db = getDB();
        $query = "SELECT * FROM scan WHERE name LIKE :name";  
        $statement = $db->prepare($query);
        if($like)
            $n = "%".$n."%";
        $statement->bindParam(':name',$n); 
        $statement->execute();  
        $count = $statement->rowCount(); 
        if($count > 0) {
            return true;
        } else {  
            return false; 
        }
    }	
    function tagExists($n) {
        $db = getDB();
        $query = "SELECT * FROM types WHERE tag LIKE :tag";  
        $statement = $db->prepare($query);
        $statement->bindParam(':tag',$n); 
        $statement->execute();  
        $count = $statement->rowCount(); 
        if($count > 0) {
            return true;
        } else {  
            return false; 
        }
    }
    function statusExists($n) {
        $db = getDB();
        $query = "SELECT * FROM status WHERE name LIKE :name";  
        $statement = $db->prepare($query);
        $statement->bindParam(':name',$n); 
        $statement->execute();  
        $count = $statement->rowCount(); 
        if($count > 0) {
            return true;
        } else {  
            return false; 
        }
    }

    function showMessage($id, $txt) { 
        switch ($id) {
            case 0:
                $alert = "success";
                $glyph = "success";
                break;
            case 1:
                $alert = "danger";
                $glyph = "remove";
                break;
        }
        $result = '<div class="alert alert-'.$alert.'"><span class="glyphicon glyphicon-'.$glyph.'"></span>'.$txt.'</div>';
        return $result;
    }

    function getSingleValue($tableName, $prop, $value, $columnName) {
        $db = getDB();
        $q = $db->query("SELECT `$columnName` FROM `$tableName` WHERE $prop='".$value."'");
        $f = $q->fetch();
        $result = $f[$columnName];
        return $result;
    }
    function getFieldValue($dev, $fname) {
        $db = getDB();
        $q = $db->query("SELECT * FROM fieldvalue WHERE name='$dev' AND fieldname='$fname' ORDER BY date DESC LIMIT 1");
        $f = $q->fetch();
        $result = $f["value"];
        return $result;
    }
	function checkAccess($req) {
		$lvl = getSingleValue('users','username',$_SESSION["username"],'access');
		if($req>$lvl)
			return false;
		else 
			return true;
	}
?>
