var App = App || {};

App.Admin = function() {
    var clickLogin = true;
    var login = function() {
        if (clickLogin) {
            clickLogin = false;
            $('#admin_login_loading').show();
            var $form = $('#admin-login-form');
            if ($form.length > 0) {
                var data = $form.serialize();
                $.ajax({
                    url: baseurl + '/admin/user/login',
                    dataType: 'json', type: 'post',
                    data: data,
                    success: function(res) {
                        clickLogin = true;
                        $('#admin_login_loading').hide();
                        if (res.status) {
                            if (res.url_redirect) {
                                window.location.href = res.url_redirect;
                            }
                        } else {
                            App.Popup.showInline('#admin-msg', '#admin-login-msg', res.message);
                        }
                    },
                    error: function() {
                        console.log('Error');
                    }
                });
            }
        }
    };

    var init = function() {
        var $form = $('#admin-login-form');
        if ($form.length > 0) {
            $form.find('input').bind('click keypress', function(e) {
                if (e.keyCode === 13 || e.which === 13) {
                    login();
                }
            });
        }
    };

    return {
        login: login,
        init: init
    };
}();

App.Popup = function() {
    var showAlert = function(msg) {
        alert(msg);
    };

    var timeShowMsgAction;
    var showInline = function(el, elMsg, msg, color, delay, speed) {
        var colorRed = 'red';
        color = typeof (color) !== 'undefined' ? color : colorRed;
        delay = typeof (delay) !== 'undefined' ? delay : 5000;
        speed = typeof (speed) !== 'undefined' ? speed : 500;

        var $el = $(el);
        if ($el.length > 0) {
            clearTimeout(timeShowMsgAction);
            $el.find(elMsg).html(msg).css('color', color);
            $el.slideDown(speed, function() {
                timeShowMsgAction = setTimeout(function() {
                    $el.find(elMsg).css('color', colorRed);
                    $el.slideUp(speed);
                }, delay);
            });
        }
    };

    return {
        showAlert: showAlert,
        showInline: showInline
    };
}();

$(document).ready(function() {
    App.Admin.init();
});