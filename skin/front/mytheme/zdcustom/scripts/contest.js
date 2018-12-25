var Handle = Handle || {};
$(function () {

    Handle.Contest.init();

});
Handle.Contest = function (){


    var init = function() {
        $('#goal-location').change(function(){
            resetContestValidation();
        });
        //
        $('.form-share a').click(function(){
            resetContestValidation();
        });

        $('#share-title').focus(function(){
            resetContestValidation();
        });

        $('#share-content').focus(function(){
            resetContestValidation();
        });

        //preview action
        $('#contest-preview').click(function(){
            var check_form = checkContestSubmitForm();

            var images_preview = '';
            if(check_form){
                $('.form-share .img-load').each(function(){
                    if($(this).attr('src') != '#') {
                        images_preview += '<div class="item"> <div class="img-articel" style="background-image: url(' + $(this).attr('src') + '); background-position: center center;"> <img src="' + baseurlskinimage + '/transparent-img-articel.png" alt=""> </div> </div>';
                    }
                });

                $('#article-slider').html(images_preview);

                //preview title
                $('#preview-title').text($('#share-title').val());

                //preview content
                var shareContent = $('#share-content').val().replace(/<(?:.|\n)*?>/gm, '');
                shareContent = shareContent.replace(/\r?\n/g,'<br/>');
                $('#preview-content').html(shareContent);

                //show popup
                openPopupPreview();

                //add images to preview
                setTimeout(function(){
                    window.sliderArticel(); //show preview popup
                    }, 500);
            }
        });

        //send contest button
        $('#send-contest-btn').click(function(){
            submitContest();
        });

        $('#preview-send-contest').click(function(){
            submitContest();
        });

        //contest ajax load more
        $('#contest-load-more').click(function(){
            contestAjaxLoad($(this), 'contest');
        });

        //contest ajax load more
        $('#winner-load-more').click(function(){
            contestAjaxLoad($(this), 'winner');
        });

        /**
         * jQuery.textareaCounter
         * Version 1.0
         * Copyright (c) 2011 c.bavota - http://bavotasan.com
         * Dual licensed under MIT and GPL.
         * Date: 10/20/2011
         **/
        (function($){
            $.fn.textareaCounter = function(options) {
                // setting the defaults
                // $("textarea").textareaCounter({ limit: 100 });
                var defaults = {
                    limit: 1000
                };
                var options = $.extend(defaults, options);

                // and the plugin begins
                return this.each(function() {
                    var obj, text, wordcount, limited;

                    obj = $(this);
                    //obj.after('<span style="font-size: 11px; clear: both; margin-top: 3px; display: block;" id="counter-text">Max. '+options.limit+' words</span>');

                    obj.keyup(function() {
                        text = obj.val();
                        if(text === "") {
                            wordcount = 0;
                        } else {
                            wordcount = $.trim(text).split(" ").length;
                        }
                        if(wordcount > options.limit) {
                            $(".textarea-word-count").html('0/'+options.limit);
                            limited = $.trim(text).split(" ", options.limit);
                            limited = limited.join(" ");
                            $(this).val(limited);
                        } else {
                            $(".textarea-word-count").html((options.limit - wordcount)+'/'+options.limit);
                        }
                    });
                });
            };
        })(jQuery);
        $("#share-content").textareaCounter({ limit: 1000 });
    }

    var openPopupPreview = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-articel' } }); }, 500);
    }

    var submitContest = function(){
        var check_form = checkContestSubmitForm();

        if(check_form){
            //change flag submission to false
            flag_submission = false;

            //append to form data
            var formData = new FormData();
            $(".form-share").find('input[type=file]').each(function(index, file){
                if(file.files[0] != undefined){
                    formData.append('file[]', file.files[0]);
                }
            });

            //add loading icon
            $('#send-contest-btn').addClass('show-loading');

            $.ajax({
                url: baseurl + "/"+languageShort+"/contest-submit", //submit to contest submit action
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                type: 'post',
                success: function (res) {
                    //change flag submission to true
                    flag_submission = true;

                    //add loading icon
                    $('#send-contest-btn').removeClass('show-loading');

                    gtag('event', 'submit-success');
                    gtag('config', 'UA-81046101-40', {
                        'page_path': '/ga-submit-success'
                    });

                    var result = JSON.parse(res);
                    if(result.status){
                        finishSavingContestSubmit(result.fileuploaded);
                    }else{
                        if(result.limit_capacity){
                            $('#empty-upload-error').removeClass('d-none');
                            $('#empty-upload-error span').text(trans_MaxCapacity);
                        }else{
                            helperJs.bzClosePopup();
                            helperJs.bzOpenPopup(
                                {items:
                                        { src: '#pop-alert'},
                                    beforeOpen(){
                                        $('#pop-alert > div > p').text(result.message);
                                    },
                                    afterClose(){
                                        if(result.user_contest_exist){
                                            location.href = baseurl+'/'+languageShort+'/contest';
                                        }
                                    }
                                });
                        }


                    }
                }
            });
        }
    }

    var finishSavingContestSubmit = function(media){
        var shareContent = $('#share-content').val().replace(/<(?:.|\n)*?>/gm, '');
        shareContent = shareContent.replace(/\r?\n/g,'<br/>');
        var dataObject = {};
        dataObject.media_title = $('#share-title').val();
        dataObject.media_destination = $('#goal-location').val();
        dataObject.media_description = shareContent;
        dataObject.media_type = 'images';
        dataObject.media = media.join(',');

        //change flag submission to false
        flag_submission = false;

        //add loading icon
        $('#send-contest-btn').addClass('show-loading');

        $.ajax({
            type : "POST",
            url  : baseurl+"/"+languageShort+"/contest-submit",
            data : dataObject,
            success :  function(res){
                //change flag submission to true
                flag_submission = true;
                //remove loading icon
                $('#send-contest-btn').removeClass('show-loading');

                //do something here
                var result = JSON.parse(res);

                if(result.status){
                    helperJs.bzClosePopup();
                    helperJs.bzOpenPopup(
                        {items:
                                { src: '#pop-contestSuccess'},
                            beforeOpen(){
                            },
                            afterClose(){
                                location.href = baseurl+'/'+languageShort+'/contest';
                            }
                        });
                }else{
                    helperJs.bzClosePopup();
                    helperJs.bzOpenPopup(
                        {items:
                                { src: '#pop-alert'},
                            beforeOpen(){
                                $('#pop-alert > div > p').text(result.message);
                            },
                            afterClose(){

                            }
                        });
                }


            }
        });
    }

    var checkContestSubmitForm = function(){
        //reset validation
        resetContestValidation();

        //check select goal location
        var goal_location = $('#goal-location').val();
        if(goal_location == -1){
            $('#goal-location').closest('.form-group').addClass('error');
            return false;
        }

        //check image selection
        if($('.form-share .img-load').attr('src') == '#'){
            $('#empty-upload-error').removeClass('d-none');
            $('#empty-upload-error span').text(trans_Image_upload_missing);
            return false;
        }

        var check_extension = true;
        $(".form-share").find('input[type=file]').each(function(index, file){
            var fileExtension = ['jpeg', 'jpg', 'png'];

            if(file.files[0] != undefined){
                if ($.inArray(file.value.split('.').pop().toLowerCase(), fileExtension) == -1) {
                    check_extension = false;
                }
            }
        });
        if(!check_extension){
            $('#empty-upload-error').removeClass('d-none');
            $('#empty-upload-error span').text(trans_ImageFormatError);
            return false;
        }

        if($('#share-title').val() == ''){
            $('#share-title').closest('.form-group').addClass('error');
            $('.coment .messages-error div').text(trans_InsertComment);
            return false;
        }

        if($('#share-content').val() == ''){
            $('.coment').addClass('error');
            $('.coment .messages-error div').text();
            return false;
        }

        if($.trim($('#share-content').val()).split(" ").length >= 1000){
            $('.coment').addClass('error');
            $('.coment .messages-error div').text(trans_CommentMoreThan1000);
            return false;
        }

        if(!flag_submission){
            return false;
        }

        return true;
    }

    var clearContestForm = function(){
        $('#share-title').val('');
        $('#share-content').val('');
    }

    var resetContestValidation = function(){
        $('#goal-location').closest('.form-group').removeClass('error');
        $('#empty-upload-error').addClass('d-none');
        $('#share-title').closest('.form-group').removeClass('error');
        $('.coment').removeClass('error');
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

    /**/
    var voteContest = function(contestId){

    }

    /*winner contest send button*/
    $('#preview-send-submit-contest').click(function(){
        winnerSubmitContest();
    });
    $('#send-winner-contest').click(function(){
        winnerSubmitContest();
    });

    var winnerSubmitContest = function(){
        var check_form = checkContestSubmitForm();
        console.log(check_form);

        if(check_form){
            //change flag submission to false
            flag_submission = false;

            //append to form data
            var formData = new FormData();
            $(".form-share").find('input[type=file]').each(function(index, file){
                if(file.files[0] != undefined){
                    formData.append('file[]', file.files[0]);
                }
            });

            //add loading icon
            $('#send-winner-contest').addClass('show-loading');

            $.ajax({
                url: baseurl + "/"+languageShort+"/winner-submit", //submit to contest submit action
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                type: 'post',
                success: function (res) {
                    //change flag submission to true
                    flag_submission = true;

                    //add loading icon
                    $('#send-contest-btn').removeClass('show-loading');

                    gtag('event', 'submit-success');
                    gtag('config', 'UA-81046101-40', {
                        'page_path': '/ga-submit-success'
                    });

                    var result = JSON.parse(res);
                    if(result.status){
                        finishSavingWinerSubmit(result.fileuploaded);
                    }else{
                        if(result.limit_capacity){
                            $('#empty-upload-error').removeClass('d-none');
                            $('#empty-upload-error span').text(trans_MaxCapacity);
                        }else{
                            helperJs.bzClosePopup();
                            helperJs.bzOpenPopup(
                                {items:
                                        { src: '#pop-alert'},
                                    beforeOpen(){
                                        $('#pop-alert > div > p').text(result.message);
                                    },
                                    afterClose(){
                                        if(result.user_contest_exist){
                                            location.href = baseurl+'/'+languageShort+'/list-winner-submit';
                                        }
                                    }
                                });
                        }


                    }
                }
            });
        }
    }

    var finishSavingWinerSubmit = function(media){
        var shareContent = $('#share-content').val().replace(/<(?:.|\n)*?>/gm, '');
        shareContent = shareContent.replace(/\r?\n/g,'<br/>');
        var dataObject = {};
        dataObject.media_title = $('#share-title').val();
        dataObject.media_destination = $('#goal-location').val();
        dataObject.media_description = shareContent;
        dataObject.media_type = 'winner';
        dataObject.media = media.join(',');

        //change flag submission to false
        flag_submission = false;

        //add loading icon
        $('#send-contest-btn').addClass('show-loading');

        $.ajax({
            type : "POST",
            url  : baseurl+"/"+languageShort+"/winner-submit",
            data : dataObject,
            success :  function(res){
                //change flag submission to true
                flag_submission = true;
                //remove loading icon
                $('#send-contest-btn').removeClass('show-loading');

                //do something here
                var result = JSON.parse(res);

                if(result.status){
                    helperJs.bzClosePopup();
                    helperJs.bzOpenPopup(
                        {items:
                                { src: '#pop-contestSuccess'},
                            beforeOpen(){
                            },
                            afterClose(){
                                location.href = baseurl+'/'+languageShort+'/list-winner-submit';
                            }
                        });
                }else{
                    helperJs.bzClosePopup();
                    helperJs.bzOpenPopup(
                        {items:
                                { src: '#pop-alert'},
                            beforeOpen(){
                                $('#pop-alert > div > p').text(result.message);
                            },
                            afterClose(){

                            }
                        });
                }


            }
        });
    }

    var contestAjaxLoad = function(element, contest_type){
        var next_page = parseInt($('#contest-cur-page').val()) + 1;
        var formData = new FormData();
        formData.append('page', next_page);
        formData.append('contest_type', contest_type);

        //start loading
        $('.paginaion-bar-container .ajax-paging').addClass('show-loading');
        element.hide();

        $.ajax({
            url: baseurl + "/contest-ajax-load", //submit to contest submit action
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            type: 'post',
            success: function (res) {
                var response = JSON.parse(res);
                var html = response.html;

                //end loading
                $('.paginaion-bar-container .ajax-paging').removeClass('show-loading');
                element.show();

                if(html == ''){
                    element.remove();
                }

                $('.mod-list-item .row').append(html);
                $('#contest-cur-page').val(next_page);
            }
        });
    }

    return {
        init:init
    }
}();