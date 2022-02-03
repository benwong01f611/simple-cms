<?php
	session_start();
	if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/data/ok")){
		header("Location: /");
		die();
	}
	$_SESSION["init"] = true;
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
            <form action="/core/init/init_db.php" method="POST">
                <div class="form-floating">
                    <input type="text" name="dbip" class="form-control bg-dark text-light" id="floatingAddress" placeholder="Database address">
                    <label for="floatingAddress" class="text-light">Database server address</label>
                </div>
                <div class="form-floating">
                    <input type="number" name="dbport" class="form-control bg-dark text-light" id="floatingPort" placeholder="Database port" min="1" max="65535" value="3306">
                    <label for="floatingPort" class="text-light">Database server port</label>
                </div>
                <div class="form-floating">
                    <input type="text" name="dbname" class="form-control bg-dark text-light" id="floatingName" placeholder="Database name">
                    <label for="floatingName" class="text-light">Database name</label>
                </div>
                <div class="form-floating">
                    <input type="text" name="dbuser" class="form-control bg-dark text-light" id="floatingUsername" placeholder="Database username">
                    <label for="floatingUsername" class="text-light">Username</label>
                </div>
                <div class="form-floating">
                    <input type="password" name="dbpw" class="form-control bg-dark text-light" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword" class="text-light">Password</label>
                </div>
                <div class="form-floating">
					<select name="dbtype"  class="form-select bg-dark text-light" id="floatingType">
						<option selected value="mysql">MySQL / MariaDB</option>
						<option value="pgsql">PostgreSQL</option>
						<option value="sqlite">SQLite</option>
						<option value="sqlsrv">Microsoft SQL Server / SQL Azure</option>
					</select>
                    <label for="floatingType" class="text-light">Database type</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Confirm</button>
            </form>
        </main>
    </body>


</html>