define(function (require) {
    return Popdown;
    
    function Popdown(message,time)
    {
        time = time || 5000;
        var div = document.createElement("div");
        div.className = "popdown";
        div.innerHTML = message;
        document.body.appendChild(div);
        setTimeout(function(){
            div.parentElement.removeChild(div);
        },time);
        
        setTimeout(function() {
            window.requestAnimationFrame(function() {
                div.classList.add("down");
            });
        },100);
    }
});