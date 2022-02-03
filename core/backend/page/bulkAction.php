<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/pageEditFunc.php");
    switch($_POST["bulkoption"]){
        case "delete":
            foreach($_POST["page"] as $pageid)
                deletePage($pageid, 1);
            break;
        case "publish":
            foreach($_POST["page"] as $pageid)
                publishPage($pageid, 1);
            break;
        case "unpublish":
            foreach($_POST["page"] as $pageid)
                publishPage($pageid, 0);
            break;
    }
    header("Location: /backend/pages");
    die();
?>