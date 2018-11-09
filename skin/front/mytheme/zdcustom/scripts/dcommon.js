var App = App || {};

//---MAIN----
$(function () {
    App.Site.init();
    App.Contact.init(); 
    App.Subscribe.init();
    App.VoteShare.init();
    //App.Facebook.init();
    
    if($(".btn-login-facebook").length > 0 || $(".fb-share").length > 0){App.Facebook.init();}
    App.Google.init();

    App.Contest.init();
});

//--All site
App.Site = function(){

    var init = function(){
        changeLang();
    };

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

    var globalSearch = function(formEl){
        var $formSearch = $(formEl);
        if($formSearch.length > 0){
            var $keyWord = $formSearch.find('#s_key_word').val();
            if($keyWord!==''){
                $formSearch.submit();
            }
        }
    };

    //--Auto resize element
    var autoResize = function(id){
        var newheight;
        var newwidth;

        if(document.getElementById){
            newheight = document.getElementById(id).contentWindow.document .body.scrollHeight;
            newwidth = document.getElementById(id).contentWindow.document .body.scrollWidth;
        }

        document.getElementById(id).height = (newheight) + "px";
        document.getElementById(id).width = (newwidth) + "px";
    }

    //--Auto add class img-resposive for image--
    var autoAddClassRespImage = function(contaner){
        var $els = $(contaner).find('img');
        if($els.length > 0){
            $els.each(function(){
                $(this).addClass('img-responsive').removeAttr('style');
            });
        }
    };

    //Auto add class resp for Iframe---
    var autoAddClassRespIframe = function(contaner){
        var $els = $(contaner).find('iframe');
        if($els.length > 0){
            $els.each(function(){
                var src = $(this).attr('src');
                var elNewstr = '<div class="embed-responsive embed-responsive-16by9">'+
                                '<iframe allowfullscreen frameborder="0" class="embed-responsive-item" src="'+src+'"></iframe>'+
                                '</div>';
                $(this).replaceWith(elNewstr);
            });
        }
    };

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

    var logoutSuccess = function(){
        console.log('success');
        location.reload();
    }

    return {
        init:init,
        autoResize:autoResize,
        globalSearch:globalSearch,
        autoAddClassRespImage:autoAddClassRespImage,
        autoAddClassRespIframe:autoAddClassRespIframe,
        userLogout:userLogout
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
                version    : 'v2.5'
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
                        //show modal popup with phone input
                        $('#phone-active-modal').modal('show');
                    }

                    if(res.need_active){
                        //show resend popup with phone editable
                        $('#resend-code-modal').modal('show');
                        $('#resend-phone').val(res.data.phone);
                        $('#resend-location').val(res.data.location + '_' + res.data.phone.substr(0, 2));
                    }
                }else{
                    //actived user logged in
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
                                if(res.data.status == "0") { //nếu chưa active
                                    if (res.data.phone == "") {
                                        //show modal popup with phone input
                                        $('#phone-active-modal').modal('show');
                                    }

                                    if(res.need_active){
                                        //show resend popup with phone editable
                                        $('#resend-code-modal').modal('show');
                                        $('#resend-phone').val(res.data.phone);
                                        $('#resend-location').val(res.data.location + '_' + res.data.phone.substr(0, 2));
                                    }
                                }else{
                                    //actived user logged in
                                }
                            }
                        }

                        onFailure = function(error){
                            console.log(error);
                        }

                        $('.google-signin').html('<div id="my-signin2"></div>');
                        gapi.signin2.render('my-signin2', {
                            'scope': 'profile email',
                            'width': 240,
                            'height': 50,
                            'longtitle': true,
                            'theme': 'dark',
                            'onsuccess': onSuccess,
                            'onfailure': onFailure
                          });
                    }
                });
            });
        });

        $('#activation-code-send-btn').click(function(){
            var phone = $('#phone-number').val();
            var country_code = $('#location').val();
            var location = country_code.split('_')[0];
            var phone_code = country_code.split('_')[1];

            if(phone && phone != ''){ //check phone function later
                $.ajax({
                    url: baseurl+"/user-update-profile",
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
                            $('#phone-active-modal').modal('hide');
                            //show activation fill code
                            showActivationFillCode(res.data);
                        }
                    }
                });
            }
        });

        var showActivationFillCode = function(data){
            $('#code-active-modal').modal('show');
            $('#mobile-code').html(data.mobile_code);
        }

        $('#active-btn').click(function(){
            var mobile_code = $('#receive-code-number').val();
            if(mobile_code.length == 4){
                $.ajax({
                    url: baseurl+"/user-verify-sms",
                    dataType: 'json',
                    data: {
                        'mobile_code': mobile_code
                    },
                    type:'post',
                    success: function(res){
                        alert(res.message);
                        location.reload();
                    }
                });
            }
        });

        $('#edit-phone').click(function(){
            var action = $(this).attr('action');

            if(action == 'edit'){
                $('#resend-phone').removeAttr('readonly');
                $('#resend-phone').focus();
                $(this).text('Save');
                $(this).attr({'action':'save'});
            }else if(action = 'save'){
                var phone = $('#resend-phone').val();
                var country_code = $('#resend-location').val();
                var location = country_code.split('_')[0];
                var phone_code = country_code.split('_')[1];

                //save change phone
                $.ajax({
                    url: baseurl+"/user-update-profile",
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
                            $('#resend-phone').attr({'readonly': ''});
                            $('#edit-phone').text('Edit');
                            $('#edit-phone').attr({'action':'edit'});
                        }
                    }
                });
            }

        });

        $('#resend-btn').click(function(){
            var country_code = $('#resend-location').val();
            var location = country_code.split('_')[0];

            $.ajax({
                url: baseurl+"/user-update-profile",
                dataType: 'json',
                data: {
                    'act': 'sendcode',
                    'location': location
                },
                type:'post',
                success: function(res){
                    if(res.status){
                        $('#resend-code-modal').modal('hide');
                        showActivationFillCode(res.data);
                    }
                }
            });
        });

        $('#reg-account-btn').click(function(){
            var reg_name = $('#reg-name').val();
            var reg_phone = $('#reg-phone').val();
            var country_code = $('#reg-location').val();
            var location = country_code.split('_')[0];
            var phone_code = country_code.split('_')[1];
            var reg_email = $('#reg-email').val();
            var reg_password = $('#reg-password').val();
            var reg_repassword = $('#reg-repassword').val();

            $.ajax({
                url: baseurl+"/user-register",
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
                    alert(res.message);
                    if(res.status){
                        //redirect to login page
                        location.href = baseurl + '/vi/login';
                    }
                }
            });
        });

        $('#login-btn').click(function(){
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
                            alert(res.message);
                            if(res.phone != ''){
                                $('#resend-code-modal').modal('show');
                                $('#resend-phone').val(res.phone);
                                $('#resend-location').val(res.location + '_' + res.phone.substr(0, 2));
                            }else{
                                //show modal popup with phone input
                                $('#phone-active-modal').modal('show');
                            }

                        }
                    }
                }
            });
        });
    };

    return {
        init:init
    }
}();

