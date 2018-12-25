<?php

namespace Contest\Front\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\FrontController;

class ContestController extends FrontController {
	public function indexAction(){
        /*Check user login*/
        $userLogin = $this->getUserLogin();

        $params = $this->getParams();
        $this->layout()->setVariables(array('parent_page'=>'home', 'page'=>'contest'));
        //$model = $this->getContestModel();
        //$model->setLimit(6);
        //$model->setParams($params);
        //$listContest = $model->getItems();
        //$listContest = $listContest->toArray();
        //print_r($listContest );die;
        $this->setMetaData(array(), $this->translate('Gallery'));

        $modelBestContest = $this->getContestModel();
        $modelBestContest->setLimit(12);
        $modelBestContest->setState('order.field', 'votes');
        $modelBestContest->setState('filter.keyword', false);
        $modelBestContest->setState('filter.winnercontest', false);
        $bestContest = $modelBestContest->getItems();
        $bestContest = $bestContest->toArray();

        $modelNewestContest = $this->getContestModel();
        $modelNewestContest->setParams($params);
        $modelNewestContest->setLimit(12);
        $modelNewestContest->setState('order.field', 'created');
        //if exist contest search keyword
        if(isset($params['contest-search'])){
            $modelNewestContest->setState('filter.keyword', $params['contest-search']);
        }else{
            $modelNewestContest->setState('filter.keyword', false);
        }
        $newestContest = $modelNewestContest->getItems();
        $newestContest = $newestContest->toArray();

        //return to view index.phtml
        return new ViewModel(array(
            'userLogin'=> $userLogin,
            'newestContest'=> $newestContest,
            'bestContent' => $bestContest,
            'paging' => $modelNewestContest->getPaging()
        ));

	}

