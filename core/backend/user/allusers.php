<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    $users = connectDB("SELECT * FROM user;");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    $currentUser = new Permission($_SESSION["user"]);
?>
<script src="/resources/js/bulkAction.js"></script>

<div>
    <?php
        if($currentUser->permissionCheck("account", "add"))
            echo "<a href=\"/backend/users/add\"><button class=\"btn btn-info btn-sm\">Add User</button></a>";
    ?>
    <form id="allusers" action="/backend/users/bulkAction" method="POST">
        <?php
            if($currentUser->permissionCheck("account", "en_dis") || $currentUser->permissionCheck("account", "delete"))
                echo  "<div class=\"col-12 col-lg-1\">Bulk operation:
                <select id=\"bulkSelect\" class=\"form-select\" name=\"bulkoption\" disabled onChange=\"document.getElementById('allusers').submit();\">
                    <option hidden disabled selected></option>";
            if($currentUser->permissionCheck("account", "en_dis"))
                echo "  <option value=\"enable\">Enable</option>
                        <option value=\"disable\">Disable</option>";
            if($currentUser->permissionCheck("account", "delete"))
                echo "<option value=\"delete\">Delete</option>";
            echo "</select></div>";
        $headers = "
            <th>Select</th>
            <th>Username</th>
            <th>Last login date</th>
            <th>Enabled</th>
            <th>Action</th>
        ";
        $content_rows = "";
        foreach($users as $user){
            $content_rows .= "
            <tr>
                <td><input class=\"selectItem form-check-input\" onChange=\"enableDisablebulk()\" type=\"checkbox\" name=\"user[]\" value=\"" . $user["username"] . "\"" . ($currentUser->permissionCheck("account", "en_dis") || $currentUser->permissionCheck("account", "delete") ? "" : "disabled") . "></td>
                <td>" . $user["username"] . "</td>
                <td>" . $user["lastLogin"] . "</td>
                <td>" . ($user["enabled"] ? "Enabled" : "Disabled") . "</td>
                <td>
                    <div class=\"input-group justify-content-md-center\">
                        <a href=\"/backend/user/" . $user["username"] . "\"><button type=\"button\" class=\"btn btn-outline-secondary\">View</button></a>
                        <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                            <span class=\"visually-hidden\"></span>
                        </button>
                        <ul class=\"dropdown-menu bg-dark\">
                            " . ($currentUser->permissionCheck("account", "passwordother") || $currentUser->permissionCheck("account", "editpermission") || ($user["username"] == $_SESSION["user"] && $currentUser->permissionCheck("account", "password")) ? "
                            <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" href=\"/backend/user/" . $user["username"] . "/manage\">Manage</a></li>" : "") . ($currentUser->permissionCheck("account", "delete") ? "
                            <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" href=\"/backend/user/" . $user["username"] . "/delete\">Delete</a></li>" : "") . ($currentUser->permissionCheck("account", "en_dis") ? "
                            <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" href=\"/backend/user/" . $user["username"] . "/enable\">Enable</a></li>
                            <li><a class=\"dropdown-item text-light border border-secondary\" href=\"/backend/user/" . $user["username"] . "/disable\">Disable</a></li>
                            " : "") . "
                        </ul>
                    </div>
                </td>
            </tr>";
        }
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/table.php");
        echo getTable($headers, $content_rows);
        ?>
    </form>
</div>