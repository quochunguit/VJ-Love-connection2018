var App = App || {};

//---MAIN----
$(function () {
    App.Site.init();
    App.Contact.init(); 
    App.Subscribe.init();
    App.VoteShare.init();
    
    if($(".btn-login-facebook").length > 0 || $(".fb-share").length > 0){App.Facebook.init();}
    App.Google.init();
    setTimeout(function(){
        var checkFg = $('#show-fg').val();
        if(checkFg){
            helperJs.bzOpenPopup({items: { src: '#pop-recover' } });
        }
        ; }, 1000);

});

//--All site
App.Site = function(){

    var init = function(){
        changeLang();
        forgotpass();
        formValidate();
        recoverpass();
    };

    var forgotpass = function(){
        $(".btn-forgotpass").click(function(){
            $.ajax({
                url: baseurl + "/"+languageShort+"/user-forget-pass",
                type: 'post',
                dataType: 'json',
                data: {
                    email:$('#fgemail').val()
                },
                success: function (res) {

                    helperJs.bzClosePopup();
                    helperJs.bzOpenPopup(
                        {items:
                        { src: '#pop-alert'},
                            beforeOpen(){
                        $('#pop-alert > div > p').text(res.message);
                    },
                    afterClose(){
                        location.href ='/';

                    }
                }
            );
                }
            });
        });
    }

    var recoverpass = function(){
        $(".btn-recovertpass").click(function(){
            $.ajax({
                url: baseurl +"/"+languageShort+"/user-recover",
                type: 'post',
                dataType: 'json',
                data: {
                    password:$('#rcvpass').val(),
                    cfpassword:$('#re-rcvpass').val(),
                    forget_pass_code:$('#show-fg').val(),
                },
                success: function (res) {
                    helperJs.bzClosePopup();
                    helperJs.bzOpenPopup(
                        {items:
                        { src: '#pop-alert'},
                            beforeOpen(){
                        $('#pop-alert > div > p').text(res.message);
                    },
                    afterClose(){
                        setTimeout(function(){ App.Popup.openLogin(); }, 1000);

                    }
                }
            );
                }
            });
        });
    }

    var changeLang = function(selectChangeLang){
        var $elChaneLang = $(selectChangeLang);
        if($elChaneLang.length>0){
            $elChaneLang.change(function(){
                var urlChange = $(this).val();
                App.Debug.consoleLog(urlChange);
                if(urlChange!==''){
                    window.location.href = urlChange;
                }
            });
        }
    };

    var showUserLogin = function(userid, username){
        $('li.login').html('<a href="javascript:void(0);" onclick="App.Site.userLogout('+userid+');" class="popup-is-open" data-htmlclass="html-popup-content"><i class="icomoon icon-login" aria-hidden="true"></i> '+username+'</a>');
        $('li.send-contest a').attr({'onclick': 'App.Site.goToContestSubmit('+userid+')'});
    }

    var userLogout = function(userId){
        $.ajax({
            url: baseurl+"/user-logout",
            dataType: 'json',
            data: {
                user_id:userId
            },
            type:'post',
            success: logoutSuccess
        });
    };

    var logoutSuccess = function(res){
        if(res.status){
            location.reload();
        }else{
            console.log(res.message);
        }

    }

    var goToContestSubmit = function(userId){
        //check user login
        $.ajax({
            url: baseurl+"/user-profile",
            dataType: 'json',
            data: {
                user_id:userId
            },
            type:'post',
            success: function(res){
                if(res.status){
                    //go to contest submit page
                    window.location.href = baseurl + '/' + languageShort + '/contest-submit';
                }else{
                        //alert(res.message);
                        if(res.phone != ''){
                            App.Popup.openResendCode();
                            $('#resend-phone').val(res.phone);
                            $('#resend-location').val(res.location + '_' + res.phone.substr(0, 2));
                        }else{
                            //show update phone popup
                            App.Popup.openUpdatePhone();
                        }


                }
            }
        });
    }

    var formValidate = function(){
        //register form
        var register_validate = $("form[name='register-form']").validate({
            onkeyup: false,
            onfocusout: false,
            // Specify validation rules
            rules: {
                regname: "required",
                regphone: {
                    required: true,  // <-- no such method called "matches"!
                    minlength:5,

                },
                regemail: {
                    required: true,
                    // Specify that email should be validated
                    // by the built-in "email" rule
                    email: true,
                },
                regpassword: {
                    required: true,
                    minlength: 6
                },
                regrepassword: {
                    minlength: 6,
                    equalTo : '[name="regpassword"]'
                }
            },
            // Specify validation error messages
            messages: {
                regname : trans_Validatename,
                regphone: {
                    required: trans_Validatephone,
                    minlength: trans_Validatecharacter5
                },
                regemail: trans_Validatevalidemail,
                regpassword: {
                    required: trans_Validatepassword,
                    minlength: trans_Validatecharacter6
                },
                regrepassword: {
                    equalTo: trans_Validatenotmatched,
                    minlength: trans_Validatecharacter6
                }
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid

            errorPlacement: function(error, element) {
                var pa_element = element.closest('.form-group');
                pa_element.addClass('error');

                element = element.next().children();
                element.text(error.text());
            },
            unhighlight: function(element, errorClass, validClass) {
                var pa_element = $(element).closest('.form-group');
                pa_element.removeClass('error');
            },
            submitHandler: function(form) {
                var reg_name = $('#reg-name').val();
                var reg_phone = $('#reg-phone').val();
                var country_code = $('#reg-location').val();
                var location = country_code.split('_')[0];
                var phone_code = country_code.split('_')[1];
                var reg_email = $('#reg-email').val();
                var reg_password = $('#reg-password').val();
                var reg_repassword = $('#reg-repassword').val();

                $.ajax({
                    url: baseurl+"/"+languageShort+"/user-register",
                    dataType: 'json',
                    data: {
                        'name': reg_name,
                        'phone': reg_phone,
                        'email': reg_email,
                        'phonecode': phone_code,
                        'location': location,
                        'password': reg_password,
                        'cfpassword': reg_repassword
                    },
                    type:'post',
                    success: function(res){

                        if(res.status){
                            helperJs.bzOpenPopup(
                                {items:
                                { src: '#pop-alert'},
                                    beforeOpen(){
                                    $('#pop-alert > div > p').text(res.message);
                                    $('#pop-alert > div > div > button').text(res.button);
                                    },
                                    afterClose(){

                                        setTimeout(function(){ showActivationFillCode(res.data); }, 1000);

                                    }
                                }
                            );

                        }else{
                            var elementError = res.element;
                            var error = res.message;
                            var errorArray = {};
                            var element = elementError;
                            errorArray[element] = error;
                            register_validate.showErrors(errorArray);
                    }
                    }
                });
                return false;
            }
        });

        //login form
        var login_validate = $("form[name='login-form']").validate({
            onkeyup: false,
            onfocusout: false,
            // Specify validation rules
            rules: {
                loginemail: {
                    required: true,
                    // Specify that email should be validated
                    // by the built-in "email" rule
                    email: true
                },
                loginpassword: {
                    required: true,
                    minlength: 6
                }
            },

            // Specify validation error messages
            messages: {
                loginpassword: {
                    required: trans_Validatepassword,
                    minlength: trans_Validatecharacter6
                },
                loginemail: trans_Validatevalidemail
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid

            errorPlacement: function(error, element) {
                var pa_element = element.closest('.form-group');
                pa_element.addClass('error');

                element = element.next().children();
                element.text(error.text());
            },
            unhighlight: function(element, errorClass, validClass) {
                var pa_element = $(element).closest('.form-group');
                pa_element.removeClass('error');
            },
            submitHandler: function(form) {
                var login_email = $('#login-email').val();
                var login_password = $('#login-password').val();

                $.ajax({
                    url: baseurl+"/user-login",
                    dataType: 'json',
                    data: {
                        'email': login_email,
                        'password': login_password
                    },
                    type:'post',
                    success: function(res){
                        if(res.status){
                            location.reload();
                        }else{
                            if(res.status_key == 'inactive'){
                                App.Site.showUserLogin(res.id, res.name);

                                //alert(res.message);
                                if(res.phone != ''){
                                    App.Popup.openResendCode();
                                    $('#resend-phone').val(res.phone);
                                    $('#resend-location').val(res.location + '_' + res.phone.substr(0, 2));
                                }else{
                                    //show update phone popup
                                    App.Popup.openUpdatePhone();
                                }

                            }else{
                                $('#login-error').html(trans_LoginFailed);
                            }
                        }
                    }
                });
                return false;
            }
        });

        //update phone form
        var updatephone_validate = $("form[name='updatephone-form']").validate({
            onkeyup: false,
            onfocusout: false,
            // Specify validation rules
            rules: {
                phonenumber: {
                    required: true,  // <-- no such method called "matches"!
                    minlength:5
                }
            },

            // Specify validation error messages
            messages: {
                phonenumber: {
                    required: trans_Validatephone,
                    minlength: trans_Validatecharacter5
                }
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid

            errorPlacement: function(error, element) {
                var pa_element = element.closest('.form-group');
                pa_element.addClass('error');

                element = element.next().children();
                element.text(error.text());
            },
            unhighlight: function(element, errorClass, validClass) {
                var pa_element = $(element).closest('.form-group');
                pa_element.removeClass('error');
            },
            submitHandler: function(form) {
                var phone = $('#phone-number').val();
                var country_code = $('#location').val();
                var location = country_code.split('_')[0];
                var phone_code = country_code.split('_')[1];

                if(phone && phone != ''){ //check phone function later
                    $.ajax({
                        url: baseurl+"/"+languageShort+"/user-update-profile",
                        dataType: 'json',
                        data: {
                            'act': 'sendcode',
                            'location': location,
                            'phonecode': phone_code,
                            'phone': phone
                        },
                        type:'post',
                        success: function(res){
                            console.log(res);
                            if(res.status){
                                //$('#phone-active-modal').modal('hide');
                                //show activation fill code
                                showActivationFillCode(res.data);
                            }else{
                                $('#phone-number').closest('.form-group').addClass('error');
                                $('#phone-number').next().children().text(res.message);
                            }
                        }
                    });
                }
                return false;
            }
        });

        //resend code form
        var resendcode_validate = $("form[name='resendcode-form']").validate({
            onkeyup: false,
            onfocusout: false,
            // Specify validation rules
            rules: {
                phonenumber: {
                    required: true,  // <-- no such method called "matches"!
                    minlength:5
                }
            },

            // Specify validation error messages
            messages: {
                phonenumber: {
                    required: trans_Validatephone,
                    minlength: trans_Validatecharacter5
                }
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid

            errorPlacement: function(error, element) {
                var pa_element = element.closest('.form-group');
                pa_element.addClass('error');

                element = element.next().children();
                element.text(error.text());
            },
            unhighlight: function(element, errorClass, validClass) {
                var pa_element = $(element).closest('.form-group');
                pa_element.removeClass('error');
            },
            submitHandler: function(form) {
                //check phonenumber
                var phone = $('#resend-phone').val();
                var country_code = $('#resend-country').val();
                var location = country_code.split('_')[0];
                var phone_code = country_code.split('_')[1];

                //save change phone
                $.ajax({
                    url: baseurl+"/"+languageShort+"/user-update-profile",
                    dataType: 'json',
                    data: {
                        'act': 'updatephone',
                        'location': location,
                        'phonecode': phone_code,
                        'phone': phone
                    },
                    type:'post',
                    success: function(res){
                        if(res.status){
                            $('#resend-phone').closest('.form-group').removeClass('error');

                            showActivationFillCode(res.data);
                        }else{
                            $('#resend-phone').closest('.form-group').addClass('error');
                            $('#resend-phone').next().children().text(res.message);
                        }
                    }
                });

                return false;
            }
        });

        $('#resend-country').change(function(){
            $('#resend-phone').val('');
        });

        //resend code form
        var activecode_validate = $("form[name='activecode-form']").validate({
            onkeyup: false,
            onfocusout: false,
            // Specify validation rules
            rules: {
                receivecodenumber: "required"
            },

            // Specify validation error messages
            messages: {
                receivecodenumber: trans_Validateactivationcode
            },
            // Make sure the form is submitted to the destination defined
            // in the "action" attribute of the form when valid

            errorPlacement: function(error, element) {
                var pa_element = element.closest('.form-group');
                pa_element.addClass('error');

                element = element.next().children();
                element.text(error.text());
            },
            unhighlight: function(element, errorClass, validClass) {
                var pa_element = $(element).closest('.form-group');
                pa_element.removeClass('error');
            },
            submitHandler: function(form) {
                var mobile_code = $('#receive-code-number').val();
                $.ajax({
                    url: baseurl+"/"+languageShort+"/user-verify-sms",
                    dataType: 'json',
                    data: {
                        'mobile_code': mobile_code
                    },
                    type:'post',
                    success: function(res){
                        if(res.status){
                            helperJs.bzOpenPopup(
                                {items:
                                { src: '#pop-alert'},
                                    beforeOpen(){
                                $('#pop-alert > div > p').text(res.message);
                                $('#pop-alert > button.change-text-pop').text(trans_Continue);
                            },
                            afterClose(){
                                location.href = baseurl+'/'+languageShort+'/contest-submit';

                            }
                        }
                        )


                        }else{
                            $('#receive-code-number').closest('.form-group').addClass('error');
                            $('#receive-code-number').next().children().text(res.message);
                        }
                    }
                });
                return false;
            }
        });

    }

    var showActivationFillCode = function(data){
        //$('#code-active-modal').modal('show');
        App.Popup.openActiveCode();
        $('#pop-activecode .note').html(trans_Activationcodesendtophonenumber+' +'+data.phone+' ('+data.mobile_code+')');
    }

    var showWinnerList = function(){
        helperJs.bzClosePopup();
        helperJs.bzOpenPopup(
            {items:
                    { src: '#pop-alert'},
                beforeOpen(){
                    $('#pop-alert > div > p').text(trans_WinnerListEmpty);
                },
                afterClose(){
                }
            });
    }

    return {
        init:init,
        showUserLogin: showUserLogin,
        userLogout:userLogout,
        showWinnerList: showWinnerList,
        goToContestSubmit: goToContestSubmit
    };
}();    
//--End All site

//--Contact
App.Contact = function(){

    var $form = $('#frmNLContact');

    var init = function(){
        $form.find('input').bind('click keypress', function(e){
            if (e.keyCode === 13 || e.which === 13) {
                App.Contact.submit();
            }
        });
    };

    var clickSubmit = true;
    var submit = function () {
        if (clickSubmit) {
            clickSubmit = false;
            $form.find('#ajax-loading').slideDown();       
            var data = $form.serialize();
            $.ajax({
                url: baseurl + "/contact/",
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (res) {
                    clickSubmit = true;
                    $form.find('#ajax-loading').slideUp();
                    if (res.status) {   
                        if(res.url_redirect){      
                            window.location.href = res.url_redirect;
                        }
                        App.Contact.resetForm(); //Reset
                        App.Popup.showInline('#show-error-msg',res.message,'Green');
                    } else {
                        App.Popup.showInline('#show-error-msg',res.message);
                    }
                }
            });
        }
    };

    var resetForm = function(){
         $form.find('input[type="text"], textarea').val(''); //--reset form
    };

    return {
        init:init,
        submit:submit,
        resetForm:resetForm
    };
}();    
//--End contact


//--Subscribe
App.Subscribe = function(){

    var $form = $('#subscribe_form');

    var init = function(){
        $form.find('input').bind('click keypress', function(e){
            if (e.keyCode === 13 || e.which === 13) {
                App.Contact.submit();
            }
        });
    };

    var clickSubmit = true;
    var submit = function () {
        if (clickSubmit) {
            clickSubmit = false;
            $form.find('#subscribe-ajax-loading').slideDown();       
            var data = $form.serialize();
            $.ajax({
                url: baseurl + "/ajax/contact/subscribe",
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (res) {
                    clickSubmit = true;
                    $form.find('#subscribe-ajax-loading').slideUp();
                    if (res.status) {   
                        if(res.url_redirect){      
                            window.location.href = res.url_redirect;
                        }
                        App.Contact.resetForm(); //Reset
                        App.Popup.showInline('#subscribe-error-msg',res.message,'Green');
                    } else {
                        App.Popup.showInline('#subscribe-error-msg',res.message);
                    }
                }
            });
        }
    };

    var resetForm = function(){
         $form.find('input[type="text"], textarea').val(''); //--reset form
    };

    return {
        init:init,
        submit:submit,
        resetForm:resetForm
    };
}();    
//--End Subscribe

//--FACEBOOK
App.Facebook = function(){
    var init = function(){
        window.fbAsyncInit = function() {
            FB.init({
                appId      : app_id,
                xfbml      : true,

                cookie: true,
                oauth: true,
                version    : 'v2.9'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        $(".btn-login-facebook").click(function(){
            console.log("clickclickclick");

            FB.login(function(response) {
                console.log("FB.login "+response.authResponse);
                facebookLogin(response);
            }, {
                scope: 'email'
            });
            //FB.getLoginStatus(function(response) {
            //    console.log("FB.getLoginStatus "+JSON.stringify(response));
            //    if (response.status === 'connected') {
            //        console.log('Logged in.');
            //        facebookLogin(response);
            //    }
            //});

            facebookLogin = function(response){
                if (response.authResponse) {
                    $("#token").val(response.authResponse.accessToken);
                    $("#fbuid").val(response.authResponse.userID);
                    //$(".loading-invisible").fadeIn();
                    $.ajax({
                        url: baseurl+"/user-login-fb",
                        dataType: 'json',
                        data: {
                            access_token:$("#token").val()
                        },
                        type:'post',
                        success: saveUserComplete
                    });
                }
            }

            saveUserComplete = function(res){
                if(res.data.status == "0") { //nếu chưa active
                    if (res.data.phone == "") {
                        //show update phone popup
                        App.Popup.openUpdatePhone();
                    }

                    if(res.need_active){
                        //show resend popup with phone editable
                        App.Popup.openResendCode();
                        $('#resend-phone').val(res.data.phone);
                        $('#resend-location').val(res.data.location + '_' + res.data.phone.substr(0, 2));
                    }

                }else{
                    if(res.status){
                        location.href = baseurl+'/'+languageShort+'/contest-submit';
                    }

                }
            }
        });
    };

    var fixIframeFB = function() {
        var isInIframe = (window.location !== window.parent.location) ? true : false;
        if (!isInIframe) {
            var url_iframe = "http://www.facebook.com/" + fanpage_name + "/app_" + app_id + "?app_data=" + window.location.pathname;
            window.location.href = url_iframe;
        }
    };

    var fixLayoutFB = function(elWrap, heightFix) {
        heightFix = typeof heightFix !== 'undefined' ? heightFix : 0;
        var contentHeight = parseInt($(elWrap).height()) + heightFix;
        if (typeof FB !== 'undefined') {
            FB.Canvas.setSize({
                width: 810,
                height: contentHeight
            });

            var heightCoverFix = 375; //315px is height of cover
            FB.Canvas.scrollTo(0, heightCoverFix); //Height cover fanpage
        }
    };

    return {
        init:init
    }
}();



//--Google
App.Google = function () {
    var init = function (){
        //load google script
        var url = "https://apis.google.com/js/platform.js";
        loadScript = function (url, callback) {
            var script = document.createElement( "script" )
            script.type = "text/javascript";
            if(script.readyState) {  //IE
            script.onreadystatechange = function() {
              if ( script.readyState === "loaded" || script.readyState === "complete" ) {
                script.onreadystatechange = null;
                callback();
              }
            };
            } else {  //Others
            script.onload = function() {
              callback();
            };
            }

            script.src = url;
            document.getElementsByTagName( "head" )[0].appendChild( script );
        }

        //show logout html
        showLogOut = function(auth2){
            var btnLogout = '<button id="g-logout">LogOut</button>';
            $('.google-signin').append(btnLogout);

            $('#g-logout').click(function(){
                        auth2.signOut().then(function () {
                          window.location.reload();
                        });
                      });
        }

        loadScript(url, function(){
            //if user is signed in already
            gapi.load('auth2', function() {
                gapi.auth2.init(
                        {
                            client_id: google_app_id
                        }
                    ).then(function(){
                    var auth2 = gapi.auth2.getAuthInstance();

                    if (auth2.isSignedIn.get()) {
                      /*var profile = auth2.currentUser.get().getBasicProfile();
                      console.log(profile);
                      var loggedInHtml = '<h4>Signed In</h4>';
                      loggedInHtml += '<p>ID: ' + profile.getId() + '</p>';
                      loggedInHtml += '<p>Full Name: ' + profile.getName() + '</p>';
                      loggedInHtml += '<p>Given Name: ' + profile.getGivenName() + '</p>';
                      loggedInHtml += '<p>Family Name: ' + profile.getFamilyName() + '</p>';
                      loggedInHtml += '<p>Image URL: ' + profile.getImageUrl() + '</p>';
                      loggedInHtml += '<p>Email: ' + profile.getEmail() + '</p>';

                      $('.google-signin').html(loggedInHtml);
                      showLogOut(auth2);*/
                      auth2.signOut();
                    }else{
                        onSuccess = function(googleUser){
                            var auth2 = gapi.auth2.getAuthInstance();
                            
                            if (auth2.isSignedIn.get()) {
                                console.log(googleUser.getBasicProfile());
                                $('.google-signin').html('Logged in as: ' + googleUser.getBasicProfile().getName());
                                //showLogOut(auth2);

                                //
                                $.ajax({
                                    url: baseurl+"/user-login-gg",
                                    dataType: 'json',
                                    data: {
                                        google_user: googleUser.getBasicProfile()
                                    },
                                    type:'post',
                                    error: saveUserError,
                                    success: saveUserComplete
                                });
                            }
                        }
                        saveUserError = function(){
                            console.log('error');
                        }

                        saveUserComplete = function(res){
                            console.log(res);
                            //location.reload();
                            if(res.status){
                                App.Site.showUserLogin(res.data.id, res.data.name);

                                if(res.data.status == "0") { //nếu chưa active
                                    if (res.data.phone == "") {
                                        //show update phone popup
                                        App.Popup.openUpdatePhone();
                                    }

                                    if(res.need_active){
                                        //show resend popup with phone editable
                                        App.Popup.openResendCode();
                                        $('#resend-phone').val(res.data.phone);
                                        $('#resend-location').val(res.data.location + '_' + res.data.phone.substr(0, 2));
                                    }
                                }else{
                                    //actived user logged in
                                    location.reload();
                                }
                            }
                        }

                        onFailure = function(error){
                            console.log(error);
                        }

                        //$('.google-signin').html('<div id="my-signin2"></div>');
                        /*gapi.signin2.render('my-signin2', {
                            'scope': 'profile email',
                            'width': 240,
                            'height': 50,
                            'longtitle': true,
                            'theme': 'dark',
                            'onsuccess': onSuccess,
                            'onfailure': onFailure
                          });*/
                        attachSignin = function(element) {
                            auth2.attachClickHandler(element, {},
                                function(googleUser) {
                                    onSuccess(googleUser);
                                    //document.getElementById('name').innerText = "Signed in: " +
                                        //googleUser.getBasicProfile().getName();
                                }, function(error) {
                                    console.log(JSON.stringify(error, undefined, 2));
                                });
                        }

                        attachSignin(document.getElementById('my-signin2'));

                    }
                });
            });
        });

        /*$('#activation-code-send-btn').click(function(){

        });*/

        var showActivationFillCode = function(data){
            //$('#code-active-modal').modal('show');
            App.Popup.openActiveCode();
            $('#pop-activecode .note').html(trans_Activationcodesendtophonenumber + ' +'+data.phone);
        }



        /*$('#reg-account-btn').click(function(){

        });

        $('#login-btn').click(function(){
            //validate form


        });*/
    };

    return {
        init:init
    }
}();


//--Scroll
App.Scroll = function () {
    var to = function (el, delay, topAdd) {
        topAdd = typeof (topAdd) !== 'undefined' ? topAdd : 0;
        delay = typeof (delay) !== 'undefined' ? delay : 700;
        var $el = $(el);
        if ($el.length > 0) {
            $('body,html').animate({
                scrollTop: $el.offset().top + topAdd
            }, delay);
        }
    };

    return {
        to: to
    };
}();

//--Share
App.VoteShare = function () {

    var init = function(){
        $(document).ready(function(){
            $('.vote-button').click(function(){
                var object_id = $(this).attr('data-object-id');
                App.VoteShare.vote(object_id, 'contest', '');
            });
        });
    }

    var shareSite = function () {
        var title = $('meta[property="og:title"]').attr('content');
        var link = $('meta[property="og:url"]').attr('content');
        var img = $('meta[property="og:image"]').attr('content');
        var description = $('meta[property="og:description"]').attr('content');
        shareFB(title, link, img, description);
    };

    var shareFB = function (FBTitle,FBLink, FBPic, FBDesc) {
        if (typeof (FB) !== 'undefined') {
            FB.ui({
                method: 'share_open_graph',
                action_type: 'og.shares',
                action_properties: JSON.stringify({
                    object: {
                        'og:url': FBLink,
                        'og:title': FBTitle,
                        'og:description': FBDesc,
                        'og:image': FBPic
                    }
                })
            });

        } else {
            App.Debug.consoleLog('Please try again later...');
        }
    };

    var clickVote = true;
    var vote = function (objectId, extension, url_redirect) {
        if (objectId && objectId !== '0') {
            if (clickVote) {
                clickVote = false;
                $.ajax({
                    url: baseurl + "/api/vote",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'object_id': objectId,
                        'extension': extension,
                        'url_redirect':url_redirect,
                        'type':'votes'
                    },
                    success: function (res) {
                        if(res.status){
                            helperJs.bzClosePopup();
                            helperJs.bzOpenPopup(
                                {items:
                                        { src: '#pop-alert'},
                                    beforeOpen(){
                                        $('#pop-alert > div > p').text(res.message);
                                    },
                                    afterClose(){
                                        //increase number likes
                                        $('.count-like').text(res.currentCount);
                                    }
                                });
                        }else{
                                helperJs.bzClosePopup();
                                helperJs.bzOpenPopup(
                                    {items:
                                            { src: '#pop-alert'},
                                        beforeOpen(){
                                            $('#pop-alert > div > p').text(res.message);
                                        },
                                        afterClose(){
                                            if(res.is_login){
                                                //show login popup
                                                App.Popup.openLogin();
                                            }
                                        }
                                    });
                        }
                        /*clickVote = true;
                        console.log(res);
                        if (res.status) {
                            var $classShowNum = $('.num_vote_' + objectId);
                            if ($classShowNum.length > 0) {
                                var curNum = $classShowNum.eq(0).text();
                                curNum = parseInt(curNum) + 1;
                                $classShowNum.text(curNum);
                            }
                            alert(res.message);
                        } else {
                            if(res.is_login){
                                //alert(res.message);
                                //return to login page
                                console.log('return to login page');
                                location.href = baseurl;
                            }else{
                                alert(res.message);
                            }   
                        }

                        var voteButton = $('.vote-button-' + objectId);
                        voteButton.off('click');
                        voteButton.html('Voted');
                        voteButton.removeClass('btn-info');*/
                    }
                });
            }
        } else {
            App.Debug.consoleLog('success');
        }
    };

    var share = function (id, title, link, img, description, caption, isTrackCms, extension) {
        isTrackCms = typeof (isTrackCms) != 'undefined' ? isTrackCms : true;
        if (typeof (FB) !== 'undefined') {
            FB.ui(
                {
                    method: 'feed',
                    name: title,
                    link: link,
                    picture: img,
                    caption: caption,
                    description: description
                },
                function (response) {
                    if (isTrackCms) {
                        if (response) {
                            if (id && id !== '0') {
                                $.ajax({
                                    url: baseurl + "/vote/",
                                    type: 'post',
                                    dataType: 'json',
                                    data: {
                                        'object_id': id,
                                        'extension': extension,
                                        'type':'shares'
                                    },
                                    success: function (res) {
                                        if (res.status) {
                                            var $classShowNum = $('.num_share_' + id);
                                            if ($classShowNum.length > 0) {
                                                var curNum = $classShowNum.eq(0).text();
                                                curNum = parseInt(curNum) + 1;
                                                $classShowNum.text(curNum);
                                            }
                                             App.Popup.showAlert(res.message);
                                        } else {

                                            App.Popup.showAlert(res.message);
                                        }
                                    }
                                });
                            } else {
                                App.Debug.consoleLog('success');
                            }
                        }
                    }
                    App.Debug.consoleLog('done...');
                }
            );
        } else {
            App.Debug.consoleLog('Please try again later...');
        }
    };

    return {
        init: init,
        shareSite: shareSite,
        share:share,
        vote:vote,
        shareFB:shareFB
    };
}();

//Popup
App.Popup = function () {
    var showAlert = function (msg, btn, urlRedirect) {

       var $popup = $('#pop-alert');
          if ($popup.length > 0) {
            $('#pop-alert .content p').html(msg);
            bzPopup({rel:'#pop-alert'});

            if(btn==true){
                $('.btn-chung').show();
                var $btnRedirect = $popup.find('.btn_redirect');
                if ($btnRedirect.length > 0) {
                    $btnRedirect.each(function() {
                        if (typeof (urlRedirect) !== 'undefined' && urlRedirect !== '') {
                            $(this).removeAttr('onclick').attr({'href': urlRedirect});
                        } else {
                            $(this).attr({'href': 'javascript:;', 'onclick': 'javascript: $.magnificPopup.close();'});
                        }
                    });
                }
            }
        }
    };

    var timeShowMsgAction;
    var showInline = function (el, msg, color, delay, speed) {
        var colorRed = 'red';
        color = typeof (color) !== 'undefined' ? color : colorRed;
        delay = typeof (delay) !== 'undefined' ? delay : 5000;
        speed = typeof (speed) !== 'undefined' ? speed : 500;

        var $el = $(el);
        if ($el.length > 0) {
            clearTimeout(timeShowMsgAction);
            $el.html(msg).css('color', color).slideDown(speed, function () {
                timeShowMsgAction = setTimeout(function () {
                    $el.css('color', colorRed).slideUp(speed);
                }, delay);
            });
        }
    };

    return {
        showAlert: showAlert,
        showInline: showInline
    };
}();

App.magnificPopup = function () {
    var open = function (btnClick) {
        $btnClick = $(btnClick);
        if ($btnClick.length > 0) {
            $btnClick.magnificPopup('open');
        }
    };

    var close = function (btnClick) {
        $btnClick = $(btnClick);
        if ($btnClick.length > 0) {
            $btnClick.magnificPopup('close');
        }
    };

    var init = function (btnClick) {
        $btnClick = $(btnClick);
        if ($btnClick.length > 0) {
            $btnClick.magnificPopup({
                type: 'inline',
                midClick: true,
                closeOnBgClick: false,
                callbacks: {
                    beforeOpen: function () {

                    }
                }
            });
        }
    };

    return {
        init: init,
        open: open,
        close: close
    };
}();

App.Popup = function () {
    var openForgotpass = function () {
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-forgotpass' } }); }, 1000);

    };

    var openLogin = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-login' } }); }, 500);
    }

    var openRegister = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-register' } }); }, 500);
    }

    var openUpdatePhone = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-updatephone' } }); }, 500);
    }

    var openActiveCode = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-activecode' } }); }, 500);
    }

    var openResendCode = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-resendcode' } }); }, 500);
    }

    var openPopAlert = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-alert' } }); }, 500);
    }

    var openPopContestSuccess = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-contestSuccess' } }); }, 500);
    }

    return {
        openForgotpass: openForgotpass,
        openLogin: openLogin,
        openRegister: openRegister,
        openUpdatePhone: openUpdatePhone,
        openActiveCode: openActiveCode,
        openResendCode: openResendCode,
        openPopAlert: openPopAlert,
        openPopContestSuccess: openPopContestSuccess
    };
}();

