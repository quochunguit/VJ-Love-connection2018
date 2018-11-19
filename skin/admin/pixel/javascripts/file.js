
var Media = function() {

    var media_append_element = '';
    var element_media_name = '';
    var is_begin = 0;
    var $currentMediaButton;

    function addMedia(path) {
        $('#filechoose').val(path);
    }
    function resizeIframe(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    }
    function displayFile(file) {
        if (isImageFile(file)) {
            displayImage(file);
        } else {
            displayOtherFile(file);
        }
    }
    function displayImage(file) {
        var template = "\
                        <div class='file-row'> \
                         <img src='<%=file%>' class='file-preview-image'/> \
                         <span class='file-name'><%=fileName%></span> \
                         <input type='hidden' class='file_hidden' name='<%=name%>' value='<%=file%>'>\
                         <button class='btn btn-default btn-delete-file'>delete</button> \
                       </div> \
                     ";
        var html = new EJS({text: template}).render({file: file, fileName: getFileName(file), name: element_media_name + ""});
        appendFile(html);
    }
    function appendFile(html) {
        media_append_element.append(html);
        init();
    }
    function displayOtherFile(file) {
        var icon = getFileIcon(file);
        var template = "\
                    <div class='file-row'> \
                        <i class='fa <%=icon%> fa-3x green'></i> \
                        <span class='file-name'><%=fileName%></span> \
                        <input type='hidden' class='file_hidden' name='<%=name%>' value='<%=file%>'>\
                         <button class='btn btn-default  btn-delete-file'>delete</button> \
                    </div> \
                            ";
        var html = new EJS({text: template}).render({icon: icon, file: file, fileName: getFileName(file), name: element_media_name + ""});
        appendFile(html);

    }
    function getFileIcon(file) {
        if (isVideo(file)) {
            return 'fa-file-video-o';
        }
        if (isPdf(file)) {
            return 'fa-file-pdf-o';
        }
        if (isPpt(file)) {
            return 'fa-file-powerpoint-o';
        }
        if (isTxt(file)) {
            return 'fa-file-text-o';
        }
        if (isExcel(file)) {
            return 'fa-file-excel-o';
        }
        if (isMsWord(file)) {
            return 'fa-file-word-o';
        }
        if (isZip(file)) {
            return 'fa-file-archive-o';
        }
        return 'fa-file-o';
    }

    function getFileExt(file) {
        var ext = file.split('.').pop().toLowerCase();
        return ext;
    }
    function getFileName(file) {
        var fileName = file.split('/').pop();
        return fileName;
    }
    function isImageFile(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) > -1) {
            return true;
        }
    }
    function isPdf(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['pdf']) > -1) {
            return true;
        }
    }
    function isZip(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['zip', 'rar']) > -1) {
            return true;
        }
    }
    function isMsWord(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['doc', 'docx']) > -1) {
            return true;
        }
    }
    function isVideo(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['flv', 'mp3', 'mp4']) > -1) {
            return true;
        }
    }
    function isExcel(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['xls', 'xlsx']) > -1) {
            return true;
        }
    }
    function isPpt(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['ppt', 'pptx']) > -1) {
            return true;
        }
    }
    function isTxt(file) {
        var ext = getFileExt(file);
        if ($.inArray(ext, ['txt']) > -1) {
            return true;
        }
    }

    function createModal() {
        if ($('#mediaModal').length > 0) {
            return;
        }
        var template = " \
                <div class='modal fade'  id='mediaModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'> \
                                <div class='modal-dialog'>\
                                    <div class='modal-content'>\
                                        <div class='modal-header'>\
                                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>\
                                            <h4 class='modal-title'>Media Manager</h4>\
                                        </div>\
                                        <div class='modal-body'></div>\
                                        <div class='modal-footer'>\
                                            <div class='form-horizontal'>\
                                               <div class='row form-group'>\
                                                        <label for='filechoose' class='col-sm-4 control-label'>File Choose:</label>\
                                                        <div class='col-sm-8'>\
                                                                <input type='text' name='filechoose' id='filechoose' class='form-control' readonly='readonly'/>\
                                                        </div>\
                                                </div>\
                                            </div>\
                                            <div class='clear'>&nbsp;</div>\
                                            <button type='button' class='btn btn-primary'  id='btn-choose-file'  data-dismiss='modal'>Choose</button>\
                                            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
			";
        $('body').append(template);
    }
    function isMultiple() {
        if ($currentMediaButton.attr('data-muiltiple') == 'true') {
            return true;
        }
        return false;
    }
    function getCountFiles() {
        var mediaDataAppendElement = $currentMediaButton.attr('data-media-append');
        var append_element = $('.' + mediaDataAppendElement);
        var count = append_element.find('.file-row').length;
        return count;
    }
    function checkMediaButton() {
        $('.show-media-popup').each(function(index, el) {
            var $showMedia = $(this);
            $currentMediaButton = $showMedia;
            if (isMultiple()) {
                $currentMediaButton.fadeIn();
                return true;
            } else {
                if (getCountFiles() >= 1) {
                    //hidden
                    $currentMediaButton.fadeOut();
                }else{
                    $currentMediaButton.fadeIn();
                }
            }

        });

    }
    function initData() {


        $('.show-media-popup').each(function(index, el) {
            var $showMedia = $(this);
            var mediaDataAppendElement = $showMedia.attr('data-media-append');
            element_media_name = $showMedia.attr('name');
            media_append_element = $('.' + mediaDataAppendElement);
            if (mediaDataAppendElement && window[mediaDataAppendElement]) {
                var savedFiles = window[mediaDataAppendElement];
                for (var idx = 0; idx < savedFiles.length; idx++) {
                    var file = savedFiles[idx];
                    displayFile(file);
                }
            }
        });
    }
    function init() {
        if (!hasMediaPopup()) {
            return;
        }
        createModal();

        if (is_begin == 0) {
            is_begin = 1;
            initData();

        }

        $('.show-media-popup').click(function() {
            var $showMedia = $(this);
            $currentMediaButton = $showMedia;
            var mediaDataAppendElement = $showMedia.attr('data-media-append');
            var mediaFolder = $showMedia.attr('data-media-folder');
            var mediaFilter = $showMedia.attr('data-media-filter');
            element_media_name = $showMedia.attr('name');
            media_append_element = $('.' + mediaDataAppendElement);
            var mediaUrl = 'admin/media/popup';
            
            var params = new Array();
            if(mediaFolder!=undefined && mediaFolder !=''){
                params.push("d="+ mediaFolder);
            }
             if(mediaFilter!=undefined && mediaFilter !=''){
                params.push("filter="+ mediaFilter);
            }
            
            
            if(params.length > 0){
                var paramStr = params.join('&');
                mediaUrl+="?"+paramStr;
            }
            
            var iframe = "<iframe src='"+mediaUrl+"' width='100%' height='100%' frameborder='0' scrolling='no' id='iframe-media' onload='javascript:Media.resizeIframe(this);'></iframe>"
            $('#mediaModal .modal-body').html(iframe);
            $('#mediaModal').modal();
        });


        $('.btn-delete-file').click(function() {
            $(this).parent('.file-row').remove();
            checkMediaButton();
        });

        $('#btn-choose-file').click(function() {
            if ($('#filechoose').val().length > 3) {
                var file = $('#filechoose').val();
                if (file) {
                    displayFile(file);
                    $('#filechoose').val('');
                }

            }
        });
        checkMediaButton();
    }
    function hasMediaPopup() {
        if ($('.show-media-popup').length > 0) {
            return true;
        }
        return false;
    }
    return {
        init: init,
        addMedia: addMedia,
        resizeIframe: resizeIframe,
        displayFile: displayFile
    };

}(jQuery);


$(document).ready(function() {
    Media.init();
});