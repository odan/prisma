/**
 * JavaScript core library
 *
 * @author odan https://github.com/odan
 * @licence MIT
 */

/**
 * Checks if the variable is empty.
 *
 * @param {*} v
 * @returns {Boolean}
 */
function isset(v) {
    if (typeof v === 'undefined' || v === null) {
        return false;
    }
    return true;
}

/**
 * Checks if the variable is empty.
 *
 * @param {*} v Value
 * @returns {Boolean}
 */
function empty(v) {

    if (typeof v === 'undefined' || v === null || v === "" || v === 0 || v === "0" || v === false) {
        return true;
    }

    if (typeof v === 'object') {
        var key;
        for (key in v) {
            return false;
        }
        return true;
    }
    return false;
}

/**
 * Check whether a variable is empty and not numeric.
 *
 * @param {*} v Value
 * @returns {Boolean}
 */
function blank(v) {
    return empty(v) && !(v === 0 || v === '0');
}

/**
 * Get value by key from object
 * Accessing nested objects with key
 * e.g. part1.name or someObject.part1.name
 *
 * @param {Object} obj
 * @param {String} key
 * @param {*} defaultValue (optional)
 * @returns {*}
 */
function gv(obj, key, defaultValue) {
    if (!isset(obj) || typeof obj !== 'object') {
        return defaultValue;
    }
    // strip a leading dot
    key = key.replace(/^\./, '');
    var a = key.split('.');
    while (a.length) {
        var n = a.shift();
        if (n in obj) {
            obj = obj[n];
        } else {
            return defaultValue;
        }
    }
    return obj;
}

/**
 * Trim.
 *
 * @param {String} s
 * @returns {String}
 */
function trim(s) {
    return $.trim(gs(s));
}

/**
 * Convert object to string (get string).
 *
 * @param {Object} o
 * @returns {String}
 */
function gs(o) {
    if (!isset(o)) {
        return '';
    }
    return o.toString();
}

/**
 * Html encoding.
 *
 * @param {String} str
 * @returns {String} html encoded string
 */
function gh(str) {
    return $d.encodeHtml(str);
}

/**
 * Returns a formatted string using the first argument as a printf-like format.
 *
 * The first argument is a string that contains zero or more placeholders.
 * Each placeholder is replaced with the converted value from its corresponding argument.
 *
 * Supported placeholders are:
 *
 * %s - String.
 * %d - Number (both integer and float).
 * %% - single percent sign ('%'). This does not consume an argument.
 *
 * Argument swapping:
 *
 * %1$s ... %n$s
 *
 * When using argument swapping, the n$ position specifier must come immediately
 * after the percent sign (%), before any other specifiers, as shown in the example below.
 *
 * If the placeholder does not have a corresponding argument, the placeholder is not replaced.
 *
 * @author odan
 *
 * @param {...*} format [, args [, ...*]
 * @returns {String}
 */
function sprintf() {
    if (arguments.length < 2) {
        return arguments[0];
    }
    var args = arguments;
    var index = 1;
    var result = (args[0] + '').replace(/%((\d)\$)?([sd%])/g, function (match, group, pos, format) {
        if (match === '%%') {
            return '%';
        }
        if (typeof pos === 'undefined') {
            pos = index++;
        }
        if (pos in args && pos > 0) {
            return args[pos];
        } else {
            return match;
        }
    });
    return result;
}

//
// Root Namespace $d
//
var $d = {};

/**
 * Global cache
 * @type object
 */
$d.cache = {};

/**
 * Config
 * @type object
 */
$d.cfg = {};

/**
 * Plugins and Functions
 * @type object
 */
$d.fn = {};

/**
 * Console logging.
 *
 * @param {String} msg
 * @returns {void}
 */
$d.log = function (msg) {
    try {
        if (!console) {
            return;
        }
        console.log(msg);
    } catch (ex) {
    }
};

/**
 * Map object recursively with callback function.
 *
 * @param {Object} obj
 * @param {callback} f callback
 * @returns {Object} mapped object
 */
$d.map = function (obj, f) {
    var result = {};
    for (var k in obj) {
        if ({}.hasOwnProperty.call(obj, k)) {
            if (typeof obj[k] === 'object') {
                result[k] = $d.map(obj[k], f);
            } else {
                result[k] = f(k, obj[k]);
            }
        }
    }
    return result;
};

/**
 * JSON ajax call.
 *
 * @param {String} method
 * @param {Object} params
 * @param {function} fncDone
 * @param {Object} settings
 * @returns {Object}
 */
$d.call = function (method, params, fncDone, settings) {
    var url = $('head base').attr('href') + 'json';
    var request = {
        method: method
    };
    if (typeof params !== 'undefined' && params !== null) {
        request.params = params;
    }

    var data = $d.encodeJson(request);

    settings = $.extend({
        type: 'POST',
        url: url,
        data: data,
        processData: false,
        dataType: 'json',
        contentType: 'application/json',
        success: function (data) {
            if (typeof fncDone === 'function') {
                fncDone(data);
            }
        }
    }, settings);

    return $.ajax(settings);
};

/**
 * Check response object for error and show error message.
 *
 * @param {Object} response
 * @returns {Boolean}
 */
$d.handleResponse = function (response) {
    var result = true;

    if (!response || (!response.error && !response.result)) {
        return false;
    }

    if ($d.hideLoad) {
        $d.hideLoad();
    }

    if (response.error) {
        result = false;
        if ($d.alert) {
            $d.alert(response.error.message);
        } else {
            alert(response.error.message);
        }
    }

    // append translations
    if (response.result && response.result.text) {
        $d.addText(response.result.text);
    }

    return result;
};

/**
 * Convert object to integer (default base = 10)
 *
 * @param {*} obj
 * @param {number} base
 * @returns {number}
 */
