window.isString = function(param) {
    return (typeof param === "string" || param instanceof String);
}

window.isObject = function(param) {
    return (typeof param === "object" || param instanceof Object);
}

window.isNumeric = function(param) {
    return (!isNaN(param) && typeof param === 'number' || param instanceof Number );
}

window.isEmptyObject = function(obj) {
    for (var i in obj) {
        if (obj[i]>0 || obj[i]!='') {
            return false;
        }
    }
    return true;
}
window.getConfig = function() {
    return {
        'content-type': 'multipart/form-data'
    }
}
