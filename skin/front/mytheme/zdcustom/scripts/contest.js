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
                $('#preview-content').html($('#share-content').val().replace(/\r?\n/g,'<br/>'));

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
    }

    var openPopupPreview = function(){
        helperJs.bzClosePopup();
        setTimeout(function(){  helperJs.bzOpenPopup({items: { src: '#pop-articel' } }); }, 500);
    }

    var submitContest = function(){
        var check_form = checkContestSubmitForm();

        if(check_form){
            //append to form data
            var formData = new FormData();
            $(".form-share").find('input[type=file]').each(function(index, file){
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
                                    }
                                });
                        }


                    }
                }
            });
        }
    }

    var finishSavingContestSubmit = function(media){
        var dataObject = {};
        dataObject.media_title = $('#share-title').val();
        dataObject.media_destination = $('#goal-location').val();
        dataObject.media_description = $('#share-content').val().replace(/\r?\n/g,'<br/>');
        dataObject.media_type = 'images';
        dataObject.media = media.join(',');

        $.ajax({
            type : "POST",
            url  : baseurl+"/"+languageShort+"/contest-submit",
            data : dataObject,
            success :  function(res){
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

        if($('#share-content').val().match(/\S+/g).length > 1000){
            $('.coment').addClass('error');
            $('.coment .messages-error div').text(trans_CommentMoreThan1000);
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


    return {
        init:init
    }
}();