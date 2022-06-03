window.makeToast = function (app,message, title = 'Системное уведомление') {
    app.$bvToast.toast(message, {
        title: title,
        autoHideDelay: 5000,
        appendToast: true
    })
}