$d.getInt = function (obj, base) {
    var tmp;
    var type = typeof (obj);

    if (type === 'boolean') {
        return +obj;
    } else if (type === 'string') {
        tmp = parseInt(obj, base || 10);
        return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;
    } else if (type === 'number' && isFinite(obj)) {
        return obj | 0;
    } else {
        return 0;
    }
};

/**
 * Validate numbers
 *
 * @param {number} num
 * @returns {Boolean}
 */
$d.isNumeric = function (num) {
    if (!isset(num)) {
        return false;
    }
    return /^(\-)?([0-9]+|[0-9]+\.[0-9]+)$/.test(num);
};

/**
 * Check for valid date (dd.mm.yyyy)
 *
 * @param {String} str
 * @returns {Boolean}
 */
$d.isDate = function (str) {
    if (typeof str !== 'string') {
        return false;
    }
    var pattern = /^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/g;
    var match = pattern.exec(str);
    if (match === null) {
        return false;
    }
    var d = Date.parse(match[3] + '-' + match[2] + '-' + match[1]);
    var result = (typeof d === 'number' && !isNaN(d));
    return result;
};

/**
 * Check for valid email address
 *
 * @param {String} email
 * @returns {Boolean}
 */
$d.isEmail = function (email) {
    if (!isset(email)) {
        return false;
    }
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

/**
 * Escape URI parameters
 *
 * @param {String} string
 * @return {String}
 */
$d.encodeUrl = function (str) {
    str = (str + '').toString();
    str = encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
    return str;
};

/**
 * Url decoding
 *
 * @param {String} s
 * @returns {String}
 */
$d.decodeUrl = function (s) {
    s = decodeURIComponent((s + '').replace(/\+/g, '%20'));
    return s;
};

/**
 * Escape HTML characters
 *
 * @param {String} str
 * @returns {String}
 */
$d.encodeHtml = function (str) {
    if (!isset(str)) {
        return '';
    }
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    var result = str.toString().replace(/[&<>"']/g, function (m) {
        return map[m];
    });
    return result;
};

/**
 * Remove HTML end return text
 *
 * @param {String} s
 * @returns {String}
 */
$d.removeHtml = function (s) {
    if (!isset(s)) {
        return '';
    }
    return s.toString().replace(/<[^>]*>/g, '');
};

/**
 * Return UUID
 *
 * @returns {String}
 */
$d.uuid = function () {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }

    var str = s4() + '' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    return str;
};

/**
 * Base64 encoding
 *
 * @param {String} data
 * @returns {String}
 */
$d.encodeBase64 = function (data) {
    data = (data + '').toString();
    return window.btoa(unescape(encodeURIComponent(data)));
};

/**
 * Base64 decoding
 *
 * @param {String} data
 * @returns {String}
 */
$d.decodeBase64 = function (data) {
    data = (data + '').toString();
    return decodeURIComponent(escape(window.atob(data)));
};

/**
 * Json encoder.
 *
 * @param {Object} obj
 * @returns {String}
 */
$d.encodeJson = function (obj) {
    try {
        if ($.isArray(obj)) {
            throw new Error('Array is not supported');
        }
        var json = JSON.stringify(obj);
        return json;
    } catch (e) {
        $d.log(e);
        return null;
    }
};

/**
 * Json decoder.
 *
 * @param {String} str
 * @returns {Object}
 */
$d.decodeJson = function (str) {
    try {
        var obj = $.parseJSON(str);
        return obj;
    } catch (e) {
        $d.log(e);
        return null;
    }
};

/**
 * Interpolates context values into the message placeholders.
 *
 * @param {String} str
 * @param {Object} replacePairs
 * @returns {String}
 */
$d.interpolate = function (str, replacePairs) {
    var key, re;
    for (key in replacePairs) {
        if (replacePairs.hasOwnProperty(key)) {
            re = new RegExp('{' + key + '}', 'g');
            str = str.replace(re, replacePairs[key]);
        }
    }
    return str;
};

/**
 * Left padding.
 *
 * @param {String} str
 * @param {Number} length
 * @param {String} char
 * @returns {String}
 */
$d.padLeft = function (str, length, char) {
    str = String(str);
    char = char || ' ';
    while (str.length < length) {
        str = char + str;
    }
    return str;
};

/**
 * The most recent unique ID.
 *
 * @type {Number}
 */
$d.cfg.uniqueid = Math.random() * 0x80000000 | 0;

/**
 * Generates and returns a string which is unique in the current document.
 * This is useful, for example, to create unique IDs for DOM elements.
 *
 * @param {String} prefix optional
 * @return {String} A unique id.
 */
$d.createId = function (prefix) {
    prefix = prefix || '';
    var result = prefix + $d.cfg.uniqueid++;
    return result;
};

/**
 * Escape jQuery selector
 *
 * @param {String} str
 * @returns {String}
 */
$d.jq = function (str) {
    str = gs(str);
    // str = str.replace(/(:|\.|\[|\])/g, "\\$1" );
    // whitelist
    str = str.replace(/([^a-zA-Z0-9\-\_])/g, '\\$1');
    return str;
};

/**
 * Redirect browser.
 *
 * @param {String} url
 * @param {Boolean} replace
 * @returns {undefined}
 */
$d.redirect = function (url, replace) {
    if (replace === true) {
        // similar behavior as an HTTP redirect
        window.location.replace(url);
    } else {
        // similar behavior as clicking on a link
        window.location.href = url;
    }
};

/**
 * Returns absolute URL from path
 *
 * <base href="http://domain.tld/">
 * $d.getBaseUrl('contact');
 * returns http://domain.tld/contact
 *
 * @param {String} path
 * @returns {String}
 */
$d.getBaseUrl = function (path) {
    var url = '';
    if (!isset(path)) {
        path = '';
    }
    if (path && path.indexOf('/') === 0) {
        // already rooted
        return path;
    }

    // look for base url in html document: html.head.base.href
    var elBase = document.getElementsByTagName('base');
    if (!elBase || !elBase.length) {
        return path;
    }

    var baseHref = elBase[0].getAttribute('href');
    if (!baseHref) {
        return path;
    }
    url = baseHref + path;
    return url;
};

