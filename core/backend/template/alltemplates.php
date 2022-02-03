<?php
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    $templates = connectDB("SELECT * FROM pageTemplate WHERE deleteDate IS NULL;");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    $currentUser = new Permission($_SESSION["user"]);
?>
<script src="/resources/js/bulkAction.js"></script>
<div>
    <?php
        if($currentUser->permissionCheck("template", "add"))
            echo "<a href=\"/backend/template/add\"><button class=\"btn btn-info btn-sm\">Add Template</button></a> ";
        if($currentUser->permissionCheck("template", "history"))
            echo "<a href=\"/backend/templates/history\"><button class=\"btn btn-info btn-sm\">Template History</button></a>";
    ?>
    <form id="alltemplates" action="/backend/templates/bulkAction" method="POST">
        <?php
            if($currentUser->permissionCheck("template", "delete"))
                echo "<div class=\"col-12 col-lg-1\">Bulk operation: <select id=\"bulkSelect\" class=\"form-select\" name=\"bulkoption\" disabled onChange=\"document.getElementById('alltemplates').submit();\">
                <option hidden disabled selected></option>
                <option value=\"delete\">Delete</option>
            </select></div>";
            $headers = "
                <th>Select</th>
                <th>Template Name</th>
                <th>Last modify date</th>
                <th>Action</th>
            ";
            $content_rows = "";
            foreach($templates as $template){
                $content_rows .= "
                <tr>
                    <td><input class=\"selectItem form-check-input\" onChange=\"enableDisablebulk()\" type=\"checkbox\" name=\"template[]\" value=\"" . $template["id"] . "\"" . (!$currentUser->permissionCheck("template", "delete") ? " disabled" : "") . "></td>
                    <td>" . $template["name"] . "</td>
                    <td>" . $template["lastmodifyDate"] . "</td>
                    <td>
                        <div class=\"input-group justify-content-md-center\">
                        <a href=\"/backend/template/" . $template["name"] . "\"><button type=\"button\" class=\"btn btn-outline-secondary\">View</button></a>
                        <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                            <span class=\"visually-hidden\"></span>
                        </button>
                        <ul class=\"dropdown-menu bg-dark\">
                            " . ($currentUser->permissionCheck("template", "edit") ? "
                            <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" href=\"/backend/template/" . $template["name"] . "/edit\">Edit</a></li>" : "") . "
                            <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" onclick=\"window.open('/backend/template/" . $template["name"] . "/download', '_blank'); return false;\">Download</a></li>" . ($currentUser->permissionCheck("page", "delete") ? "
                            <li><a class=\"dropdown-item text-light border border-secondary\" href=\"/backend/template/" . $template["name"] . "/deleteprompt\">Delete</a></li>" : "") . "
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