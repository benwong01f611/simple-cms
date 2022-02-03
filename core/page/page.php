<?php
	function err($code, $intcode){
		global $error;
		global $title;
		global $body;
		global $name;
		global $tags;
		$error = true;
		$title = $code;
		$body = "";
		$tags = "error";
		http_response_code($intcode);
	}
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
	$error = false;
	$body = "";
	$title = "";
	if(isset($_SESSION["action"]))
		unset($_SESSION["action"]);
	if(isset($_SESSION["error"])){
		switch($_SESSION["error"]){
			case "403":
				err("403 Forbidden", 403);
				break;
			case "404":
				err("404 Not Found", 404);
				break;
			default:
				err("Error", 200);
				break;
		}
		unset($_SESSION["error"]);
		$error = true;
	}
	else{
		if(isset($_GET["error"])){
			$error = true;
			err("404 Not Found", 404);
		}
		else{
			require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
			if(isset($_GET["history"])){
				// View page history
				$id = explode("-", $_GET["page"])[0];
				$revid = explode("-", $_GET["page"])[1];
				$result = connectDB("SELECT title, body, templateid, tags FROM pagehist WHERE id = " . $id . " AND revid = " . $revid . ";");
				if(count($result)){
					$page = $result[0];
					$title = $page["title"];
					$body = $page["body"];
					$templateid = $page["templateid"];
					$tags = $page["tags"];
				}
				else
					err("404 Not Found", 404);
			}
			else{
				if(isset($_GET["alias"])){
					$result = connectDB("SELECT alias, title, body, templateid, published, tags FROM content WHERE alias = \"" . $_GET["alias"] . "\";");
					if(count($result)){
						$page = $result[0];
						// If it is unpublished, return 404
						if(($page["published"] == 0 && !$_SESSION["login"]))
							err("404 Not Found", 404);
						else{
							$title = $page["title"];
							$body = $page["body"];
							$templateid = $page["templateid"];
							$tags = $page["tags"];
						}
					}
					else
						err("404 Not Found", 404);
				}
				else
					err("404 Not Found", 404);
			}
			
		}
	}

	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
	if(!$error)
		$result = connectDB("SELECT body FROM pageTemplate WHERE id = " . $templateid . ";");
	// If the server couldn't find the page, return the 404 page with default template
	else
		$result = connectDB("SELECT body FROM pageTemplate WHERE id = 0;");
	$template = $result[0][0];
	// Print the title and body
	$template = str_replace("{title}", $title, $template);
	$template = str_replace("{body}", $body, $template);
	if(isset($tags)){
		$alltags = explode(";", $tags);
		$tags_formatted = array();
		foreach($alltags as $tag){
			if($tag != "error")
				array_push($tags_formatted, "<a href=\"/search?tag=" . $tag . "\"><span class=\"badge bg-info text-dark\">" . $tag . "</span></a>");
			else
				array_push($tags_formatted, "<span class=\"badge bg-info text-dark\">" . $tag . "</span>");
		}
		$tags = join("&nbsp;", $tags_formatted);
		$template = str_replace("{tags}", $tags, $template);
	}
	else
		$template = str_replace("{tags}", "", $template);
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/siteDetails.php");
	
	if($_SESSION["login"]){
		require_once($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
		$navbar = getTemplate("navbar.tpl");
		$template = "<link href=\"/resources/css/navbar.css\" rel=\"stylesheet\">" . $navbar . $template;
		if(!$error){
			$template = str_replace("{url_page_edit}", "<li><a class=\"dropdown-item\" href=\"/backend/page/" . $_GET["alias"] . "/edit\">Edit</a></li>", $template);
			$template = str_replace("{url_page_delete}", "<li><a class=\"dropdown-item\" href=\"/backend/page/" . $_GET["alias"] . "/deleteprompt\">Delete</a></li>", $template);
		}
		else{
			$template = str_replace("{url_page_edit}", "", $template);
			$template = str_replace("{url_page_delete}", "", $template);
		}
		$template = str_replace("{url_template_edit}", "", $template);
		$template = str_replace("{url_template_delete}", "", $template);
	}
	$template = str_replace("{sitename}", $sitename = getSiteJson()["sitename"], $template);
	$template = str_replace("{username}", $_SESSION["user"], $template);
	echo $template;
	
	if(isset($_SESSION["toast"])){
		$toast = getTemplate("toast.tpl");
		$toast = str_replace("{toastbody}", $_SESSION["toast"]["text"], $toast);
		$toast = str_replace("{bg}", $_SESSION["toast"]["class"], $toast);
		echo $toast;
		unset($_SESSION["toast"]);
	}
?>
