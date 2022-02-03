<?php
    // Hash the password before calling these functions!
    function addUser($username, $pw, $enabled, $pagePermission, $templatePermission, $accountPermission, $sitePermission){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        // Add user
        connectDB("INSERT INTO user VALUES (
            NOW(),
            NULL,"
            . (int) $enabled . ", \""
            . $username . "\", \"" 
            . $pw . "\");");
        // Set user permission
        $result = connectDB("INSERT INTO userPermission VALUES (\""
            . $username             . "\", "
            . $pagePermission       . ", "
            . $templatePermission   . ", " 
            . $accountPermission    . ", "
            . $sitePermission       . ");"
        );
    }

    function editUserFull($username, $newUsername, $pw, $enabled, $pagePermission, $templatePermission, $accountPermission, $sitePermission){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        editUser($username, $newUsername, $pw, $enabled);
        editUserPermission($newUsername, $pagePermission, $templatePermission, $accountPermission, $sitePermission);
    }

    function editUser($username, $newUsername, $pw, $enabled){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        // Update user
        connectDB("UPDATE user SET
            username=\"" . $newUsername . "\",
            enabled=" . (int) $enabled . ",
            pw=\"" . $pw . "\"
            WHERE username=\"" . $username . "\";"
        );
        // If the username has updated, update the username in table userPermission also
        if($username != $newUsername){
            connectDB("UPDATE userPermission SET
            username=\"" . $newUsername . 
            "WHERE username=\"" . $username . "\";");
        }
    }

    function changePassword($username, $newPassword, $logout){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/user/userEditFunc.php");
        editUser($username, $username, password_hash($newPassword, PASSWORD_ARGON2ID), 1);
        if($logout){
            header("Location: /logout");
            die();
        }
    }

    function editUserPermission($username, $pagePermission, $templatePermission, $accountPermission, $sitePermission){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        // Set user permission
       connectDB("UPDATE userPermission SET
            page="              . $pagePermission       . ",
            template="          . $templatePermission   . ",
            account="           . $accountPermission    . ",
            site="              . $sitePermission       . "
            WHERE username=\""  . $username             . "\";"
        );
    }
    
    function enDisUser($username, $enable){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        connectDB("UPDATE user SET enabled=" . (int)$enable . " WHERE username=\"" . $username . "\";");
    }

    function getNewPermission(){
        $newPermission = array(
            "page" => 0,
            "template" => 0,
            "account" => 0,
            "site" => 0,
        );
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
        $permissions = Permission::permissions;
        foreach($permissions as $category => $permission_type){
            foreach($permissions[$category] as $permission_type => $permission_value){
                if(isset($_POST["permission-" . $category . "-" . $permission_type]))
                    $newPermission[$category] += $permission_value;
            }
        }
        // Some permissions are pre-requisite of other permissions, grant them in backend (Front end should have scripts to do this, but in case something goes wrong)
        if(($newPermission["page"] & $permissions["page"]["delete"]) != 0 && ($newPermission["page"] & $permissions["page"]["history"]) == 0)
            $newPermission["page"] += $permissions["page"]["history"];
        if((($newPermission["page"] & $permissions["page"]["add"]) != 0 || ($newPermission["page"] & $permissions["page"]["history"]) != 0 || ($newPermission["page"] & $permissions["page"]["delete"]) != 0) && ($newPermission["page"] & $permissions["page"]["edit"]) == 0)
            $newPermission["page"] += $permissions["page"]["edit"];
        if(($newPermission["template"] & $permissions["template"]["delete"]) != 0 && ($newPermission["template"] & $permissions["template"]["history"]) == 0)
            $newPermission["template"] += $permissions["template"]["history"];
        if(((($newPermission["template"] & $permissions["template"]["add"]) != 0) || (($newPermission["template"] & $permissions["template"]["history"]) != 0)) && ($newPermission["template"] & $permissions["template"]["edit"]) == 0)
            $newPermission["template"] += $permissions["template"]["edit"];
        if( (($newPermission["account"] & $permissions["account"]["passwordother"]) != 0 || 
            ($newPermission["account"] & $permissions["account"]["add"]) != 0) && ($newPermission["account"] & $permissions["account"]["password"]) == 0)
            $newPermission["account"] += $permissions["account"]["password"];
        if(($newPermission["account"] & $permissions["account"]["add"]) != 0 && (($newPermission["account"] & $permissions["account"]["editpermission"]) == 0))
            $newPermission["account"] += $permissions["account"]["editpermission"];
        if(($newPermission["account"] & $permissions["account"]["editpermission"]) != 0){
            $accountFullPermission = 0;
            foreach($permissions["account"] as $value)
                $accountFullPermission += $value;
            $newPermission["account"] = $accountFullPermission;
        }
        return $newPermission;
    }

    function deleteUser($username){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        connectDB("DELETE FROM user WHERE username=\"" . $username . "\";");
        connectDB("DELETE FROM userPermission WHERE username=\"" . $username . "\";");
    }

	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"] && !$_SESSION["init"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    $admin = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/site.json"), true)["adminac"];
    if(isset($_GET["action"])){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
        $currentUser = new Permission($_SESSION["user"]);
        switch($_GET["action"]){
            case "delete":
                if($currentUser->permissionCheck("account", "delete")){
                    if($_GET["user"] != $admin)
                        deleteUser($_GET["user"]);
                }
                header("Location: /backend/users");
                break;
            case "enable":
                if($currentUser->permissionCheck("account", "en_dis")){
                    if($_GET["user"] != $admin)
                        enDisUser($_GET["user"], 1);
                }
                header("Location: /backend/users");
                break;
            case "disable":
                if($currentUser->permissionCheck("account", "en_dis")){
                    if($_GET["user"] != $admin)
                        enDisUser($_GET["user"], 0);
                }
                header("Location: /backend/users");
                break;
            case "update":
                $username = $_GET["user"];
                // Has permission to modify user permission
                if(isset($_POST["permission"])){
                    if($_GET["user"] != $admin){
                        $newPermission = getNewPermission();
                        // Account enabled
                        if(isset($_POST["permission-account-enabled"]))
                            enDisUser($_GET["user"], 1);
                        else
                            enDisUser($_GET["user"], 0);
                        editUserPermission($_GET["user"], $newPermission["page"], $newPermission["template"], $newPermission["account"], $newPermission["site"]);
                    }
                }

                // Has permission to edit password
                if(isset($_POST["password-newpassword"])){
                    if($_POST["password-newpassword"] != ""){
                        if(isset($_POST["password-originalPassword"])){
                            // User initiated password change
                            // Check the password matches with old password or not
                            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                            $user = connectDB("SELECT pw, username FROM user WHERE username = \"" . $username . "\";")[0];
                            if(password_verify($_POST["password-originalPassword"], $user["pw"]))
                                changePassword($username, $_POST["password-newpassword"], true);
                            else{
                                // Password does not match! return to edit page
                                $_SESSION["error"] = "Password does not match";
                                header("Location: /backend/user/" . $_GET["user"] . "/manage");
                                die();
                            }
                        }
                        else{
                            // Admin with changing other users' password permission
                            // Checking original password is not needed
                            // As it is editing other users' password, don't log out
                            changePassword($username, $_POST["password-newpassword"], false);
                        }
                    }
                }
                header("Location: /backend/user/" . $_GET["user"]);
                break;
            case "add":
                // Check whether the current user has the permission to add user
                if($currentUser->permissionCheck("account", "add")){
                    // Check whether the user already exists
                    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
                    $result = connectDB("SELECT username FROM user WHERE username=\"" . $_POST["username"] . "\";");
                    if(count($result)){
                        $_SESSION["error"] = "User exists";
                        $_SESSION["data"] = $_POST;
                        header("Location: /backend/users/add");
                        die();
                    }
                    $permission = getNewPermission();
                    addUser($_POST["username"], password_hash($_POST["password"], PASSWORD_ARGON2ID), (int) isset($_POST["permission-account-enabled"]), $permission["page"], $permission["template"], $permission["account"], $permission["site"]);
                    header("Location: /backend/user/" . $_POST["username"]);
                }
                else
                    header("Location: /backend/user/" . $_SESSION["user"]);
                break;
        }
    }
?>