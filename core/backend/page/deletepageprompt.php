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
        if(isset($_GET["page"])){
            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
            $result = connectDB("SELECT alias, title, id FROM content WHERE alias = \"" . $_GET["page"] . "\";");
            if(count($result)){
                $page = $result[0];
                $alias = $page["alias"];
                $pageTitle = $page["title"];
                $_SESSION["page-id"] = $page["id"];
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
<title>Confirm Delete Page - <?php echo $pageTitle; ?></title>
<script src="/resources/js/bootstrap.bundle.min.js"></script>
<link href="/resources/css/bootstrap.min.css" rel="stylesheet">
<h4>Confirm Delete Page - <?php echo $pageTitle; ?></h4>
Are you sure to delete this page?<br>
<a href="/backend/page/<?php echo $pageTitle;?>/delete"><button class="btn btn-danger">Confirm</button></a>
<?php
    if(isset($_SESSION["action"]))
        $cancelURL = "/backend/page/" . $pageTitle . "/edit";
    else
        $cancelURL = "/backend/pages";
?>
<a href="<?php echo $cancelURL; ?>"><button class="btn btn-secondary">Cancel</button></a>