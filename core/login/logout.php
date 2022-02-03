<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    $_SESSION["login"] = false;
    $_SESSion["user"] = "";
    header("Location: /");
    die();
?>