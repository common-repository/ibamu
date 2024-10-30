!function(){"use strict";
    var n, s=document.createElement("script");
    s.setAttribute("data-lawwwing", "cookie-widget"),
        (s.type="text/javascript"),
        (s.async=!1),
        (s.src="https://cdn.lawwwing.com/widgets/current/" + ibamu_widget_config["ibamu_widget_uuid"] + "/cookie-widget.min.js"),
        (n = document.getElementsByTagName("script")[0]).parentNode.insertBefore(s, n);
    s.addEventListener("load", function(){
        window.Ibamu.widget({
            type: "cookies",
            wid: ibamu_widget_config["ibamu_widget_uuid"],
            key: ibamu_widget_config["ibamu_api_key"],
        })}, false);
}();
