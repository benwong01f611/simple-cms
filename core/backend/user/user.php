<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    $list = connectDB("SELECT * FROM user WHERE username = \"" . $_GET["user"] . "\";");
    if(count($list)){
        $user = $list[0];
        $username = $user["username"];
        $createDate = $user["createDate"];
    }
    else{
        // User not found
        $invalid = true;
    }
?>

<html>
    <head>
        <title>User info - <?php if(isset($invalid)) echo "Invalid user"; else echo $username;?></title>
    </head>
    <body>
        <?php
            if(isset($invalid))
                echo "Invalid user";
            else{
                ?>
                Username: <?php echo $username; ?><br>
                Account Creation date: <?php echo $createDate;?><br>
                <?php 
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
                    $currentUserPermission = new Permission($_SESSION["user"]);
                    if($currentUserPermission->permissionCheck("account", "password") || $currentUserPermission->permissionCheck("account", "passwordother") || $currentUserPermission->permissionCheck("account", "en_dis") || $currentUserPermission->permissionCheck("account", "editpermission")) echo "<a href=\"/backend/user/" . $_GET["user"] . "/manage\"><button class=\"btn btn-info btn-sm\">Manage User</button></a>&nbsp;";
                    if($_GET["user"] == $_SESSION["user"]) echo "<a href=\"/logout\"><button class=\"btn btn-info btn-sm\">Logout</button></a>";
            }
                ?>
        
    </body>
</html>