/**
 * Returns URL parameters as object.
 *
 * @returns {Object}
 */
$d.urlParams = function () {
    var search = location.search.substring(1);
    var query = search ? $.parseJSON('{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}') : {};

    query = $d.map(query, function (k, v) {
        return $d.decodeUrl(v);
    });
    return query;
};

/**
 * Returns url parameter by name.
 *
 * @param {String} name
 * @returns {String}
 */
$d.urlParam = function (name) {
    name = name.replace(/[\[]/g, "\\[").replace(/[\]]/g, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
    var results = regex.exec(window.location.search);
    results = results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    return results;
};

/**
 * Returns date in ISO format (hh:mm:ss).
 *
 * @param {Date} date
 * @returns {String|null}
 */
$d.getDate = function (date) {
    if (!$d.isValidDateObject(date)) {
        return null;
    }
    var d = $d.padLeft(date.getDate(), 2, '0');
    var m = $d.padLeft(date.getMonth() + 1, 2, '0');
    var y = date.getYear();
    if (y < 999) {
        y = y + 1900;
    }
    y = $d.padLeft(y, 4, '0');
    var sret = new String().concat(d, '.', m, '.', y);
    return sret;
};

/**
 * Returns time in ISO format (hh:mm:ss).
 *
 * @param {Date} date
 * @returns {String|null}
 */
$d.getTime = function (date) {
    if (!$d.isValidDateObject(date)) {
        return null;
    }
    var h = $d.padLeft(date.getHours(), 2, '0');
    var i = $d.padLeft(date.getMinutes(), 2, '0');
    var s = $d.padLeft(date.getSeconds(), 2, '0');
    var sret = new String().concat(h, ':', i, ':', s);
    return sret;
};

/**
 * Returns iso date-time (yyyy-mm-dd hh:mm:ss) from Date object.
 *
 * @param {Date} date
 * @returns {String|null}
 */
$d.getDateTimeIso = function (date) {
    if (!$d.isValidDateObject(date)) {
        return null;
    }
    var d = $d.padLeft(date.getDate(), 2, '0');
    var m = $d.padLeft(date.getMonth() + 1, 2, '0');
    var y = date.getYear();
    if (y < 999) {
        y = y + 1900;
    }
    y = $d.padLeft(y, 4, '0');
    var h = $d.padLeft(date.getHours(), 2, '0');
    var i = $d.padLeft(date.getMinutes(), 2, '0');
    var s = $d.padLeft(date.getSeconds(), 2, '0');
    var sret = new String().concat(y, '-', m, '-', d, ' ', h, ':', i, ':', s);
    return sret;
};

/**
 * Returns true if date is a valid Date object.
 *
 * @param {Date} date
 * @returns {Boolean}
 */
$d.isValidDateObject = function (date) {
    var result = (Object.prototype.toString.call(date) === "[object Date]"
        && isNaN(date.getTime()) === false);
    return result;
};

/**
 * Text translation (i18n)
 */
$d.cache.text = {};

/**
 * Set text array.
 *
 * @param {Object} o
 */
$d.setText = function (o) {
    $d.cache.text = o;
};

/**
 * Set text value.
 *
 * @param {String} key
 * @param {String} value
 */
$d.setTextValue = function (key, value) {
    $d.cache.text[key] = value;
};

/**
 * Set text array.
 *
 * @param {Object} o
 */
$d.addText = function (o) {
    $.extend($d.cache.text, o);
};

/**
 * Clear all text variables.
 */
$d.clearText = function () {
    $d.setText({});
};

/**
 * Translate text.
 *
 * @param {String} Message
 * @param {String|Object} ...context
 * @returns {String}
 */
function __(message) {
    if (message in $d.cache.text) {
        message = $d.cache.text[message] + '';
    }
    if (arguments.length > 1) {
        if (typeof arguments[1] === 'object') {
            // Named placeholders
            message = $d.interpolate(message, arguments[1]);
        } else {
            // sprintf placeholders (%s)
            var args = Array.prototype.slice.call(arguments);
            message = sprintf.apply(this, args);
        }
    }
    return message;
};

/**
 * Returns browser language
 *
 * @returns {String}
 */
$d.getLanguage = function () {
    var l = 'en';
    // Firefox, Chrome,...
    if (navigator.language) {
        l = navigator.language;
    }
    if (window.navigator.language) {
        l = window.navigator.language;
    }
    // IE only
    if (window.navigator.systemLanguage) {
        l = window.navigator.systemLanguage;
    }
    if (window.navigator.userLanguage) {
        l = window.navigator.userLanguage;
    }
    var s = l.substring(0, 2);
    return s;
};

/**
 * Returns user agent.
 *
 * @returns {String} chrome,ie,firefox,safari,opera or '' (another browser)
 */
$d.getBrowser = function () {
    var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
    var is_explorer = navigator.userAgent.indexOf('MSIE') > -1;
    var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
    var is_safari = navigator.userAgent.indexOf("Safari") > -1;
    var is_opera = navigator.userAgent.indexOf("Presto") > -1;

    // Chrome has both 'Chrome' and 'Safari' inside userAgent string.
    // Safari has only 'Safari'.
    if ((is_chrome) && (is_safari)) {
        is_safari = false;
    }
    if (is_chrome) {
        return 'chrome';
    }
    if (is_explorer) {
        return 'ie';
    }
    if (is_firefox) {
        return 'firefox';
    }
    if (is_safari) {
        return 'safari';
    }
    if (is_opera) {
        return 'opera';
    }
    return '';
};

/**
 * Returns the version of Internet Explorer or a -1
 * (indicating the use of another browser).
 *
 * @returns {float}
 */
$d.getIeVersion = function () {
    var num = -1;
    if (navigator.appName === 'Microsoft Internet Explorer') {
        var ua = navigator.userAgent;
        var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) !== null) {
            num = parseFloat(RegExp.$1);
        }
    }
    return num;
};

