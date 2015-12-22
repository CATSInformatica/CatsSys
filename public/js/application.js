/**
 * 
 * Application Module
 */
var standardConfig = (function () {
    // your module code goes here
    var config = null;

    return {
        init: function () {
            
        },
        setConfig: function (newConfig) {
            config = newConfig;
        },
        resetConfig: function () {
            config = null;
        },
        getConfig: function () {
            return config;
        }
    };
}());

