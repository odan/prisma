/**
 * JavaScript core library
 *
 * @author odan https://github.com/odan
 * @licence MIT
 */

/**
 * Checks if the variable is empty
 * @param {mixed} v
 * @returns {Boolean}
 */
function isset(v) {
    if (typeof v === 'undefined' || v === null) {
        return false;
    }
    return true;
}

/**
 * Checks if the variable is empty
 * @param {*} v
 * @returns {boolean}
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
 * Check whether a variable is empty and not numeric
 * @param {*} v
 * @returns {boolean}
 */
function blank(v) {
    return empty(v) && !(v === 0 || v === '0');
}

/**
 * Get value by key from object
 * Accessing nested objects with key
 * e.g. part1.name or someObject.part1.name
 *
 * @param {object} obj
 * @param {string} strKey
 * @param {object} mixDefault (optional)
 * @returns {object}
 */
function gv(obj, strKey, mixDefault) {
    if (!isset(obj) || typeof obj !== 'object') {
        return mixDefault;
    }
    // strip a leading dot
    strKey = strKey.replace(/^\./, '');
    var a = strKey.split('.');
    while (a.length) {
        var n = a.shift();
        if (n in obj) {
            obj = obj[n];
        } else {
            return mixDefault;
        }
    }
    return obj;
}

/**
 * Trim
 *
 * @param {String} s
 * @returns {String}
 */
function trim(s) {
    return $.trim(gs(s));
}

/**
 * Convert object to string (get string)
 *
 * @param {object} o
 * @returns {string}
 */
function gs(o) {
    if (!isset(o)) {
        return '';
    }
    return o.toString();
}

/**
 * Html encoding
 *
 * @param {string} str
 * @returns {string} html encoded string
 */
function gh(str) {
    return $d.encodeHtml(str);
}

/**
 * Url encoding (get url)
 *
 * @param {string} str
 * @returns {string}
 */
function gu(str) {
    return $d.encodeUrl(str);
}

/**
 * HTML Attribute Encoding
 *
 * @param {string} str string to encode
 * @returns {string}
 */
function ga(str) {
    return $d.encodeHtml(str);
}

/**
 * Gettext (translation)
 *
 * @param {string} str
 * @param {object} replace
 * @returns {string}
 */
function __(str, replace) {
    return $d.getText(str, replace);
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
 * Console logging
 *
 * @param {string} msg
 * @returns {void}
 */
$d.log = function(msg) {
    try {
        if (!console) {
            return;
        }
        console.log(msg);
    } catch (ex) {
    }
};

/**
 * Map object recursively with callback function
 *
 * @param {object} obj
 * @param {callback} f callback
 * @returns {object} mapped object
 */
$d.map = function(obj, f) {
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
 * JSON-RPC 2.0 Call
 *
 * @param {string} method
 * @param {object} params
 * @param {function} fncDone
 * @param {object} settings
 * @returns {object}
 */
$d.rpc = function(method, params, fncDone, settings) {
    var url = $('head base').attr('href') + 'rpc';
    var id = 'a' + Math.floor(Math.random() * 9999999) + 1;

    var request = {
        jsonrpc: '2.0',
        id: id,
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
        success: function(data) {
            if (typeof fncDone === 'function') {
                fncDone(data);
            }
        }
    }, settings);

    return $.ajax(settings);
};

/**
 * Check response object for error and show error message
 *
 * @param {object} response
 * @returns {boolean}
 */
$d.handleResponse = function(response) {
    var boolReturn = true;

    if (!response || (!response.error && !response.result)) {
        return false;
    }

    if ($d.hideLoad) {
        $d.hideLoad();
    }

    if (response.error) {
        boolReturn = false;
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

    return boolReturn;
};

/**
 * Copy (clone) a JavaScript object
 *
 * Object, Array, Date: a copy is just a reference
 * String, Number, Boolean: a copy is a real copy (don't worry about it changing)
 *
 * $.extend doesn't create a "real" copy. Some references still exist.
 * var obj = $.extend({}, sourceobj); // don't use this
 *
 * Better use
 * var obj = $d.copy(sourceobj);
 *
 * @author A. Levy
 * @link http://stackoverflow.com/questions/728360/most-elegant-way-to-clone-a-javascript-object
 * @param {object} obj
 * @returns {obj|Date}
 */
$d.copyObject = function(obj) {
    // Handle the 3 simple types, and null or undefined
    if (obj === null || typeof obj !== 'object') {
        return obj;
    }

    // Handle Date
    if (obj instanceof Date) {
        var clone = new Date();
        clone.setTime(obj.getTime());
        return clone;
    }

    // Handle Array
    if (obj instanceof Array) {
        var clone = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            clone[i] = copy(obj[i]);
        }
        return clone;
    }

    // Handle Object
    if (obj instanceof Object) {
        var clone = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) {
                clone[attr] = copy(obj[attr]);
            }
        }
        return clone;
    }

    throw new Error("Unable to copy object! Its type isn't supported.");
};

/**
 * Return value of character
 *
 * @example
 * example 1: ord('K'); // returns 1: 75
 * example 2: ord('\uD800\uDC00'); // surrogate pair to create a single Unicode character
 * returns 2: 65536
 *
 * @param {string} str
 * @returns {number}
 */
