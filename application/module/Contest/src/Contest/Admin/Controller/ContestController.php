<?php

namespace Contest\Admin\Controller;
use Core\Controller\AdminController;
use Zend\EventManager\EventInterface as Event;
use Zend\View\Model\ViewModel;
use Core\Plugin\Youtube;


class ContestController extends AdminController {
    protected $userModel;

    public $routerName = 'contest';

    public function __construct() {
        $this->modelServiceName = 'Contest\Admin\Model\Contest';
    }

    public function getUserModel() {
        if (!$this->userModel) {
            $this->userModel = $this->getServiceLocator()->get('User\Front\Model\User');
        }
        return $this->userModel;
    }

    public function getForm() {
        if (empty($this->form)) {
            $this->form = $this->getServiceLocator()->get('FormElementManager')->get('Contest\Admin\Form\ContestForm');
        }
        return parent::setupForm($this->form);
    }  

    //-----Process action--------
    public function onBeforeListing(Event $e) {
         $params = $e->getParams();

         
        if ($params) {
            $model = $this->getModel();

            if (!$params['page']) {
                //Reset filter
                $model->setState('filter.status', '');
                $model->setState('filter.search', '');
                $model->setState('filter.featured', '');
                //End Reset filter
            }

            $type = $params['type'];
            if ($type) {
                $model->setState('filter.type', $type);
            }
        }
    }

    public function onBeforeCreate(Event $e) {
        $params = $e->getParams();
        $this->processCropImage('image', $params);     
    }

    public function onBeforeEdit(Event $e) {
        $params = $e->getParams();
        $this->processCropImage('image', $params);
    }

    public function onBeforeDelete(Event $e) {
        $params = $e->getParams();
        $this->processDeleteImage(array('image', 'large_image'), $params);
    }

    public function onAfterPublish(Event $e){
        $params = $e->getParams();
        $contestModel = $this->getModel();
        $postIds = $params['data']['Choose'];
        $item=$contestModel->getItem(array('id'=>$postIds));
        $userModel = $this->getUserModel();
        $userItem = $userModel->getItem(array('id'=>$item['user_id']));
        //print_r($userItem);die;
        if ($postIds) {
            foreach ($postIds as $postId) {
                $mail = $this->getServiceLocator()->get('SendMail');

                if($item['language']=='vi_VN'){
                    $mail->sendPhpMailler(array(
                        'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                        'to' => array('name' => $userItem['name'], 'email' => $userItem['email']),
                        'subject' => 'VIETJET - BÀI DỰ THI CỦA BẠN ĐÃ ĐƯỢC DUYỆT THÀNH CÔNG',
                        'template' => 'email/submitsuccess',
                        'data' => array(
                            'contest_url'=>BASE_URL.'/vi/'.$item['slug'].'/'.$item['id'],
                        )
                    ));
                }else{
                    $mail->sendPhpMailler(array(
                        'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                        'to' => array('name' => $userItem['name'], 'email' => $userItem['email']),
                        'subject' => 'VIETJET - YOUR SUBMISSION HAS BEEN APPROVED SUCCESSFULLY!',
                        'template' => 'email/submit_en',
                        'data' => array(
                            'contest_url'=>BASE_URL.'/en/'.$item['slug'].'/'.$item['id']
                        )
                    ));
                }
                //--TODO: send email--
                // $item = $postModel->getByIdAndSlug($postId);
                // if($item){
                //     $mail = $this->getServiceLocator()->get('SendMail');
                //     $mail->send(array(
                //         'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                //         'to' => array('name' => $item['user_name'], 'email' => $item['user_email']),
                //         'subject' => 'Bài chia sẻ của bạn đã được công nhận hợp lệ!',
                //         'template' => 'email/contest_publish',
                //         'data' => array(
                //             'contest_url'=>BASE_URL.'/chi-tiet-bai-thi/'.$postId
                //         )
                //     ));
                //     $this->addMessage('Bài [id='.$postId.'] đã được duyệt. Một email đã gửi đến cho user.');
                // }
                //--TODO: End send email--
            }
        }    
    }
    //-----End Process action-------

