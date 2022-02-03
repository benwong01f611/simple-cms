<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
	if(!file_exists($_SERVER["DOCUMENT_ROOT"] . "/data/ok") && isset($_POST["dbip"]) && isset($_POST["dbuser"]) && isset($_POST["dbpw"]) && isset($_POST["dbname"]) && isset($_POST["dbport"]) && isset($_POST["dbtype"])){
		$db_info = [
			"dbip"		=> 	$_POST["dbip"],
			"dbuser"	=> 	$_POST["dbuser"],
			"dbpw"		=> 	$_POST["dbpw"],
			"dbname"	=> 	$_POST["dbname"],
			"dbport"	=> 	$_POST["dbport"],
			"dbtype"	=> 	$_POST["dbtype"]
		];
		
		try{
			$dst = $db_info["dbtype"] . ":host=" . $db_info["dbip"] . ";dbname=" . $db_info["dbname"] . ";port=" . $db_info["dbport"] . ";charset=UTF8";
			
			$conn = new PDO(
				$dst,
				$db_info["dbuser"],
				$db_info["dbpw"]
			);
			// Content
			$prep = $conn->prepare("CREATE TABLE content(
				id INT unsigned AUTO_INCREMENT,
				revid INT unsigned,
				createDate DATETIME,
				lastmodifyDate DATETIME,
				deleteDate DATETIME,
				alias VARCHAR(255),
				title TEXT,
				body LONGTEXT,
				templateid INT unsigned,
				published BIT(1),
				tags LONGTEXT,
				hidden BIT(1),
				PRIMARY KEY (id)
			);");
			$prep->execute();
			
			// Content tags
			$prep = $conn->prepare("CREATE TABLE tags(
				id INT unsigned AUTO_INCREMENT,
				name VARCHAR(255),
				PRIMARY KEY (id)
			);");
			$prep->execute();

			// User
			$prep = $conn->prepare("CREATE TABLE user(
				createDate DATETIME,
				lastLogin DATETIME,
				enabled BIT(1),
				username VARCHAR(255),
				pw MEDIUMTEXT,
				PRIMARY KEY (username)
			);");
			$prep->execute();
			
			// User permission
			$prep = $conn->prepare("CREATE TABLE userPermission(
				username VARCHAR(255),
				page INT unsigned,
				template INT unsigned,
				account INT unsigned,
				site INT unsigned,
				PRIMARY KEY (username)
			);");
			$prep->execute();

			// Page history
			$prep = $conn->prepare("CREATE TABLE pagehist (
				id INT unsigned,
				revid INT unsigned,
				createDate DATETIME,
				lastmodifyDate DATETIME,
				deleteDate DATETIME,
				alias VARCHAR(255),
				title TEXT,
				body LONGTEXT,
				templateid INT unsigned,
				published BIT(1),
				tags LONGTEXT,
				hidden BIT(1),
				CONSTRAINT page PRIMARY KEY (id, revid)
			);");
			$prep->execute();

			// Page template
			$prep = $conn->prepare("CREATE TABLE pageTemplate (
				id INT unsigned,
				revid INT unsigned,
				createDate DATETIME,
				lastmodifyDate DATETIME,
				deleteDate DATETIME,
				name VARCHAR(255),
				body LONGTEXT,
				PRIMARY KEY (id)
			);");
			$prep->execute();
			
			// Page template history
			$prep = $conn->prepare("CREATE TABLE pageTemplateHist (
				id INT unsigned,
				revid INT unsigned,
				createDate DATETIME,
				lastmodifyDate DATETIME,
				deleteDate DATETIME,
				name VARCHAR(255),
				body LONGTEXT,
				CONSTRAINT template PRIMARY KEY (id, revid)
			);");
			$prep->execute();

			// Default template
			require_once($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
			$defaultTemplate = str_replace("\"", "\\\"", getTemplate("default_template.tpl"));
			$prep = $conn->prepare("INSERT INTO pageTemplate VALUES (0, 1, NOW(), NOW(), NULL, \"default\", \"" . $defaultTemplate . "\");");
			$prep->execute();
			
			unset($prep);
			unset($conn);

			mkdir($_SERVER["DOCUMENT_ROOT"] . "/data", 0777);
			$settings_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/data/database.json", "w");
			$data = json_encode($db_info);
			fwrite($settings_file, $data);
			fclose($settings_file);
		}
        catch(Throwable $e){
            $_SESSION["init-error"] = $e->getMessage();
            // If it failed, return to init.php and try another database settings
            header("Location: /core/init/init_db_user.php");
            die();
        }
        // Database setup complete, proceed to site setup
		unset($_SESSION["init-error"]);
		header("Location: /core/init/init_site_user.php");
		die();
	}
	else{
		// No init if the CMS is already set up
		header("Location: /");
		die();
	}
?>
