<?php
	session_start();
	if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/data/ok")){
		header("Location: /");
		die();
	}
?>
<html>
	<head>
		<title>Initial Setup</title>
        <script src="/resources/js/bootstrap.bundle.min.js"></script>
        <link href="/resources/css/bootstrap.min.css" rel="stylesheet">
        <link href="/resources/css/login.css" rel="stylesheet">
	</head>
	<body class="bg-dark">
		<?php
		require($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
			if(isset($_SESSION["init-error"]) && $_SESSION["init-error"] != ""){
				$toast = getTemplate("toast.tpl");
				$toast = str_replace("{toastbody}", $_SESSION["init-error"], $toast);
				$toast = str_replace("{bg}", "bg-danger", $toast);
				echo $toast;
			}
		?>
        <main class="form-signin">
            <form action="/core/init/init_site.php" method="POST">
                <div class="form-floating">
                    <input type="text" name="sitename" class="form-control bg-dark text-light" id="floatingName" placeholder="Site name">
                    <label for="floatingName" class="text-light">Site name</label>
                </div>
                <div class="form-floating">
                    <input type="text" name="adminacname" class="form-control bg-dark text-light" id="floatingUsername" placeholder="Admin username">
                    <label for="floatingUsername" class="text-light">Administrator Username</label>
                </div>
                <div class="form-floating">
                    <input type="password" name="adminacpw" class="form-control bg-dark text-light" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword" class="text-light">Administrator Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Confirm</button>
            </form>
        </main>
    </body>
</html>