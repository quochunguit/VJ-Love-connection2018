<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class Multiupload extends AbstractHelper {

    function upload($id, $files, $attributes, $isLoadLibrary = false) {
        $files = json_decode($files, true);
        $filePath = $attributes['file_path'];
        $filePath = $filePath ? $filePath : BASE_URL . '/media/images/';
        ?>

        <?php
        if(!$isLoadLibrary){
        ?>
            <link href="<?php echo BASE_URL ?>/skin/common/swfupload/default.css" rel="stylesheet" type="text/css" />     

            <script type="text/javascript" src="<?php echo BASE_URL; ?>/skin/common/swfupload/swfupload.js"></script>
            <script type="text/javascript" src="<?php echo BASE_URL; ?>/skin/common/swfupload/swfupload.queue.js"></script>
            <script type="text/javascript" src="<?php echo BASE_URL; ?>/skin/common/swfupload/fileprogress.js"></script>
            <script type="text/javascript" src="<?php echo BASE_URL; ?>/skin/common/swfupload/handlers.js"></script>
        <?php } ?>
        <script type="text/javascript">
            var mvCount = '<?php echo sizeof($files) > 0 ? sizeof($files) : 0 ?>';
            var swfu<?php echo $id; ?>;
            var path_tmp_folder = baseurl + '/media/tmp/';
            var id_multiupload<?php echo $id; ?> = '<?php echo $id; ?>';
            var buttonStyles =
                    '.theFont {color: #FFF' +
                    ';font-weight: bold' +
                    ';font-family: Arial' +
                    ';} ' +
                    '.theFont:hover {color: #000' +
                    ';} ';

            $(document).ready(function () {
                checkEmptyFile<?php echo $id; ?>();
                var settings<?php echo $id; ?> = {
                    flash_url: "<?php echo BASE_URL; ?>/skin/common/swfupload/swfupload.swf",
                    upload_url: "<?php echo BASE_URL; ?>/upload/admin_upload_multi_file.php",
                    post_params: {"PHPSESSID": ""},
                    file_size_limit: "100 MB",
                    file_types: "*.*",
                    file_types_description: "All Files",
                    file_upload_limit: 100,
                    file_queue_limit: 0,
                    custom_settings: {
                        progressTarget: "fsUploadProgress<?php echo $id; ?>",
                        cancelButtonId: "btnCancel<?php echo $id; ?>"
                    },
                    debug: false,
                    // Button settings
                    button_image_url: "<?php echo BASE_URL ?>/skin/common/swfupload/xuanze_71x22.png",
                    button_placeholder_id: "spanButtonPlaceHolder<?php echo $id; ?>",
                    button_width: 71,
                    button_height: 22,
                    button_text: '<span class="swfBtnSel">Browse</span>',
                    button_text_style: '.swfBtnSel{font-size:12px;background-color:#FFF; }',
                    button_text_left_padding: 15,
                    button_text_top_padding: 2,
                    button_cursor: SWFUpload.CURSOR.HAND,
                    // The event handler functions are defined in handlers.js
                    file_queued_handler: fileQueued<?php echo $id; ?>,
                    file_queue_error_handler: fileQueueError<?php echo $id; ?>,
                    file_dialog_complete_handler: fileDialogComplete<?php echo $id; ?>,
                    upload_start_handler: uploadStart<?php echo $id; ?>,
                    upload_progress_handler: uploadProgress<?php echo $id; ?>,
                    upload_error_handler: uploadError<?php echo $id; ?>,
                    upload_success_handler: uploadSuccess<?php echo $id; ?>,
                    upload_complete_handler: uploadComplete<?php echo $id; ?>,
                    queue_complete_handler: queueComplete<?php echo $id; ?>	// Queue plugin event
                };

                swfu<?php echo $id; ?> = new SWFUpload(settings<?php echo $id; ?>);
            });

            /*======= Handle file =========*/
            function fileQueued<?php echo $id; ?>(file) {
                try {
                    var progress = new FileProgress(file, this.customSettings.progressTarget);
                    progress.setStatus("Pending...");
                    progress.toggleCancel(true, this);

                } catch (ex) {
                    this.debug(ex);
                }

            }

            function fileQueueError<?php echo $id; ?>(file, errorCode, message) {
                try {
                    if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
                        alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
                        return;
                    }

                    var progress = new FileProgress(file, this.customSettings.progressTarget);
                    progress.setError();
                    progress.toggleCancel(false);

                    switch (errorCode) {
                        case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                            progress.setStatus("File is too big.");
                            this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                        case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                            progress.setStatus("Cannot upload Zero Byte files.");
                            this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                        case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                            progress.setStatus("Invalid File Type.");
                            this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                        default:
                            if (file !== null) {
                                progress.setStatus("Unhandled Error");
                            }
                            this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                    }
                } catch (ex) {
                    this.debug(ex);
                }
            }

            function fileDialogComplete<?php echo $id; ?>(numFilesSelected, numFilesQueued) {
                try {
                    if (numFilesSelected > 0) {
                        document.getElementById(this.customSettings.cancelButtonId).disabled = false;
                    }

                    /* I want auto start the upload and I can do that here */
                    this.startUpload();
                } catch (ex) {
                    this.debug(ex);
                }
            }

            function uploadStart<?php echo $id; ?>(file) {
                try {
                    /* I don't want to do any file validation or anything,  I'll just update the UI and
                     return true to indicate that the upload should start.
                     It's important to update the UI here because in Linux no uploadProgress events are called. The best
                     we can do is say we are uploading.
                     */
                    var progress = new FileProgress(file, this.customSettings.progressTarget);
                    progress.setStatus("Uploading...");
                    progress.toggleCancel(true, this);
                }
                catch (ex) {
                }

                return true;
            }

            function uploadProgress<?php echo $id; ?>(file, bytesLoaded, bytesTotal) {
                try {
                    var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

                    var progress = new FileProgress(file, this.customSettings.progressTarget);
                    progress.setProgress(percent);
                    progress.setStatus("Uploading...");
                } catch (ex) {
                    this.debug(ex);
                }
            }

            function uploadSuccess<?php echo $id; ?>(file, serverData) {
                try {
                    file.name = serverData;
                    var progress = new FileProgress(file, this.customSettings.progressTarget);
                    progress.setComplete();
                    progress.setStatus("Complete.");
                    //progress.toggleCancel(false);
                    updateMultiUpload<?php echo $id; ?>(file.id, serverData);
                    cancelUpload<?php echo $id; ?>(file.id, serverData);
                    checkEmptyFile<?php echo $id; ?>();
                } catch (ex) {
                    this.debug(ex);
                }
            }
            //--Custom by nhanptit
            function updateMultiUpload<?php echo $id; ?>(id, filename) {
                var value = $('#' + id_multiupload<?php echo $id; ?>).val();
                if (value) {
                    $('#' + id_multiupload<?php echo $id; ?>).val(value + ',' + filename);
                } else {
                    $('#' + id_multiupload<?php echo $id; ?>).val(filename);
                }
                $('#' + id + ' .progressName').html(filename);
                $('#' + id + ' .box').html('<img style="max-width:760px;" src="' + path_tmp_folder + filename + '"/><br/><input style="min-width:315px" type="text" name="' + id_multiupload<?php echo $id; ?> + '_caption[]"/>');
            }

            var checkEmptyFile<?php echo $id; ?> = function () {
                var $progressBox = $('#fsUploadProgress<?php echo $id; ?>');
                var $images = $progressBox.find('img');
                if ($images.length > 0) {
                    $progressBox.show();
                } else {
                    $progressBox.hide();
                }
            };

            function cancelUpload<?php echo $id; ?>(id) {
                $('#'+id+' .progressCancelDev<?php echo $id; ?>').click(function () {
                    $('#' + id).remove();
                    var multiimage = '';
                    $('.progressWrapperDev<?php echo $id; ?>').each(function () {
                        if (multiimage !== '') {
                            multiimage = multiimage + ',' + $(this).find('.progressName').html();
                        } else {
                            multiimage = $(this).find('.progressName').html();
                        }
                    });
                    $('#' + id_multiupload<?php echo $id; ?>).val(multiimage);
                    checkEmptyFile<?php echo $id; ?>();
                });
            }
            //--End Custom by nhanptit

            function uploadError<?php echo $id; ?>(file, errorCode, message) {
                try {
                    var progress = new FileProgress(file, this.customSettings.progressTarget);
                    progress.setError();
                    progress.toggleCancel(false);

                    switch (errorCode) {
                        case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
                            progress.setStatus("Upload Error: " + message);
                            this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
                            break;
                        case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
                            progress.setStatus("Upload Failed.");
                            this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                        case SWFUpload.UPLOAD_ERROR.IO_ERROR:
                            progress.setStatus("Server (IO) Error");
                            this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
                            break;
                        case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
                            progress.setStatus("Security Error");
                            this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
                            break;
                        case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                            progress.setStatus("Upload limit exceeded.");
                            this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                        case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
                            progress.setStatus("Failed Validation.  Upload skipped.");
                            this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                        case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
                            // If there aren't any files left (they were all cancelled) disable the cancel button
                            if (this.getStats().files_queued === 0) {
                                document.getElementById(this.customSettings.cancelButtonId).disabled = true;
                            }
                            progress.setStatus("Cancelled");
                            progress.setCancelled();
                            break;
                        case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
                            progress.setStatus("Stopped");
                            break;
                        default:
                            progress.setStatus("Unhandled Error: " + errorCode);
                            this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
                            break;
                    }
                } catch (ex) {
                    this.debug(ex);
                }
            }

            function uploadComplete<?php echo $id; ?>(file) {
                if (this.getStats().files_queued === 0) {
                    document.getElementById(this.customSettings.cancelButtonId).disabled = true;
                }
            }

            // This event comes from the Queue Plugin
            function queueComplete<?php echo $id; ?>(numFilesUploaded) {
                var status = document.getElementById("divStatus<?php echo $id; ?>");
                status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
            }

            /*======= Handle file =========*/
        </script>
        <div class="multiupload-box">
            <?php
            $strFile = '';
            foreach ($files as $value) {
                if ($strFile == '') {
                    $strFile = $value['file'];
                } else {
                    $strFile .= ',' . $value['file'];
                }
            }
            $strFile = $strFile != '' ? $strFile : '';
            ?>
            <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $strFile; ?>">
            <div class="fieldset flash" id="fsUploadProgress<?php echo $id; ?>">
                <?php if ($files) { ?>
                    <?php
                    foreach ($files as $key => $value) {
                        ?>
                        <div class="progressWrapper progressWrapperDev<?php echo $id; ?>" id="SWFUpload_0_<?php echo $id.$key; ?>" style="opacity: 1;">
                            <div class="progressContainer blue">
                                <a class="progressCancel progressCancelDev<?php echo $id; ?>" href="javascript:void(0);" style="visibility: visible;"> </a>
                                <div class="progressName"><?php echo $value['file']; ?></div>
                                <div class="progressBarStatus">Complete.</div>
                                <div class="progressBarComplete" style=""></div>
                                <div class="box">
                                    <img style="max-width:450px;" src="<?php echo $filePath . '/' . $value['file']; ?>"> <br>
                                    <input style="width:100%" value='<?php echo $value['caption']; ?>' type="text" name="<?php echo $id; ?>_caption[]">
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <div id="divStatus<?php echo $id; ?>" style="display:none;"></div>
            <div>
                <span id="spanButtonPlaceHolder<?php echo $id; ?>"></span>
                <br/><br/>
                <input id="btnCancel<?php echo $id; ?>" type="button" style="display:none;" value="Cancel All Uploads" onclick="swfu<?php echo $id; ?>.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
            </div>
        </div>
        <?php if ($files) {
            ?>
            <script type="text/javascript">
                $(function () {
                    $('.progressWrapperDev<?php echo $id; ?>').each(function () {
                        cancelUpload<?php echo $id; ?>($(this).attr('id'));
                    });
                });
            </script>
        <?php }
        ?>
        <style type='text/css'>
            .multiupload-box .progressWrapper {width:99%;}
            #fsUploadProgress<?php echo $id; ?> {max-height: 1000px; width:100%; overflow-x:scroll;}
            .progressContainer .box {text-align: center;}
            .progressContainer .box img {margin-bottom: 10px;}
        </style>
        <?php
    }

}
?>