	public function submitAction(){
        $this->setMetaData(array(), $this->translate('Submitions'));

	    /*Check user login*/
        $codeShort = $this->getLangCode(true);
        $userLogin = $this->getUserLogin();
        $username = ($userLogin) ? @$userLogin['name'] : '';
        $model = $this->getContestModel();
        $this->layout()->setVariables(array('parent_page'=>'home', 'page'=>'submit'));
        $curLang = $this->getLangCode();

        if($userLogin['status']==1){
            if (!empty($_FILES['file'])) {
                //check if this user has published contest
                /*$publishContest = $model->getContestByUser($userLogin[id], 1, 0);
                if($publishContest && count($publishContest) > 0){
                    $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('ContestPublished')));
                }*/

                $submitscontest = $model->limitContestByUser($userLogin[id]);
                $countContest = 0;
                $maximuContest = 20;
                if($submitscontest && count($submitscontest) > 0){
                    foreach($submitscontest as $k => $limitcontest){
                        if($limitcontest['status'] != 2){
                            $countContest++;
                        }
                    }
                    if($countContest >= $maximuContest){
                        $this->returnJsonAjax(array('status' => false, 'user_contest_exist' => true, 'message' => $this->translate('UserContestMaximum')));
                    }
                }

                /*if(isset($_POST['media_type']) && $_POST['media_type'] == 'video'){
                    if($file_type == "video/mov" || $file_type  == "video/mp4" || $file_type == "video/avi") {
                        $file_size_limit = 31457280;
                        if ($_FILES["file"]["size"] < $file_size_limit) {
                            $target_dir = WEB_ROOT . '/media/videos/';

                            $digits = 3;
                            $random = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                            $newName = round(microtime(true) * 1000) . $random . substr($_FILES['file']['name'], -4);;
                            // start uploading file
                            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $newName)) {
                                //if success uploading file, save to database


                                $this->returnJsonAjax(array('status' => true, 'filename'=> $newName, 'message' => 'Upload video successfully!'));
                            } else { // if not success
                                $this->returnJsonAjax(array('status' => false, 'message' => 'error upload, please try again!'));
                            }
                        } else {
                            $this->returnJsonAjax(array('status' => false, 'message' => 'File size must be smaller 30MB'));
                        }
                    }else{
                        $this->returnJsonAjax(array('status'=>false,'message'=>'Invalid file format, we just allow mp4, avi, mov video format, choose another one, thanks!'));
                    }
                }else{*/
                $fileuploaded = array();
                $sumSize = 0;
                //print_r($_FILES["file"]);

                foreach($_FILES["file"]["size"] as $k => $value){
                    if($value > 5242880){
                        $this->returnJsonAjax(array('status' => false, 'limit_capacity'=>true, 'message' => $this->translate('Max_Capacity')));
                    }
                }

                for($i = 0; $i < count($_FILES["file"]["name"]); $i++){
                    //print_r($file);
                    $file_type = strtolower($_FILES["file"]["type"][$i]);
                    if($file_type == "image/jpeg" || $file_type == "image/png"){
                        $target_dir = WEB_ROOT . '/media/images/';

                        $digits = 3;
                        $random = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                        $newName = round(microtime(true) * 1000) . $random . substr($_FILES["file"]["name"][$i], -4);

                        if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $target_dir . $newName)) {
                            //if success uploading file, save to database
                            //$this->returnJsonAjax(array('status' => true, 'filename'=> $newName, 'message' => 'Upload images successfully!'));
                            array_push($fileuploaded, $newName);
                        } else { // if not success
                            $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('UploadImageError')));
                        }
                    }else{
                        $this->returnJsonAjax(array('status'=>false,'message'=> $this->translate('ImageFormatError')));
                    }
                }
                $this->returnJsonAjax(array('status' => true, 'fileuploaded'=> $fileuploaded, 'message' => 'Upload images successfully!'));
            }

            //save contest submit to database
            if(isset($_POST["media"])){
                $mediaTitle = $_POST["media_title"];
                $mediaDestination = $_POST["media_destination"];
                $mediaDes = $_POST["media_description"];
                $mediaType = $_POST["media_type"];
                $mediaValue = $_POST["media"];

                $contestInfos = array();
                $contestInfos["user_id"] = $userLogin->id;
                $contestInfos["title"] = strip_tags($mediaTitle,'<br>');
                $contestInfos["destination"] = $mediaDestination;
                $contestInfos["descriptions"] = strip_tags($mediaDes,'<br>');
                $contestInfos["type"] = $mediaType;
                $contestInfos["created"] = date();
                if($mediaType == 'video'){
                    $contestInfos["video"] = $mediaValue;
                }else if($mediaType == 'images'){
                    $contestInfos["images"] = $mediaValue;
                }
                $contestInfos["slug"] = $this->slug($mediaTitle);
                $contestInfos['language']= $curLang;

                $contestModel = $this->getContestModel();
                $return = $contestModel->save($contestInfos);

                if ($return['status']) {
                    return  $this->returnJsonAjax(array('status' => true, 'message' => $this->translate('SaveContestSuccessful')));
                }
            }
        }else{
            if($userLogin['phone']!=0 && $userLogin['status']==0){
                $_SESSION['need_active']==true;
            }
            if($userLogin['phone']==0){
                $_SESSION['need_update']==true;
            }
            return $this->redirectToRoute('home',array('lang'=>$codeShort));
        }


        return new ViewModel(array(
            'username'=>$username
        ));
	}

	public function detailAction(){
        $params = $this->getParams();
        $contestModel = $this->getContestModel();
        $this->layout()->setVariables(array('parent_page'=>'home', 'page'=>'detail'));
        $codeShort = $this->getLangCode(true);

        $contest_id = $params['id'];
        $contest_slug = $params['slug'];
        $contest_infos = $contestModel->getByIdAndSlug($contest_id, false);
        $userModel = $this->getUserModel();
        $userContest = $userModel->getItem(array('id'=>$contest_infos['user_id']));

        $modelBestContest = $this->getContestModel();
        $modelBestContest->setLimit(10);
        $modelBestContest->setState('order.field', 'votes');
        $bestContest = $modelBestContest->getItems();
        $bestContest = $bestContest->toArray();
        $imageArray = explode(',',$contest_infos['images']);//print_r($imageArray);die;

        $titleShare = str_replace("'","´",$contest_infos['title']);
        $titleShare = str_replace('"','¨',$titleShare);
        $desShare = $this->_substr($contest_infos['descriptions'],40);
        $desShare = str_replace("'","´",$desShare);
        $desShare = str_replace('"','¨',$desShare);
        $urlShare = BASE_URL.'/'.$codeShort.'/'.$contest_slug.'/'.$contest_id;
        $urlImage = BASE_URL_MEDIA.'/images/'.$imageArray[0];
        $this->layout()->setVariable('data', array('titleShare'=>$titleShare,'desShare'=>$desShare,'urlImage'=>$urlImage));
        $this->setMetaData(array(), $titleShare);
        return new ViewModel(array(
            'contest_infos'=> $contest_infos,
            'bestContent' => $bestContest,
            'titleShare'=>$titleShare,
            'urlshare'=>$urlShare,
            'desShare'=>$desShare,
            'urlImage'=>$urlImage,
            'userName'=>$userContest['name']
        ));
    }

    public function winnersubmitAction(){
        $this->setMetaData(array(), $this->translate('Winer Submitions'));

        /*Check user login*/
        $codeShort = $this->getLangCode(true);
        $userLogin = $this->getUserLogin();
        $username = ($userLogin) ? @$userLogin['name'] : '';
        $model = $this->getContestModel();
        $this->layout()->setVariables(array('parent_page'=>'submit', 'page'=>'winnersubmit'));
        $curLang = $this->getLangCode();

        if($userLogin['status']==1){
            //check if user has winner contest
            $win_contest = $model->getContestByUser($userLogin[id], 1, 0, 1);
            if($win_contest && count($win_contest) > 0){
                $is_win_week = true;
            }else{
                $is_win_week = false;
            }

            //check if user has winner submission
//            $win_submit = $model->getContestByUser($userLogin[id], '', 0, '', 'winner');
//            if($win_submit && count($win_submit) > 0){
//                $is_win_submited = true;
//            }else{
//                $is_win_submited = false;
//            }

            if (!empty($_FILES['file'])) {
                //winner just can post 1 winner submit
                if(!$is_win_week){
                    $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('Bạn không được viết bài chiến thắng!')));
                }

//                if($is_win_submited){
//                    $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('Bạn đã viết bài chiến thắng rồi!')));
//                }

                $fileuploaded = array();
                $sumSize = 0;
                //print_r($_FILES["file"]);

                foreach($_FILES["file"]["size"] as $k => $value){
                    if($value > 5242880){
                        $this->returnJsonAjax(array('status' => false, 'limit_capacity'=>true, 'message' => $this->translate('Max_Capacity')));
                    }
                }

                for($i = 0; $i < count($_FILES["file"]["name"]); $i++){
                    //print_r($file);
                    $file_type = strtolower($_FILES["file"]["type"][$i]);
                    if($file_type == "image/jpeg" || $file_type == "image/png"){
                        $target_dir = WEB_ROOT . '/media/images/';

                        $digits = 3;
                        $random = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
                        $newName = round(microtime(true) * 1000) . $random . substr($_FILES["file"]["name"][$i], -4);

                        if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $target_dir . $newName)) {
                            //if success uploading file, save to database
                            //$this->returnJsonAjax(array('status' => true, 'filename'=> $newName, 'message' => 'Upload images successfully!'));
                            array_push($fileuploaded, $newName);
                        } else { // if not success
                            $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('UploadImageError')));
                        }
                    }else{
                        $this->returnJsonAjax(array('status'=>false,'message'=> $this->translate('ImageFormatError')));
                    }
                }
                $this->returnJsonAjax(array('status' => true, 'fileuploaded'=> $fileuploaded, 'message' => 'Upload images successfully!'));
            }

            //save contest submit to database
            if(isset($_POST["media"])){
                $mediaTitle = $_POST["media_title"];
                $mediaDestination = $_POST["media_destination"];
                $mediaDes = $_POST["media_description"];
                $mediaType = $_POST["media_type"];
                $mediaValue = $_POST["media"];

                $contestInfos = array();
                $contestInfos["user_id"] = $userLogin->id;
                $contestInfos["title"] = strip_tags($mediaTitle,'<br>');
                $contestInfos["destination"] = $mediaDestination;
                $contestInfos["descriptions"] = strip_tags($mediaDes,'<br>');
                $contestInfos["type"] = $mediaType;
                $contestInfos["created"] = date();
                $contestInfos["images"] = $mediaValue;

                $contestInfos["slug"] = $this->slug($mediaTitle);
                $contestInfos['language']= $curLang;

                $contestModel = $this->getContestModel();
                $return = $contestModel->save($contestInfos);

                if ($return['status']) {
                    return  $this->returnJsonAjax(array('status' => true, 'message' => $this->translate('SaveContestSuccessful')));
                }
            }
        }else{
            if($userLogin['phone']!=0 && $userLogin['status']==0){
                $_SESSION['need_active']==true;
            }
            if($userLogin['phone']==0){
                $_SESSION['need_update']==true;
            }
            return $this->redirectToRoute('home',array('lang'=>$codeShort));
        }


        return new ViewModel(array(
            'username'=>$username,
            'is_win_week'=>$is_win_week,
            'is_win_submited'=>$is_win_submited
        ));
    }

    public function listwinnersubmitAction(){
        /*Check user login*/
        $userLogin = $this->getUserLogin();

        $params = $this->getParams();
        $this->layout()->setVariables(array('parent_page'=>'contest', 'page'=>'winnercontest'));
        //$model = $this->getContestModel();
        //$model->setLimit(6);
        //$model->setParams($params);
        //$listContest = $model->getItems();
        //$listContest = $listContest->toArray();
        //print_r($listContest );die;
        $this->setMetaData(array(), $this->translate('Gallery'));

        $modelBestContest = $this->getContestModel();
        $modelBestContest->setLimit(12);
        $modelBestContest->setState('order.field', 'votes');
        $modelBestContest->setState('filter.keyword', false);
        $modelBestContest->setState('filter.winnercontest', 'winner');
        $bestContest = $modelBestContest->getItems();
        $bestContest = $bestContest->toArray();

        $modelNewestContest = $this->getContestModel();
        $modelNewestContest->setParams($params);
        $modelNewestContest->setLimit(12);
        $modelNewestContest->setState('order.field', 'created');
        //if exist contest search keyword
        if(isset($params['contest-search'])){
            $modelNewestContest->setState('filter.keyword', $params['contest-search']);
        }else{
            $modelNewestContest->setState('filter.keyword', false);
        }
        $newestContest = $modelNewestContest->getItems();
        $newestContest = $newestContest->toArray();

        //return to view index.phtml
        return new ViewModel(array(
            'userLogin'=> $userLogin,
            'newestContest'=> $newestContest,
            'bestContent' => $bestContest,
            'paging' => $modelNewestContest->getPaging()
        ));
    }

    public function contestajaxloadAction(){
        $params = $this->getParams();

        $modelNewestContest = $this->getContestModel();
        $modelNewestContest->setParams(array('page'=> $params['page']));
        $modelNewestContest->setLimit(12);
        $modelNewestContest->setState('order.field', 'created');

        if($params['contest_type'] == 'winner'){
            $modelNewestContest->setState('filter.winnercontest', 'winner');
        }

        $newestContest = $modelNewestContest->getItems();
        $newestContest = $newestContest->toArray();

        $template = 'partial/contest_item';
        $data = array('newestContest'=>$newestContest);
        $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
        $htmlBody = $this->renderer->render($template, $data);
        //echo $htmlBody;
        //exit();
        $this->returnJsonAjax(array('status'=>true, 'html'=>$htmlBody));
    }

    public function gen_slug($str){
        //pecial accents
        $a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','Ð','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','?','?','J','j','K','k','L','l','L','l','L','l','?','?','L','l','N','n','N','n','N','n','?','O','o','O','o','O','o','Œ','œ','R','r','R','r','R','r','S','s','S','s','S','s','Š','š','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Ÿ','Z','z','Z','z','Ž','ž','?','ƒ','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','?','?','?','?','?','?');
        $b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/','/[ -]+/','/^-|-$/'),array('','-',''),str_replace($a,$b,$str)));
    }
}
