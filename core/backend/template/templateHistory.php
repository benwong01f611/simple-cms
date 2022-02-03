<?php
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    $results = connectDB("SELECT * FROM pageTemplateHist" . (isset($_GET["templateid"]) ? "WHERE id=" . $_GET["templateid"] : "") . ";");
    
?>
<html>
    <head>
    </head>
    <body>
        <?php
            $headers = "
            <th>Template ID</th>
            <th>Revision ID</th>
            <th>Create Date</th>
            <th>Modify Date</th>
            <th>Delete Date</th>
            <th>name</th>
            <th>Action</th>
                ";
            $content_rows = "";
            foreach($results as $record){
                $content_rows .= "  <tr>
                            <td>" . $record["id"]  . "</td>
                            <td>" . $record["revid"]  . "</td>
                            <td>" . $record["createDate"]  . "</td>
                            <td>" . $record["lastmodifyDate"]  . "</td>
                            <td>" . $record["deleteDate"]  . "</td>
                            <td>" . $record["name"]  . "</td>
                            <td>
                                <div class=\"input-group justify-content-md-center\">
                                    <a href=\"/backend/templatehistory/" . $record["id"] . "-" . $record["revid"] . "\"><button type=\"button\" class=\"btn btn-outline-secondary text-light\">View</button></a>
                                    <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                        <span class=\"visually-hidden\"></span>
                                    </button>
                                    <ul class=\"dropdown-menu bg-dark\">
                                        <li><a class=\"dropdown-item text-light border border-secondary\" href=\"/backend/templatehistory/" . $record["id"] . "-" . $record["revid"] . "/revert\">Revert</a></li>
                                    </ul>
                                </div>
                            </td>
                            </tr>";
            }
            require_once($_SERVER["DOCUMENT_ROOT"] . "/core/table.php");
            echo getTable($headers, $content_rows);
            ?>
    </body>
</html>