<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
	if(!file_exists("data/ok")){
		header("Location: /core/init/init_db_user.php");
		die();
	}
	else{
		// Display homepage
		require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/siteDetails.php");
		unset($_SESSION["action"]);
		unset($_SESSION["data"]);
		unset($_SESSION["page-id"]);
		unset($_SESSION["init-error"]);
		unset($_SESSION["error"]);
		require($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
		$index = getTemplate("index.tpl");
		$index = str_replace("{sitename}", getSiteJson()["sitename"], $index);
		require($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
		$result = connectDB("SELECT * FROM content WHERE published=1 AND hidden=0 ORDER BY createDate desc LIMIT 5;");
		$content = "";
		foreach($result as $row){
			$pagebody = strip_tags($row["body"]);
			if(strlen($pagebody) > 100){
				$pagebody = substr($pagebody, 0, 500);
				$pagebody .= "...";
			}
			$pagetitle = "<h3>" . $row["title"] . "</h3>";
			$content .= "<a class=\"d-block content-link\" href=\"/page/" . $row["alias"] . "\">" . $pagetitle . $pagebody . "</a><hr>";
		}
		$index = str_replace("{content}", $content, $index);		
		if($_SESSION["login"]){
			$navbar = getTemplate("navbar.tpl");
			$index = "<link href=\"/resources/css/navbar.css\" rel=\"stylesheet\">" . $navbar . $index;
			require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/siteDetails.php");
			
			$index = str_replace("{sitename}", $sitename = getSiteJson()["sitename"], $index);
			
			$index = str_replace("{url_page_edit}", "", $index);
			$index = str_replace("{url_page_delete}", "", $index);
			$index = str_replace("{url_template_edit}", "", $index);
			$index = str_replace("{url_template_delete}", "", $index);
			$index = str_replace("{username}", $_SESSION["user"], $index);
		}
		echo $index;
	}
	$_SESSION["lastPage"] = "/";
?>