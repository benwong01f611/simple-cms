<?php
    function deleteTemplate($templateid){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        // Put the current template to template history
        connectDB("INSERT INTO pageTemplateHist (SELECT * FROM pageTemplate WHERE id=" . $templateid . ")");
        // Update pageTemplate
        connectDB("UPDATE pageTemplate SET revid=(SELECT revid FROM pageTemplate WHERE id=" . $templateid. "), lastModifyDate=NOW(), deleteDate=NOW(), name=NULL, body=NULL WHERE id=" . $templateid . ";");
        unset($templateid);
        unset($_SESSION["action"]);
    }

    function saveTemplate($type, $name, $body, $templateid){
        if($type == "add"){
            $previous_id = connectDB("SELECT MAX(id) FROM pageTemplate;")[0][0];
            $result = connectDB("INSERT INTO pageTemplate VALUES ( "
                . ((int) $previous_id) + 1              // template id
                . ", 1,"                                // rev id
                . "NOW(),"                              // create date
                . "NOW(),"                              // last modify date
                . "NULL, \""                            // delete date
                . $name . "\", \""                      // name
                . $body . "\""                          // body
            . ");");
            $newtemplateid = connectDB("SELECT MAX(id) FROM pageTemplate;")[0][0];
        }
        elseif ($type == "edit"){
            // Move the previous revision to template history
            $move = connectDB("INSERT INTO pageTemplateHist (SELECT * FROM pageTemplate WHERE id = " . $templateid . ");");
            // Update template
            connectDB("UPDATE pageTemplate SET 
                revid=(SELECT revid + 1 FROM pageTemplate WHERE id=" . $templateid . "), 
                lastModifyDate=NOW(), 
                name=\"" . $name . "\", 
                body=\"" . $body . "\" 
                WHERE id=" . $templateid . ";"
            );
            $revid = connectDB("SELECT revid FROM pageTemplate WHERE id = " . $templateid . ";")[0][0];
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
                if($currentUser->permissionCheck("template", "delete") && $_SESSION["template-id"] != 0)
                    deleteTemplate($_SESSION["template-id"]);
                header("Location: /backend/templates");
                $_SESSION["toast"] = array("text" => "Template deleted.", "class" => "bg-success");
                break;
            case "save":
                if($currentUser->permissionCheck("template", "add") || $currentUser->permissionCheck("template", "edit")){
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                    $uploaded_file = $_SERVER["DOCUMENT_ROOT"] . "/data/upload/" . basename($_FILES["templatefile"]["name"]);
                    if (move_uploaded_file($_FILES["templatefile"]["tmp_name"], $uploaded_file)){
                        $content = file_get_contents($uploaded_file);
                        unlink($uploaded_file);
                    }
                    else{
                        $_SESSION["error"] = "File error";
                        $_SESSION["data"] = array(
                            "name" => $_POST["template-name"]
                        );
                        if($_SESSION["action"] == "add")
                            header("Location: /backend/template/add");
                        elseif ($_SESSION["action"] == "edit")
                            header("Location: /backend/template/" . $_POST["template-name"] . "/edit");
                        $_SESSION["toast"] = array("text" => "File error.", "class" => "bg-danger");
                        die();
                    }
                    //$body = str_replace("\"", "\\\"", $_POST["template-body"]);
                    $content = str_replace("\"", "\\\"", $content);
                    $rows = count(connectDB("SELECT * FROM pageTemplate WHERE name = \"" . $_POST["template-name"] . "\";"));
                    if($_SESSION["action"] == "add"){
                        // If the template name was found, return to the previous template
                        if($rows == 1){
                            $_SESSION["error"] = "Duplicated template name";
                            $_SESSION["data"] = array(
                                "name" => $_POST["template-name"]
                            );
                            // Return to edit template based on it is editing or adding template
                            if($_SESSION["action"] == "add")
                                header("Location: /backend/template/add");
                            elseif ($_SESSION["action"] == "edit")
                                header("Location: /backend/template/" . $_POST["template-name"] . "/edit");
                                $_SESSION["toast"] = array("text" => "Dulicated template name.", "class" => "bg-danger");
                            die();
                        }
                        else{
                            if($currentUser->permissionCheck("template", "add")){
                                saveTemplate("add", $_POST["template-name"], $content, 1);
                                $_SESSION["toast"] = array("text" => "Template saved.", "class" => "bg-success");
                            }
                        }
                    }
                    elseif ($_SESSION["action"] == "edit"){
                        if($currentUser->permissionCheck("template", "edit")){
                            saveTemplate("edit", $_POST["template-name"], $content, $_SESSION["template-id"]);
                            $_SESSION["toast"] = array("text" => "Template saved.", "class" => "bg-success");
                        }
                    }
                }   
                
                unset($_SESSION["action"]);
                unset($_SESSION["data"]);
                unset($_SESSION["template-id"]);
                header("Location: /backend/template/" . $_POST["template-name"]);
                break;
            case "revert":
                // Check whether the current user has the permission to revert pages from history
                
                if($currentUser->permissionCheck("template", "history")){
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                    $templateid = explode("-", $_GET["template"])[0];
                    $revid = explode("-", $_GET["template"])[1];
                    $templateToRevert = connectDB("SELECT * FROM pageTemplateHist WHERE id = " . $templateid  . " AND revid = " . $revid . ";")[0];
                    $name = $templateToRevert["name"];
                    $body = $templateToRevert["body"];
                    connectDB("INSERT INTO pageTemplateHist (SELECT * FROM pageTemplate WHERE id = " . $templateid . ");");
                    $originalrevid = connectDB("SELECT revid FROM pageTemplate WHERE id = " . $templateid . ";")[0][0];
                    connectDB("UPDATE pageTemplate SET 
                        revid=(SELECT revid + 1 FROM pageTemplate WHERE id=" . $templateid . "), 
                        lastModifyDate=NOW(), 
                        name=\"" . $name . "\",
                        body=\"" . $body . "\", 
                        WHERE id=" . $templateid . ";"
                    );
                    $_SESSION["toast"] = array("text" => "Template reverted.", "class" => "bg-success");
                }
                else
                header("Location: /backend/template/" . $name);
                break;
            case "download":
                $body = connectDB("SELECT body FROM pageTemplate WHERE name = \"" . $_GET["template"] . "\";")[0]["body"];
                $body = json_encode($body);
                echo "
                    <body>
                    </body>
                    <script>
                        var blob = new Blob([" . $body . "], {type: \"text/plain\"});
                        const blobURL = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = blobURL;
                        link.download = \"" . $_GET["template"] . ".html\";
                        document.body.appendChild(link);
                        link.click();
                        window.URL.revokeObjectURL(link);
                        setTimeout( function() { window.close(); }, 10);
                    </script>
                ";
                break;
        }
        if($_GET["action"] != "download")
            die();
    }

?>