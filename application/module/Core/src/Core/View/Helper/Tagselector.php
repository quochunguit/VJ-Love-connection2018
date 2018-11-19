<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class Tagselector extends AbstractHelper {

    function tagselector($id, $curValues, $allValues) {
        ?>
        <link href="<?php echo BASE_URL ?>/skin/common/tagselector/jquery-ui-1.11.3.custom/jquery-ui.css" rel="stylesheet" type="text/css" />    
        <link href="<?php echo BASE_URL ?>/skin/common/tagselector/jquery-tagselector.css" rel="stylesheet" type="text/css" />    

        <script type="text/javascript" src="<?php echo BASE_URL; ?>/skin/common/tagselector/jquery-ui-1.11.3.custom/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo BASE_URL; ?>/skin/common/tagselector/jquery-tagselector.js"></script>
        <script>
            $(function() {

        <?php
        if ($allValues) {
            ?>
                    var tags = [
            <?php
            foreach ($allValues as $key => $value) {
                ?>
                            {id: <?php echo $key; ?>, toString: function() {
                                    return '<?php echo $value; ?>';
                                }},
            <?php } ?>
                    ];
                    $('#tags_d').tagSelector(tags, '<?php echo $id; ?>[]');
        <?php } ?>
            });
        </script>
        <div id="tags_d" class="tagSelector" style="width:100%">
            <?php
            if ($curValues) {
                foreach ($curValues as $key => $value) {
                    ?>

                    <span class="tag">
                        <?php echo $value; ?> <a>&#120;</a>
                        <input class="tags" name="<?php echo $id; ?>[]" type="hidden" value="<?php echo $key; ?>">
                    </span>
                    <?php
                }
            }
            ?>
            <input type="text">
        </div> 
        <?php
    }

}
?>