    //----Process upload video to youtube...
    public function uploadyoutubev3Action() {

        $params = $this->getParams();
        $itemId = $params['id'];

        if($itemId){
            $model = $this->getModel();
            $item = $model->getItemById($itemId);
            if($item){
                $youtube = new Youtube();

                $data['youtube_app_name'] = YOUTUBE_APP_NAME; 
                $data['youtube_client_id'] = YOUTUBE_CLIENT_ID;
                $data['youtube_client_secret'] = YOUTUBE_CLIENT_SECRET;
                $data['youtube_redirect_url'] = YOUTUBE_REDIRECT_URI;

                $data['video_path'] = WEB_ROOT . DS . "media" .DS.'videos'.DS. $item['media_file'];     
                $data['video_title'] = 'Video name '.$itemId;
                $data['video_description'] = $item['body'];
                //$data['video_category'] = 22;
                //$data['video_tags'] = array("youtube", "nhan pro");
                
                $processResult = $youtube->processUpload($data);
                if($processResult['status']){

                    $result = $processResult['info'];
                    if($result->status['uploadStatus'] == 'uploaded'){            
                        $saveResult = $model->save(array(
                            'id' => $itemId,
                            'media_link' => "http://www.youtube.com/watch?v=" . $result['id'], 
                            'media_picture' => $result->snippet['thumbnails']['high']['url'],
                            'media_vid'=>$result['id']
                            ));
                    }

                }
                
                echo json_encode($processResult);
                exit();
                
            }else{
                echo json_encode(array('status'=>false,'Contest is not exist!'));
                exit();
            }
        }else{
            echo json_encode(array('status'=>false,'Data is invalid!'));
            exit();
        }
        
        exit();
    }
    //----END Process upload video to youtube...

    //--Export file zipfile image----
    public function exportAction(){
        $params = $this->getParams();
        $type = $params['type'];
        $factory = $this->getServiceLocator()->get('ServiceFactory');
        $model = $this->getModel();
        $data = $model->getDataExport();
        if($type == 'zip'){ //create_zip    

            if($data){
                $zipName = 'image_'.date('Y_m_d_H_i_s').'.zip';  
                $zipPathSave = WEB_ROOT.DS.'media'.DS.'tmp'.DS. $zipName;
                $linkDownload = BASE_URL.'/media/tmp/'.$zipName;

                $arrImg = array();
                foreach ($data as $kP => $vP) {
                    $arrImg2= explode(',',$vP['images']);

                    foreach($arrImg2 as $v){
                        $realImagePath = WEB_ROOT.DS.'media'.DS.'images'.DS.$v;
                        if(file_exists($realImagePath)){
                            $arrImg[]  = $realImagePath;
                        }
                    }

                }
                $isSuccess = $this->createZip($arrImg, $zipPathSave);
                if($isSuccess){
                    return $this->redirectToUrl($linkDownload);
                }else{
                    echo 'Da xay ra loi, vui long thu lai sau!'; die;
                }
            }
        }else{
            //$this->processExport();  /*Export with no image*/
            $this->processExportHasImage(); /*Export with image*/
        }
    }

