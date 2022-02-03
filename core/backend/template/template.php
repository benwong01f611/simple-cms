<?php
	function err($code){
		global $error;
		global $title;
		global $body;
		global $name;
		global $tags;
		$error = true;
		$title = $code;
		$body = "";
		$tags = "error";
		http_response_code(404);
	}
	$name = "";
	$body = "";
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
	if(isset($_GET["history"])){
		// View template history
		$id = explode("-", $_GET["template"])[0];
		$revid = explode("-", $_GET["template"])[1];
		$result = connectDB("SELECT name, body FROM pageTemplateHist WHERE id = " . $id . " AND revid = " . $revid . ";");
		if(count($result)){
			$template = $result[0];
			$name = $template["name"];
			$body = $template["body"];
			$body = htmlspecialchars($body, ENT_HTML5 | ENT_QUOTES);
		}
		else
			err("404 Not Found", 404);
	}
	else{
		if(isset($_GET["template"])){
			$result = connectDB("SELECT name, body, deleteDate, id FROM pageTemplate WHERE name = \"" . $_GET["template"] . "\";");
			if(count($result)){
				$template = $result[0];
				// If deleted, return 404
				if($template[2] != NULL)
					err("404 Not Found", 404);
				$name = $template[0];
				$body = $template[1];
				$body = nl2br(htmlspecialchars($body, ENT_HTML5 | ENT_QUOTES));
			}
			else
				err("404 Not Found", 404);
		}
		else
			err("404 Not Found", 404);
	}
?>

<html>
    <head>
        <title><?php echo $name;?></title>
    <head>
    <body>
        <h2><?php echo $name;?></h2>
        <code><?php echo $body; ?></code>
    <body>
</html>