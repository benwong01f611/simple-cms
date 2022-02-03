<?php
	function connectDB($sql){
		try{
			$dbsettingsfile = fopen($_SERVER["DOCUMENT_ROOT"] . "/data/database.json", "r");
			$dbsettings = fread($dbsettingsfile, filesize($_SERVER["DOCUMENT_ROOT"] . "/data/database.json"));
			$db = json_decode($dbsettings, true);
			fclose($dbsettingsfile);
			$dst = $db["dbtype"] . ":dbname=" . $db["dbname"] . ";host=" . $db["dbip"] . ";port=" . $db["dbport"] . ";charset=UTF8";
			$conn = new PDO(
				$dst,
				$db["dbuser"],
				$db["dbpw"]
			);
			$prep = $conn->prepare($sql);
			$prep->execute();
			$result = $prep->fetchAll();
			unset($prep);
			unset($conn);
			unset($exec_result);
			return $result;
		}
		catch(Throwable $e){
			return $e->getMessage();
		}
	}
	function connectDBmultiple($sqls){
		try{
			$dbsettingsfile = fopen($_SERVER["DOCUMENT_ROOT"] . "/data/database.json", "r");
			$dbsettings = fread($dbsettingsfile, filesize($_SERVER["DOCUMENT_ROOT"] . "/data/database.json"));
			$db = json_decode($dbsettings, true);
			fclose($dbsettingsfile);
			$dst = $db["dbtype"] . ":dbname=" . $db["dbname"] . ";host=" . $db["dbip"] . ";port=" . $db["dbport"] . ";charset=UTF8";
			$conn = new PDO(
				$dst,
				$db["dbuser"],
				$db["dbpw"]
			);

			$results = [];
			$num_sql = sizeof($sqls);
			foreach ($sqls as $sql){
				$prep = $conn->prepare($sql);
				$exec_result = $prep->execute();
				$result = $exec_result->fetchAll();
				array_push($results, $result);
			}
			unset($sql);
			unset($result);
			unset($num_sql);
			unset($conn);
			return $results;
		}
		catch(Throwable $e){
			return $e->getMessage();
		}
	}
?>
