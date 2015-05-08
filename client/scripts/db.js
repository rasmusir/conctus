define(function(require) {
    var recreate;
    var exports = {
        run: function() {
            recreate = document.querySelector("button[name=recreate]");
            recreate.onclick = function() {
                Alloy.fireServerEvent("/db","test",{name:"Raz"},function(response) {
                    if (response.err)
                    {
                        var popdown = require("/client/scripts/popup.js");
                        new popdown(response.err);
                    }
                    else
                    {
                        var popdown = require("/client/scripts/popup.js");
                        new popdown("Success!");
                    }
                });
            };
        }
    }
    
    return exports;
});