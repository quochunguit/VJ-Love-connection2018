
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