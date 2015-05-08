define(function(require) {
    if (document.readyState == "complete")
    {
        init();
    }
    else
        window.addEventListener("load",function() { init(); });
    function init()
    {
        var search = document.querySelector("#quicksearch");
        search.addEventListener("keydown", function(e) {
            if (e.keyCode == 13)
            {
                var split = search.value.split(":");
                var keywords = false;
                if (split.length > 1)
                    keywords = split[0].replace(" ",".");
                Alloy.request("/search/"+(keywords ? keywords+":"+split[1] : split[0]),null);
            }
        });
    }
    
    Alloy.on("toast",function(msg) {
        var p = require("/client/scripts/popup.js");
        new p(msg);
    });
    
 });