/**
 * Returns the version of Mozilla Firefox or a -1
 * (indicating the use of another browser).
 *
 * @returns {Float}
 */
$d.getFirefoxVersion = function () {
    var num = -1;
    var strAgent = navigator.userAgent.toLowerCase();
    if (strAgent.indexOf('firefox') > -1) {
        num = parseInt(strAgent.match(/firefox\/(\d+)\./)[1], 10);
    }
    return num;
};

/**
 * Returns the version of Google Chrome or a -1
 * (indicating the use of another browser).
 *
 * @returns {Float}
 */
$d.getChromeVersion = function () {
    var num = -1;
    var strAgent = navigator.userAgent.toLowerCase();
    if (strAgent.indexOf('chrome') > -1) {
        num = parseInt(strAgent.match(/chrome\/(\d+)\./)[1], 10);
    }
    return num;
};

/**
 * Returns the version of the Browser or a -1
 * (indicating the use of another browser).
 *
 * @returns {Float}
 */
$d.getBrowserVersion = function () {
    var num = -1;
    var browser = $d.getBrowser();
    if (browser === 'chrome') {
        num = $d.getChromeVersion();
    }
    if (browser === 'firefox') {
        num = $d.getFirefoxVersion();
    }
    if (browser === 'ie') {
        num = $d.getIeVersion();
    }
    return num;
};

/**
 * Download Url.
 *
 * @param {String} url
 */
$d.downloadUrl = function (url) {
    $('<iframe>', {
        src: url
    }).hide().appendTo('body').remove();
};

/**
 * Download File.
 *
 * @param {String} key
 */
$d.downloadFile = function (key) {
    var url = 'file.php?download=1&key=' + key;
    window.location.href = url;
};

/**
 * Download and try to open a file.
 *
 * @param {String} key
 */
$d.openFile = function (key) {
    var url = 'file.php?download=0&key=' + key;
    window.location.href = url;
};

/**
 * Template parser
 *
 * @syntax {placeholder|encoding}
 *
 * With placeholder you can access nested objects with string key:
 * {placeholder.part1.name}
 *
 * You can also combine a nested object key with encoding:
 * {placeholder.part1.id|url}
 *
 * The filter is optional and by default html.
 *
 * Valid encoding filters are:
 *
 * - html (default)      e.g. {placeholder|html}
 * - attr (html attribute encoding) e.g. {placeholder|attr}
 * - raw  (no encoding)  e.g. {placeholder|raw}
 * - url  (url encoding) e.g. {placeholder|url}
 *
 * @example
 *
 * // default html encoding
 * var tpl = '<div>{placeholder}</div>';
 * var data = {
 *      placeholder: 'hello world'
 * };
 *
 * // render template
 * var html = $d.template(tpl, data);
 *
 * @param {String} html html string
 * @param {Object} data
 * @returns {String}
 */
$d.template = function (html, data) {
    // interpolate replacement values into the string and return
    html = html.replace(/\{([\w\.]+)\|?(raw|html|url|attr)?}/g, function (match, key, encoding) {
        encoding = encoding || 'html';
        var v = gv(data, key, '');
        if (v === '' || encoding === 'raw') {
            return v;
        }
        if (encoding === 'html') {
            v = $d.encodeHtml(v);
        }
        if (encoding === 'url') {
            v = $d.encodeUrl(v);
        }
        if (encoding === 'attr') {
            v = $d.encodeHtml(v);
        }
        return v;
    });
    return html;
};

//------------------------------------------------------------------------------
// User Interface (UI) for Bootstrap
//------------------------------------------------------------------------------
/**
 * Show modal window
 *
 * @param {Object} config
 *
 * window.title - window caption text
 * window.body - html content
 * window.buttons - footer buttons
 *
 * @returns {Object} jquery object
 *
 * show
 * This event fires immediately when the show instance method is called.
 *
 * shown
 * This event is fired when the modal has been made visible to the user
 * (will wait for css transitions to complete).
 *
 * hide
 * This event is fired immediately when the hide instance method has been called.
 *
 * hidden
 * This event is fired when the modal has finished being hidden
 * from the user (will wait for css transitions to complete).
 *
 */
