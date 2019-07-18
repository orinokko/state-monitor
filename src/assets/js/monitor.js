function monitorAddEvent(message,priority,url,method,params,user,domain) {
    if (message === null || typeof message == "undefined")
        message = '';
    if (priority === null || typeof priority == "undefined")
        priority = 0;
    if (url === null || typeof url == "undefined")
        url = '';
    if (method === null || typeof method == "undefined")
        method = '';
    if (params === null || typeof params == "undefined")
        params = [];
    if (user === null || typeof user == "undefined")
        user = '';
    if (domain === null || typeof domain == "undefined")
        domain = '';

    var xhr = new XMLHttpRequest();

    xhr.open('POST', '/monitorAddEvent');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200 && xhr.responseText !== newName) {
            //alert('Something went wrong.  Name is now ' + xhr.responseText);
            console.log('ok');
        }
        else if (xhr.status !== 200) {
            console.error('monitor call failed! status: '+xhr.status);
        }
    };
    xhr.send(monitorParam({
        message: message,
        priority: priority,
        url: url,
        method: method,
        params: params,
        user: user,
        domain: domain,
    }));
}

function monitorParam(object) {
    var encodedString = '';
    for (var prop in object) {
        if (object.hasOwnProperty(prop)) {
            if (encodedString.length > 0) {
                encodedString += '&';
            }
            encodedString += encodeURI(prop + '=' + object[prop]);
        }
    }
    return encodedString;
}