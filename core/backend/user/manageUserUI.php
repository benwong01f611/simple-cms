<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    if(isset($_SESSION["error"])){
        echo $_SESSION["error"] . "<p></p>";
        unset($_SESSION["error"]);
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    if(isset($_GET["user"]))
        $targetUserPermission = new Permission($_GET["user"]);
    $currentUserPermission = new Permission($_SESSION["user"]);
    if(!($currentUserPermission->permissionCheck("account", "add") || $currentUserPermission->permissionCheck("account", "password") || $currentUserPermission->permissionCheck("account", "passwordother") || $currentUserPermission->permissionCheck("account", "editpermission"))){
        header("Location: " . $_GET["user"]);
        die();
    }
    $changePermissionContent = "";
    $changePasswordContent = "";
    if(isset($_GET["action"])){
        $newUserUI = "<b>Account details</b>
        <hr>
        <div class=\"col-12 col-md-3\">
            Username: <input type=\"text\" class=\"form-control\" name=\"username\"><br>
            Password: <input type=\"password\" class=\"form-control\" name=\"password\"><br>
            <p><label class=\"form-check-label\" for=\"permission-account-enabled\">Enabled</label> <input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-enabled\" name=\"permission-account-enabled\"></p>
            <p><input type=\"text\" name=\"permission\" hidden>
        </div>
        <b>Page</b>
        <hr>
        <label class=\"form-check-label\" for=\"permission-page-add\">          Add Page                    </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-page-add\" id=\"permission-page-add\" onchange=\"permissionCheck('permission-page-add')\"><br>
        <label class=\"form-check-label\" for=\"permission-page-edit\">         Edit Page                   </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-page-edit\" id=\"permission-page-edit\" onchange=\"permissionCheck('permission-page-edit')\"><br>
        <label class=\"form-check-label\" for=\"permission-page-pub_unpub\">    Publish / Unpublish Page    </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-page-pub_unpub\" id=\"permission-page-pub_unpub\" onchange=\"permissionCheck()\"><br>
        <label class=\"form-check-label\" for=\"permission-page-delete\">       Delete Page                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-page-delete\" id=\"permission-page-delete\" onchange=\"permissionCheck('permission-page-delete')\"><br>
        <label class=\"form-check-label\" for=\"permission-page-history\">      View Page History           </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-page-history\" id=\"permission-page-history\" onchange=\"permissionCheck('permission-page-history')\"><br>
        <br>
        <b>Template</b>
        <hr>
        <label class=\"form-check-label\" for=\"permission-template-add\">      Add Template                </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-template-add\" id=\"permission-template-add\" onchange=\"permissionCheck('permission-template-add')\"><br>
        <label class=\"form-check-label\" for=\"permission-template-edit\">     Edit Template               </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-template-edit\" id=\"permission-template-edit\" onchange=\"permissionCheck('permission-template-edit')\"><br>
        <label class=\"form-check-label\" for=\"permission-template-delete\">   Delete Template             </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-template-delete\" id=\"permission-template-delete\" onchange=\"permissionCheck('permission-template-delete')\"><br>
        <label class=\"form-check-label\" for=\"permission-template-history\">  View Template History       </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-template-history\" id=\"permission-template-history\" onchange=\"permissionCheck('permission-template-history')\"><br>
        <br>
        <b>Account</b>
        <hr>
        <label class=\"form-check-label\" for=\"permission-account-password\">          Change Password                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-account-password\" id=\"permission-account-password\" onchange=\"permissionCheck('permission-account-password')\"><br>
        <label class=\"form-check-label\" for=\"permission-account-passwordother\">     Change Other Users' Password    </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-account-passwordother\" id=\"permission-account-passwordother\" onchange=\"permissionCheck('permission-account-passwordother')\"><br>
        <label class=\"form-check-label\" for=\"permission-account-en_dis\">            Enable / Disable Account        </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-account-en_dis\" id=\"permission-account-en_dis\" onchange=\"permissionCheck('permission-account-en_dis')\"><br>
        <label class=\"form-check-label\" for=\"permission-account-add\">               Add Account                     </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-account-add\" id=\"permission-account-add\" onchange=\"permissionCheck('permission-account-add')\"><br>
        <label class=\"form-check-label\" for=\"permission-account-delete\">            Delete Account                  </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-account-delete\" id=\"permission-account-delete\" onchange=\"permissionCheck('permission-account-delete')\"><br>
        <label class=\"form-check-label\" for=\"permission-account-editpermission\">    Edit Permission                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-account-editpermission\" id=\"permission-account-editpermission\" onchange=\"permissionCheck('permission-account-editpermission')\"><br>
        <br>
        <b>Site</b>
        <hr>
        <label class=\"form-check-label\" for=\"permission-site-settings\">    Edit site settings                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" name=\"permission-site-settings\" id=\"permission-site-settings\" onchange=\"permissionCheck('permission-site-settings')\"><br>
        </p>";
    }
    else{
        $changePassword = false;
        $changePermission = false;
        // Check whether the current user has the permission to enter this page
        if( ($_GET["user"] == $_SESSION["user"] && $currentUserPermission->permissionCheck("account", "password")) ||
            ($_GET["user"] != $_SESSION["user"] && $currentUserPermission->permissionCheck("account", "passwordother"))){
            // If they are the same user, which means the user is editing its own account
            // Change Password
            $changePassword = true;
            $changePasswordContent = "
                <b>Change Password</b>
                <hr>
                <div class=\"col-12 col-md-3\">
                <p><input type=\"text\" class=\"form-control\" name=\"password\" hidden>" . 
                (!$currentUserPermission->permissionCheck("account", "editpermission") ? "Original Password: <input type=\"password\" class=\"form-control\" name=\"password-originalPassword\"><br>" : "" ) . "
                New Password: <input type=\"password\" class=\"form-control\" name=\"password-newpassword\" id=\"newpw1\" onkeyup=\"checkPW()\"><br>
                Enter New Password Again: <input type=\"password\" class=\"form-control\" name=\"password-newpassword2\" id=\"newpw2\" onkeyup=\"checkPW()\"><br>
                <span id=\"ok\"></span><br></p>
                
            </div>";
        }
        
        if($currentUserPermission->permissionCheck("account", "editpermission")){
            // Change permission
            $changePermission = true;
            $admin = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/site.json"), true)["adminac"];
            if($_GET["user"] == $admin)
                $disabled = true;
            else
                $disabled = false;
            $changePermissionContent = "
            <p><label class=\"form-check-label\" for=\"permission-account-enabled\">Enabled</label> <input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-enabled\" name=\"permission-account-enabled\""                . (connectDB("SELECT enabled FROM user WHERE username=\"" . $_GET["user"] . "\";")[0]["enabled"]  ? "checked" : "" ) . ($disabled ? " disabled" : "") . "></p>
            <p><input type=\"text\" class=\"form-control\" name=\"permission\" hidden>
            <b>Page</b>
            <hr>
            <label class=\"form-check-label\" for=\"permission-page-add\">          Add Page                    </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-page-add\" name=\"permission-page-add\""              . ($targetUserPermission->permissionCheck("page", "add")         ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-page-add')\"><br>
            <label class=\"form-check-label\" for=\"permission-page-edit\">         Edit Page                   </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-page-edit\" name=\"permission-page-edit\""            . ($targetUserPermission->permissionCheck("page", "edit")        ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-page-edit')\"><br>
            <label class=\"form-check-label\" for=\"permission-page-pub_unpub\">    Publish / Unpublish Page    </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-page-pub_unpub\" name=\"permission-page-pub_unpub\""  . ($targetUserPermission->permissionCheck("page", "pub_unpub")   ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck()\"><br>
            <label class=\"form-check-label\" for=\"permission-page-delete\">       Delete Page                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-page-delete\" name=\"permission-page-delete\""        . ($targetUserPermission->permissionCheck("page", "delete")      ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-page-delete')\"><br>
            <label class=\"form-check-label\" for=\"permission-page-history\">      View Page History           </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-page-history\" name=\"permission-page-history\""      . ($targetUserPermission->permissionCheck("page", "history")     ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-page-history')\"><br>
            <br>
            <b>Template</b>
            <hr>
            <label class=\"form-check-label\" for=\"permission-template-add\">      Add Template            </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-template-add\" name=\"permission-template-add\""          . ($targetUserPermission->permissionCheck("template", "add")     ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-template-add')\"><br>
            <label class=\"form-check-label\" for=\"permission-template-edit\">     Edit Template           </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-template-edit\" name=\"permission-template-edit\""        . ($targetUserPermission->permissionCheck("template", "edit")    ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-template-edit')\"><br>
            <label class=\"form-check-label\" for=\"permission-template-delete\">   Delete Template         </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-template-delete\" name=\"permission-template-delete\""    . ($targetUserPermission->permissionCheck("template", "delete")  ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-template-delete')\"><br>
            <label class=\"form-check-label\" for=\"permission-template-history\">  View Template History   </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-template-history\" name=\"permission-template-history\""  . ($targetUserPermission->permissionCheck("template", "history") ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-template-history')\"><br>
            <br>
            <b>Account</b>
            <hr>
            <label class=\"form-check-label\" for=\"permission-account-password\">          Change Password                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-password\" name=\"permission-account-password\""              . ($targetUserPermission->permissionCheck("account", "password")         ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-account-password')\"><br>
            <label class=\"form-check-label\" for=\"permission-account-passwordother\">     Change Other Users' Password    </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-passwordother\" name=\"permission-account-passwordother\""    . ($targetUserPermission->permissionCheck("account", "passwordother")    ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-account-passwordother')\"><br>
            <label class=\"form-check-label\" for=\"permission-account-en_dis\">            Enable / Disable Account        </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-en_dis\" name=\"permission-account-en_dis\""                  . ($targetUserPermission->permissionCheck("account", "en_dis")           ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-account-en_dis')\"><br>
            <label class=\"form-check-label\" for=\"permission-account-add\">               Add Account                     </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-add\" name=\"permission-account-add\""                        . ($targetUserPermission->permissionCheck("account", "add")              ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-account-add')\"><br>
            <label class=\"form-check-label\" for=\"permission-account-delete\">            Delete Account                  </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-delete\" name=\"permission-account-delete\""                  . ($targetUserPermission->permissionCheck("account", "delete")           ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-account-delete')\"><br>
            <label class=\"form-check-label\" for=\"permission-account-editpermission\">    Edit Permission                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-account-editpermission\" name=\"permission-account-editpermission\""  . ($targetUserPermission->permissionCheck("account", "editpermission")   ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-account-editpermission')\"><br>
            <br>
            <b>Site</b>
            <hr>
            <label class=\"form-check-label\" for=\"permission-site-settings\">          Edit Site Settings                 </label>&nbsp;<input type=\"checkbox\" class=\"form-check-input\" id=\"permission-site-settings\" name=\"permission-site-settings\""              . ($targetUserPermission->permissionCheck("site", "settings")         ? "checked" : "" ) . ($disabled ? " disabled" : "") . " onchange=\"permissionCheck('permission-site-settings')\"><br>
            </p>";
        }
    
        if(!$changePassword && !$changePermission){
            http_response_code(403);
            $_SESSION["error"] = "403";
            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
            die();
        }
    }
    
?>

<html>
    <head>
        <script>
            function checkPW(){
                if(document.getElementById("newpw1").value != document.getElementById("newpw2").value){
                    document.getElementById("ok").innerText = "Password does not match";
                    document.getElementById("submitbtn").disabled = true;
                }
                else{
                    document.getElementById("submitbtn").disabled = false;
                    document.getElementById("ok").innerText = "Password match";
                }
            }
            function permissionCheck(checkbox){
                switch(checkbox){
                    case "permission-page-add":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-page-edit")[0].checked = true;
                        break;
                    case "permission-page-edit":
                        if(!document.getElementsByName(checkbox)[0].checked){
                            document.getElementsByName("permission-page-add")[0].checked = false;
                            document.getElementsByName("permission-page-delete")[0].checked = false;
                            document.getElementsByName("permission-page-history")[0].checked = false;
                        }
                        break;
                    case "permission-page-delete":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-page-edit")[0].checked = true;
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-page-history")[0].checked = true;
                        break;
                    case "permission-page-history":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-page-edit")[0].checked = true;
                        break;
                    case "permission-template-add":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-template-edit")[0].checked = true;
                        break;
                    case "permission-template-edit":
                        if(!document.getElementsByName(checkbox)[0].checked){
                            document.getElementsByName("permission-template-add")[0].checked = false;
                            document.getElementsByName("permission-template-delete")[0].checked = false;
                            document.getElementsByName("permission-template-history")[0].checked = false;
                        }
                        break;
                    case "permission-template-delete":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-template-edit")[0].checked = true;
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-template-history")[0].checked = true;
                        break;
                    case "permission-template-history":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-template-edit")[0].checked = true;
                        break;
                    case "permission-account-passwordother":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-account-password")[0].checked = true;
                        if(!document.getElementsByName(checkbox)[0].checked)
                          document.getElementsByName("permission-account-editpermission")[0].checked = false
                        break;
                    case "permission-account-password":
                        if(!document.getElementsByName(checkbox)[0].checked){
                            document.getElementsByName("permission-account-passwordother")[0].checked = false;
                            document.getElementsByName("permission-account-editpermission")[0].checked = false;
                        }
                        break;
                    case "permission-account-add":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-account-editpermission")[0].checked = true;
                    case "permission-account-en_dis":
                    case "permission-account-delete":
                        if(!document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-account-editpermission")[0].checked = false;
                        break;
                    case "permission-account-editpermission":
                        if(document.getElementsByName(checkbox)[0].checked)
                            document.getElementsByName("permission-account-password")[0].checked = true;
                            document.getElementsByName("permission-account-passwordother")[0].checked = true;
                            document.getElementsByName("permission-account-en_dis")[0].checked = true;
                            document.getElementsByName("permission-account-add")[0].checked = true;
                            document.getElementsByName("permission-account-delete")[0].checked = true;
                        break;
                    
                }
            }
        </script>
    </head>
    <body>
        <form action="/backend/user<?php if(isset($_GET["action"])) echo "s";?>/<?php if(!isset($_GET["action"])) echo $_GET["user"]; else echo "add"; ?>/update" method="POST">
            <?php
                if(isset($_GET["action"])){
                    echo $newUserUI;
                }
                else{
                    echo $changePasswordContent . $changePermissionContent;
                }
            ?>
            <button type="submit" class="btn btn-primary" id="submitbtn">Save</button>
        </form>
    </body>
</html>