$d.window = function (config) {

    config = $.extend({
            title: false,
            body: '',
            buttons: [],
            focus: 'first'
            // width: '560px'
            // height: '400px',
            // maxheight: '400px'
        },
        config);

    var footer = '';

    // calculate width
    // if (config.width) {
    //    var value = gs(config.width);
    //    if (value.indexOf('%')) {
    //        var value = ($(window).width() / 100) * parseFloat(value) - 100;
    //        config['width'] = value + 'px';
    //    }
    // }

    // calculate height
    if (config.height) {
        var value = gs(config.height);
        if (value.indexOf('%')) {
            value = ($(window).height() / 100) * parseFloat(value) - 100;
            config['height'] = value + 'px';
        }
    }

    // calculate maxheight
    if (config.maxheight) {
        var value = gs(config.maxheight);
        if (value.indexOf('%')) {
            value = ($(window).height() / 100) * parseFloat(value) - 100;
            config['maxheight'] = value + 'px';
        }
    }

    var btnCallbacks = {};

    for (var i in config.buttons) {
        var btn = config.buttons[i];
        var strButtonText = btn['text'] || '';
        var strButtonClass = btn['class'] || 'btn';
        // modal = close window on click
        var strButtonDismiss = config.buttons[i]['dismiss'] || '';
        var strButtonId = $d.createId('d_ui_window_btn_' + i + '_');

        var tpl = '<button type="button" id="{id}" class="{class}" \
            data-dismiss="{dismiss}">{text}</button>\n';

        footer += $d.interpolate(tpl, {
            id: strButtonId,
            'class': strButtonClass,
            text: gh(strButtonText),
            dismiss: strButtonDismiss
        });

        if (typeof btn.callback === 'function') {
            btnCallbacks[strButtonId] = btn.callback;
        }

    }

    // fade importand for the 'shown' event
    var html = '';
    // http://jschr.github.io/bootstrap-modal/
    //html += '<div class="modal modal-size-{id} hide fade" style="{top}" id="{id}" tabindex="-1">\n';
    html += '<div class="modal fade" style="{top}" id="{id}" tabindex="-1">\n';
    //html += '<style>.modal-size-{id} {  }</style>\n';
    html += '<style>.modal-body-size-{id} { {height} {maxheight} }</style>\n';
    html += '<style>.modal-dialog-size-{id} { {width}  }</style>\n';
    //html += '<style>.modal-content-size-{id} { }</style>\n';
    html += '<div class="modal-dialog modal-dialog-size-{id}">\n';
    html += '<div class="modal-content">\n';

    if (config.title !== false) {
        html += '<div class="modal-header">\n';
        html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\n';
        html += '<h4 class="modal-title">{title}&nbsp;</h4>\n';
        html += '</div>\n';
    }

    html += '<div id="modal_content_div_{id}" class="modal-body modal-body-size-{id}">\n';
    //html += '{body}\n';
    html += '</div>';

    var top = (config.top) ? 'top: ' + config.top + '; ' : '';
    var width = (config.width) ? 'width: ' + config.width + '; ' : '';
    var marginLeft = (config.marginleft) ? 'margin-left: ' + config.marginleft + '; ' : '';
    var height = (config.height) ? 'height: ' + config.height + '; ' : '';
    var maxHeight = (config.maxheight) ? 'max-height: ' + config.maxheight + '; ' : '';
    var id = $d.createId('d_ui_window_');

    html = $d.interpolate(html, {
        id: id,
        title: gh(config.title),
        //body: config.body,
        top: top,
        width: width,
        marginleft: marginLeft,
        height: height,
        maxheight: maxHeight
    });

    if (!empty(footer)) {
        html += '<div class="modal-footer">';
        html += footer;
        html += '</div>';
    }

    html += '</div></div>';
    html += '</div>';
    var modal = $(html);

    // append modal content
    $(modal).find('#modal_content_div_' + id).append(config.body);

    $(modal).on('show.bs.modal', function () {
        // append button events
        $(this).find('button').each(function () {
            var strBtnId = $(this).attr('id');
            if (strBtnId in btnCallbacks) {
                $(this).on('click', function (e) {
                    e.preventDefault();
                    btnCallbacks[strBtnId](e, this);
                });
            }
        });
    });

    // init window
    $(modal).on('shown.bs.modal', function () {
        // focus the first field in modal forms
        if (config.focus === 'first') {
            $(modal).find(':text,:radio,:checkbox,select,textarea', modal).each(function () {
                if (!this.readOnly && !this.disabled && $(this).css('display') !== 'none') {
                    this.focus(); // Dom method
                    //this.select(); // Dom method
                    return false;
                }
            });
        }
    });

    $(modal).on('hidden.bs.modal', function () {
        // remove window from dom
        $(modal).remove();

        // fix the scrollbar
        if ($('body').find('.modal-backdrop').length === 0) {
            $('body').removeClass('modal-open');
        }
    });

    $(modal).data('id', id);
    return modal;
};

/**
 * Show message box (bootstrap).
 *
 * @param {Object} config
 * config.title - window title
 * config.text - content as text
 * config.html - content as html
 *
 * @param {function} callback
 * @returns {Object} window
 *
 * @example
 *
 * $d.alert('Hello World', function() {
 *     alert('ok');
 * });
 *
 * $d.alert({
 *     text: 'Test Message',
 *     title: __('Title')
 * }, function() {
 *     alert('Hello world callback');
 * });
 *
 */
$d.alert = function (config, callback) {
    var text = '';
    if (typeof config === 'string') {
        text = config;
        config = {};
    }

    config = $.extend({
            title: false,
            text: text,
            modal: {
                backdrop: 'static',
                keyboard: false,
                show: true
            }
        },
        config);

    var wnd = $d.window({
        title: config.title,
        body: config.html || gh(config.text),
        buttons: [{
            text: __('OK'),
            'class': 'btn btn-primary',
            dismiss: 'modal'
        }]
    });

    if (typeof callback === 'function') {
        // hidden
        $(wnd).on('hidden.bs.modal', function (event) {
            callback(event, wnd);
            callback = null;
        });
    }

    return $(wnd).modal(config.modal);
};

/**
 * Show prompt.
 *
 * @param {Object} config
 * @param {function} callback
 * @returns {Object} window
 *
 * @example
 *
 * $d.promt({
 *      text: 'Test Message',
 *      defaultText: '',
 *      title: __('Title'),
 *      validate: function(value) {
 *          if (!value) {
 *              return __('required');
 *          }
 *          return true;
 *      }
 * }, function(value) {
 *      alert(value);
 * });
 *
 */
