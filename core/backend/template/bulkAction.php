<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/templateEditFunc.php");
    switch($_POST["bulkoption"]){
        case "delete":
            foreach($_POST["template"] as $templateid)
                deleteTemplate($templateid);
            break;
    }
    header("Location: /backend/templates");
    die();
?>