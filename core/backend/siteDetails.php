<?php
    function getSiteJson(){
        return json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/site.json"), true);
    }
?>