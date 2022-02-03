<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    $currentUser = new Permission($_SESSION["user"]);
    if(!($currentUser->permissionCheck("template", "add") || $currentUser->permissionCheck("template", "edit"))){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    // If previous edit has error, keep the changes and ask user to edit again
    if(isset($_SESSION["error"])){
        $error = $_SESSION["error"];
        unset($_SESSION["error"]);
        $name = $_SESSION["data"]["name"];
        if($_SESSION["action"] == "add")
            $formAction = "\"/backend/template/add/save\"";
        else
            $formAction = "\"/backend/template/" . $name . "/save\"";
    }
    else{
        if(isset($_GET["template"]) && $_GET["template"] != ""){
            $result = connectDB("SELECT name, body, id FROM pageTemplate WHERE name = \"" . $_GET["template"] . "\";");
            if(count($result)){
                $row = $result[0];
                $name = $row[0];
                $body = $row[1];
                $_SESSION["action"] = "edit";
                $_SESSION["template-id"] = $row[2];
                $delete = true;
            }
            else{
                // 404, which should be impossible except someone is trying to direct access this page
                header("Location: /");
                die();
            }
            $formAction = "\"/backend/template/" . $name ."/save\"";
        }
        else{
            // Create new template
            if(!$currentUser->permissionCheck("template", "edit")){
                http_response_code(403);
                $_SESSION["error"] = "403";
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
                die();
            }
            $_SESSION["action"] = "add";
            $formAction = "\"/backend/template/add/save\"";
        }
    }
    unset($_SESSION["data"]);
?>

<?php //if(isset($error)) echo "<div style=\"background-color:#eea29a;\">" . $error . "</div>"; ?>
<form action=<?php echo $formAction; ?> method="POST" enctype="multipart/form-data">
    <p>
        <div class="col-12 col-md-3">
            <label class="form-label">Template name:</label>
            <input type="text" class="form-control" name="template-name" required <?php if(isset($name)) echo "value=\"" . $name . "\""; ?>>
        </div>
    </p>
    <p>
        <div class="col-12 col-md-3">
            <label for="formFile" class="form-label">Template file upload:</label>
            <input class="form-control" type="file" name="templatefile">
        </div>
    </p>
<?php
    if(isset($name)){
        echo "
            <p>
                <button class=\"btn btn-info\" onclick=\"window.open('/backend/template/" . $name . "/download?edit=1', '_blank'); return false;\">Download Template</button>
            </p>
        ";
    }
?>
    <input type="submit" value="Save">
    <input type="button" value="Cancel" onclick="window.location='/backend/templates'">
    <?php if(isset($delete)) echo "<a href=\"/backend/template/" . $name . "/deleteprompt\"><input type=\"button\" value=\"Delete\" ></a>"; ?>
    <input type="text" value="<?php echo $_SESSION["action"];?>" hidden>
</form>