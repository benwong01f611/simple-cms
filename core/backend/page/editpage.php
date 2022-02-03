<?php
	require_once($_SERVER["DOCUMENT_ROOT"] . "/core/session.php");
    if(!$_SESSION["login"]){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/backend/permission/permissionCheck.php");
    $currentUser = new Permission($_SESSION["user"]);
    if(!($currentUser->permissionCheck("page", "add") || $currentUser->permissionCheck("page", "edit"))){
        http_response_code(403);
        $_SESSION["error"] = "403";
        require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
        die();
    }
    require_once($_SERVER["DOCUMENT_ROOT"] . "/core/conn_db.php");
    // If previous edit has error, keep the changes and ask user to edit again
    if(isset($_SESSION["error"])){
        unset($_SESSION["error"]);
        $title = $_SESSION["data"]["title"];
        $body = $_SESSION["data"]["body"];
        $body = htmlspecialchars($body, ENT_HTML5 | ENT_QUOTES);
        $alias = $_SESSION["data"]["alias"];
        $templateid = $_SESSION["data"]["templateid"];
        $pagetitle = $title;
        $published = $_SESSION["data"]["published"];
        $hidden = $_SESSION["data"]["hidden"];
        $tags = $_SESSION["data"]["tags"];
        if($_SESSION["action"] == "add")
            $formAction = "\"/backend/page/add/save\"";
        else
            $formAction = "\"/backend/page/" . $alias . "/save\"";
    }
    else{
        // Find the page that would like to edit
        if(isset($_GET["page"]) && $_GET["page"] != ""){
            $result = connectDB("SELECT alias, title, body, id, templateid, published, tags, hidden FROM content WHERE alias = \"" . $_GET["page"] . "\";");
            if(count($result)){
                $row = $result[0];
                $alias = $row["alias"];
                $title = $row["title"];
                $body = $row["body"];
                $body = htmlspecialchars($body, ENT_HTML5 | ENT_QUOTES);
                $_SESSION["action"] = "edit";
                $_SESSION["page-id"] = $row["id"];
                $templateid = $row["templateid"];
                $pagetitle = $title;
                $published = $row["published"];
                $hidden = $row["hidden"];
                $tags = $row["tags"];
            }
            else{
                // 404, which should be impossible except someone is trying to direct access this page
                header("Location: /");
                die();
            }
            $pagetitle = $title;
            $formAction = "\"/backend/page/" . $alias ."/save\"";
            $delete = true;
        }
        else{
            // Create new page
            if(!$currentUser->permissionCheck("page", "edit")){
                http_response_code(403);
                $_SESSION["error"] = "403";
                require_once($_SERVER["DOCUMENT_ROOT"] . "/core/page/page.php");
                die();
            }
            $_SESSION["action"] = "add";
            if(!isset($_SESSION["error"]))
                $pagetitle = "New page";
            $formAction = "\"/backend/page/add/save\"";
            $published = true;
            $hidden = false;
        }
    }
    
    // Setup template select box
    $template_options = connectDB("SELECT name, id, deleteDate FROM pageTemplate;");
    $template_str = "<select name=\"template-type\"  class=\"form-control ignore-tags\">";
    foreach($template_options as $template){
        if($template["deleteDate"] == NULL){
            if(isset($templateid) && $templateid == $template["id"])
                $template_str .= "<option selected value=\"" . $template["id"] . "\">" . $template["name"] . "</option>";
            else
                $template_str .= "<option value=\"" . $template["id"] . "\">" . $template["name"] . "</option>";
        }
    }
    $template_str .= "</select>";
    unset($_SESSION["data"]);
?>

<form id="f" action=<?php echo $formAction; ?> method="POST">
    <p>
        <div class="col-12 col-md-3">
            Title:<input type="text" class="form-control" name="content-title" <?php if(isset($title)) echo "value=\"" . $title . "\""; ?>>
        </div>
    </p>
    <p>
        <div class="col-12 col-md-3">
            Alias:<input type="text" class="form-control" name="content-alias" <?php if(isset($title)) echo "value=\"" . $alias . "\""; ?>>
        </div>
    </p>
    <p>
        <div class="col-12 col-md-3">
            Template: <?php echo $template_str; ?>
        </div>
    </p>
    <p>
        <div class="col-12 col-md-3">
            <label for="validationTagsJson" class="form-label">Tags</label>
            <select class="form-select" id="validationTagsJson" name="content-tags[]" multiple data-allow-new="true" data-server="../../tags" data-live-server="1" data-server-params='{"key":"val"}'>
                <option disabled hidden value="">Choose a tag...</option>
                <?php
                    $tags_array = explode(";", $tags);
                    foreach ($tags_array as $single_tag){
                        if($single_tag != "")
                            echo "<option value=\"" . $single_tag . "\" selected=\"selected\">" . $single_tag . "</option>";
                    }
                ?>
            </select>
        </div>
    </p>
    <p>Body:<textarea name="content-body" class="editor"><?php if(isset($title)) echo $body; ?></textarea></p>
    <input type="checkbox" class="form-check-input" name="published" value="1" id="published"  <?php if($published) echo "checked";?>><label class="form-check-label" for="published">&nbsp;Published</label><br>
    <input type="checkbox" class="form-check-input" name="hidden" value="1"  id="hidden" <?php if($hidden) echo "checked";?>><label class="form-check-label" for="hidden">&nbsp;Hidden from search</label><br>
    <input type="button" onclick="save()" value="Save">
    <input type="button" onclick="window.location='/backend/pages'" value="Cancel">
    <?php if(isset($delete)) echo "<a href=\"/backend/page/" . $alias . "/deleteprompt\"><input type=\"button\" value=\"Delete\" ></a>"; ?>
    <input type="text" value="<?php echo $_SESSION["action"];?>" hidden>
</form>
<script src="/resources/ckeditor5/ckeditor.js"></script>
<script src="/resources/ckfinder3/ckfinder.js"></script>
<script src="/resources/js/cksettings.js"></script>
<script src="/resources/js/jquery.js"></script>
<script type="module" src="/resources/js/tags_html.js"></script>
<script src="/resources/js/tagsinput.js"></script>
<link href="/resources/css/tagsinput.css" rel="stylesheet">
<script>
function save(){
    // When saving with source mode on, the textarea won't update, therefore updating it manually
    if(document.getElementsByClassName("ck-source-editing-area").length == 1){
        $(".ck-button_with-text").each(function(index){
        if($(this).text() === "SourceSource")
            $(this).click();
    });
    }
    $("#f").submit();
}
</script>
