<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/siteDetails.php");
    $sitename = getSiteJson()["sitename"];
?>
<html>
    <head>
        <title>Login - <?php echo $sitename;?></title>
        <script src="/resources/js/bootstrap.bundle.min.js"></script>
        <link href="/resources/css/bootstrap.min.css" rel="stylesheet">
        <link href="/resources/css/login.css" rel="stylesheet">
    </head>

<?php
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if($_SESSION["login"]){
        header("Location: /backend/user/" . $_SESSION["user"]);
        die();
    }
    if(isset($_POST["username"]) && isset($_POST["pw"])){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
        $_POST["username"] = str_replace("\"", "\\\"", $_POST["username"]);
        $result = connectDB("SELECT pw, enabled FROM user WHERE username = \"" . $_POST["username"] . "\";");
        if(count($result)){
            // If the password match, log the user in
            if(password_verify($_POST["pw"], $result[0][0])){
                if(!$result[0]["enabled"]){
                    // If the account is disabled, return account disabled message
                    $_SESSION["error"] = "disabled";
                }
                else{
                    $_SESSION["login"] = true;
                    $_SESSION["user"] = $_POST["username"];
                    unset($_SESSION["error"]);
                    connectDB("UPDATE user SET lastLogin=NOW() WHERE username=\"" . $_POST["username"] . "\";");
                    header("Location: /backend/user/" . $_POST["username"]);
                    die();
                }
            }
            else{
                // Incorrect password
                $_SESSION["error"] = "log_cred_invalid";
            }
        }
        else{
            // No user found
            $_SESSION["error"] = "log_cred_invalid";
        }
    }
    // If the user cannot log on, display error messages
    if(isset($_SESSION["error"])){
        switch($_SESSION["error"]){
            case "log_cred_invalid":
                $err = "Incorrect username or password, please try again";
                break;
            case "disabled":
                $err = "Account disabled, please contact administrator.";
                break;
        }
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
        $toast = getTemplate("toast.tpl");
        $toast = str_replace("{toastbody}", $err, $toast);
        $toast = str_replace("{bg}", "bg-danger", $toast);
        echo $toast;
    }
    unset($_SESSION["error"]);
?>


    <body class="bg-dark">
        <main class="form-signin">
            <form action="/login" method="POST">
                <div class="form-floating">
                    <input type="text" name="username" class="form-control bg-dark text-light" id="floatingInput" placeholder="user">
                    <label for="floatingInput" class="text-light">Username</label>
                </div>
                <div class="form-floating">
                    <input type="password" name="pw" class="form-control bg-dark text-light" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword" class="text-light">Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
            </form>
        </main>
    </body>
</html>