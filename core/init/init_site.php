<?php
    session_start();
    // Site settings
    $settings_file = fopen($_SERVER["DOCUMENT_ROOT"] . "/data/site.json", "w");
    $sitesettings = [
        "sitename"=>$_POST["sitename"],
        "adminac"=>$_POST["adminacname"]
    ];
    $data = json_encode($sitesettings);
    fwrite($settings_file, $data);
    fclose($settings_file);
    // Add admin account credentials to database
    if(!file_exists($_SERVER["DOCUMENT_ROOT"] . "/data/ok")){
        $adminpw = password_hash($_POST["adminacpw"], PASSWORD_ARGON2ID);    
        // 2147483647 means full permission
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/userEditFunc.php");
        addUser("admin", $adminpw, 1, 2147483647, 2147483647, 2147483647, 2147483647);
        // Add an indicator of setup completed
        $ok = fopen($_SERVER["DOCUMENT_ROOT"] . "/data/ok", "w");
        fwrite($ok, "ok");
        fclose($ok);
        unset($_SESSION["init-error"]);
        unset($_SESSION["init"]);
	}
    header("Location: /");
    die();
?>
