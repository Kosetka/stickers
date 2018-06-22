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
                $glyph = "ok";
                break;
            case 1:
                $alert = "danger";
                $glyph = "remove";
                break;
            case 2:
                $alert = "info";
				$glyph = "info-sign";
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

	function backup_tables($tables) {
		$DBH = getDB();
		$DBH->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL );
		//Script Variables
		$compression = false;
		$BACKUP_PATH = "";
		$nowtimename = "stickers_".date("d.m.Y_H.i");
		//create/open files
		if ($compression) {
			//$file = "stickers_".date("d.m.Y_H.i").".sql";
			$zp = gzopen($BACKUP_PATH.$nowtimename.'.sql.gz', "a9");
		} else {
			$handle = fopen($BACKUP_PATH.$nowtimename.'.sql','a+');
			$file = $nowtimename.'.sql';
		}
		//array of all database field types which just take numbers 
		$numtypes=array('tinyint','smallint','mediumint','int','bigint','float','double','decimal','real');
		//get all of the tables
		if(empty($tables)) {
			$pstm1 = $DBH->query('SHOW TABLES');
			while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
				$tables[] = $row[0];
			}
		} else {
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		//cycle through the table(s)
		foreach($tables as $table) {
			$result = $DBH->query("SELECT * FROM $table");
			$num_fields = $result->columnCount();
			$num_rows = $result->rowCount();
			$return="";
			//uncomment below if you want 'DROP TABLE IF EXISTS' displayed
			//$return.= 'DROP TABLE IF EXISTS `'.$table.'`;'; 
			//table structure
			$pstm2 = $DBH->query("SHOW CREATE TABLE $table");
			$row2 = $pstm2->fetch(PDO::FETCH_NUM);
			$ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
			$return.= "\n\n".$ifnotexists.";\n\n";
			if ($compression) {
				gzwrite($zp, $return);
			} else {
				fwrite($handle,$return);
			}
			$return = "";
			//insert values
			if ($num_rows){
				$return= 'INSERT INTO `'."$table"."` (";
				$pstm3 = $DBH->query("SHOW COLUMNS FROM $table");
				$count = 0;
				$type = array();
				while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) {
					if (stripos($rows[1], '(')) {$type[$table][] = stristr($rows[1], '(', true);
												} else $type[$table][] = $rows[1];
					$return.= "`".$rows[0]."`";
					$count++;
					if ($count < ($pstm3->rowCount())) {
						$return.= ", ";
					}
				}
				$return.= ")".' VALUES';
				if ($compression) {
					gzwrite($zp, $return);
				} else {
					fwrite($handle,$return);
				}
				$return = "";
			}
			$count =0;
			while($row = $result->fetch(PDO::FETCH_NUM)) {
				$return= "\n\t(";

				for($j=0; $j<$num_fields; $j++) {

					//$row[$j] = preg_replace("\n","\\n",$row[$j]);


					if (isset($row[$j])) {

						//if number, take away "". else leave as string
						if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j]))) $return.= $row[$j] ; else $return.= $DBH->quote($row[$j]); 

					} else {
						$return.= 'NULL';
					}
					if ($j<($num_fields-1)) {
						$return.= ',';
					}
				}
				$count++;
				if ($count < ($result->rowCount())) {
					$return.= "),";
				} else {
					$return.= ");";
				}
				if ($compression) {
					gzwrite($zp, $return);
				} else {
					fwrite($handle,$return);
				}
				$return = "";
			}
			$return="\n\n--------------------------------------------------\n\n";
			if ($compression) {
				gzwrite($zp, $return);
			} else {
				fwrite($handle,$return);
			}
			$return = "";
		}
		$error1= $pstm2->errorInfo();
		$error2= $pstm3->errorInfo();
		$error3= $result->errorInfo();
		echo $error1[2];
		echo $error2[2];
		echo $error3[2];
		if ($compression) {
			gzclose($zp);
		} else {
			fclose($handle);
		}
		echo '**'.$handle;
		if(!$handle){ // file does not exist
			die('file not found');
		} else {
			redirect($file);
		}
	}
?>
