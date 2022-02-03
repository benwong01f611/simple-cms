<?php
    //header("Content-Type: text/plain");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    $currentUser = new Permission($_SESSION["user"]);
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/siteDetails.php");
    if(isset($_GET["action"]) && $_GET["action"] == "update" && $currentUser->permissionCheck("site", "settings")){
        $sitedetails = getSiteJson();
        $sitedetails["sitename"] = $_POST["sitename"];
        
        // Site settings
        $settings_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/data/site.json", "w");
        $data = json_encode($sitedetails);
        fwrite($settings_file, $data);
        fclose($settings_file);
        
        $_SESSION["toast"] = array("text" => "Site name updated.", "class" => "bg-success");
        header("Location: /backend/site");
        die();
    }
    $sitename = getSiteJson()["sitename"];
?>
<form action="/backend/site/update" method="POST">
    <div class="col-12 col-md-3">
        Site name: <input type="text" class="form-control" name="sitename" <?php echo ($currentUser->permissionCheck("site", "settings") ? "value" : "placeholder") .  "=\"" . $sitename;?>" <?php echo ($currentUser->permissionCheck("site", "settings") ? "" : "disabled"); ?>>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Save</button>
</form>