$d.prompt = function (config, callback) {
    var text = '';
    if (typeof config === 'string') {
        text = config;
        config = {};
    }

    var callbackFlag = false;

    config = $.extend({
            title: false,
            text: text,
            defaultText: '',
            validate: function (value) {
                return true;
            },
            buttons: [{
                text: __('OK'),
                'class': 'btn btn-primary',
                dismiss: 'modal',
                callback: function (e) {
                    callbackFlag = true;
                }
            }, {
                text: __('Cancel'),
                'class': 'btn',
                dismiss: 'modal',
                callback: function (e) {
                    callbackFlag = false;
                }
            }],
            primary: 'ok',
            modal: {
                backdrop: 'static',
                keyboard: false,
                show: true
            }
        },
        config);

    var html = '<div class="row"><div class="col-sm-12">';
    html += '<form role="form" onsubmit="return false">';
    html += '<div class="form-group">';
    html += '<label class="col-md-12 control-label">' + gh(config.text) + '</label>';
    html += '<div class="col-md-12">';
    html += '<input type="text" class="form-control" maxlength="255" name="data[text]" value="' + gh(config.defaultText) + '">';
    html += '<p class="help-block"></p>';
    html += '</div>';
    html += '</div>';
    html += '</form>';
    html += '</div></div>';
    config.html = html;

    var wnd = $d.window({
        title: config.title,
        body: config.html || gh(config.text),
        buttons: config.buttons
    });

    $(wnd).on('hide.bs.modal', function (e) {
        if (callbackFlag === true && typeof callback === 'function') {
            var val = $(wnd).find('input[name=data\\[text\\]]').val();
            var validation = config.validate(val);
            if (validation == true) {
                callback(val, wnd);
            } else {
                e.preventDefault();
                $d.showValidation($(wnd).find('form'), {
                    'text': validation
                });
            }
        }
    });

    return $(wnd).modal(config.modal);
};

/**
 * Confirm.
 *
 * @param {Object} config
 *
 * config.title - caption text, default = false (no caption)
 * config.text - content as text
 * config.html - content as html
 * config.buttons - button codes 'ok,cancel,apply,retry,ignore,yes,no'
 * config.primary - primary button code e.g. 'ok'
 *
 * @param {function} callback
 *
 * @example
 *
 * $d.confirm('Are you sure?', function(result) {
 *      alert(result); // true or false
 * });
 *
 */
$d.confirm = function (config, callback) {
    var text = '';
    if (typeof config === 'string') {
        text = config;
        config = {};
    }

    var callbackFlag = true;

    config = $.extend({
            title: false,
            text: text,
            buttons: [{
                text: __('OK'),
                'class': 'btn btn-primary',
                dismiss: 'modal',
                callback: function (e) {
                    if (typeof callback === 'function') {
                        callbackFlag = false;
                        callback(true);
                    }
                }
            }, {
                text: __('Cancel'),
                'class': 'btn',
                dismiss: 'modal',
                callback: function (e) {
                    if (typeof callback === 'function') {
                        callbackFlag = false;
                        callback(false);
                    }
                }
            }],
            primary: 'ok',
            modal: {
                backdrop: 'static',
                keyboard: false,
                show: true
            }
        },
        config);

    var wnd = $d.window({
        title: config.title,
        body: config.html || gh(config.text),
        buttons: config.buttons
    });

    $(wnd).on('hidden.bs.modal', function () {
        if (callbackFlag === true && typeof callback === 'function') {
            callback(false, wnd);
        }
    });

    return $(wnd).modal(config.modal);
};

$d.showLoad = function () {
    $d.hideLoad();
    var html = '<div class="d-overlay"></div><div id="d_csspinner">';
    html += '<div class="csspinner no-overlay traditional"></div></div>';
    $('body').append(html);
};

$d.hideLoad = function () {
    $('#d_csspinner').remove();
    $('.d-overlay').remove();
    $('.csspinner').remove();
};

/**
 * Returns all form elements as object
 * inspired by jquery.formparams.js
 *
 * @param {Object} id
 * @returns {unresolved}
 */
$d.getForm = function (id) {
    // get form elements as array
    var arr = $(id).serializeArray();

    // Because serializeArray() ignores unset checkboxes and radio buttons
    arr = arr.concat(
        $(id).find('input[type=checkbox]:not(:checked)').map(
            function () {
                var ret = {
                    name: this.name,
                    value: 0
                };
                return ret;
            }).get());

    var obj = $d.serializeObject(arr);
    return obj;
};

/**
 * Returns selected form elements text as an object of names and values.
 *
 * @param {Object} selector
 * @returns {Object}
 */
$d.getFormText = function (selector) {
    var arr = $(selector).find('select option:selected').map(function () {
        var ret = {
            name: $(this).parent().attr('name'),
            value: $(this).text()
        };
        return ret;
    }).get();
    var obj = $d.serializeObject(arr);
    return obj;
};

/**
 * Encode a array of form elements as an object of names and values.
 *
 * @param {Array} arr
 * @returns {Object}
 */
$d.serializeObject = function (arr) {
    var o = {};
    var keyBreaker = /[^\[\]]+/g;
    $(arr).each(function (n, el) {
        var current;
        var key = el.name;
        var value = el.value;
        if (value === 'true' || value === 'false') {
            value = Boolean(value);
        }

        //make an array of values
        var parts = key.match(keyBreaker);
        var lastPart;

        // go through and create nested objects
        current = o;
        for (var i = 0; i < parts.length - 1; i++) {
            if (!current[parts[i]]) {
                current[parts[i]] = {};
            }
            current = current[parts[i]];
        }
        lastPart = parts[parts.length - 1];

        //now we are on the last part, set the value
        if (lastPart in current) {
            if (!$.isArray(current[lastPart])) {
                current[lastPart] = current[lastPart] === undefined ? [] : [current[lastPart]];
            }
            current[lastPart].push(value);
        } else if (!current[lastPart]) {
            current[lastPart] = value;
        }
    });
    return o;
};

/**
 * Get el from field name (data[name] or data[name][sub]) in form.
 *
 * @param form jquery Form element (or text selector)
 * @param fieldName name of field to look for
 * @returns objEl jquery element object
 */
$d.getField = function (form, fieldName) {
    var selector = "input[name=data{s}],select[name=data{s}],textarea[name=data{s}]";
    if (fieldName.indexOf('[') === -1) {
        fieldName = '[' + fieldName + ']';
    }
    selector = $d.interpolate(selector, {
        s: $d.jq(fieldName)
    });
    var result = $(form).find(selector);
    return result;
};

