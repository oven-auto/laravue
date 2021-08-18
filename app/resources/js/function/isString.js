window.isString = function(param) {
    return (typeof param === "string" || param instanceof String);
}

window.isObject = function(param) {
    return (typeof param === "object" || param instanceof Object);
}

window.isNumeric = function(param) {
    return (!isNaN(param) && typeof param === 'number' || param instanceof Number );
}
