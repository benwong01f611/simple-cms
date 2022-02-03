<?php
    function getTable($heading, $rows){
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/templates/templates.php");
        $output = getTemplate("tables.tpl");
        $output = str_replace("{header_row}", $heading, $output);
        $output = str_replace("{content_row}", $rows, $output);
        return $output;
    }
?>