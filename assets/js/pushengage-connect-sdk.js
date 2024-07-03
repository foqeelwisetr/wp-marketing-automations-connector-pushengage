(function(w, d) {
    w.PushEngage = w.PushEngage || [];
    w._peq = w._peq || [];
    PushEngage.push(['init', {
        appId: peConnectorData?.site_key
    }]);

    var e = d.createElement('script');

    e.src = 'https://clientcdn.pushengage.com/sdks/pushengage-web-sdk.js';
    e.async = true;
    e.type = 'text/javascript';
    d.head.appendChild(e);
})(window, document);