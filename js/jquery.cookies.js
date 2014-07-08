/*!
 * jQuery Cookie Plugin v1.4.0
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
            return config.json ? JSON.parse(s) : s;
        } catch (e) {
        }
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function (key, value, options) {

        // Write

        if (value !== undefined && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setTime(+t + days * 864e+5);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {};

        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
        var cookies = document.cookie ? document.cookie.split('; ') : [];

        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = parts.join('=');

            if (key && key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) === undefined) {
            return false;
        }

        // Must not alter options, thus extending a fresh object...
        $.cookie(key, '', $.extend({}, options, { expires: -1 }));
        return !$.cookie(key);
    };

}));

function unserialize(inp) {	// Creates a PHP value from a stored representation
    //
    // +   original by: Arpad Ray (mailto:arpad@php.net)

    error = 0;
    if (inp == "" || inp.length < 2) {
        errormsg = "input is too short";
        return;
    }
    var val, kret, vret, cval;
    var type = inp.charAt(0);
    var cont = inp.substring(2);
    var size = 0, divpos = 0, endcont = 0, rest = "", next = "";

    switch (type) {
        case "N": // null
            if (inp.charAt(1) != ";") {
                errormsg = "missing ; for null";
            }
            // leave val undefined
            rest = cont;
            break;
        case "b": // boolean
            if (!/[01];/.test(cont.substring(0, 2))) {
                errormsg = "value not 0 or 1, or missing ; for boolean";
            }
            val = (cont.charAt(0) == "1");
            rest = cont.substring(1);
            break;
        case "s": // string
            val = "";
            divpos = cont.indexOf(":");
            if (divpos == -1) {
                errormsg = "missing : for string";
                break;
            }
            size = parseInt(cont.substring(0, divpos));
            if (size == 0) {
                if (cont.length - divpos < 4) {
                    errormsg = "string is too short";
                    break;
                }
                rest = cont.substring(divpos + 4);
                break;
            }
            if ((cont.length - divpos - size) < 4) {
                errormsg = "string is too short";
                break;
            }
            if (cont.substring(divpos + 2 + size, divpos + 4 + size) != "\";") {
                errormsg = "string is too long, or missing \";";
            }
            val = cont.substring(divpos + 2, divpos + 2 + size);
            rest = cont.substring(divpos + 4 + size);
            break;
        case "i": // integer
        case "d": // float
            var dotfound = 0;
            for (var i = 0; i < cont.length; i++) {
                cval = cont.charAt(i);
                if (isNaN(parseInt(cval)) && !(type == "d" && cval == "." && !dotfound++)) {
                    endcont = i;
                    break;
                }
            }
            if (!endcont || cont.charAt(endcont) != ";") {
                errormsg = "missing or invalid value, or missing ; for int/float";
            }
            val = cont.substring(0, endcont);
            val = (type == "i" ? parseInt(val) : parseFloat(val));
            rest = cont.substring(endcont + 1);
            break;
        case "a": // array
            if (cont.length < 4) {
                errormsg = "array is too short";
                return;
            }
            divpos = cont.indexOf(":", 1);
            if (divpos == -1) {
                errormsg = "missing : for array";
                return;
            }
            size = parseInt(cont.substring(1, divpos - 1));
            cont = cont.substring(divpos + 2);
            val = new Array();
            if (cont.length < 1) {
                errormsg = "array is too short";
                return;
            }
            for (var i = 0; i + 1 < size * 2; i += 2) {
                kret = unserialize(cont, 1);
                if (error || kret[0] == undefined || kret[1] == "") {
                    errormsg = "missing or invalid key, or missing value for array";
                    return;
                }
                vret = unserialize(kret[1], 1);
                if (error) {
                    errormsg = "invalid value for array";
                    return;
                }
                val[kret[0]] = vret[0];
                cont = vret[1];
            }
            if (cont.charAt(0) != "}") {
                errormsg = "missing ending }, or too many values for array";
                return;
            }
            rest = cont.substring(1);
            break;
        case "O": // object
            divpos = cont.indexOf(":");
            if (divpos == -1) {
                errormsg = "missing : for object";
                return;
            }
            size = parseInt(cont.substring(0, divpos));
            var objname = cont.substring(divpos + 2, divpos + 2 + size);
            if (cont.substring(divpos + 2 + size, divpos + 4 + size) != "\":") {
                errormsg = "object name is too long, or missing \":";
                return;
            }
            var objprops = unserialize("a:" + cont.substring(divpos + 4 + size), 1);
            if (error) {
                errormsg = "invalid object properties";
                return;
            }
            rest = objprops[1];
            var objout = "function " + objname + "(){";
            for (key in objprops[0]) {
                objout += "" + key + "=objprops[0]['" + key + "'];";
            }
            objout += "}val=new " + objname + "();";
            eval(objout);
            break;
        default:
            errormsg = "invalid input type";
    }
    return (arguments.length == 1 ? val : [val, rest]);
}

function serialize(mixed_val) {    // Generates a storable representation of a value
    // 
    // +   original by: Ates Goral (http://magnetiq.com)
    // +   adapted for IE: Ilia Kantor (http://javascript.ru)

    switch (typeof(mixed_val)) {
        case "number":
            if (isNaN(mixed_val) || !isFinite(mixed_val)) {
                return false;
            } else {
                return (Math.floor(mixed_val) == mixed_val ? "i" : "d") + ":" + mixed_val + ";";
            }
        case "string":
            return "s:" + mixed_val.length + ":\"" + mixed_val + "\";";
        case "boolean":
            return "b:" + (mixed_val ? "1" : "0") + ";";
        case "object":
            if (mixed_val == null) {
                return "N;";
            } else if (mixed_val instanceof Array) {
                var idxobj = { idx: -1 };
                var map = [];
                for (var i = 0; i < mixed_val.length; i++) {
                    idxobj.idx++;
                    var ser = serialize(mixed_val[i]);

                    if (ser) {
                        map.push(serialize(idxobj.idx) + ser)
                    }
                }

                return "a:" + mixed_val.length + ":{" + map.join("") + "}"

            }
            else {
                var class_name = get_class(mixed_val);

                if (class_name == undefined) {
                    return false;
                }

                var props = new Array();
                for (var prop in mixed_val) {
                    var ser = serialize(mixed_val[prop]);

                    if (ser) {
                        props.push(serialize(prop) + ser);
                    }
                }
                return "O:" + class_name.length + ":\"" + class_name + "\":" + props.length + ":{" + props.join("") + "}";
            }
        case "undefined":
            return "N;";
    }

    return false;
}