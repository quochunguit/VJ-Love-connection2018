<?php
namespace Core\View\Helper;
use Zend\View\Helper\AbstractHelper;

class Cropimageupload extends AbstractHelper {

    function upload($id, $files, $attributes, $options, $Ratio, $isCrop = true) {
        $fileHidden = $attributes['file'] ? $attributes['file'] : $files;
        $filePath = $attributes['file_path'];
        $filePath = $filePath ? $filePath : BASE_URL . '/media/images/';
        ?>
        <link rel="stylesheet" href="<?php echo BASE_URL ?>/skin/common/cropimage/css/jquery.Jcrop.css" type="text/css" />
        <script type="text/javascript" src="<?php echo BASE_URL ?>/skin/common/cropimage/js/ajaxupload.js"></script>
        <script type="text/javascript" src="<?php echo BASE_URL ?>/skin/common/cropimage/js/jquery.Jcrop.js"></script>
        <script type="text/javascript">
            var jcrop_api_<?php echo $id; ?>;
            var rect_<?php echo $id; ?> = [0, 0, <?php echo $options[0] ?>, <?php echo $options[1] ?>];
            $(document).ready(function() {
                initUploadImageArticle_<?php echo $id; ?>();
                $("input#delete_<?php echo $id ?>").click(function() {
                    $('.display_<?php echo $id ?>').hide();
                    if ($('#delete_<?php echo $id ?>').attr('checked') == 'checked') {
                        //$('#<?php echo $id ?>').val('');
                    } else {
                        $('.display_<?php echo $id ?>').show();
                    }
                });

                $('#cancel_<?php echo $id; ?>').click(function() {
                    $('.container_view_<?php echo $id; ?>').hide();
                    $(this).hide();
                });

            });

            function initUploadImageArticle_<?php echo $id; ?>() {
                var buttonz = $('#btn_uploadimage_<?php echo $id; ?>');
                var ajaxupload_<?php echo $id; ?> = new AjaxUpload(buttonz, {
                    action: '<?php echo BASE_URL; ?>/upload/admin_upload_crop.php',
                    name: 'myfile',
                    onSubmit: function(file, ext) {
                        if (!(ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext))) {
                            alert('Please choose files .jpg, .png, .jpeg');
                            return false;
                        }
                    },
                    onComplete: function(file, response) {
                        response = jQuery.parseJSON(response);
                        if (response.status === true) {
                            $('#<?php echo $id; ?>').val(response.filename);
                            if (jcrop_api_<?php echo $id; ?>) {
                                if (jcrop_api_<?php echo $id; ?>.setImage) {
                                    jcrop_api_<?php echo $id; ?>.setImage(baseurl + '/media/tmp/' + response.filename);
                                    jcrop_api_<?php echo $id; ?>.setSelect(rect_<?php echo $id; ?>);
                                }
                            } else {
                                var jcrop_target_<?php echo $id; ?> = $('#image_to_crop_<?php echo $id; ?>');
                                $(jcrop_target_<?php echo $id; ?>).attr('src', baseurl + '/media/tmp/' + response.filename);
                                <?php
                                if($isCrop){
                                ?>
                                    initCrop_<?php echo $id; ?>(jcrop_target_<?php echo $id; ?>);
                                <?php } ?>

                                $('#cancel_<?php echo $id; ?>').show();
                            }
                            this.disable();

                            $('#cancel_<?php echo $id; ?>').show();
                            $('.container_view_<?php echo $id; ?>').show();

                        } else {
                            alert(response.message);
                        }
                    }
                });
                $('#cancel_<?php echo $id; ?>').click(function() {
                    ajaxupload_<?php echo $id; ?>.enable();
                });
            }

            function initCrop_<?php echo $id; ?>(jcrop_target_<?php echo $id; ?>) {
                $(jcrop_target_<?php echo $id; ?>).Jcrop({
                    onChange: showPreview_<?php echo $id; ?>,
                    onSelect: showPreview_<?php echo $id; ?>,
                    aspectRatio: getAspectRatio_<?php echo $id; ?>(),
                    setSelect: rect_<?php echo $id; ?>
                    //minSize: [<?php echo $options[0] ?>, <?php echo $options[0] ?>],
                    //maxSize: [<?php echo $options[1] ?>, <?php echo $options[1] ?>]
                }, function() {
                    jcrop_api_<?php echo $id; ?> = this;
                });
            }
            function getAspectRatio_<?php echo $id; ?>() {
                var ratio = <?php echo $Ratio ?>;
                return ratio;
            }
            function showPreview_<?php echo $id; ?>(coords)
            {
                $('#x_<?php echo $id; ?>').val(coords.x);
                $('#y_<?php echo $id; ?>').val(coords.y);
                $('#w_<?php echo $id; ?>').val(coords.w);
                $('#h_<?php echo $id; ?>').val(coords.h);
            }
        </script>
        <div class="block_crop block_crop_img_<?php echo $id; ?>">
            <fieldset class="img_page" style="margin-top: 10px;">
                <table>
                    <input type="hidden" value="<?php echo $fileHidden; ?>" name="<?php echo $id; ?>" id="<?php echo $id; ?>"/>
                    <?php if ($files) { ?>
                        <tr>
                            <td colspan="2">
                                <img style="max-width: 400px;" class="display_<?php echo $id; ?>" src="<?php echo $filePath . $files; ?>"/><Br/><br/>
                                <input style="margin:0px;padding:0px;" type="checkbox" class="fix-check" value="1" name="delete_<?php echo $id; ?>" id="delete_<?php echo $id; ?>"/>

                                <label class="fix" for="delete_<?php echo $id; ?>">Delete?</label>
                                <input type="button" id="btn_uploadimage_<?php echo $id; ?>" class="btn_uploadimage_<?php echo $id; ?>" name="" value="Upload Image"/>
                                <br/>
                                <hr/>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>
                            <div style="margin-top: 0px;" class="container_view_<?php echo $id; ?>">
                                <div class="uploadimage_<?php echo $id; ?>" style="display: block;">
                                    <div class="files">
                                        <img src="" id="image_to_crop_<?php echo $id; ?>" style="max-width: 4096px !important;"/>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <?php if (!$files) { ?>
                                <input type="button" id="btn_uploadimage_<?php echo $id; ?>" class="btn_uploadimage_<?php echo $id; ?>" name="" value="Upload Image"/>
                            <?php } ?>

                            <?php
                            if($isCrop){
                            ?>
                                <input type="button" value="Cancel" id="cancel_<?php echo $id; ?>" style="display: none;">
                                <input type="hidden" id="x_<?php echo $id; ?>" name="x_<?php echo $id; ?>" value="0"/>
                                <input type="hidden" id="y_<?php echo $id; ?>" name="y_<?php echo $id; ?>" value="0"/>
                                <input type="hidden" id="w_<?php echo $id; ?>" name="w_<?php echo $id; ?>" value="0"/>
                                <input type="hidden" id="h_<?php echo $id; ?>" name="h_<?php echo $id; ?>" value="0"/>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                    if($isCrop){
                    ?>
                        <tr>
                            <td>
                               <p>==* Deselect if you want to take the original image *==</p>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </fieldset>
        </div>
        <style type="text/css">
            .block_crop {width: 100%; overflow-x: scroll; }            
        </style>
        <?php
    }
}
?>