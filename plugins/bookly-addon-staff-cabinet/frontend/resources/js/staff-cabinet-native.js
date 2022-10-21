;(function () {
    if (window.alert) {
        window.booklyNativeAlert = window.alert;
    } else {
        let iframe = document.createElement("iframe");
        document.body.appendChild(iframe);
        window.booklyNativeAlert = iframe.contentWindow.alert;
    }
})();