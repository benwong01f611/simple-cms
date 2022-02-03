<?php
    if (session_id() == "")
        session_start();
    if(!isset($_SESSION["login"])){
        $_SESSION["login"] = false;
        $_SESSION["user"] = false;
    }
?>