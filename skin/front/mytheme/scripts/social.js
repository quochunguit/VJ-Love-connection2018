
var Social = function($) {
    function init() {
        if ($('.btn-login-facebook').length > 0) {
            $('.btn-login-facebook').click(facebookLogin);
        }
        if ($('.btn-login-google').length > 0) {
            $('.btn-login-google').click(googleLogin);
        }
        if ($('.btn-login-yahoo').length > 0) {
            $('.btn-login-yahoo').click(yahooLogin);
        }
    }
    function googleLogin() {
        var w = window.open(baseurl + '/user/googlelogin', 'openid_popup', 'width=450,height=500,location=1,status=1,resizable=yes');
        var coords = App.getCenteredCoords(450, 500);
        w.moveTo(coords[0], coords[1]);
    }
    function yahooLogin() {
        var w = window.open(baseurl + '/user/yahoologin', 'openid_popup', 'width=450,height=500,location=1,status=1,resizable=yes');
        var coords = App.getCenteredCoords(450, 500);
        w.moveTo(coords[0], coords[1]);
    }
    function facebookLogin() {
        var w = window.open(baseurl + '/user/facebooklogin', 'openid_popup', 'width=450,height=500,location=1,status=1,resizable=yes');
        var coords = App.getCenteredCoords(450, 500);
        w.moveTo(coords[0], coords[1]);

    }

    return {
        googleLogin: googleLogin,
        yahooLogin: yahooLogin,
        facebookLogin: facebookLogin,
        init: init
    }
}(jQuery);
jQuery(document).ready(function() {
    Social.init();
});



