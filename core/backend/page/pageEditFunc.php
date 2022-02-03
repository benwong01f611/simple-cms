<?php
    function deletePage($pageid, $bulk){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        // Put the current page content to page history
        connectDB("INSERT INTO pagehist (SELECT * FROM content WHERE id=" . $pageid . ");");
        // Update content
        connectDB("UPDATE content SET revid=(SELECT revid + 1 FROM content WHERE id=" . $pageid . "), lastModifyDate=NOW(), deleteDate=NOW(), alias=NULL, title=NULL, body=NULL, templateid=NULL, published=NULL, tags=NULL WHERE id=" . $pageid . ";");
        unset($_SESSION["action"]);
        unset($_SESSION["page-id"]);
        if(!$bulk)
            header("Location: /");
    }

    function publishPage($pageid, $publish){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        connectDB("UPDATE content SET published=" . $publish . " WHERE id=" . $pageid . ";");
    }

    function savePage($type, $title, $body, $alias, $templateid, $tags, $published, $hidden, $pageid){
        if($type == "add"){
            $result = connectDB("INSERT INTO content (revid, createDate, lastmodifyDate, alias, title, body, templateid, published, tags, hidden) VALUES ( "
            . "1,"                                  // rev id
            . "NOW(),"                              // create date
            . "NOW(),\""                            // last modify date
            . $alias . "\", \""                     // URL alias
            . $title . "\", \""                     // title
            . $body . "\", "                        // body
            . $templateid . ", "                    // Template id
            . $published . ", \""                   // Published
            . $tags . "\", "                        // Tags for the page
            . $hidden
        . ");");
            $newpageid = connectDB("SELECT MAX(id) FROM content;")[0][0];
        }
        elseif ($type == "edit"){
            // Move the previous revision to page history
            connectDB("INSERT INTO pagehist (SELECT * FROM content WHERE id = " . $pageid . ");");
            // Update content
            $result = connectDB("UPDATE content SET 
                revid=(SELECT revid + 1 FROM content WHERE id=" . $pageid . "), 
                lastModifyDate=NOW(), 
                alias=\"" . $alias . "\", 
                title=\"" . $title . "\",
                body=\"" . $body . "\", 
                templateid=" . $templateid . ",
                published=" . $published . " ,
                tags=\"" . $tags . "\",
                hidden=" . $hidden . "  
                WHERE id=" . $pageid . ";"
            );
            $revid = connectDB("SELECT revid FROM content WHERE id = " . $pageid . ";")[0][0];
        }
    }

	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    if(isset($_GET["action"])){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
        $currentUser = new Permission($_SESSION["user"]);
        switch($_GET["action"]){
            case "delete":
                if($currentUser->permissionCheck("page", "delete")){
                    deletePage($_SESSION["page-id"], 0);
                    $_SESSION["toast"] = array("text" => "Page deleted.", "class" => "bg-success");
                }
                header("Location: /backend/pages");
                break;
            case "save":
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                if($_POST["content-alias"] == ""){
                    if($_SESSION["action"] == "edit"){
                        $new_alias = connectDB("SELECT id + 1 FROM content WHERE id=" . $_SESSION["page-id"] . ";")[0][0];
                    }
                    else{
                        $new_alias = connectDB("SELECT MAX(id) + 1 FROM content;")[0][0];
                    }
                    $_POST["content-alias"] = $new_alias;
                }
                if($currentUser->permissionCheck("page", "edit")){
                    $_POST["content-tags"] = isset($_POST["content-tags"]) ? join(";", $_POST["content-tags"]) : "";
                    $published = (int)isset($_POST["published"]);
                    $hidden = (int)isset($_POST["hidden"]);
                    $body = str_replace("\"", "\\\"", $_POST["content-body"]);
                    $rows = count(connectDB("SELECT * FROM content WHERE alias = \"" . $_POST["content-alias"] . "\";"));
                    if($_SESSION["action"] == "add"){
                        // If an alias was found, return to the previous page
                        if($rows == 1){
                            $_SESSION["error"] = 1;
                            $_SESSION["data"] = array(
                                "title"         => $_POST["content-title"],
                                "body"          => $_POST["content-body"],
                                "alias"         => $_POST["content-alias"],
                                "templateid"    => $_POST["template-type"],
                                "tags"          => $_POST["content-tags"],
                                "published"     => $published,
                                "hidden"        => $hidden,
                            );
                            // Return to edit page based on it is editing or adding page
                            if($_SESSION["action"] == "add")
                                header("Location: /backend/pages/add");
                            elseif ($_SESSION["action"] == "edit")
                                header("Location: /backend/page/" . $_POST["content-alias"] . "/edit");
                            $_SESSION["toast"] = array("text" => "Dulicated alias.", "class" => "bg-danger");
                            die();
                        }
                        else{
                            if($currentUser->permissionCheck("page", "add")){
                                savePage("add", $_POST["content-title"], $body, $_POST["content-alias"], $_POST["template-type"], $_POST["content-tags"], $published, $hidden, -1);
                                $_SESSION["toast"] = array("text" => "Page saved.", "class" => "bg-success");
                            }
                        }
                    }
                    elseif ($_SESSION["action"] == "edit"){
                        if($currentUser->permissionCheck("page", "edit")){
                            $_SESSION["toast"] = array("text" => "Page saved.", "class" => "bg-success");
                            savePage("edit", $_POST["content-title"], $body, $_POST["content-alias"], $_POST["template-type"], $_POST["content-tags"], $published, $hidden, $_SESSION["page-id"]);
                        }
                    }
                }
                unset($_SESSION["action"]);
                unset($_SESSION["data"]);
                unset($_SESSION["page-id"]);
                header("Location: /page/" . $_POST["content-alias"]);
                break;
            case "publish":
                if($currentUser->permissionCheck("page", "pub_unpub")){
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                    $pageid = connectDB("SELECT id FROM content WHERE alias=\"" . $_GET["page"] . "\";")[0]["id"];
                    publishPage($pageid, 1);
                    $_SESSION["toast"] = array("text" => "Page published.", "class" => "bg-success");
                }
                header("Location: /backend/pages");
                break;
            case "unpublish":
                if($currentUser->permissionCheck("page", "pub_unpub")){
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                    $pageid = connectDB("SELECT id FROM content WHERE alias=\"" . $_GET["page"] . "\";")[0]["id"];
                    publishPage($pageid, 0);
                    $_SESSION["toast"] = array("text" => "Page unpublished.", "class" => "bg-success");
                }
                header("Location: /backend/pages");
                break;
            case "revert":
                // Check whether the current user has the permission to revert pages from history
                
                if($currentUser->permissionCheck("page", "history")){
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                    $pageid = explode("-", $_GET["page"])[0];
                    $revid = explode("-", $_GET["page"])[1];
                    $pageToRevert = connectDB("SELECT * FROM pagehist WHERE id = " . $pageid  . " AND revid = " . $revid . ";")[0];
                    $alias = $pageToRevert["alias"];
                    $title = $pageToRevert["title"];
                    $body = $pageToRevert["body"];
                    $templateid = $pageToRevert["templateid"];
                    $published = $pageToRevert["published"];
                    $tags = $pageToRevert["tags"];
                    connectDB("INSERT INTO pagehist (SELECT * FROM content WHERE id = " . $pageid . ");");
                    $originalrevid = connectDB("SELECT revid FROM content WHERE id = " . $pageid . ";")[0][0];
                    connectDB("UPDATE content SET 
                        revid=(SELECT revid + 1 FROM content WHERE id=" . $pageid . "), 
                        lastModifyDate=NOW(), 
                        deleteDate=NULL,
                        alias=\""       . $alias        . "\", 
                        title=\""       . $title        . "\",
                        body=\""        . $body         . "\", 
                        templateid="    . $templateid   . ",
                        published="     . $published    . ",
                        tags=\""        . $tags         . " 
                        WHERE id="      . $pageid       . ";"
                    );
                    $_SESSION["toast"] = array("text" => "Page saved.", "class" => "bg-success");
                }
                header("Location: /page/" . $alias);
                break;
        }
        die();
    }

?>