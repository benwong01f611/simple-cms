<?php
    $ok = false;
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    else{
        if(isset($_GET["template"])){
            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
            $result = connectDB("SELECT name, id FROM pageTemplate WHERE name = \"" . $_GET["template"] . "\";");
            if(count($result)){
                $template = $result[0];
                $templateName = $template["name"];
                $_SESSION["template-id"] = $template["id"];
                $ok = true;
            }
        }
    }
    if(!$ok){
        http_response_code(404);
        $_SESSION["error"] = "404";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }

?>
<title>Confirm Delete Template - <?php echo $templateName; ?></title>
<script src="/resources/js/bootstrap.bundle.min.js"></script>
<link href="/resources/css/bootstrap.min.css" rel="stylesheet">
<h4>Confirm Delete Template - <?php echo $templateName; ?></h4>
Are you sure to delete this template?<br>
<a href="/backend/template/<?php echo $templateName;?>/delete"><button class="btn btn-danger">Confirm</button></a>
<?php
    if(isset($_SESSION["action"]))
        $cancelURL = "/backend/template/" . $templateName . "/edit";
    else
        $cancelURL = "/backend/templates";
?>
<a href="<?php echo $cancelURL; ?>"><button class="btn btn-secondary">Cancel</button></a>