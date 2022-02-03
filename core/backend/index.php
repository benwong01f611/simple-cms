<?php
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    $currentUser = new Permission($_SESSION["user"]);
    $render = true;
    ob_start();
    if(isset($_GET["backendpage"])){
        switch($_GET["backendpage"]){
            case "pages":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/allpages.php");
                break;
            case "templates":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/alltemplates.php");
                break;
            case "users":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/allusers.php");
                break;
            case "pages/history":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/pageHistory.php");
                break;
            case "pages/add":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/editpage.php");
                break;
            case "pages/bulkAction":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/bulkAction.php");
                break;
            case "template/add":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/edittemplate.php");
                break;
            case "templates/history":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/templateHistory.php");
                break;
            case "templates/bulkAction":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/bulkAction.php");
                break;
            case "users/add":
                $_GET["action"] = "add";
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/manageUserUI.php");
                break;
            case "users/add/update":
                $_GET["action"] = "add";
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/userEditFunc.php");
                break;
            case "users/bulkAction":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/bulkAction.php");
                break;
            case "tags":
                $render = false;
                if(isset($_GET["query"]))
                    $query = $_GET["query"];
                else
                    $query = "";
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                $tags = connectDB("SELECT tags FROM content;");
                $alltags = array();
                foreach ($tags as $tag){
                    if ($tag["tags"] != "" || $tag["tags"] != NULL){
                        foreach (explode(";", $tag["tags"]) as $subtag){
                            if($subtag != "" && (strpos($subtag, $query) !== false || $query == ""))
                                array_push($alltags, $subtag);
                        }
                    }
                }
                $tags_output = array();
                $alltags = array_unique($alltags);
                foreach ($alltags as $tag){
                    array_push($tags_output, array("value" => $tag, "label" => $tag));
                }
                header("Content-Type: application/json");
                echo json_encode($tags_output);
                break;
            default:
                $link = explode("/", $_GET["backendpage"]);
                switch($link[0]){
                    case "page":
                        $_GET["page"] = $link[1];
                        if(isset($link[2]))
                            $_GET["action"] = $link[2];
                        switch($link[2]){
                            case "edit":
                                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/editpage.php");
                                break;
                            case "deleteprompt":
                                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/deletepageprompt.php");
                                break;
                            default:
                                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/pageEditFunc.php");
                                break;
                        }
                        break;
                    case "pagehistory":
                        $_GET["page"] = $link[1];
                        if(isset($link[2])){
                            $_GET["action"] = $link[2];
                            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/page/pageEditFunc.php");
                        }
                        else{
                            $_GET["history"] = "1";
                            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
                        }
                        break;
                    case "template":
                        $_GET["template"] = $link[1];
                        if(isset($link[2])){
                            $_GET["action"] = $link[2];
                            switch($link[2]){
                                case "edit":
                                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/edittemplate.php");
                                    break;
                                case "deleteprompt":
                                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/deletetemplateprompt.php");
                                    break;
                                default:
                                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/templateEditFunc.php");
                                    break;
                            }
                        }
                        else
                            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/template.php");
                        break;
                    case "templatehistory":
                        $_GET["template"] = $link[1];
                        if(isset($link[2])){
                            $_GET["action"] = $link[2];
                            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/templateEditFunc.php");
                        }
                        else{
                            $_GET["history"] = "1";
                            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/template/template.php");
                        }
                        break;
                    case "user":
                        $_GET["user"] = $link[1];
                        if(isset($link[2])){
                            if($link[2] == "manage"){
                                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/manageUserUI.php");
                            }
                            else{
                                $_GET["action"] = $link[2];
                                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/userEditFunc.php");
                            }
                        }
                        else{
                            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/user.php");
                        }
                        break;
                    case "site":
                        if(isset($link[1]))
                            $_GET["action"] = $link[1];
                        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/site/siteSettings.php");
                        break;
                }
                break;
        }
    }
    $backend_content = ob_get_clean();
    if($render){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
        $backend_index = getTemplate("backend_index.tpl");
        $backend_index = str_replace("{backendcontent}", $backend_content, $backend_index);
    
        $navbar = getTemplate("navbar.tpl");
        $backend_index = str_replace("{navbar}", $navbar, $backend_index);
        $backend_index = str_replace("{username}", $_SESSION["user"], $backend_index);

        if(explode("/", $_GET["backendpage"])[0] == "template"){
            if(!isset($error)){
                $backend_index = str_replace("{url_template_edit}", "<li><a class=\"dropdown-item\" href=\"/backend/template/" . explode("/", $_GET["backendpage"])[1] . "/edit\">Edit</a></li>", $backend_index);
                $backend_index = str_replace("{url_template_delete}", "<li><a class=\"dropdown-item\" href=\"/backend/template/" . explode("/", $_GET["backendpage"])[1] . "/deleteprompt\">Delete</a></li>", $backend_index);
            }
            else{
                $backend_index = str_replace("{url_template_edit}", "", $backend_index);
                $backend_index = str_replace("{url_template_delete}", "", $backend_index);
            }
        }
        else{
            $backend_index = str_replace("{url_template_edit}", "", $backend_index);
            $backend_index = str_replace("{url_template_delete}", "", $backend_index);
        }
        $backend_index = str_replace("{url_page_edit}", "", $backend_index);
        $backend_index = str_replace("{url_page_delete}", "", $backend_index);
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/siteDetails.php");
        $backend_index = str_replace("{sitename}", getSiteJson()["sitename"], $backend_index);
        
        echo $backend_index;
        if(isset($_SESSION["toast"])){
            $toast = getTemplate("toast.tpl");
            $toast = str_replace("{toastbody}", $_SESSION["toast"]["text"], $toast);
            $toast = str_replace("{bg}", $_SESSION["toast"]["class"], $toast);
            echo $toast;
            unset($_SESSION["toast"]);
        }
    }
    else{
        echo $backend_content;
    }
?>