//--Contest Submit
App.Contest = function (){
    /*var video_duration_limit = 60;
    var media_type = 'video';
    var media_images = '';
    var image_upload_error = '';

    var init = function(){
        //new upload


        //dropzone
        $(document).ready(function(){
            Dropzone.forElement(".dropzone").options.autoProcessQueue = false;
            Dropzone.forElement(".dropzone").options.maxFiles = 10;
            Dropzone.forElement(".dropzone").options.parallelUploads = 10;

            $('#switch_left').click(function(){
                media_type = 'video';
                $('.multi-image-upload').addClass('disabledArea');
                $('.video-upload').removeClass('disabledArea');
            });

            $('#switch_right').click(function(){
                media_type = 'images';
                $('.multi-image-upload').removeClass('disabledArea');
                $('.video-upload').addClass('disabledArea');
            });
        });

        //contest submit button event
        $('#contestSubmit').click(function(){
            //reset validate
            $('#video-title-error').html('');
            $('#video-des-error').html('');
            $('#video-file-error').html('');

            //validate video upload form
            var video_title = $('#video-title').val();
            var video_description = $('#video-description').val();
            var video_file = $('#fileupload').val();

            if(video_title == ''){$('#video-title-error').html('Title should not be empty!'); return;}
            if(video_description.length < 1){$('#video-des-error').html('Description must be over 50 words!'); return;}

            //if video upload option chosen
            if(media_type == 'video'){
                if(video_file == ''){$('#video-file-error').html('Choose a video file to upload!'); return;}
                //check video file type
                var file_ext = $('#fileupload').val().split('.').pop().toLowerCase();

                if($.inArray(file_ext, ['mp4','avi','mov']) == -1) {
                    alert('Invalid file format, we just allow mp4, avi, mov video format, choose another one, thanks!');
                }else{
                    //Get input files
                    var file_data = $('#fileupload').prop('files')[0];
                    //Get file type
                    var type = file_data.type;

                    //check video file duration
                    var myVideos = [];
                    myVideos.push(file_data);
                    var video = document.createElement('video');
                    video.preload = 'metadata';
                    video.onloadedmetadata = function() {
                        window.URL.revokeObjectURL(video.src);
                        var duration = video.duration;
                        myVideos[myVideos.length - 1].duration = duration;

                        if(myVideos[0].duration < video_duration_limit){
                            //start upload video
                            startUploadVideo(file_data);
                        }else{
                            alert('Video upload should not be longer than 1 minute!');
                        }
                    }
                    video.src = URL.createObjectURL(file_data);
                }
            }else if(media_type == 'images'){
                if($('.dz-file-preview').length == 0 && $('.dz-image-preview').length == 0){
                    $('#images-file-error').html('Choose image file to upload!'); return;
                }else if($('.dz-image-preview').length > 10){
                    $('#images-file-error').html('Maximum 10 images!'); return;
                }

                //start upload images
                Dropzone.forElement(".dropzone").processQueue();
                Dropzone.forElement(".dropzone").on("success", function (file, response) {
                    var result = JSON.parse(response);
                    if(result.status){
                        if(media_images == ''){
                            media_images += result.filename;
                        }else{
                            media_images += ',' + result.filename;
                        }
                    }else{
                        image_upload_error += result.message;
                    }
                });
                Dropzone.forElement(".dropzone").on("complete", function (file, response) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        if(media_images != ''){
                            finishSavingContestSubmit(media_images);
                        }

                        if(image_upload_error != ''){alert(image_upload_error);}
                    }
                });
            }
        });
    }

    var startUploadVideo = function(file_data){
        //init form object
        var form_data = new FormData();
        //add file data to form object
        form_data.append('file', file_data);
        form_data.append('media_type', 'video');

        //using ajax post
        $.ajax({
            url: baseurl+"/vi/contest-submit", //submit to contest submit action
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            xhr: function ()
            {
                var jqXHR = null;
                if ( window.ActiveXObject )
                {
                    jqXHR = new window.ActiveXObject( "Microsoft.XMLHTTP" );
                }
                else
                {
                    jqXHR = new window.XMLHttpRequest();
                }
                //Upload progress
                jqXHR.upload.addEventListener( "progress", function ( evt )
                {
                    if ( evt.lengthComputable )
                    {
                        var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                        //Do something with upload progress
                        console.log( 'Uploaded percent', percentComplete );
                    }
                }, false );
                //Download progress
                jqXHR.addEventListener( "progress", function ( evt )
                {
                    if ( evt.lengthComputable )
                    {
                        var percentComplete = Math.round( (evt.loaded * 100) / evt.total );
                        //Do something with download progress
                        $('#bar1').css({'width': percentComplete +'%'});
                        $('#percent1').html(percentComplete + '%');
                        console.log( 'Downloaded percent', percentComplete );
                    }
                }, false );
                return jqXHR;
            },
            success: function (res) {
                var result = JSON.parse(res);
                if(result.status){
                    finishSavingContestSubmit(result.filename);
                }else{
                    alert(result.message);
                }
            }
        });
    }

    var finishSavingContestSubmit = function(media){
        var dataObject = {};
        dataObject.media_title = $('#video-title').val();
        dataObject.media_description = $('#video-description').val();
        dataObject.media_type = media_type;
        dataObject.media = media;

        $.ajax({
            type : "POST",
            url  : baseurl+"/vi/contest-submit",
            data : dataObject,
            success :  function(res){
                //do something here
                var result = JSON.parse(res);
                alert(result.message);
            }
        });
    }*/

    var init = function() {
        createUploadHtml(1);

        //send contest button
        $('#send-contest-btn').click(function(){
            var goal_location = $('#goal-location').val();
            var contest_content = $('.contest-content').val();

            if(goal_location == -1){
                alert('Vui lòng chọn điểm đến');
                return;
            }
            if($('.img-upload-item img').length == 0){
                alert('chọn ít nhất 1 hình');
                return;
            }
            if(contest_content == ''){
                alert('Vui lòng gửi thông điệp');
                return;
            }
            if(contest_content.match(/\S+/g).length > 1000){
                alert('Thông điệp không được quá 1000 chữ');
                return;
            }

            //send ajax
            //append to form data
            var formData = new FormData();
            $('#image-upload-form').find('input[type=file]').each(function(index, file){
                if(file.files[0] != undefined){
                    formData.append('file[]', file.files[0]);
                }
            });

            $.ajax({
                url: baseurl + "/vi/contest-submit", //submit to contest submit action
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                type: 'post',
                success: function (res) {
                    var result = JSON.parse(res);
                    if(result.status){
                        finishSavingContestSubmit(result.fileuploaded);
                    }else{
                        alert(result.message);
                    }
                }
            });
        });
    }

    var addnewuploadhtml = function(num){
        var current = num - 1;
        var upload_image = $('.img-upload-area-'+current+ ' img');

        if(upload_image.length == 0){
            alert('Vui lòng upload ảnh');
        }else{
            if(num > 5){
                alert('Upload tối đa 5 hình!');
            }else{
                createUploadHtml(num);
            }

            var upload_action = $('.img-upload-action-' + current);
            upload_action.html('<span class="text-danger" onclick="App.Contest.removeuploadhtml('+current+')" for="img-upload-area-'+current+'"><i class="fa fa-trash"></i> XOÁ HÌNH</span>');
        }
    }

    var createUploadHtml = function(num){
        var next = num + 1;
        var addButton = '<span class="text-success" onclick="App.Contest.addnewuploadhtml('+next+')" for="img-upload-area-'+num+'"><i class="fa fa-plus"></i> THÊM HÌNH</span>';

        var img_upload_item = '<div class="img-upload-item col-md-12"><div class="col-md-4">Hình Ảnh '+num+'</div><div class="col-md-4 text-center img-upload-area-'+num+'"><div class="upload-btn-wrapper"><button class="btn btn-upload">UPLOAD ẢNH</button><input type="file" name="image-upload" for="img-upload-area-'+num+'" onchange="App.Contest.readURL(this);"/></div></div><div class="col-md-4 text-right img-upload-action img-upload-action-'+num+'">'+addButton+'</div></div>';
        $('#image-upload-form').append(img_upload_item);
    }

    var removeuploadhtml = function(num){
        $('.img-upload-area-'+num+' .upload-btn-wrapper').show();
        $('.img-upload-area-'+num+' .select-image').remove();
        $('.img-upload-area-'+num+' input').val('');
    }

    //new upload
    var readURL = function(input){
        if (input.files && input.files[0]) {
            var upload_area = $(input).attr('for');
            $('.'+upload_area + ' .upload-btn-wrapper').hide();
            $('.'+upload_area).append('<div class="select-image"><img src="" alt="" /></div>')

            var reader = new FileReader();

            reader.onload = function (e) {
                //console.log(e.target.result);
                $('.'+upload_area + ' img').attr('src', e.target.result)
                    .width(150);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    var finishSavingContestSubmit = function(media){
        var dataObject = {};
        dataObject.media_title = 'Title';
        dataObject.media_destination = $('#goal-location').val();
        dataObject.media_description = $('.contest-content').val();
        dataObject.media_type = 'images';
        dataObject.media = media.join(',');

        $.ajax({
            type : "POST",
            url  : baseurl+"/vi/contest-submit",
            data : dataObject,
            success :  function(res){
                //do something here
                var result = JSON.parse(res);
                alert(result.message);
            }
        });
    }


    return {
        init:init,
        readURL:readURL,
        addnewuploadhtml:addnewuploadhtml,
        removeuploadhtml:removeuploadhtml
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
        var caption = $('meta[property="og:site_name"]').attr('content');
        var description = $('meta[property="og:description"]').attr('content');
        share(title, link, img, caption, description);
    };

    var shareFB = function (name, link, img, caption, description) {
        if (typeof (FB) !== 'undefined') {
            FB.ui(
                {
                    method: 'feed',
                    name: name,
                    link: link,
                    picture: img,
                    caption: caption,
                    description: description
                },
                function (response) {
                    App.Debug.consoleLog(response);
                }
            );
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
                        clickVote = true;
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
                        voteButton.removeClass('btn-info');
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
        vote:vote
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
    var open = function (id,url,res) {

    };

    var close = function () {

    };


    return {
        open: open,
        close: close
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