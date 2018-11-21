<?php

namespace Core\Controller;

use Core\Controller\CoreController;
use Zend\View\Model\ViewModel;

class FrontController extends CoreController {

    /*======Block getModel ============*/
    protected $userModel;
    protected $postModel;
    protected $postService;
    protected $categoryModel;
    protected $langModel;
    protected $factory;
    protected $contactModel;
    protected $contestModel;
    protected $voteModel;
    protected $voteService;
    protected $viewRender;

    public function getUserModel() {
        if (!$this->userModel) {
            $this->userModel = $this->getServiceLocator()->get('User\Front\Model\User');
        }
        return $this->userModel;
    }

    public function getContestModel() {
        if (!$this->contestModel) {
            $this->contestModel = $this->getServiceLocator()->get('Contest\Front\Model\Contest');
        }
        return $this->contestModel;
    }

    public function getPostModel() {
        if (!$this->postModel) {
            $this->postModel = $this->getServiceLocator()->get('Content\Front\Model\Post');
        }
        return $this->postModel;
    }

    public function getPostService() {
        if (!$this->postService) {
            $this->postService = $this->getServiceLocator()->get('Content\Front\Service\PostService');
        }
        return $this->postService;
    }

    public function getCategoryModel() {
        if (!$this->categoryModel) {
            $this->categoryModel = $this->getServiceLocator()->get('Category\Front\Model\Category');
        }
        return $this->categoryModel;
    }

    public function getLangModel() {
        if (!$this->langModel) {
            $this->langModel = $this->getServiceLocator()->get('Language\Front\Model\Language');
        }
        return $this->langModel;
    }

    public function getFactory() {
        if (!$this->factory) {
            $this->factory = $this->getServiceLocator()->get('ServiceFactory');
        }
        return $this->factory;
    }

    public function getContactModel() {
        if (!$this->contactModel) {
            $this->contactModel = $this->getServiceLocator()->get('Contact\Front\Model\Contact');
        }
        return $this->contactModel;
    }

    public function getVoteModel() {
        if (!$this->voteModel) {
            $this->voteModel = $this->getServiceLocator()->get('Vote\Front\Model\Vote');
        }
        return $this->voteModel;
    }
    public function getVoteService() {
        if (!$this->voteService) {
            $this->voteService = $this->getServiceLocator()->get('Vote\Front\Service\VoteService');
        }
        return $this->voteService;
    }
   
    public function getViewRender(){
        //---Use--
        // $viewModel = new ViewModel(array(
        //     'storeType' => $storeTypeFix,
        //     'titleOfList' => $titleOfList,
        //     'hasData' => $countData == 0 ? false : true,
        //     'curCityId' => $termId
        // ));

        // $viewModel->setTemplate('application/index/partials/storebycity');
        // $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
        // $htmlBody = $this->renderer->render($viewModel);
        //--End use

        if(!$this->viewRender){
            $this->viewRender = $this->getServiceLocator()->get('ViewRenderer');
        }
        return $this->viewRender;  
    }
    /*======End Block getModel ============*/

    /*====== Block onDispatch ============*/
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->languageProcess();
        //$this->detectDevice(); //TODO: detect device
        //$this->detectCountry();
        $_SESSION['chooseLang']= true;

