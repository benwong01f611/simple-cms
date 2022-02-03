<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    $result = connectDB("SELECT body FROM pageTemplate WHERE id = 0;");
	$template = $result[0][0];
	$template = str_replace("{title}", "Search Result", $template);
    $body = "";
    $result = connectDB("SELECT title, body, alias, tags FROM content WHERE body IS NOT NULL LIMIT 5;");
    foreach($result as $row){
        $tags = explode(";", $row["tags"]);
        $keyword_match = isset($_GET["keyword"]) ? strpos($row["title"], $_GET["keyword"]) !== false || strpos($row["body"], $_GET["keyword"]) !== false : true;
        $tag_match = isset($_GET["tag"]) ? in_array($_GET["tag"], $tags) : true;
        
        if($keyword_match && $tag_match){
            $pagebody = strip_tags($row["body"]);
            if(strlen($pagebody) > 100){
                $pagebody = substr($pagebody, 0, 500);
                $pagebody .= "...";
            }
			$pagetitle = "<h3>" . $row["title"] . "</h3>";
			$body .= "<a class=\"d-block content-link\" href=\"/page/" . $row["alias"] . "\">" . $pagetitle . $pagebody . "</a><hr>";

        }
    }
    
    if($body == "")
        $body = "No results could be found.";

	$template = str_replace("{body}", $body, $template);
    $template = str_replace("{tags}", "", $template);
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/siteDetails.php");
	
	if($_SESSION["login"]){
		require_once($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
		$navbar = getTemplate("navbar.tpl");
		$template = "<link href=\"/resources/css/navbar.css\" rel=\"stylesheet\">" . $navbar . $template;
        $template = str_replace("{url_page_edit}", "", $template);
        $template = str_replace("{url_page_delete}", "", $template);
		$template = str_replace("{url_template_edit}", "", $template);
		$template = str_replace("{url_template_delete}", "", $template);
	}
	$template = str_replace("{sitename}", $sitename = getSiteJson()["sitename"], $template);
    $template = str_replace("{username}", $_SESSION["user"], $template);
	echo $template;
?>