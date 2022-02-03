<?php
    function getTemplate($name){
        return file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/core/templates/" . $name);
    }
?>