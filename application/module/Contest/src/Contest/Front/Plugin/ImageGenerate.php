<?php
namespace Contest\Front\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class ImageGenerate extends AbstractPlugin implements ServiceManagerAwareInterface {

    protected $serviceManager;

    public function imageMerge($pathImgMerge, $pathImgMergeShare, $pathSave){
        try {
            $imgMerge = new \Imagick();
            if (FALSE === $imgMerge->readImage($pathImgMerge)) {
                throw new Exception();
            }

            $imgMergeShare = new \Imagick();
            if (FALSE === $imgMergeShare->readImage($pathImgMergeShare)) {
                throw new Exception();
            }

            $imgMergeShare->compositeImage($imgMerge, \Imagick::COMPOSITE_DEFAULT, 7, 9); /*Border left, top*/
            $imgMergeShare->setImageFileName($pathSave);

            /*Let's write the image.*/ 
            if (FALSE == $imgMergeShare->writeImage()) {
                throw new Exception();
            }
        } catch (Exception $e) {
            /* echo 'Caught exception: ' . $e->getMessage() . "\n"; */
        }
    }

    /*THIS IS CODE DEMO (NOT USE IT)*/
    public function imageMergeDemo($pathImgFace, $pathImgFood, $pathSave){
        try {
            //Face
            list($widthFace, $heightFace, $type, $attr) = getimagesize($pathImgFace);
            $imgFace = new \Imagick();
            if (FALSE === $imgFace->readImage($pathImgFace)) {
                throw new Exception();
            }

            //Food type
            $imgFood = new \Imagick();
            if (FALSE === $imgFood->readImage($pathImgFood)) {
                throw new Exception();
            }

            //Result
            $imgResult = new \Imagick();
            $imgResult->newImage(1200, 628, new \ImagickPixel('transparent')); /*transparent, red, white,...*/
            $imgResult->setImageFormat('png');

            $imgResult->compositeImage($imgFace, \Imagick::COMPOSITE_DEFAULT, 0, 0);
            $imgResult->compositeImage($imgFood, \Imagick::COMPOSITE_DEFAULT, $widthFace, 0);
            $imgResult->setImageFileName($pathSave);

            // Let's write the image. 
            if (FALSE == $imgResult->writeImage()) {
                throw new Exception();
            }
        } catch (Exception $e) {
            //echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public function codeDemo($imageData){
        //print_r($imageData);die;

        $random=rand(1,2);
        switch ($random) {
            case 1:
                $backgroundListImage = WEB_ROOT . '/media/bg_wifi/01_cn.png';
                $backgroundShareImage = WEB_ROOT . '/media/bg_wifi/01_share.png';
                break;
            case 2:
                $backgroundListImage = WEB_ROOT . '/media/bg_wifi/02_cn.png';
                $backgroundShareImage = WEB_ROOT . '/media/bg_wifi/02_share.png';
                break;
        }

        $bgimage = new \Imagick();
        $bgimage->newImage(602, 358, new \ImagickPixel('none'));
        $bgimage->setImageFormat('png');

        $bgimage2 = new \Imagick();
        $bgimage2->newImage(602, 566, new \ImagickPixel('none'));
        $bgimage2->setImageFormat('png');

        $frameimageList = new \Imagick($backgroundListImage);
        $frameimageShare = new \Imagick($backgroundShareImage);

        $frameimageList->scaleImage(602, 358,true);
        $frameimageShare->scaleImage(602, 566,true);

        $bgimage->compositeImage($frameimageList, \Imagick::COMPOSITE_DEFAULT, 0, 0);
        $bgimage2->compositeImage($frameimageShare, \Imagick::COMPOSITE_DEFAULT, 0, 0);


        $draw_constantText = new \ImagickDraw();
        $draw_constantText->setFillColor( '#b60807' );
        $draw_constantText->setTextKerning(1);
        $draw_constantText->setStrokeColor("#ffff");
        $draw_constantText->setStrokeWidth(1);
        $draw_constantText->setFont(WEB_ROOT.'/font/VNF-Lobster.ttf');
        $draw_constantText->setFontSize(20);


        $draw_constantName = new \ImagickDraw();
        $draw_constantName->setFillColor( '#b60807' );
        $draw_constantName->setTextKerning(1);
        $draw_constantName->setStrokeColor("#ffff");
        $draw_constantName->setStrokeWidth(1);
        $draw_constantName->setFont(WEB_ROOT.'/font/VNF-Lobster.ttf');
        $draw_constantName->setFontSize(24);

        $draw_constantThanks = new \ImagickDraw();
        $draw_constantThanks->setFillColor( '#b70d0d' );
        $draw_constantThanks->setTextKerning(1);
        $draw_constantThanks->setFont(WEB_ROOT.'/font/VNF-Lobster.ttf');
        $draw_constantThanks->setFontSize(30);

        $draw_constantMess = new \ImagickDraw();
        $draw_constantMess->setFillColor( '#b70d0d' );
        $draw_constantMess->setTextKerning(1);
        $draw_constantMess->setFont(WEB_ROOT.'/font/VNF-Lobster.ttf');
        $draw_constantMess->setFontSize(15);

        $draw_toThanks = new \ImagickDraw();
        $draw_toThanks->setFillColor( '#b70d0d' );
        $draw_toThanks->setTextKerning(1);

        $draw_toThanks->setFont(WEB_ROOT.'/font/VNF-Lobster.ttf');
        $draw_toThanks->setFontSize(65);

        $draw_toUser = new \ImagickDraw();
        $draw_toUser->setFillColor( '#fff' );
        $draw_toUser->setTextKerning(1);
        $draw_toUser->setStrokeColor("#fff");
        $draw_toUser->setStrokeWidth(1);
        $draw_toUser->setFont(WEB_ROOT.'/font/VNF-Lobster.ttf');
        $draw_toUser->setFontSize(28);


        $bgimage->annotateImage($draw_constantText,260,40,0,'Gửi từ:');
        $bgimage->annotateImage($draw_constantName,240,65,0,$imageData['name']);

        $bgimage->annotateImage($draw_constantThanks,220,105,0,'Cảm ơn');

        $bgimage->annotateImage($draw_toThanks,200,160,0,$imageData['toThanks']);

        $bgimage->annotateImage($draw_constantThanks,300,160,0,'vì');

        $bgimage->annotateImage($draw_constantMess,185,190,0,$imageData['message']);

        $bgimage->annotateImage($draw_constantText,260,300,0,'Đến:');
        $bgimage->annotateImage($draw_constantText,240,325,0,$imageData['toUser']);



        $bgimage->flattenImages();
        $imageName =strtotime(date('Y-m-d H:i:s')).'.png';
        $resultImgPath = WEB_ROOT . '/media/files/images/'.$imageName;
        $bgimage->setImageFileName($resultImgPath);



        $bgimage2->annotateImage($draw_constantText,260,150,0,'Gửi từ:');
        $bgimage2->annotateImage($draw_constantName,240,175,0,$imageData['name']);

        $bgimage2->annotateImage($draw_constantThanks,220,215,0,'Cảm ơn');

        $bgimage2->annotateImage($draw_toThanks,200,270,0,$imageData['toThanks']);

        $bgimage2->annotateImage($draw_constantThanks,300,270,0,'vì');

        $bgimage2->annotateImage($draw_constantMess,185,300,0,$imageData['message']);

        $bgimage2->annotateImage($draw_constantText,260,410,0,'Đến:');
        $bgimage2->annotateImage($draw_constantText,240,435,0,$imageData['toUser']);
        $bgimage2->annotateImage($draw_toUser,324,515,0,$imageData['toUser']);


        $bgimage2->flattenImages();

        $resultImgPath2 = WEB_ROOT . '/media/files/images/share/'.$imageName;

        $bgimage2->setImageFileName($resultImgPath2);



        if (FALSE == $bgimage2->writeImage()) {
            throw new Exception();
        }


        if (FALSE == $bgimage->writeImage()) {
            throw new Exception();
        }

        return $imageName;
    }

    public function codeDemo1($avatar, $saveAvatar, $typeBuild = 'default') {
        try {
            switch ($typeBuild) {
                case 'user': //Mom
                    $imgMask = '/mom_shield_mask.png';
                    $imgShield = '/mom_shield.png';
                    break;
                default: //Child
                    $imgMask = '/mask_img.png';
                    $imgShield = '/top_img.png';
                    break;
            }

            // Let's check whether we can perform the magick. 
            if (TRUE !== extension_loaded('imagick')) {
                throw new Exception('Imagick extension is not loaded.');
            }
            // This check is an alternative to the previous one. 
            // Use the one that suits you better. 
            if (TRUE !== class_exists('Imagick')) {
                throw new Exception('Imagick class does not exist.');
            }
            // Let's find out where we are. 
            $dir = AVATAR_BUILD_PATH;
            // Let's read the images. 
            $glasses = new \Imagick();
            if (FALSE === $glasses->readImage($dir . '/transparent_bg.png')) {
                throw new Exception();
            }

            $mask = new \Imagick();
            if (FALSE === $mask->readImage($dir . $imgMask)) {
                throw new Exception();
            }

            $face1 = new \Imagick();
            if (FALSE === $face1->readImage($avatar)) {
                throw new Exception();
            }

            // $face1->liquidRescaleImage(150, 150, 0, 0);
            $face1->resizeImage(150, 150, \Imagick::FILTER_CATROM, 1);
            $face1->compositeImage($mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0, \Imagick::CHANNEL_ALL);


            // Let's put the glasses on (10 pixels from left, 20 pixels from top of face). 
            $glasses->compositeImage($face1, \Imagick::COMPOSITE_DEFAULT, 0, 0);



            //tree
            $tree = new \Imagick();
            if (FALSE === $tree->readImage($dir . $imgShield)) {
                throw new Exception();
            }

            // Let's put the glasses on (10 pixels from left, 20 pixels from top of face). 
            $glasses->compositeImage($tree, \Imagick::COMPOSITE_DEFAULT, 0, 0);

            // Let's merge all layers (it is not mandatory). 
            $glasses->flattenImages();

            // We do not want to overwrite face.jpg. 
            $glasses->setImageFileName($saveAvatar);

            // Let's write the image. 
            if (FALSE == $glasses->writeImage()) {
                throw new Exception();
            }
        } catch (Exception $e) {
            //echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
    /*END THIS IS CODE DEMO (NOT USE IT)*/

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager) {
        
    }

}
