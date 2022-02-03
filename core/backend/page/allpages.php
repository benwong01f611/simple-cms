<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    $pages = connectDB("SELECT * FROM content WHERE deleteDate IS NULL;");
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    $currentUser = new Permission($_SESSION["user"]);
?>
<script src="/resources/js/bulkAction.js"></script>
<div>
    <?php if($currentUser->permissionCheck("page", "add")) echo "<a href=\"/backend/pages/add\"><button class=\"btn btn-info btn-sm\">Add Page</button></a>"; ?>
    <?php if($currentUser->permissionCheck("page", "history")) echo "<a href=\"/backend/pages/history\"><button class=\"btn btn-info btn-sm\">Page History</button></a>"; ?>
    <form id="allpages" action="/backend/pages/bulkAction" method="POST">
        <?php
            if($currentUser->permissionCheck("page", "pub_unpub") || $currentUser->permissionCheck("page", "delete")){
                echo  "<div class=\"col-12 col-lg-1\">Bulk operation: 
                <select id=\"bulkSelect\" class=\"form-select\" name=\"bulkoption\" disabled onChange=\"document.getElementById('allpages').submit();\">
                    <option hidden disabled selected></option>";
                if($currentUser->permissionCheck("page", "pub_unpub"))
                    echo "<option value=\"publish\">Publish</option>
                    <option value=\"unpublish\">Unpublish</option>";
                if($currentUser->permissionCheck("page", "delete"))
                    echo "<option value=\"delete\">Delete</option>"; 
                echo "</select></div>";
            }
            $headers = "
                <th>Select</th>
                <th>Title</th>
                <th>Last modify date</th>
                <th>URL alias</th>
                <th>Published</th>
                <th>Public/Hidden</th>
                <th>Action</th>
            ";
            $content_rows = "";
            foreach($pages as $page){
                $content_rows .= "
                <tr>
                    <td><input class=\"selectItem form-check-input\" onChange=\"enableDisablebulk()\" type=\"checkbox\" name=\"page[]\" value=\"" . $page["id"] . "\"" . (!$currentUser->permissionCheck("page", "pub_unpub") && !$currentUser->permissionCheck("page", "delete") ? " disabled" : "") . "></td>
                    <td>" . $page["title"] . "</td>
                    <td>" . $page["lastmodifyDate"] . "</td>
                    <td>" . $page["alias"] . "</td>
                    <td>" . ($page["published"] ? "Published" : "Unpublished") . "</td>
                    <td>" . ($page["hidden"] ? "Hidden" : "Public") . "</td>
                    <td>
                        <div class=\"input-group justify-content-md-center\">
                            <a href=\"/page/" . $page["alias"] . "\"><button type=\"button\" class=\"btn btn-outline-secondary\">View</button></a>
                            <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                <span class=\"visually-hidden\"></span>
                            </button>
                            <ul class=\"dropdown-menu bg-dark\">
                                " . ($currentUser->permissionCheck("page", "edit") ? "
                                <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" href=\"/backend/page/" . $page["alias"] . "/edit\">Edit</a></li>" : "") . ($currentUser->permissionCheck("page", "delete") ? "
                                <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" href=\"/backend/page/" . $page["alias"] . "/deleteprompt\">Delete</a></li>" : "") . ($currentUser->permissionCheck("page", "pub_unpub") ? "
                                <li><a class=\"dropdown-item text-light border border-bottom-0 border-secondary\" href=\"/backend/page/" . $page["alias"] . "/publish\">Publish</a></li>
                                <li><a class=\"dropdown-item text-light border border-secondary\" href=\"/backend/page/" . $page["alias"] . "/unpublish\">Unpublish</a></li>" : "") . "   
                            </ul>
                        </div>
                    </td>
                </tr>
                ";
            }
            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/table.php");
            echo getTable($headers, $content_rows);
            ?>
    </form>
</div>