    private function processExport(){
        $factory = $this->getServiceLocator()->get('ServiceFactory');
        $model = $this->getModel();
        $data = $model->getDataExport();

        require_once VENDOR_INCLUDE_DIR . '/phpoffice/phpexcel-1.8.1/Classes/PHPExcel.php';

        //Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();

        //Set document properties
        $objPHPExcel->getProperties()->setCreator("Bizzon")
        ->setLastModifiedBy("Bizzon")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription('Bizzon')
        ->setKeywords("office 2007 openxml php")
        ->setCategory('Bizzon');

        //---Set name--   
        $style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($style);     
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "DANH SÁCH THAM GIA CUỘC THI");
        $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
        $objPHPExcel->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true)->setSize(15); //Size of Intro file
        $objPHPExcel->getActiveSheet()->getStyle("A2:K2")->getFont()->setBold(true)->setSize(14); //Set size Head Title
        //--End set name--

        //Add some data
        $indexCellTitle = 2;
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$indexCellTitle, 'STT')
        ->setCellValue('B'.$indexCellTitle, 'Id')
        ->setCellValue('C'.$indexCellTitle, 'Họ và tên')
        ->setCellValue('D'.$indexCellTitle, 'Email')
        ->setCellValue('E'.$indexCellTitle, 'Điện thoại')
        ->setCellValue('F'.$indexCellTitle, 'Tiêu đề')
        ->setCellValue('G'.$indexCellTitle, 'Thông điệp')
        ->setCellValue('H'.$indexCellTitle, 'Hình ảnh 1')
        ->setCellValue('I'.$indexCellTitle, 'Hình ảnh 2')
        ->setCellValue('J'.$indexCellTitle, 'Hình ảnh 3')
        ->setCellValue('K'.$indexCellTitle, 'Hình ảnh 4')
        ->setCellValue('L'.$indexCellTitle, 'Hình ảnh 5')
        ->setCellValue('M'.$indexCellTitle, 'Điểm')
        ->setCellValue('N'.$indexCellTitle, 'Ngày tham gia')
        ->setCellValue('O'.$indexCellTitle, 'Trạng thái')
        ->setCellValue('P'.$indexCellTitle, 'URL')
        ->setCellValue('Q'.$indexCellTitle, 'Chuyến đi đã chọn');
        
        $cell = 0;
        $status ='';
        foreach($data as $key => $val){
            $arrayImg = explode(',',$val['images']);
            if($val['created']=='0000-00-00'){$val['created']='2018-11-23';};
            if($val['status']==0){$status='unpublish';}elseif($val['status']==1){$status='publish';}else{$status='Rejected';}
            $user = $factory->getUser($val['user_id']);
            $cell = 3 + $key;
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $cell, $key + 1)
            ->setCellValue('B' . $cell, $val['id'])
            ->setCellValue('C' . $cell, $user['name'])
            ->setCellValue('D' . $cell, $user['email'])
            ->setCellValue('E' . $cell, $user['phone'])

            ->setCellValue('F' . $cell, $val['title'])
                ->setCellValue('G' . $cell, $val['description'])
                ->setCellValue('H' . $cell, '<img src="images/' . $arrayImg[0] . '"/>')
                ->setCellValue('I' . $cell, '<img src="images/' . $arrayImg[1] . '"/>')
                ->setCellValue('J' . $cell, '<img src="images/' . $arrayImg[2] . '"/>')
                ->setCellValue('K' . $cell, '<img src="images/' . $arrayImg[3] . '"/>')
                ->setCellValue('L' . $cell, '<img src="images/' . $arrayImg[4] . '"/>')

                ->setCellValue('M' . $cell, $val['featured'])
            ->setCellValue('N'. $cell, $val['created'])
            ->setCellValue('O'. $cell, $status)
                ->setCellValue('P'. $cell, BASE_URL.'/'.$this->getShortLang($val['language']).'/'.$val['slug'].'/'.$val['id'])
            ->setCellValue('Q'. $cell, $this->translate($val['destination']));

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(100);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(100);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(100);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(100);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(100);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(50);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(50);

          
            $objPHPExcel->getActiveSheet()->getStyle('G'. $cell)->getAlignment()->setWrapText(true);
        }
        $objPHPExcel->getActiveSheet()->getStyle("A2:K2".$cell)->getFont()->setSize(13); //Set size content

        //--Set border---
        // $style = array(
        //     'borders' => array(
        //         'allborders' => array(
        //             'style' => \PHPExcel_Style_Border::BORDER_THIN
        //         )
        //     )
        // );
        // $objPHPExcel->getActiveSheet()->getStyle("A2:K2".$cell)->applyFromArray($style);
        //--End Set border---

        //Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Contest');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //--Set protected--
        //$objSheet = $objPHPExcel->getActiveSheet();
        //$objSheet->getProtection()->setSheet(true)->setPassword('contest');
        //--End Set protected--

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Contest_' .time(). '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //--Khi export co image thi can zip image (admin/contest/export?type=zip) va download ve de cung folder voi file excel nay
    public function processExportHasImage(){

        require_once VENDOR_INCLUDE_DIR . '/excelwriter/simpleexcel.php';
        require_once VENDOR_INCLUDE_DIR . '/excelwriter/excelwriter.inc.php';

        $factory = $this->getServiceLocator()->get('ServiceFactory');
        $model = $this->getModel();
        $data = $model->getDataExport();
     
        $filename = "Contest" . time() . ".xls";
        $file = WEB_ROOT .'/media/tmp/'.$filename;
        $excel = new \ExcelWriter($file);
        if ($excel == false) {
            echo $excel->error;
        }
        $row_title = array(' ', 'Danh sách Bài Dự Thi', ' ', ' ', ' ', ' ', ' ', ' ');
        $row_space = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');

        $excel->writeLine($row_title);
        $excel->writeLine($row_space);
        $lineTitle = array(
            'STT','Id', 'Họ và tên', 'Email', 'Điện thoại', 'Tiêu đề','Thông điệp','Hình ảnh 1','Hình ảnh 2','Hình ảnh 3','Hình ảnh 4','Hình ảnh 5', 'Ngày tham gia','Điểm','Votes','URL','Chuyến', 'Trạng thái',"Winner");
        $excel->writeLine($lineTitle);
        foreach ($data as $key => $value) {
            $user = $factory->getUser($value['user_id']);
            if($value['created']=='0000-00-00 00:00:00'){$value['created']=$user['created'];};
            $winner = 'No';

            $imageArr = explode(',',$value['images']);
            $image1 = 'images/'.$imageArr[0];
            $image2 = 'images/'.$imageArr[1];
            $image3 = 'images/'.$imageArr[2];
            $image4 = 'images/'.$imageArr[3];
            $image5 = 'images/'.$imageArr[4];

            $imgEl1 = '<a   href="' .BASE_URL_MEDIA.'/'. $image1 . '"/>'.$image1;
            $imgEl2 = '<a   href="' .BASE_URL_MEDIA.'/'. $image2 . '"/>'.$image2;
            $imgEl3 = '<a   href="' .BASE_URL_MEDIA.'/'. $image3 . '"/>'.$image3;
            $imgEl4 = '<a   href="' .BASE_URL_MEDIA.'/'. $image4 . '"/>'.$image4;
            $imgEl5 = '<a   href="' .BASE_URL_MEDIA.'/'. $image5 . '"/>'.$image5;
            $url = '<a   href="' .BASE_URL.'/'.$this->getShortLang($value['language']).'/'.$value['slug'].'/'.$value['id'] . '"/>'. BASE_URL.'/'.$this->getShortLang($value['language']).'/'.$value['slug'].'/'.$value['id'].'</a>';
            if($value['is_win_week']==1){
                $winner = 'Yes';
            }
            $excel->writeLine(array(
                $key+1, 
                $value['id'],
                $user['name'],
                $user['email'],
                $user['phone'],

                $value['title'],
                $value['descriptions'],
                $imgEl1,$imgEl2,$imgEl3,$imgEl4,$imgEl5,

                $value['created'],
                $value['featured'],
                    $value['votes'],
                    $url,
                $this->translate($value['destination']),
                $value['status'] == 1 ? 'Publish' : ($value['status'] == 2 ? 'Rejected' : 'Unpublish'),
                    $winner


                )
            );
        }
        $excel->close();
        $filesize = filesize($file);
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=$filename");
        readfile($file);
        exit();
    }
    //--End Export file zipfile image----

    //--Set win--
    public function setwinAction(){
        $params = $this->getParams();
        $winType = $params['win_type'];
        $postId = $params['post_id'];

        $winTypeArr = array('week','final'); //Validate win type
        if($winType && in_array($winType, $winTypeArr) && $postId){
            $postModel = $this->getModel();
            $post = $postModel->getItemById($postId, array('id','title','user_id','created','is_win_week','is_win_final'));
      
            if($post['is_win_'.$winType] == 0){
                 $isWin = 1;
                 $textAction = 'Thiết lập ';
                 $textWinType = '<span style="color:Green; font-weight:bold">Yes</span>';
            }else{
                 $isWin = 0;
                 $textAction = 'Hủy ';
                 $textWinType = '<span style="color:Red; font-weight:bold">No</span>';
            }

            $resultSave = $postModel->save(array('is_win_'.$winType => $isWin),$postId);
            if($resultSave['status']){
//                switch ($winType) {
//                    case 'week':
//                        //--TODO: send email--
//                         $item = $postModel->getByIdAndSlug($postId);
//                         $userModel = $this->getUserModel();
//                         $userItem = $userModel->getItem(array('id'=>$item['user_id']));
//                        if($item['is_win_week'] == 1) {
//                            if ($item['language'] == 'vi_VN') {
//                                $mail = $this->getServiceLocator()->get('SendMail');
//                                $mail->sendPhpMailler(array(
//                                    'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
//                                    'to' => array('name' => $userItem['name'], 'email' => $userItem['email']),
//                                    'subject' => 'VIETJET - CHÚC MỪNG BẠN ĐÃ THẮNG GIẢI THƯỞNG CUỘC THI "KẾT NỐI YÊU THƯƠNG - YÊU LÀ PHẢI TỚI"',
//                                    'template' => 'email/winner',
//                                    'data' => array(
//                                        'contest_url' => BASE_URL . '/vi/' . $item['slug'] . '/' . $item['id'],
//                                        'destination' =>$this->translate($item['destination'])
//                                    )
//                                ));
//                            } else {
//                                $mail = $this->getServiceLocator()->get('SendMail');
//                                $mail->sendPhpMailler(array(
//                                    'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
//                                    'to' => array('name' => $userItem['name'], 'email' => $userItem['email']),
//                                    'subject' => 'VIETJET -  CONGRATULATION! YOU ARE THE WINNER OF “LOVE CONNECTION - LOVE IS REAL TOUCH” CAMPAIGN OF VIETJET',
//                                    'template' => 'email/winner_en',
//                                    'data' => array(
//                                        'contest_url' => BASE_URL . '/vi/' . $item['slug'] . '/' . $item['id'],
//                                        'destination' =>$this->translate($item['destination'])
//                                    )
//                                ));
//                            }
//                        }
//
//                        //--TODO: End send email--
//
//                        $winTextVn = ' tuần ';
//                        break;
//                    case 'final':
//                        $winTextVn = ' chung cuộc ';
//                        break;
//                    default:
//                        $winTextVn = ' ';
//                        break;
//                }
                $winTextVn = ' tuần ';
                $this->returnJsonAjax(array('status' => true, 'message' => $textAction. 'thắng giải'. $winTextVn .'thành công!', 'textWinType'=>$textWinType));
            }else{
                $this->returnJsonAjax(array('status' => false, 'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau!')); 
            }

        }else{
           $this->returnJsonAjax(array('status' => false, 'message' => 'Dữ liệu không hợp lệ!')); 
        }
    }
    //--End Set win--

    //--Reject contest--
    public function rejectAction(){
        $params = $this->getParams();
        $id = $params['id'];

        if($id){
            $contestModel = $this->getModel();
            $item=$contestModel->getItem(array('id'=>$id));
            $userModel = $this->getUserModel();
            $userItem = $userModel->getItem(array('id'=>$item['user_id']));
            if($item && $item['status'] == 0){
                $resultSave = $contestModel->save(array('status' => 2, 'id'=>$item['id'])); //Reject
                if($resultSave['status']){
                    //--TODO: send email--
                    $mail = $this->getServiceLocator()->get('SendMail');
                    if($item['language']=='vi_VN'){
                        $mail->sendPhpMailler(array(
                            'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                            'to' => array('email' => $userItem['email']),
                            'subject' => 'VIETJET - BÀI DỰ THI CỦA BẠN KHÔNG ĐƯỢC DUYỆT!',
                            'template' => 'email/submirejected_vi',
                            'data' => array(
                                'contest_url'=>BASE_URL.'/vi/'.$item['slug'].'/'.$item['id'],
                            )
                        ));
                    }else{
                        $mail->sendPhpMailler(array(
                            'from' => array('name' => EMAIL_SEND_FROM_NAME, 'email' => EMAIL_SEND_FROM_EMAIL),
                            'to' => array('name' => $userItem['name'], 'email' => $userItem['email']),
                            'subject' => 'VIETJET - YOUR SUBMISSSION HAS BEEN DECLINED!',
                            'template' => 'email/submirejected_en',
                            'data' => array(
                                'contest_url'=>BASE_URL.'/en/'.$item['slug'].'/'.$item['id']
                            )
                        ));
                    }
                    //--TODO: End send email--

                    $this->returnJsonAjax(array('status' => true, 'message' => 'Đã từ chối bài thi, một email đã gửi tới cho user!'));
                    return $this->getResponse();
                }else{
                    $this->returnJsonAjax(array('status' => false, 'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau!')); 
                    return $this->getResponse(); 
                }
            }else{
               $this->returnJsonAjax(array('status' => false, 'message' => 'Bài thi không tồn tại hoặc đã được duyệt nên không thể từ chối!')); 
               return $this->getResponse(); 
            }   
        }else{
           $this->returnJsonAjax(array('status' => false, 'message' => 'Dữ liệu không hợp lệ!')); 
           return $this->getResponse(); 
        }
        return $this->getResponse(); 
    }
    //--End Reject contest--

}

