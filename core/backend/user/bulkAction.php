<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/userEditFunc.php");
    switch($_POST["bulkoption"]){
        case "delete":
            foreach($_POST["user"] as $user){
                if($user != $admin)
                   deleteUser($user, 1);
            }
            break;
        case "enable":
            foreach($_POST["user"] as $user){
                if($user != $admin)
                    enDisUser($user, 1);
            }
            break;
        case "disable":
            foreach($_POST["user"] as $user){
                if($user != $admin)
                    enDisUser($user, 0);
            }
            break;
    }
    header("Location: /backend/users");
    die();
?>