$d.getFieldName = function (el) {
    var name = $(el).attr('name');
    var fieldName = '';
    fieldName = name.replace(/^(data\[)(.*)(\])$/g, '$2');
    return fieldName;
};

/**
 * Fill form with values.
 *
 * @param {Object} options
 * options.name - Select elements by name with name[key]. default = field
 * options.data - Form values (key, value)
 * options.form - A DOM Element, Document, or jQuery to use as context
 * @returns {unresolved}
 */
$d.loadForm = function (options) {
    // overwrite default options
    options = $.extend({
            name: 'data',
            data: null,
            form: window.document
        },
        options);

    if (empty(options.data)) {
        return;
    }

    for (var key in options.data) {
        // select element by attribute (name)
        var field = $(options.form).find('[name="' + options.name + '\\[' + key + '\\]"]');

        if (!field.length) {
            continue;
        }
        var value = options.data[key];
        var type = $(field).attr('type');
        var tagName = $(field).get(0).tagName.toLowerCase();

        if (tagName === 'input') {
            if ((type === 'checkbox') || (type === 'radio')) {
                $d.setCheckedByValue(field, value, true);
            } else {
                $(field).val(value);
                // for bootstrap modal
                $(field).attr('value', value);
            }
        }
        if (tagName === 'textarea') {
            // for bootstrap modal
            $(field).html(value);
        }
        if (tagName === 'select') {
            $(field).val(value);

            // for bootstrap modal
            var optionValue = $d.jq(value);
            $(field).find("option[value='" + optionValue + "']").attr('selected', true);
        }
    }
    return $(options.form);
};

/**
 * Load table elements with data values.
 *
 * @param {String|Object} selector
 * @param {Object} data
 * @returns {jQuery}
 */
$d.loadData = function (selector, data) {
    var el = $(selector);
    if (empty(data)) {
        return null;
    }
    for (var key in data) {
        // select element by attribute (data-name)
        var field = $(el).find('[data-name="' + $d.jq(key) + '"]');
        if (!field.length) {
            continue;
        }
        $(field).html(gh(data[key]));
    }
    return el;
};

/**
 * Fill drop-down
 *
 * @param {Object} dropdown
 * @returns {unresolved}
 */
$d.loadDropdown = function (dropdown) {

    // overwrite default settings
    dropdown = $.extend({
            control: null,
            options: null,
            value: 'value',
            text: 'text',
            blank: false,
            selected_value: null,
            // Can be array (multiple select)
            selected_text: null // Same
        },
        dropdown);

    var el = $(dropdown.control);
    if (!el.length) {
        return;
    }

    // clear options
    $(el).html('');

    if (empty(dropdown.options)) {
        return;
    }

    // first option is empty
    if (dropdown.blank === true) {
        $(el).append('<option value=""></option>');
    }

    var tpl = '<option value="{value}" {selected}>{text}</option>';

    // append options
    for (var i in dropdown.options) {
        var row = dropdown.options[i];

        var value = row[dropdown.value];
        var text = row[dropdown.text];

        var boolSelected = false;

        // Check if we should selected the current element
        // Per value
        if (dropdown.selected_value !== null) {
            if ($.isArray(dropdown.selected_value)) {
                boolSelected = ($.inArray(value, dropdown.selected_value) > -1) ? true : false;
            } else {
                boolSelected = (dropdown.selected_value == value) ? true : false;
            }
        }
        // Per text
        if (dropdown.selected_text !== null) {
            if ($.isArray(dropdown.selected_text)) {
                boolSelected = ($.inArray(value, dropdown.selected_text) > -1) ? true : false;
            } else {
                boolSelected = (dropdown.selected_text == text) ? true : false;
            }
        }

        var html = $d.interpolate(tpl, {
            value: gh(value),
            text: gh(text),
            selected: boolSelected ? "selected='selected'" : ""
        });

        $(el).append(html);
    }

};

/**
 * Set checkbox/radio checked status by value
 *
 * @param {String|Object} selector
 * @param {String} value
 * @param {Boolean} checked
 * @returns {Object}
 */
$d.setCheckedByValue = function (selector, value, checked) {
    checked = typeof checked === 'undefined' ? true : checked;
    if (typeof value === 'boolean') {
        value = value ? '1' : '0';
    }
    var chk = $(selector).filter('[value=' + value + ']');
    chk.prop('checked', checked);
    chk.trigger("change");
    return chk;
};

// form and validation reset
$d.resetForm = function (id) {
    $(id).each(function () {
        // reset form inputs
        this.reset();
        // reset validation
        $d.resetValidation(this);
    });
};

// reset validation
$d.resetValidation = function (element) {
    $(element).parent().each(function () {
        $(this).find(":input").not(":button, :submit, :reset, :hidden").each(function () {
            // remove tooltip
            $(this).parent().find('.tooltip').remove();
            $(this).tooltip('destroy');

            // default border
            $(this).css('border-color', '');

            $d.setValidation(this, '', '');
        });
    });
};

/**
 * Set validation styles for errors, warning and success.
 *
 * @param {Object} selector
 * @param {String} style: success, warning, error
 * @param {String} msg message
 * @param {String} type '' or tooltip
 * @returns {undefined}
 */
$d.setValidation = function (selector, style, msg, type) {
    var obj = $(selector).closest('div');
    if (!obj.length) {
        return;
    }

    if (type === 'tooltip' && msg) {
        $(selector).tooltip({
            'title': msg
        });
    }

    $(obj).removeClass('has-error has-warning has-success');

    if (!empty(style)) {
        $(obj).addClass('has-' + style);
    }

    var help = $(obj).find('.help-block');
    if (help.length) {
        $(help).text(msg);
    }
};

/**
 * Show validation errors.
 *
 * @param {Object|String} form Form selector
 * @param {Object} errors Array with errors
 * @returns {undefined}
 */
