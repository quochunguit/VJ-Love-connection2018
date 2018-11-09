<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Fileupload extends AbstractHelper {

    function upload($id, $files, $attributes) {
        $fileHidden = $attributes['file'] ? $attributes['file'] : $files;
        $filePath = $attributes['file_path'];
        $filePath = $filePath ? $filePath : BASE_URL . '/media/files/';
        ?>
        <script type="text/javascript" src="<?php echo BASE_URL ?>/skin/admin/pixel/javascript/ajaxupload.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                initUploadFile_<?php echo $id; ?>();
                $("input#delete_<?php echo $id ?>").click(function() {
                    $('.display_<?php echo $id ?>').hide();
                    if ($('#delete_<?php echo $id ?>').attr('checked') == 'checked') {
                        $('#<?php echo $id ?>').val('');
                    } else {
                        $('.display_<?php echo $id ?>').show();
                    }
                });

                $('#cancel_<?php echo $id; ?>').click(function() {
                    $('.container_view_<?php echo $id; ?>').hide();
                    $(this).hide();
                });

            });

            function initUploadFile_<?php echo $id; ?>() {
                var buttonz = $('#btn_upload_<?php echo $id; ?>');
                var ajaxupload_<?php echo $id; ?> = new AjaxUpload(buttonz, {
                    action: '<?php echo BASE_URL; ?>/upload/admin_upload_files.php',
                    titleButton: 'Chi chap nhan tap tin van ban (.doc, .docx, .xls, .xlsx, .ppt, .pptx, .pdf)',
                    name: 'myfile',
                    onSubmit: function(file, ext) {
                        ext = ext.toUpperCase();
                        if (!(ext && /^(DOC|DOCX|XLS|XLSX|PPT|PPTX|PDF)$/.test(ext))) {
                            alert('Chi chap nhan tap tin van ban (.doc, .docx, .xls, .xlsx, .ppt, .pptx, .pdf)');
                            return false;
                        } 
                        this.disable(); //disable button choose click
                    },
                    onComplete: function(file, response) {
                        response = jQuery.parseJSON(response);
                        this.enable();
                        if (response.status === true) {
                            $('#<?php echo $id; ?>').val(response.filename);
                            $('#files_<?php echo $id; ?>').html(response.filename);
                            $('#btn_upload_<?php echo $id; ?>').val('Try again');
                            $('.container_view_<?php echo $id; ?>').show();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        </script>
        <div>
            <fieldset class="img_page" style="margin-top: 10px;">
                <table>
                    <input type="hidden" value="<?php echo $fileHidden; ?>" name="<?php echo $id; ?>" id="<?php echo $id; ?>"/>
                    <?php if ($files) { ?>
                        <tr>
                            <td colspan="2">
                                <p class="display_<?php echo $id; ?>"><a href="<?php echo $filePath . $files; ?>"> <?php echo $files; ?></a> </p>

                                <input style="margin:0px;padding:0px;" type="checkbox" class="fix-check" value="1" name="delete_<?php echo $id; ?>" id="delete_<?php echo $id; ?>"/>
                                <label class="fix" for="delete_<?php echo $id; ?>">Check to delete</label>
                                <input type="button" id="btn_upload_<?php echo $id; ?>" class="btn_upload_<?php echo $id; ?>" name="" value="Upload File"/> 
                                <hr/>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>
                            <div style="margin-top: 0px;" class="container_view_<?php echo $id; ?>">
                                <div class="files">
                                    <span id="files_<?php echo $id; ?>"></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <?php if (!$files) { ?>
                                <input type="button" id="btn_upload_<?php echo $id; ?>" class="btn_upload_<?php echo $id; ?>" name="" value="Upload File"/>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <?php
    }
}
?>