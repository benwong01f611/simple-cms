
function singleaction(item, type){
    switch(document.getElementById(item).value){
        case "view":
            window.open("/backend/" + type + "history/" + item, "_blank");
            break;
        case "revert":
            window.location = "/backend/" + type + "history/" + item + "/revert";
            break;
    }
}