$d.showValidation = function (form, errors) {
    if (!errors) {
        return;
    }
    $.each(errors, function (key, value) {
        var message = typeof value.message !== 'undefined' ? value.message : value;
        var field = typeof value.field !== 'undefined' ? value.field : key;
        var elField = $d.getField(form, field);
        if (elField && elField.length) {
            $d.setValidation(elField, 'error', message);
        }
    });
};

/**
 * Validate form field with attribute: required.
 *
 * @param {Object} form
 * @returns {Boolean}
 */
$d.validateRequiredFields = function (form) {
    var boolValid = true;
    $(form).find("[required='required']").each(function () {
        var elField = $(this);
        var v = '';
        var type = elField[0].type;
        if (type === 'radio' || type === 'checkbox') {
            var name = $d.jq(elField[0].name);
            var selector = "input:" + type + "[name='" + name + "']:checked";
            var checked = $(form).find(selector);
            if (checked.length > 0) {
                v = checked.val();
            }
        } else {
            v = elField.val();
        }
        if (blank(trim(v))) {
            if (boolValid) {
                $(elField).focus();
            }
            boolValid = false;
            $d.setValidation(elField, 'error', __('missing'));
        }
    });
    return boolValid;
};

/**
 * Validate field with regex.
 *
 * @param {Object} form
 * @param {String} fieldName
 * @param {Object} regex
 * @returns {Boolean}
 */
$d.validateField = function (form, fieldName, regex) {
    var result = true;
    var elField = $d.getField(form, fieldName);
    if (elField.val().match(regex) === null) {
        $d.setValidation(elField, 'error', __('invalid'));
        $(elField).focus();
        result = false;
    }
    return result;
};

/**
 * Cookie settings
 */
$d.cfg.cookie = {};
$d.cfg.cookie.expires = 1; // default 1 day
$d.cfg.cookie.path = '';
$d.cfg.cookie.domain = '';
$d.cfg.cookie.secure = false;
$d.cfg.cookie.raw = false;

/**
 * Set cookie value
 * @param {String} key
 * @param {*} value
 * @param {Object} options
 */
$d.setCookie = function (key, value, options) {

    options = $.extend({}, options);

    options.expires = gv(options, 'expires', $d.cfg.cookie.expires);

    if (typeof options.expires === 'number') {
        var days = options.expires;
        var t = options.expires = new Date();
        t.setDate(t.getDate() + days);
    }

    options.path = gv(options, 'path', $d.cfg.cookie.path);
    options.domain = gv(options, 'domain', $d.cfg.cookie.domain);
    options.secure = gv(options, 'secure', $d.cfg.cookie.secure);
    options.raw = gv(options, 'raw', $d.cfg.cookie.raw);
    value = String(value);

    var cookie = [
        encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value), options.expires ? '; expires=' + options.expires.toUTCString() : '',
        options.path ? '; path=' + options.path : '', options.domain ? '; domain=' + options.domain : '', options.secure ? '; secure' : ''].join('');

    document.cookie = cookie;
};

/**
 * Get cookie value.
 *
 * @param {String} key
 * @param {*} defaultValue
 * @param {Object} options
 * @returns {*}
 */
$d.getCookie = function (key, defaultValue, options) {
    options = options || {};
    var decode = options.raw ?
        function (s) {
            return s;
        } : decodeURIComponent;

    var pairs = document.cookie.split('; ');
    for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
        // IE saves cookies with empty string as "c; ", e.g. without "=" as
        // opposed to EOMB, thus pair[1] may be undefined
        if (decode(pair[0]) === key) {
            return decode(pair[1] || '');
        }
    }
    return defaultValue;
};

/**
 * Delete cookie
 *
 * @param {String} key
 */
$d.deleteCookie = function (key) {
    $d.setCookie(key, '', {
        expires: -1
    });
};

/**
 * JavaScript and CSS loader.
 *
 * @param {Array} array
 * @param {callback} callback
 */
$d.loadElements = function (array, callback) {
    var loader = function (element, handler) {
        var el = document.createElement(element.tag);

        for (var attr in element.attr) {
            el.setAttribute(attr, element.attr[attr]);
        }
        for (var prob in element.prob) {
            el[prob] = element.prob[prob];
        }

        // callback for external scripts
        var extern = false;
        if ('src' in element.attr) {
            extern = true;
        }
        if (extern) {
            if (el.addEventListener) {
                el.addEventListener('load', handler, false);
            } else if (el.readyState) {
                el.onreadystatechange = handler;
            }
        }
        var head = document.getElementsByTagName("head")[0];
        (head || document.body).appendChild(el);

        if (!extern) {
            handler && handler();
        }
    };

    (function () {
        if (array.length !== 0) {
            loader(array.shift(), arguments.callee);
        } else {
            callback && callback();
        }
    })();
};

/**
 * Show notify on screen.
 *
 * @param {Object} options
 *
 * Variable name    Type    Posible values    Default
 *
 * type    String    success, error, warning, info    default
 * msg    String    Message
 * position    String    left, center, right, bottom    center
 * width    Integer-String    Number > 0, 'all'    400
 * height    Integer    Number between 0 and 100    60
 * autohide    Boolean    true, false    true
 * opacity    Float    From 0 to 1    1
 * multiline    Boolean    true, false    false
 * fade    Boolean    true, false    false
 * bgcolor    String    HEX color    #444
 * color    String    HEX color    #EEE
 * timeout    Integer    Miliseconds    5000
 * zindex    Integer    The z-index of the notification    null (ignored)
 * offset    Integer    The offset in pixels from the edge of the screen    0
 *
 * @returns {undefined}
 *
 * @link https://github.com/naoxink/notifIt
 *
 * <code>
 * $d.notify({
 *    msg: "<b>Ok</b> Saved succesfully!",
 *    type: "success",
 *    position: "center"
 * });
 * </code>
 */
$d.notify = function (options) {
    options = $.extend({
        position: 'center',
        multiline: true,
        zindex: 9999999
    }, options);
    return notif(options);
};
