function enableDisablebulk(){
    var cbox = document.getElementsByClassName("selectItem");
    for(var i = 0; i < cbox.length; i++){
        if(cbox[i].checked){
            document.getElementById("bulkSelect").disabled = false;
            return;
        }
    }
    document.getElementById("bulkSelect").disabled = true;;
}
function singleaction(item, title, type){
    if(type == "user"){
        switch(document.getElementById(item).value){
            case "view":
                window.location = "/backend/user/" + title;
                break;
            case "edit":
                window.location = "/backend/user/" + title + "/manage";
                break;
            case "delete":
                window.location = "/backend/user/" + title + "/delete";
                break;
            case "enable":
                window.location = "/backend/user/" + title + "/enable";
                break;
            case "disable":
                window.location = "/backend/user/" + title + "/disable";
                break;
            case "":
                break;
        }
    }
    else{
        switch(document.getElementById(item).value){
            case "view":
                if(type == "template")
                    window.location = "/backend/" + type + "/" + title;
                else
                    window.location = "/" + type + "/" + title;
                break;
            case "edit":
                window.location = "/backend/" + type + "/" + title + "/edit";
                break;
            case "delete":
                window.location = "/backend/" + type + "/" + title + "/deleteprompt";
                break;
            case "publish":
                window.location = "/backend/" + type + "/" + title + "/publish";
                break;
            case "unpublish":
                window.location = "/backend/" + type + "/" + title + "/unpublish";
                break;
        }
    }
    
}