<?php

namespace Vote\Front\Controller;

use Core\Controller\FrontController;

class VoteController extends FrontController {
    /*
    * Params:
    * 1. type: votes | shares
    * 2. object_id: id bai du thi
    * 3. user_token (Neu dung api)
    * 4. extension (Neu có, mac dinh la contest)
    */
    public function voteAction(){
        $params = $this->getParams();

        $actionAPI = false; //Neu goi api thi = true, neu dung tren site bt thì = false
        if($actionAPI){
            $token = $params['user_token'];
            $userModel = $this->getUserModel();
            $curUser = $userModel->getUserByToken($token);
        }else{
            $curUser = $this->getUserLogin();
        }

        if(!$curUser){
            $this->returnJsonAjax(array('status' => false, 'is_login'=> true, 'message' => $this->translate('UserNotLogin')));
        }
        
        $type = $params['type'];
        $validType = array('votes','shares');
        if($type && in_array($type,  $validType)){
            $extension = $params['extension'] ? $params['extension'] : 'contest';
            $objectId = $params['object_id'];
            if($curUser){
                $userId = $curUser['id'];
            }
            switch ($type) {
                case 'votes': //Vote  
                        $validUser = $this->checkUserStatus($curUser);
                        if($validUser['status']){
                            $this->saveVote($type, $objectId, $userId, $extension);
                        }else{
                             $this->returnJsonAjax($validUser);
                        }  
                    break;
                default: //Share
                        $this->saveVote($type, $objectId, $userId, $extension);
                    break;
            }        
        }else{
            $this->returnJsonAjax(array('status' => false, 'message' => 'Dữ liệu không hợp lệ!'));
        }
        exit();
    }

    function saveVote($type, $objectId, $userId, $extension){
        $contestModel = $this->getContestModel();
        $voteModel = $this->getVoteModel();
        $voteService = $this->getVoteService();

        $contest = $contestModel->getItemById($objectId);
        if(!$contest['id']){
            $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('SubmissionNotExist')));
        }

        if($type == 'votes'){
            $isVoted = $voteService->isVoted($objectId, $userId, $type, $extension);
            if ($isVoted) { //Voted
                $this->returnJsonAjax(array('status' => false, 'message' => $this->translate('SubmissionVoted')));
            } 

            $title = 'Bình chọn bài dự thi';
            $textSuccess = $this->translate('SubmissionVoteSuccess');
        }else{
            $title = 'Chia sẻ bài dự thi';
            $textSuccess = 'Bạn đã chia sẻ thành công!';
        }

        $resultSave = $voteService->saveInfo(array('user_id'=>$userId, 'title'=>$title, 'object_id'=>$objectId, 'type'=>$type, 'ip'=>$this->getIpClient(), 'extension'=>$extension));
        if ($resultSave['status']) {
            $numCount = $voteModel->getCountDataVote($objectId, $type, $extension);
            $numCount = ($contest['votes'] - $numCount + $numCount) +1;
            $resutlSave = $contestModel->save(array($type => $numCount), $objectId);//Update count for contest
            if($resutlSave['status']){
                $this->returnJsonAjax(array('status' => true, 'message' => $textSuccess,'currentCount'=> $numCount));
            }else{
                $this->returnJsonAjax(array('status' => false, 'message' => 'Đã có lỗi xảy ra, vui lòng thử lại!'));
            }
        } else {
            $this->returnJsonAjax(array('status' => false, 'message' => 'Đã có lỗi xảy ra, vui lòng thử lại!'));
        }
    }

}
