var fnFullFilePathToFileParentPath = function(JSFullFilePath) {
    var JSFileParentPath = '';
    if (JSFullFilePath) {
        JSFileParentPath = JSFullFilePath.substring(0, JSFullFilePath.lastIndexOf('/') + 1);
    } else {
        JSFileParentPath = null;
    }
    return JSFileParentPath;
};

var fnExceptionToFullFilePath = function(e) {
    var JSFullFilePath = '';

    if (e.fileName) { // firefox
        JSFullFilePath = e.fileName;
    } else if (e.stacktrace) { // opera
        var tempStackTrace = e.stacktrace;
        tempStackTrace = tempStackTrace.substr(tempStackTrace.indexOf('http'));
        tempStackTrace = tempStackTrace.substr(0, tempStackTrace.indexOf('Dummy Exception'));
        tempStackTrace = tempStackTrace.substr(0, tempStackTrace.lastIndexOf(':'));
        JSFullFilePath = tempStackTrace;
    } else if (e.stack) { // firefox, opera, chrome
        (function() {
            var str = e.stack;
            var tempStr = str;

            var strProtocolSeparator = '://';
            var idxProtocolSeparator = tempStr.indexOf(strProtocolSeparator) + strProtocolSeparator.length;

            var tempStr = tempStr.substr(idxProtocolSeparator);
            if (tempStr.charAt(0) == '/') {
                tempStr = tempStr.substr(1);
                idxProtocolSeparator++;
            }

            var idxHostSeparator = tempStr.indexOf('/');
            tempStr = tempStr.substr(tempStr.indexOf('/'));

            var idxFileNameEndSeparator = tempStr.indexOf(':');
            var finalStr = (str.substr(0, idxProtocolSeparator + idxHostSeparator + idxFileNameEndSeparator));
            finalStr = finalStr.substr(finalStr.indexOf('http'));
            JSFullFilePath = finalStr;
        }());
    } else { // internet explorer
        JSFullFilePath = null;
    }

    return JSFullFilePath;
};

var fnExceptionToFileParentPath = function(e) {
    return fnFullFilePathToFileParentPath(fnExceptionToFullFilePath(e));
};

var fnGetJSFileParentPath = function() {
    try {
        throw new Error('Dummy Exception');
    } catch (e) {
        return fnExceptionToFileParentPath(e);
    }
};

var JSFileParentPath = fnGetJSFileParentPath();

function moveRight(elem) {
    var right = 0;

    function frame() {
        left++;
        elem.style.right = right + 'px';
        if (left == 50) clearInterval(id)
    }
    var id = setInterval(frame, 10)
}

function moveLeft(elem) {
    var left = 0;

    function frame() {
        left++;
        elem.style.left = left + 'px';
        if (left == 50) clearInterval(id)
    }
    var id = setInterval(frame, 10)
}

var fullUrl = document.currentScript.src;
const params = new URLSearchParams(fullUrl)
for (const param of params) {
    site_params = atob(param[1])
}

var display_type = 'none';
var rs_ary = site_params.split('&');
var tenant_id = rs_ary[0];
if (rs_ary[1] != '' && rs_ary[1] != undefined) {
    var company_ary = rs_ary[1].split('=');
    var company_id = company_ary[1];
}
if (rs_ary[2] != '' && rs_ary[2] != undefined) {
    var mode_ary = rs_ary[2].split('=');
    var mode = mode_ary[1];
    if (mode == 'open') {
        display_type = 'block';
    }
}

var domain_url = JSFileParentPath;
var live_url = domain_url + 'quote-cleaning/' + tenant_id + '/' + company_id;
document.write('<style type="text/css">#calculatorBox{position:fixed;z-index:9999999999;right:0;top:11%;width:auto}#calculatorInnerBox{display:none;float:right;width:400px;height:500px;background:#fff;border:1px solid #f7f7f7;z-index:9999999999;padding:10px 15px;border-radius:15px 0px 0px 15px;-moz-border-radius:15px 0px 0px 15px;-ms-border-radius:15px 0px 0px 15px;-o-border-radius:15px 0px 0px 15px;-webkit-border-radius:15px 0px 0px 15px;color:#fff;-webkit-box-shadow:-3px 0px 15px -2px rgba(0, 0, 0, 0.28);-moz-box-shadow:-3px 0px 15px -2px rgba(0, 0, 0, 0.28);box-shadow:-3px 0px 15px -2px rgba(0,0,0,0.28)}#calculatorBtn{float:right;-webkit-transform:rotate(90deg);-moz-transform:rotate(90deg);-ms-transform:rotate(90deg);-o-transform:rotate(90deg);transform:rotate(90deg);background-color:dc2832;border-radius:0px 0px 15px 15px;-moz-border-radius:0px 0px 15px 15px;-ms-border-radius:0px 0px 15px 15px;-o-border-radius:0px 0px 15px 15px;-webkit-border-radius:0px 0px 15px 15px;padding:10px 20px;font-size:18px;color:#fff;margin-right:-63px;margin-top:220px;text-transform:uppercase;font-weight:bold;letter-spacing:3px;cursor:pointer;font-family:Arial,Helvetica,sans-serif;background:#dc2832;}#calculatorInnerBox div{width:180px;color:#FFF;font-size:18px;background:#3DA35A;padding:3px 10px;margin-bottom:5px;text-align:left;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;}#calculatorInnerBox input[type=submit]{width:180px;color:#FFF;font-size:18px;background:#3DA35A;padding:3px 10px;margin-bottom:5px;text-align:center;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-o-border-radius:5px;-ms-border-radius:5px;margin-top:15px}#calculatorInnerBox div span{color:#fff;font-size:14px;border-radius:5px}#iframeBox{width:100%;height:100%;border:none}</style><div id="calculatorBox"><div id="calculatorInnerBox" style="display:' + display_type + ';"> <iframe id="iframeBox" src="' + live_url + '"></iframe></div><div id="calculatorBtn" onclick="showQuoteCleaningBox(\'calculatorInnerBox\')">Book Now</div></div>');

function showQuoteCleaningBox(newId) {
    if (document.getElementById(newId).style.display == 'block') {
        document.getElementById('calculatorInnerBox').style.display = 'none';
        document.getElementById('calculatorBox').style.width = 'auto';
        document.getElementById('calculatorBtn').style.marginRight = '-64px';
    } else {
        document.getElementById('calculatorInnerBox').style.display = 'block';
        document.getElementById('calculatorBox').style.width = '650px';
        document.getElementById('calculatorBtn').style.marginRight = '-63px';
    }
}