//--GA: value must to number
App.Ga = function () {
    var trackEvent = function (action, label, value, category) {
        if (typeof ga !== 'undefined') {
            category = typeof (category) !== 'undefined' ? category : 'bzcmsdefault';
            if (typeof (value) !== 'undefined') {      
                console.log(action +'--'+label +'--'+ value +'--'+category);
                ga('send', 'event', category, action, label, value, {
                  nonInteraction: true
                });
            } else {
                ga('send', 'event', category, action, label, {
                  nonInteraction: true
                });
            }
        }
    };
    var trackPageView = function (page, title) {
        if (typeof ga !== 'undefined') {
            ga('send', 'pageview', {'page': page, 'title': title});
        }
    };

    return {
        trackEvent: trackEvent,
        trackPageView: trackPageView
    };
}();

//--Google Map--
//--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvkXlsMNofvpBhntdp7NfO1byBRIZwMCI&sensor=false"></script>
App.GMap = function(){
    var load = function (element, title, lat, lng, zoom) {
        var mapDiv = $(element);
        if(mapDiv.length > 0){
            var pos = new google.maps.LatLng(lat, lng);
            var options = {
                zoom: zoom,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: pos,
                disableDefaultUI: true,
                navigationControl: false,
                navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
                draggable: true,
                scaleControl: true
            };
            setTimeout(function () {
                var map = new google.maps.Map(mapDiv[0], options);
                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    map: map,
                    title: title,
                    position: pos
                });
            }, 100);
        }
    };
    return {
        load:load
    }

}();
//--End Google Map--

//--Debug
App.Debug = function () {
    var consoleLog = function (message) {
        console.log(message);
    };

    return {
        consoleLog: consoleLog
    };
}();