        parent::onDispatch($e);
    }

    public function detectCountry(){
        $yourIp = $this->getClientIp();
        //echo $yourIp;die;
        $result =  file_get_contents('https://www.iplocate.io/api/lookup/'.$yourIp);
        $result = json_decode($result);

        //print_r($result);die;

    }



    private function getClientIp() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    //-- Process Language--
    public function languageProcess(){
        $params = $this->getParams();
        $langRoute = $params['lang'];
        if($langRoute){
            $validLang = array('vi', 'en');
            if(in_array($langRoute, $validLang)){
                switch ($langRoute) {
                    case 'vi':
                        $myLocale = 'vi_VN';
                        break;
                    case 'en':
                        $myLocale = 'en_US';
                        break;
                }
            }else{
                $this->langRedirectDefault();
            }
        }else{
            //Only home to fix lang
            $contrl = $params['controller'];
            $action = $params['action'];
            if(!$params['code'] && $contrl == 'Application\Controller\Index' && $action == 'index'){
                $this->langRedirectDefault();
            }
            //End Only home to fix lang
        }

        $this->setLangCode($myLocale);
        $outputView = array('myLocale'=>$myLocale);

        $myLocaleArr = explode('_', trim($myLocale));
        if(count($myLocaleArr) > 0){
            $outputView['myLocaleShort'] = $myLocaleArr[0]; 
        }

        $this->layout()->setVariables($outputView);
    }

    private function langRedirectDefault(){
        $myLocale = $this->getDefaultLang();
        $myLocaleArr = explode('_', trim($myLocale));
        if(count($myLocaleArr) > 0){
            $urlHomeFix = $this->url()->fromRoute('home', array('lang'=>$myLocaleArr[0]));
            header("Location: " . $urlHomeFix);
            exit();
        }
    }

    private function getDefaultLang(){
        $langCodeDefault = 'vi_VN';
        $langModel = $this->getLangModel();
        $lang = $langModel->getItem(array('is_default'=>1, 'status'=>1));
        if($lang){
            $langCodeDefault = $lang['lang_code'];
        }
            
        return $langCodeDefault;
    }
    //--End process Language--

    //--Mobile detect--
    public function detectDevice() {
        $deviceType = $this->getDeviceType();
        //$deviceType = 'tablet';
        //$this->view->deviceType = $deviceType;
        $this->layout()->deviceType = $deviceType;
        if ($deviceType == 'phone') {
            if (strpos($_SERVER['REQUEST_URI'], '/m/') === false) {
                header("Location: " . BASE_URL . '/m/');
                exit();
            }
        }
    }

    public function getDeviceType() {
        require_once VENDOR_INCLUDE_DIR . '/detect/Mobile.php';
        $detect = new \Mobile;
        return ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
    }
    //--End Mobile detect--
    /*====== End Block onDispatch ============*/

    /*====== All function common ==============*/


    public function apiProcessPaging($paging = array()){
        $pagingFix = array();
        if($paging){
            $curPage = $paging->page;
            $total = $paging->total;
            $itemPerPage = $paging->itemPerPage;
            $totalPage = ceil($total/$itemPerPage);

            $isNext = $isPrev = false; //Default
            if($total > $itemPerPage){
                $isNext = true;
            }

            if($curPage > 1){
                $isPrev = true;
            }

            if($totalPage == $curPage){
                 $isNext = false;
            }

            if($total == $itemPerPage || $curPage > $totalPage){
                $isNext = $isPrev = false;
            }

            $pagingFix['isNext'] = $isNext;
            $pagingFix['isPrev'] = $isPrev;
            $pagingFix['totalPage'] = $totalPage;
            $pagingFix['total'] = $total;
            $pagingFix['itemPerPage'] = $itemPerPage;
        }
        return $pagingFix;
    }

    public function randomCode($length = 6, $isMobileCode = true){
        $valid_chars = $isMobileCode ? "0123456789ABCDEFGHIJKL".time()."MNOPQRSTUVWXYZ" : "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ*@#$%";
        // start with an empty random string
        $random_string = "";

        // count the number of chars in the valid chars string so we know how many choices we have
        $num_valid_chars = strlen($valid_chars);

        // repeat the steps until we've created a string of the right length
        for ($i = 0; $i < $length; $i++)
        {
            // pick a random number from 1 up to the number of valid chars
            $random_pick = mt_rand(1, $num_valid_chars);

            // take the random character out of the string of valid chars
            // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
            $random_char = $valid_chars[$random_pick-1];

            // add the randomly-chosen char onto the end of our string so far
            $random_string .= $random_char;
        }

        // return our finished random string
        return $random_string;
    }

    public function apiProcessUser($user=array()){
        $userFix = array();
        if($user){
            $userFix['id'] = $user['id'];
            if($user['token']){
                $userFix['token'] = $user['token'];
            }  
            $userFix['name'] = $user['name'];
            $userFix['email'] = $user['email'];
            $userFix['phone'] = $user['phone'];
            $userFix['location'] = $user['location'];
            $userFix['address'] = $user['address'];
            $userFix['avatar'] = $user['social_picture'];
            $userFix['is_updated_info'] =  $user['is_updated_info']; //Flag update info
            $userFix['mobile_code'] =  $user['mobile_code'];
            $userFix['status'] = $user['status'];
        }

        return  $userFix;
    }

    //---TODO: Can thay doi phu hop voi tung du an---
    public function checkUserStatus($user=array()){
        if($user){
            $status = $user['status'];
            if($user['is_updated_info'] == 0 && $status == 0 && $user['social_type'] == 'Facebook'){
                return array('status'=>false, 'status_key'=>'reg_fb','phone'=>$user['phone'], 'message'=>'Đăng ký bằng facebook!');
            }

            if($user['is_updated_info'] == 1 && $status == 0 && $user['social_type'] == 'Facebook'){
                return array('status'=>false, 'status_key'=>'reg_fb','phone'=>$user['phone'], 'message'=>'Đăng ký bằng facebook!');
            }

            if($user['is_updated_info'] == 0 && $status == 0 && $user['social_type'] == 'Google'){
                return array('status'=>false, 'id'=>$user['id'], 'name'=>$user['name'], 'status_key'=>'reg_gg', 'location'=>$user['location'], 'phone'=>$user['phone'], 'message'=>'Đăng ký bằng Google!');
            }
            if($user['is_updated_info'] == 1 && $status == 0){
                return array('status'=>false, 'need_active'=>true, 'id'=>$user['id'], 'name'=>$user['name'], 'status_key'=>'inactive', 'location'=>$user['location'], 'phone'=>$user['phone'], 'message'=>'Tài khoản chưa active!');
            }

            if($user['is_updated_info'] == 1 && $status == 2){
                return array('status'=>false, 'status_key'=>'blocked', 'message'=>'Tài khoản đã bị khóa!');
            } 

            if($status == 1){
                return array('status'=>true, 'status_key'=>'activated', 'message'=>'Tài khoản hợp lệ!');
            }     
        }else{
            return array('status'=>false,  'status_key'=>'not_exist', 'message'=>'Bạn cần đăng nhập mới có thể tiếp tục!'); 
        }
    }

    public function slug($str, $charSpace = ' ') {

        $marTViet = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă",
            "ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề"
        , "ế", "ệ", "ể", "ễ",
            "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ"
        , "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ",
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă"
        , "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ"
        , "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ",
            "&");

        $marKoDau = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a"
        , "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o"
        , "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d",
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A"
        , "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O"
        , "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D",
            "-");

        $str = str_replace($marTViet, $marKoDau, $str);
        $strArray = explode(' ', $str);
        foreach ($strArray as $key => $value) {
            if (empty($value)) {
                unset($strArray[$key]);
            }
        }
        $str = implode($charSpace, $strArray);

        $str = str_replace(' ','-',$str);
        $str = $this->seo_friendly_url($str);
        return $str;
    }

    private function seo_friendly_url($string){
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
        return strtolower(trim($string, '-'));
    }

    public function clearText($string){

        $string = preg_replace("/[^\pL0-9_\s]/", "", $string);
        return strtolower(trim($string, ' '));

    }

    public function _substr($str, $length, $minword = 3)
    {
        $sub = '';
        $len = 0;
        foreach (explode(' ', $str) as $word)
        {
            $part = (($sub != '') ? ' ' : '') . $word;
            $sub .= $part;
            $len += strlen($part);
            if (strlen($word) > $minword && strlen($sub) >= $length)
            {
                break;
            }
        }
        return $sub . (($len < strlen($str)) ? '...' : '');
    }

    /*====== End All function common ==============*/

}
