<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    $results = connectDB("SELECT * FROM pagehist" . (isset($_GET["pageid"]) ? "WHERE id=" . $_GET["pageid"] : "") . ";");
    
?>
<html>
    <head>
        <script src="/resources/js/bulkActionHistory.js"></script>
    </head>
    <body>
        <?php
            $headers = "
                    <th>Page ID</th>
                    <th>Revision ID</th>
                    <th>Create Date</th>
                    <th>Modify Date</th>
                    <th>Delete Date</th>
                    <th>Alias</th>
                    <th>Title</th>
                    <th>Template ID</th>
                    <th>Published</th>
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
                            <td>" . $record["alias"]  . "</td>
                            <td>" . $record["title"]  . "</td>
                            <td>" . $record["templateid"]  . "</td>
                            <td>" . $record["published"] . "</td>
                            <td>
                                <div class=\"input-group justify-content-md-center\">
                                    <a href=\"/backend/pagehistory/" . $record["id"] . "-" . $record["revid"] . "\"><button type=\"button\" class=\"btn btn-outline-secondary text-light\">View</button></a>
                                    <button type=\"button\" class=\"btn btn-outline-secondary dropdown-toggle dropdown-toggle-split\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                        <span class=\"visually-hidden\"></span>
                                    </button>
                                    <ul class=\"dropdown-menu bg-dark\">
                                        <li><a class=\"dropdown-item text-light border border-secondary\" href=\"/backend/pagehistory/" . $record["id"] . "-" . $record["revid"] . "/revert\">Revert</a></li>
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