$d.ord = function(str) {

    if (!isset(str)) {
        return 0;
    }
    str = gs(str);
    var code = str.charCodeAt(0);

    if (0xD800 <= code && code <= 0xDBFF) {
        var hi = code;
        if (str.length === 1) {
            return code;
        }
        var low = str.charCodeAt(1);
        return ((hi - 0xD800) * 0x400) + (low - 0xDC00) + 0x10000;
    }
    if (0xDC00 <= code && code <= 0xDFFF) {
        return code;
    }
    return code;
};

/**
 * Return a specific character
 *
 * @param {string} code
 * @returns {string}
 */
$d.chr = function(code) {
    if (!isset(code)) {
        return null;
    }
    var ret = null;
    if (code > 0xFFFF) {
        code -= 0x10000;
        ret = String.fromCharCode(0xD800 + (code >> 10), 0xDC00 + (code & 0x3FF));
    } else {
        ret = String.fromCharCode(code);
    }
    return ret;
};

/**
 * Convert object to integer (default base = 10)
 *
 * @param {*} obj
 * @param {number} base
 * @returns {number}
 */
$d.getInt = function(obj, base) {
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
 * Check for unsigned integer
 *
 * @param {number} num
 * @returns {boolean}
 */
$d.isInt = function(num) {
    if (!isset(num)) {
        return false;
    }
    return (num.toString().search(/^[0-9]+$/) === 0);
};

/**
 * Check for signed integer
 *
 * @param {number} num
 * @returns {boolean}
 */
$d.isInteger = function(num) {
    if (!isset(num)) {
        return false;
    }
    if (num === ~~num) {
        return true;
    }
    return (num.toString().search(/^-?[0-9]+$/) === 0);
};

/**
 * Check for signed float
 * http://stackoverflow.com/questions/3941052
 *
 * @param {number} num
 * @returns {boolean}
 */
$d.isFloat = function(num) {
    if (!isset(num)) {
        return false;
    }
    return +num === num && (!isFinite(num) || !!(num % 1));
};

/**
 * Validate numbers
 *
 * @param {number} num
 * @returns {boolean}
 */
$d.isNumeric = function(num) {
    if (!isset(num)) {
        return false;
    }
    return /^(\-)?([0-9]+|[0-9]+\.[0-9]+)$/.test(num);
};

/**
 * Check for valid date (dd.mm.yyyy)
 *
 * @param {string} str
 * @returns {boolean}
 */
$d.isDate = function(str) {
    if (typeof str !== 'string') {
        return false;
    }
    var pattern = /^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/g;
    var match = pattern.exec(str);
    if (match === null) {
        return false;
    }
    var d = Date.parse(match[3] + '-' + match[2] + '-' + match[1]);
    var boolReturn = (typeof d === 'number' && !isNaN(d));
    return boolReturn;
};

/**
 * Check for valid email address
 *
 * @param {string} email
 * @returns {boolean}
 */
$d.isEmail = function(email) {
    if (!isset(email)) {
        return false;
    }
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

/**
 * Escape URI parameters
 *
 * @param {string} string
 * @return {string}
 */
$d.encodeUrl = function(str) {
    str = (str + '').toString();
    str = encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
            replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
    return str;
};

/**
 * Escape HTML tag attribute
 *
 * @param {String} str
 * @returns {String}
 */
$d.encodeAttr = function(str) {
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

    str = str.toString().replace(/[&<>"']/g, function(m) {
        return map[m];
    });

    return str;
};

/**
 * Url decoding
 *
 * @param {string} s
 * @returns {string}
 */
$d.decodeUrl = function(s) {
    s = decodeURIComponent((s + '').replace(/\+/g, '%20'));
    return s;
};

/**
 * Escape HTML characters
 *
 * @param {string} str
 * @returns {string}
 */
$d.encodeHtml = function(str) {
    if (!isset(str)) {
        return '';
    }
    str = gs(str);
    str = str.replace(/[^a-z0-9A-Z ]/g, function(c) {
        return '&#' + c.charCodeAt() + ';';
    });
    return str;
};

/**
 * Remove HTML end return text
 *
 * @param {string} s
 * @returns {string}
 */
$d.removeHtml = function(s) {
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
$d.uuid = function() {
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
$d.encodeBase64 = function(data) {
    data = (data + '').toString();
    if (window.btoa) {
        // native btoa with native utf-8 encoding
        return window.btoa(unescape(encodeURIComponent(data)));
    }
    // fallback
    var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
            ac = 0,
            enc = '',
            tmp_arr = [];

    if (!data) {
        return data;
    }

    data = unescape(encodeURIComponent(data));

    do {
        // pack three octets into four hexets
        o1 = data.charCodeAt(i++);
        o2 = data.charCodeAt(i++);
        o3 = data.charCodeAt(i++);

        bits = o1 << 16 | o2 << 8 | o3;

        h1 = bits >> 18 & 0x3f;
        h2 = bits >> 12 & 0x3f;
        h3 = bits >> 6 & 0x3f;
        h4 = bits & 0x3f;

        // use hexets to index into b64, and append result to encoded string
        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);

    enc = tmp_arr.join('');

    var r = data.length % 3;

    return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
};

/**
 * Base64 decoding
 *
 * @param {String} data
 * @returns {String}
 */
$d.decodeBase64 = function(data) {
    data = (data + '').toString();
    if (window.atob) {
        // native
        return decodeURIComponent(escape(window.atob(data)));
    }
    // fallback
    var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
            ac = 0,
            dec = '',
            tmp_arr = [];

    if (!data) {
        return data;
    }

    data += '';

    do {
        // unpack four hexets into three octets using index points in b64
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));

        bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

        o1 = bits >> 16 & 0xff;
        o2 = bits >> 8 & 0xff;
        o3 = bits & 0xff;

        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);
        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);

    dec = tmp_arr.join('');

    return decodeURIComponent(escape(dec.replace(/\0+$/, '')));
};

$d.encodeUtf8 = function(s) {
    s = (s + '').toString();
    return unescape(encodeURIComponent(s));
};

$d.decodeUtf8 = function(s) {
    s = (s + '').toString();
    return decodeURIComponent(escape(s));
};

/**
 * Json encoder
 *
 * @param {object} obj
 * @returns {string}
 */
$d.encodeJson = function(obj) {
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
 * Json decoder
 *
 * @param {string} str
 * @returns {object}
 */
$d.decodeJson = function(str) {
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
 * @param {string} str
 * @param {object} replacePairs
 * @returns {string}
 */
$d.interpolate = function(str, replacePairs) {
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
 * Left padding
 *
 * @param {string} str
 * @param {number} nLen
 * @param {string} sChar
 * @returns {string}
 */
$d.padLeft = function(str, nLen, sChar) {
    str = String(str);
    sChar = sChar || ' ';
    while (str.length < nLen) {
        str = sChar + str;
    }
    return str;
};

/**
 * The most recent unique ID.
 *
 * @type {number}
 */
$d.cfg.uniqueid = Math.random() * 0x80000000 | 0;

/**
 * Generates and returns a string which is unique in the current document.
 * This is useful, for example, to create unique IDs for DOM elements.
 *
 * @param {string} strPrefix optional
 * @return {string} A unique id.
 */
$d.createId = function(strPrefix) {
    strPrefix = strPrefix || '';
    var strReturn = strPrefix + $d.cfg.uniqueid++;
    return strReturn;
};

/**
 * Escape jQuery selector
 *
 * @param {string} str
 * @returns {string}
 */
$d.jq = function(str) {
    str = gs(str);
    // str = str.replace(/(:|\.|\[|\])/g, "\\$1" );
    // whitelist
    str = str.replace(/([^a-zA-Z0-9\-\_])/g, '\\$1');
    return str;
};

/**
 * Redirect browser
 *
 * @param {string} strUrl
 * @param {boolean} boolReplace
 * @returns {undefined}
 */
$d.redirect = function(strUrl, boolReplace) {
    if (boolReplace === true) {
        // similar behavior as an HTTP redirect
        window.location.replace(strUrl);
    } else {
        // similar behavior as clicking on a link
        window.location.href = strUrl;
    }
};


/**
 * Returns absolute URL from path
 *
 * <base href="http://domain.tld/">
 * $d.getBaseUrl('contact');
 * returns http://domain.tld/contact
 *
 * @param {String} sPath
 * @returns {String}
 */
$d.getBaseUrl = function(sPath) {

    var sUrl = '';

    if (!isset(sPath)) {
        sPath = '';
    }

    if (sPath && sPath.indexOf('/') === 0) {
        // allread rooted
        return sPath;
    }

    // look for base url in html document: html.head.base.href
    var elBase = document.getElementsByTagName('base');

    if (!elBase || !elBase.length) {
        return sPath;
    }

    var sBaseHref = elBase[0].getAttribute('href');

    if (!sBaseHref) {
        return sPath;
    }

    // baseurl + path
    sUrl = sBaseHref + sPath;
    return sUrl;
};

/**
 * Returns URL parameters as object
 *
 * @returns {Object}
 */
$d.urlParams = function() {
    var search = location.search.substring(1);
    var query = search ? $.parseJSON('{"' + search.replace(/&/g, '","').
            replace(/=/g, '":"') + '"}') : {};

    query = $d.map(query, function(k, v) {
        return $d.decodeUrl(v);
    });
    return query;
};

/**
 * Returns url parameter by name
 *
 * @param {string} name
 * @returns {string}
 */
$d.urlParam = function(name) {
    name = name.replace(/[\[]/g, "\\[").replace(/[\]]/g, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
    var results = regex.exec(window.location.search);
    results = results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    return results;
};

/**
 * Returns date in ISO format (hh:mm:ss)
 *
 * @param {Date} date
 * @returns {string|null}
 */
$d.getDate = function(date) {
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
 * Returns time in ISO format (hh:mm:ss)
 *
 * @param {Date} date
 * @returns {string|null}
 */
$d.getTime = function(date) {
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
 * Returns iso date-time (yyyy-mm-dd hh:mm:ss) from Date object
 *
 * @param {Date} date
 * @returns {String|null}
 */
$d.getDateTimeIso = function(date) {
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
 * Returns true if date is a valid Date object
 *
 * @param {Date} date
 * @returns {Boolean}
 */
$d.isValidDateObject = function(date) {
    var boolReturn = (Object.prototype.toString.call(date) === "[object Date]"
            && isNaN(date.getTime()) === false);
    return boolReturn;
};

/**
 * Text translation (i18n)
 */
$d.cache.text = {};

/**
 * Set text array
 *
 * @param {object} o
 */
$d.setText = function(o) {
    $d.cache.text = o;
};

/**
 * Set text value
 *
 * @param {string} strKey
 * @param {string} strValue
 */
$d.setTextValue = function(strKey, strValue) {
    $d.cache.text[strKey] = strValue;
};

/**
 * Set text array
 *
 * @param {object} o
 */
$d.addText = function(o) {
    $.extend($d.cache.text, o);
};

/**
 * Clear all text variables
 */
$d.clearText = function() {
    $d.setText({});
};

/**
 * Get Text
 *
 * @param {string} strMessage
 * @param {object} objReplace
 * @returns {string}
 */
$d.getText = function(strMessage, objReplace) {
    var strReturn = strMessage;
    if (empty($d.cache.text)) {
        // Placeholder
        if (!empty(objReplace)) {
            strReturn = $d.interpolate(strReturn, objReplace);
        }
        return strReturn;
    }

    if (strMessage in $d.cache.text) {
        strReturn = $d.cache.text[strMessage] + '';
    }

    // Placeholder
    if (!empty(objReplace)) {
        strReturn = $d.interpolate(strReturn, objReplace);
    }
    return strReturn;
};


/**
 * Returns browser language
 *
 * @returns {string}
 */
$d.getLanguage = function() {
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
 * Returns user agent
 *
 * @returns {string} chrome,ie,firefox,safari,opera or '' (another browser)
 */
$d.getBrowser = function() {

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
 * Returns true if browser
 *
 * @param {string} browser
 * @returns {boolean}
 */
$d.isBrowser = function(browser) {
    return $d.getBrowser() === browser;
};

/**
 * Returns the version of Internet Explorer or a -1
 * (indicating the use of another browser).
 *
 * @returns {float}
 */
$d.getIeVersion = function() {
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
 * @returns {float}
 */
$d.getFirefoxVersion = function() {
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
 * @returns {float}
 */
$d.getChromeVersion = function() {
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
 * @returns {float}
 */
$d.getBrowserVersion = function() {
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
 * Download Url
 *
 * @param {string} strUrl
 */
$d.downloadUrl = function(strUrl) {
    $('<iframe>', {
        src: strUrl
    }).hide().appendTo('body').remove();
};

/**
 * Download File
 *
 * @param {string} strKey
 */
$d.downloadFile = function(strKey) {
    var strUrl = 'file.php?download=1&key=' + strKey;
    window.location.href = strUrl;
};

/**
 * Download and try to open a file
 *
 * @param {string} strKey
 */
$d.openFile = function(strKey) {
    var strUrl = 'file.php?download=0&key=' + strKey;
    window.location.href = strUrl;
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
 * var strTpl = '<div>{placeholder}</div>';
 * var data = {
 *      placeholder: 'hello world'
 * };
 *
 * // render template
 * var strHtml = $d.template(strTpl, data);
 *
 * @param {string} strHtml html string
 * @param {object} data
 * @returns {string}
 */
$d.template = function(strHtml, data) {
    // interpolate replacement values into the string and return
    strHtml = strHtml.replace(/\{([\w\.]+)\|?(raw|html|url|attr)?}/g, function(match, key, encoding) {
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
            v = $d.encodeAttr(v);
        }
        return v;
    });
    return strHtml;
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
$d.window = function(config) {

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

    var strFooter = '';

    // calculate width
    /*
     if (config.width) {
     var strValue = gs(config.width);
     if (strValue.indexOf('%')) {
     var numValue = ($(window).width() / 100) * parseFloat(strValue) - 190;
     config['width'] = numValue + 'px';
     }
     }*/

    // calculate height
    if (config.height) {
        var strValue = gs(config.height);
        if (strValue.indexOf('%')) {
            var numValue = ($(window).height() / 100) * parseFloat(strValue) - 190;
            config['height'] = numValue + 'px';
        }
    }

    // calculate maxheight
    if (config.maxheight) {
        var strValue = gs(config.maxheight);
        if (strValue.indexOf('%')) {
            var numValue = ($(window).height() / 100) * parseFloat(strValue) - 190;
            config['maxheight'] = numValue + 'px';
        }
    }

    var objBtnCallbacks = {};

    for (var i in config.buttons) {

        var btn = config.buttons[i];
        var strButtonText = btn['text'] || '';
        var strButtonClass = btn['class'] || 'btn';
        // modal = close window on click
        var strButtonDismiss = config.buttons[i]['dismiss'] || '';
        var strButtonId = $d.createId('d_ui_window_btn_' + i + '_');

        var strTpl = '<button type="button" id="{id}" class="{class}" \
            data-dismiss="{dismiss}">{text}</button>\n';

        strFooter += $d.interpolate(strTpl, {
            id: strButtonId,
            'class': strButtonClass,
            text: gh(strButtonText),
            dismiss: strButtonDismiss
        });

        if (typeof btn.callback === 'function') {
            objBtnCallbacks[strButtonId] = btn.callback;
        }

    }

    // fade importand for the 'shown' event
    var strHtml = '';
    // http://jschr.github.io/bootstrap-modal/
    //strHtml += '<div class="modal modal-size-{id} hide fade" style="{top}" id="{id}" tabindex="-1">\n';
    strHtml += '<div class="modal fade" style="{top}" id="{id}" tabindex="-1">\n';
    //strHtml += '<style>.modal-size-{id} {  }</style>\n';
    strHtml += '<style>.modal-body-size-{id} { {height} {maxheight} }</style>\n';
    strHtml += '<style>.modal-dialog-size-{id} { {width}  }</style>\n';
    //strHtml += '<style>.modal-content-size-{id} { }</style>\n';
    strHtml += '<div class="modal-dialog modal-dialog-size-{id}">\n';
    strHtml += '<div class="modal-content">\n';

    if (config.title !== false) {
        strHtml += '<div class="modal-header">\n';
        strHtml += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\n';
        strHtml += '<h4 class="modal-title">{title}&nbsp;</h4>\n';
        strHtml += '</div>\n';
    }

    strHtml += '<div id="modal_content_div_{id}" class="modal-body modal-body-size-{id}">\n';
    //strHtml += '{body}\n';
    strHtml += '</div>';

    var strTop = (config.top) ? 'top: ' + config.top + '; ' : '';
    var strWidth = (config.width) ? 'width: ' + config.width + '; ' : '';
    var strMarginLeft = (config.marginleft) ? 'margin-left: ' + config.marginleft + '; ' : '';
    var strHeight = (config.height) ? 'height: ' + config.height + '; ' : '';
    var strMaxHeight = (config.maxheight) ? 'max-height: ' + config.maxheight + '; ' : '';
    var strId = $d.createId('d_ui_window_');

    strHtml = $d.interpolate(strHtml, {
        id: strId,
        title: gh(config.title),
        //body: config.body,
        top: strTop,
        width: strWidth,
        marginleft: strMarginLeft,
        height: strHeight,
        maxheight: strMaxHeight
    });

    if (!empty(strFooter)) {
        strHtml += '<div class="modal-footer">';
        strHtml += strFooter;
        strHtml += '</div>';
    }

    strHtml += '</div></div>';
    strHtml += '</div>';
    var modal = $(strHtml);

    // append modal content
    $(modal).find('#modal_content_div_' + strId).append(config.body);

    $(modal).on('show.bs.modal', function() {
        // append button events
        $(this).find('button').each(function() {
            var strBtnId = $(this).attr('id');
            if (strBtnId in objBtnCallbacks) {
                $(this).on('click', function(e) {
                    e.preventDefault();
                    objBtnCallbacks[strBtnId](e, this);
                });
            }
        });
    });

    // init window
    $(modal).on('shown.bs.modal', function() {

        // focus the first field in modal forms
        if (config.focus === 'first') {
            $(modal).find(':text,:radio,:checkbox,select,textarea', modal).each(function() {
                if (!this.readOnly && !this.disabled && $(this).css('display') !== 'none') {
                    this.focus(); // Dom method
                    //this.select(); // Dom method
                    return false;
                }
            });
        }

    });

    $(modal).on('hidden.bs.modal', function() {

        // remove window from dom
        $(modal).remove();

        // fix the scrollbar
        if ($('body').find('.modal-backdrop').length === 0) {
            $('body').removeClass('modal-open');
        }

    });

    $(modal).data('id', strId);

    return modal;
    //return $(modal).modal(options);
};

/**
 * show message box (bootstrap)
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
$d.alert = function(config, callback) {

    var strText = '';
    if (typeof config === 'string') {
        strText = config;
        config = {};
    }

    config = $.extend({
        title: false,
        text: strText,
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
        $(wnd).on('hidden.bs.modal', function(event) {
            callback(event, wnd);
            callback = null;
        });
    }

    return $(wnd).modal(config.modal);
};

/**
 * Confirm
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
$d.confirm = function(config, callback) {

    var strText = '';
    if (typeof config === 'string') {
        strText = config;
        config = {};
    }

    var boolCallbackFlag = true;

    config = $.extend({
        title: false,
        text: strText,
        buttons: [{
                text: __('OK'),
                'class': 'btn btn-primary',
                dismiss: 'modal',
                callback: function(e) {
                    if (typeof callback === 'function') {
                        boolCallbackFlag = false;
                        callback(true);
                    }
                }
            }, {
                text: __('Cancel'),
                'class': 'btn',
                dismiss: 'modal',
                callback: function(e) {
                    if (typeof callback === 'function') {
                        boolCallbackFlag = false;
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

    $(wnd).on('hidden.bs.modal', function() {
        if (boolCallbackFlag === true && typeof callback === 'function') {
            callback(false, wnd);
        }
    });

    return $(wnd).modal(config.modal);
};

$d.showLoad = function() {
    $d.hideLoad();
    var strHtml = '<div class="d-overlay"></div><div id="d_csspinner">';
    strHtml += '<div class="csspinner no-overlay traditional"></div></div>';
    $('body').append(strHtml);
};

$d.hideLoad = function() {
    $('#d_csspinner').remove();
    $('.d-overlay').remove();
    $('.csspinner').remove();
};

/**
 * Show print preview window
 *
 * @param {object} config
 * @param {function} callback
 * @returns {object} window
 *
 * @example
 *
 * // open window with url
 * $d.showFile({'url': 'file.pdf'});
 *
 * // open window with FileStorage key
 * $d.showFile({'key': data.result.key});
 *
 * // with callback onclose (hide)
 * $d.showFile({'key': data.result.key}, function() {
 *     alert('closed');
 * });
 *
 * // download file with FileStorage key
 * $d.downloadFile(data.result.key);
 *
 */
$d.showFile = function(config, callback) {

    var boolIsChrome = $d.isBrowser('chrome');

    config = $.extend({
        url: '',
        key: null,
        // pdf viewer: external, internal
        pdfviewer: 'external',
        pdfpage: 0,
        popup: false,
        modal: {
            backdrop: 'static',
            keyboard: false,
            show: true
        }
    },
    config);

    var strUrl = config.url;

    if (config.key) {
        strUrl = 'file.php?download=0&key=' + config.key;
    }

    // pdf parameters
    // http://www.adobe.com/content/dam/Adobe/en/devnet/acrobat/pdfs/pdf_open_parameters_v9.pdf#page=6&zoom=150,0,216
    var arrAnchor = [];
    if (config.pdfviewer === 'external') {
        // force firefox and ie to use Adobe Reader. Chrome doesn't care.
        arrAnchor.push('toolbar=1');
    }
    if (config.pdfpage > 0) {
        // in chrome the fist page = 0
        if (boolIsChrome === true) {
            config.pdfpage--;
        }
        arrAnchor.push('page=' + config.pdfpage);
    }
    if (arrAnchor.length > 0) {
        strUrl += '#' + arrAnchor.join('&');
    }

    if (config.popup === true) {
        // print preview with popup
        var popup = window.open(strUrl, '_blank');
        if (typeof callback === 'function') {
            // will not work in IE
            popup.onbeforeunload = callback;
        }
        return;
    }

    // <embed> works in Chrome+FF but not in >=IE10
    // <object> works in IE>9
    // <iframe> works perfect in Chrome, bad in FF and all IE versions
    var strHtml = '<iframe id="iframe_print" onload="javascript:$d.hideLoad();this.focus();" src="{url}" style="width:99%; height:99%;" frameborder="0"></iframe>';

    if ($d.getIeVersion() >= 9) {
        // In IE 9 and 10 print will only work with object tag
        strHtml = '<object id="iframe_print" style="position:absolute;z-index:-1;width:98%; height:99%;" classid="clsid:CA8A9780-280D-11CF-A24D-444553540000"><param name="src" value="{url}" /></object>';
    }

    strHtml = $d.interpolate(strHtml, {
        url: strUrl
    });

    var arrButtons = [];

    // print button is not supported in firefox >= 19
    // FF users should use the pdf.js integrated print button
    if ($d.getFirefoxVersion() === -1) {
        arrButtons.push({
            text: __('Print'),
            'class': 'btn btn-primary',
            callback: function(event) {
                var iframe = document.getElementById('iframe_print');
                if ($d.getIeVersion() >= 9) {
                    iframe.focus();
                    iframe.print();
                } else {
                    iframe.focus();
                    iframe.contentWindow.print();
                }
            }
        });
    }

    arrButtons.push({
        text: __('Cancel'),
        event: 'cancel',
        dismiss: 'modal'
    });

    var wnd = $d.window({
        title: config.title || __('Print'),
        body: strHtml,
        width: '92%',
        height: '100%',
        maxheight: '100%',
        top: '0%',
        buttons: arrButtons
    });

    if (typeof callback === 'function') {
        $(wnd).on('hidden.bs.modal', function(event) {
            callback(event, wnd);
            callback = null;
        });
    }

    return $(wnd).modal(config.modal);

};

/**
 * Returns all form elements as object
 * inspired by jquery.formparams.js
 *
 * @param {object} id
 * @returns {unresolved}
 */
$d.getForm = function(id) {
    // get form elements as array
    var arr = $(id).serializeArray();

    // Because serializeArray() ignores unset checkboxes and radio buttons
    arr = arr.concat(
            $(id).find('input[type=checkbox]:not(:checked)').map(
            function() {
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
 * Returns selected form elements text as an object of names and values
 *
 * @param {object} selector
 * @returns {object}
 */
$d.getFormText = function(selector) {
    var arr = $(selector).find('select option:selected').map(function() {
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
 * @param {array} arr
 * @returns {object}
 */
$d.serializeObject = function(arr) {
    var o = {};
    var keyBreaker = /[^\[\]]+/g;
    $(arr).each(function(n, el) {

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
 * Get el from field name (data[name] or data[name][sub]) in objForm
 *
 * @param objForm jquery Form element (or text selector)
 * @param strFieldName name of field to look for
 * @returns objEl jquery element object
 */
$d.getField = function(objForm, strFieldName) {
    var strSel = "input[name=data{s}],select[name=data{s}],textarea[name=data{s}]";
    if (strFieldName.indexOf('[') === -1) {
        strFieldName = '[' + strFieldName + ']';
    }
    strSel = $d.interpolate(strSel, {
        s: $d.jq(strFieldName)
    });
    var result = $(objForm).find(strSel);
    return result;
};

$d.getFieldName = function(el) {
    var strName = $(el).attr('name');
    var strFieldName = '';
    strFieldName = strName.replace(/^(data\[)(.*)(\])$/g, '$2');
    return strFieldName;
};

/**
 * Fill form with values
 *
 * @param {Object} options
 * options.name - Select elements by name with name[key]. default = field
 * options.data - Form values (key, value)
 * options.form - A DOM Element, Document, or jQuery to use as context
 * @returns {unresolved}
 */
$d.loadForm = function(options) {

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

    for (var strKey in options.data) {

        // select element by attribute (name)
        var field = $(options.form).find('[name="' + options.name + '\\[' + strKey + '\\]"]');

        if (!field.length) {

            if (typeof options.data[strKey] === 'object') {
                // search for table with data-source
                field = $(options.form).find('table[data-datasource="' + strKey + '"]');

                if (field.length) {
                    // fill table
                    $d.loadTable({
                        name: strKey,
                        control: field,
                        rows: options.data[strKey]
                    });
                }
            }
            continue;
        }
        var strValue = options.data[strKey];
        var strType = $(field).attr('type');
        var strTagName = $(field).get(0).tagName.toLowerCase();

        if (strTagName === 'input') {
            if ((strType === 'checkbox') || (strType === 'radio')) {
                $d.setCheckedByValue(field, strValue, true);
            } else {
                $(field).val(strValue);
                // for bootstrap modal
                $(field).attr('value', strValue);
            }
        }

        if (strTagName === 'textarea') {
            // for bootstrap modal
            $(field).html(strValue);
        }

        if (strTagName === 'select') {
            $(field).val(strValue);

            // for bootstrap modal
            var strOptionValue = $d.jq(strValue);
            $(field).find("option[value='" + strOptionValue + "']").attr('selected', true);
        }
    }

    return $(options.form);
};

/**
 * Load table
 *
 * @param {object} options
 * @returns {object}
 */
$d.loadTable = function(options) {

    // overwrite default settings
    options = $.extend({
        name: 'data',
        control: null,
        rows: null
    },
    options);

    var el = $(options.control);
    if (!el.length) {
        return;
    }

    // remove rows
    $(el).find('tbody').html('');

    if (empty(options.rows)) {
        return;
    }

    var strTableId = $(el).attr('id');
    var strNewRow = $d.decodeBase64($("#" + strTableId).attr('data-newrow'));
    var numRow = parseInt($(el).find('tr:last').attr('data-row'), 10) + 1;
    if (isNaN(numRow)) {
        numRow = 0;
    }

    // fill rows
    for (var i in options.rows) {
        var row = options.rows[i];
        //console.log(row);
        var strNewRow2 = strNewRow.replace(/{row}/g, numRow);
        var elRow = $(strNewRow2);

        $d.loadForm({
            name: options.name + '[' + numRow + ']',
            form: elRow,
            data: row
        });

        $(el).find("tbody:last").append(elRow);
        numRow++;
    }

    /*
     var boolDifference = $(el).attr('data-difference') === '1';

     if(boolDifference) {
     $(el).find(":input").each(function () {
     $(this).on('change', function() {
     $(this).attr('data-status', 'updated');
     });
     });
     }*/

    return $(el);
};

/**
 * Fill drop-down
 *
 * @param {object} dropdown
 * @returns {unresolved}
 */
$d.loadDropdown = function(dropdown) {

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

    var strTpl = '<option value="{value}" {selected}>{text}</option>';

    // append options
    for (var i in dropdown.options) {
        var row = dropdown.options[i];

        var strValue = row[dropdown.value];
        var strText = row[dropdown.text];

        var boolSelected = false;

        // Check if we should selected the current element
        // Per value
        if (dropdown.selected_value !== null) {
            if ($.isArray(dropdown.selected_value)) {
                boolSelected = ($.inArray(strValue, dropdown.selected_value) > -1) ? true : false;
            } else {
                boolSelected = (dropdown.selected_value == strValue) ? true : false;
            }
        }
        // Per text
        if (dropdown.selected_text !== null) {
            if ($.isArray(dropdown.selected_text)) {
                boolSelected = ($.inArray(strValue, dropdown.selected_text) > -1) ? true : false;
            } else {
                boolSelected = (dropdown.selected_text == strText) ? true : false;
            }
        }

        var strHtml = $d.interpolate(strTpl, {
            value: gh(strValue),
            text: gh(strText),
            selected: boolSelected ? "selected='selected'" : ""
        });

        $(el).append(strHtml);
    }

};

/**
 * Set checkbox/radio checked status by value
 *
 * @param {object} el
 * @param {string} strName
 * @param {string} strValue
 * @param {boolean} boolChecked
 * @returns {object}
 */
$d.setCheckedByValue = function(selector, strValue, boolChecked) {
    boolChecked = typeof boolChecked === 'undefined' ? true : boolChecked;
    if (typeof strValue === 'boolean') {
        strValue = strValue ? '1' : '0';
    }
    var chk = $(selector).filter('[value=' + strValue + ']');
    chk.prop('checked', boolChecked);
    chk.trigger("change");
    return chk;
};

// return all form values as json string
$d.getFormJson = function(id) {
    var o = $d.getForm(id);
    var s = $d.encodeJson(o);
    return s;
};

// form and validation reset
$d.resetForm = function(id) {
    $(id).each(function() {

        // reset form inputs
        this.reset();

        // reset validation
        $d.resetValidation(this);
    });
};

// reset validation
$d.resetValidation = function(element) {
    $(element).parent().each(function() {
        $(this).find(":input").not(":button, :submit, :reset, :hidden").each(function() {

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
 * Set validation styles for errors, warning and success
 *
 * @param {object} selector
 * @param {string} style success, warning, error
 * @param {string} msg message
 * @param {string} type '' or tooltip
 * @returns {undefined}
 */
$d.setValidation = function(selector, style, msg, type) {
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
 * Show validation results
 *
 * @param {object} form
 * @returns {undefined}
 */
$d.showValidation = function(form, validation) {
    if (!validation) {
        return;
    }
    for (var name in validation) {
        var strMessage = validation[name];
        var elField = $d.getField(form, name);
        if (elField && elField.length) {
            $d.setValidation(elField, 'error', strMessage);
        }
    }
};

/**
 * Validate form field with attribute: required
 *
 * @param {Object} form
 * @returns {Boolean}
 */
$d.validateRequiredFields = function(form) {
    var boolValid = true;
    $(form).find("[required='required']").each(function() {
        var elField = $(this);
        var v = '';
        var strType = elField[0].type;
        if (strType === 'radio' || strType === 'checkbox') {
            var strName = $d.jq(elField[0].name);
            var strSel = "input:" + strType + "[name='" + strName + "']:checked";
            var checked = $(form).find(strSel);
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
 * Validate field with regex
 *
 * @param {object} form
 * @param {string} strFieldName
 * @param {object} regex
 * @returns {boolean}
 */
$d.validateField = function(form, strFieldName, regex) {
    var boolReturn = true;
    var elField = $d.getField(form, strFieldName);
    if (elField.val().match(regex) === null) {
        $d.setValidation(elField, 'error', __('invalid'));
        $(elField).focus();
        boolReturn = false;
    }
    return boolReturn;
};

/**
 * Cookie
 */

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
 * @param {string} key
 * @param {*} value
 * @param {object} options
 */
$d.setCookie = function(key, value, options) {

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

    var arrCookie = [
        encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value), options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
        options.path ? '; path=' + options.path : '', options.domain ? '; domain=' + options.domain : '', options.secure ? '; secure' : ''].join('');

    document.cookie = arrCookie;
};

/**
 * Get cookie value
 *
 * @param {string} key
 * @param {*} defaultValue
 * @param {object} options
 * @returns {*}
 */
$d.getCookie = function(key, defaultValue, options) {

    options = options || {};
    var decode = options.raw ?
            function(s) {
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
 * @param {string} key
 */
$d.deleteCookie = function(key) {
    $d.setCookie.set(key, '', {
        expires: -1
    });
};

//------------------------------------------------------------------------------
// Single Page Web App
//------------------------------------------------------------------------------

/**
 * Change page
 *
 * @param {String} selector
 * @param {String} strPage page id
 * @param {Object} pageParams page parameter (optional)
 * @returns {Boolean}
 */
$d.setPage = function(selector, strPage, pageParams) {

    $d.showLoad();

    var el = $(selector);

    if (!el.length) {
        $d.log('Warning: setPage selector not found');
        return false;
    }

    // trigger event: page.onchange
    var eventParams = {
        page: strPage,
        params: pageParams,
        selector: selector,
        element: $(el),
        change: true
    };
    $(el).trigger('page.beforechange', eventParams);

    // check if possible to change the page
    // triggers return value (change)
    if (!eventParams.change) {
        return false;
    }

    var nextPage = {
        page: strPage,
        selector: selector,
        params: pageParams
    };

    // load page content
    $d.rpc(strPage + '.getPageContent', nextPage, function(response) {
        if (response && response.result) {

            // set html,css and js content into dom
            $d.setPageContent(el, response.result, pageParams);
        }

        // check for errors
        if (response && response.error) {
            if ($d.alert) {
                $d.alert(response.error.message);
            } else {
                alert(response.error.message);
            }
        }
    });
};

/**
 * Set content into element (selector) and remove old dynamic elements
 *
 * @param {string} selector
 * @param {object} content
 * @param {object} params
 * @param {boolean} boolClear default=true (optional)
 * @returns {undefined}
 */
$d.setPageContent = function(selector, content, params, boolClear) {

    var el = $(selector);
    boolClear = (isset(boolClear)) ? boolClear : true;

    if (boolClear === true) {
        // remove page elements
        $('*[data-page="1"]').each(function() {
            $(this).remove();
        });
    }

    // scroll to top
    window.scrollTo(0, 0);

    // set new html content and autoload js
    $(el).html(content.html);

    if (content.elements) {
        $d.loadElements(content.elements, function() {
            // trigger event if everything is loaded
            $(el).trigger('page.init', params);
        });
    } else {
        $(el).trigger('page.init', params);
    }
};

/**
 * Event handler for page controller
 *
 * @param {string} selector
 * @param {string} strEvent
 * @param {function} callback
 */
$d.onPage = function(selector, strEvent, callback) {
    $(selector).on(strEvent, function(e, p) {
        $(this).off(strEvent);
        if (callback) {
            callback(p, e);
        }
    });
};

/**
 * Script and CSS loader
 *
 * @param {array} array
 * @param {callback} callback
 */
$d.loadElements = function(array, callback) {

    var loader = function(element, handler) {

        var el = document.createElement(element.tag);

        for (var attr in element.attr) {
            el.setAttribute(attr, element.attr[attr]);
        }

        for (var prob in element.prob) {
            el[prob] = element.prob[prob];
        }

        // callback for external scripts
        var boolExtern = false;
        if ('src' in  element.attr) {
            boolExtern = true;
        }
        if (boolExtern) {
            if (el.addEventListener) {
                el.addEventListener('load', handler, false);
            } else if (el.readyState) {
                el.onreadystatechange = handler;
            }
        }
        var head = document.getElementsByTagName("head")[0];
        (head || document.body).appendChild(el);

        if (!boolExtern) {
            handler && handler();
        }

    };

    (function() {
        if (array.length !== 0) {
            loader(array.shift(), arguments.callee);
        } else {
            callback && callback();
        }
    })();
};

/**
 * Show notify on screen
 *
 * @param {object} options
 *
 * Variable name	Type	Posible values	Default
 *
 * type	String	success, error, warning, info	default
 * msg	String	Message
 * position	String	left, center, right, bottom	center
 * width	Integer-String	Number > 0, 'all'	400
 * height	Integer	Number between 0 and 100	60
 * autohide	Boolean	true, false	true
 * opacity	Float	From 0 to 1	1
 * multiline	Boolean	true, false	false
 * fade	Boolean	true, false	false
 * bgcolor	String	HEX color	#444
 * color	String	HEX color	#EEE
 * timeout	Integer	Miliseconds	5000
 * zindex	Integer	The z-index of the notification	null (ignored)
 * offset	Integer	The offset in pixels from the edge of the screen	0
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
$d.notify = function(options) {
    options = $.extend({
        position: 'center',
        multiline: true,
        zindex: 9999999
    }, options);
    return notif(options);
};
