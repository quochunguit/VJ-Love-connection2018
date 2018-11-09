<?php
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class Sharethis extends AbstractHelper {

    public function sharethis() {
        ?>
        <span title="Facebook Share" class="st_facebook_custom" style="cursor: pointer;"><img src="<?php echo BASE_URL ?>/skin/frontend/images/share_fb.png"> </span>
        <span title="Twitter Share" class="st_twitter_custom" style="cursor: pointer;"><img src="<?php echo BASE_URL ?>/skin/frontend/images/share_tw.png"> </span>
        <span title="Google Plus Share" class="st_googleplus_custom" style="cursor: pointer;"><img src="<?php echo BASE_URL ?>/skin/frontend/images/share_ggplus.png"> </span>
        <script type="text/javascript">var switchTo5x=false;</script>
        <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
        <script type="text/javascript">stLight.options({publisher: "ur-8efc8cae-4240-ae4d-2a1a-a01bda9ee75e"}); </script>
        <?php
    }

}