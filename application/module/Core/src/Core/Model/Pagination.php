<?php
namespace Core\Model;

class Pagination {
    var $itemPerPage = 20;
    var $total = 0;
    var $params = array();
    var $pagingLink = '';
    var $pagingInfo = '';
    var $page = 1;

    public function __construct($total, $itemPerPage, $page = 1) {
        $this->total = $total;
        $this->itemPerPage = $itemPerPage;
        $this->page = $page;
    }

    public function getPagingInfo(){
        return $this->pagingInfo;
    }

    //--************** Admin **********************----
    function getPagingLink( $post = false) {
        $page = $this->page;
        $pageCurrent = $page;
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = explode('?', $request_uri);
        //print_r($request_uri[1]);
        $query_string = @$_SERVER['QUERY_STRING'];
        $url = "http://" . $_SERVER['HTTP_HOST'] . $request_uri['0'];
        $path = @$_SERVER['PATH_INFO'];
        //var_dump($query_string);

        if ($path) {
            $path = trim($path, '/');
        }

        if ($query_string) {
            $params = array();
            parse_str($query_string, $params);
            if ($params) {
                $this->params = $params;
                unset($this->params['page']);
            }
        }

        if ($this->params) {
            $paging_url = $url . $path . '?' . http_build_query($this->params) . '&page=';
        } else {
            $paging_url = $url . $path . '?page=';
        }

        $pagingLink = '';
        $totalPages = ceil($this->total / $this->itemPerPage);
        // how many link pages to show
        $numLinks = 5;
        // create the paging links only if we have more than one page of results
        if ($totalPages > 1) {
            $pageNumber = $page;
            // print 'previous' link only if we're not
            // on page one
            if ($pageNumber > 1) {
                $page = $pageNumber - 1;
                if ($pageNumber > 1) {
                    if ($post) {
                        $prev = " <a href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage(page); \" title='Prev'>Prev</a> ";
                        $first = " <a class='pagin_first' href=\"" . $paging_url . '1' . "\" onclick=\" var page= " . 1 . "; App.loadPage(page); \" title='First'>First</a> ";
                    } else {
                        $prev = " <a href=\"$paging_url$page\"  title='Prev'>Prev</a> ";
                        $first = " <a class='pagin_first'  href=\"" . $paging_url . '1' . "\" onclick=\" var page= " . 1 . "; App.loadPage(page); \"  title='First'>First</a> ";
                    }
                } else {
                    $prev = " <a href=\"#\"  title='Prev'>Prev</a> ";
                }
            } else {
                $prev = ''; // we're on page one, don't show 'previous' link
                $first = ''; // nor 'first page' link
            }

            // print 'next' link only if we're not
            // on the last page
            if ($pageNumber < $totalPages) {
                $page = $pageNumber + 1;
                if ($post) {
                    $next = " <a class='pagin_next' href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage(page);  \" title='Next' >Next</a> ";
                    $last = " <a class='pagin_last'  href=\"javascript:;\" onclick=\" var page= " . $totalPages . "; App.loadPage(page); \" title='Last'>Last</a> ";
                } else {
                    $next = " <a href=\"$paging_url$page\" onclick=\" var page= " . $page . "; App.loadPage(page);  \"   title='Next' >Next</a> ";
                    $last = " <a href=\"$paging_url$totalPages\" onclick=\" var page= " . $totalPages . "; App.loadPage(page); \" title='Last'>Last</a> ";
                }
            } else {
                $next = ''; // we're on the last page, don't show 'next' link  Next&rsaquo;&rsaquo;
                $last = ''; // nor 'last page' link
            }

            $specialNum = (int)($numLinks/2);
            if($pageNumber > 3){
                $start = $pageNumber - $specialNum;
            }

            if ($pageNumber == $totalPages) {
                $start = $pageNumber - $numLinks + 1;
            }

            if ($start <= 0) {
                $start = 1;
            }

            $end = $start + $numLinks - 1;
            $end = min($totalPages, $end);

            $pagingLink = array();
            for ($page = $start; $page <= $end; $page++) {
                if ($page == $pageNumber) {

                    $pagingLink[] = "<li class='active'><span class='paging_current'  title='$page'> $page </span></li>";   // no need to create a link to current page
                } else {
                    if ($page == 1) {
                        if ($post) {
                            $pagingLink[] = " <li><a class='paginglink' href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        } else {
                            $pagingLink[] = " <li><a href=\"$paging_url$page\"  class='paging' onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        }
                    } else {
                        if ($post) {
                            $pagingLink[] = " <li><a  class='paginglink' href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        } else {
                            $pagingLink[] = " <li><a  class='paginglink' href=\"$paging_url$page\" onclick=\" var page= " . $page . "; App.loadPage( page);  \"  title='$page'>$page</a> </li>";
                        }
                    }
                }
            }

            $pagingLink = implode('  ', $pagingLink);
            // return the page navigation link
            $pagingLink = '<div class="pull-right"><ul class="pagination"><li>'. $first.'</li>' .'<li>'. $prev .'</li>' . $pagingLink .'<li>'. $next.'</li>' .'<li>'. $last .'</li></ul></div>';
            $pagingLink = str_replace('<li></li>', '', $pagingLink);
        }

        $currPage = '<input type="hidden" id="page" value="' . @$pageNumber . '" name="page" />';
        if($pageCurrent > 1){
            $startRecord = ($pageCurrent -1 )* $this->itemPerPage + 1;
        }else{
            if($this->total==0){
                $startRecord = 0;     
            }else{
                $startRecord = 1;     
            }
          
        }

        $toRecord = $pageCurrent * $this->itemPerPage;
        if($toRecord > $this->total){
            $toRecord = $this->total;
        }

        $this->pagingInfo = $this->total > 0 ? "<div class='total_page'>Showing from $startRecord to $toRecord of total $this->total</div>" : "";
        $this->pagingLink =  $pagingLink;
        
        return $this->pagingLink;
    }

    //--************** Front **********************----
    function getPagingLinkFront( $post = false, $noprevnext = false) {
        $page = $this->page;
        $pageCurrent = $page;
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = explode('?', $request_uri);
        //print_r($request_uri[1]);
        $query_string = @$_SERVER['QUERY_STRING'];

        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        $url = ($https ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'] . $request_uri['0'];
        $path = @$_SERVER['PATH_INFO'];
        //var_dump($query_string);

        if ($path) {
            $path = trim($path, '/');
        }
        if ($query_string) {
            $params = array();
            parse_str($query_string, $params);
            if ($params) {
                $this->params = $params;
                unset($this->params['page']);
            }
        }

        if ($this->params) {
            $paging_url = $url . $path . '?' . http_build_query($this->params) . '&page=';
        } else {
            $paging_url = $url . $path . '?page=';
        }
        $pagingLink = '';
        $totalPages = ceil($this->total / $this->itemPerPage);
        // how many link pages to show
        $numLinks = 5;
        // create the paging links only if we have more than one page of results
        if ($totalPages > 1) {
            $pageNumber = $page;
            // print 'previous' link only if we're not
            // on page one
            if ($pageNumber > 1 && $noprevnext) {
                $page = $pageNumber - 1;
                if ($pageNumber > 1) {
                    if ($post) {
                        $prev = " <li><a class=\"prev\" href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage(page); \" title='<<'><<</a> </li>";
                        $first = " <li><a href=\"" . $paging_url . '1' . "\" onclick=\" var page= " . 1 . "; App.loadPage(page); \" title='Đầu'>Đầu</a> </li>";
                    } else {
                        $prev = " <li><a class=\"prev\" href=\"$paging_url$page\"  title='<<'><<</a> </li>";
                        $first = " <li><a href=\"" . $paging_url . '1' . "\" onclick=\" var page= " . 1 . "; App.loadPage(page); \"  title='Đầu'>Đầu</a> </li>";
                    }
                } else {
                    $prev = " <li><a href=\"#\"  title='<<'><<</a> </li>";
                }
            } else {
                $prev = ''; // we're on page one, don't show 'previous' link
                $first = ''; // nor 'first page' link
            }

            // print 'next' link only if we're not
            // on the last page
            if ($pageNumber < $totalPages && $noprevnext) {
                $page = $pageNumber + 1;
                if ($post) {
                    $next = " <li><a class=\"next\" href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage(page);  \" title='>>' >>></a> </li>";
                    $last = " <li><a href=\"javascript:;\" onclick=\" var page= " . $totalPages . "; App.loadPage(page); \" title='Cuối'>Cuối</a> </li>";
                } else {
                    $next = " <li><a class=\"next\" href=\"$paging_url$page\" onclick=\" var page= " . $page . "; App.loadPage(page);  \"   title='>>' >>></a> </li>";
                    $last = " <li><a href=\"$paging_url$totalPages\" onclick=\" var page= " . $totalPages . "; App.loadPage(page); \" title='Cuối'>Cuối</a> </li> ";
                }
            } else {
                $next = ''; // we're on the last page, don't show 'next' link  Next&rsaquo;&rsaquo;
                $last = ''; // nor 'last page' link
            }
            $specialNum = (int)($numLinks/2);
            if($pageNumber > 3){
                $start = $pageNumber - $specialNum;
            }

            if ($pageNumber == $totalPages) {
                $start = $pageNumber - $numLinks + 1;
            }
            if ($start <= 0) {
                $start = 1;
            }
            $end = $start + $numLinks - 1;

            $end = min($totalPages, $end);

            $pagingLink = array();

            for ($page = $start; $page <= $end; $page++) {
                if ($page == $pageNumber) {

                    $pagingLink[] = "<li class='page-item active'><a class='page-link' style =\"cursor:default\" title='$page'> $page </a></li>";   // no need to create a link to current page
                } else {
                    if ($page == 1) {
                        if ($post) {
                            $pagingLink[] = " <li class='page-item'><a class='page-link' href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        } else {
                            $pagingLink[] = " <li class='page-item'><a class='page-link' href=\"$paging_url$page\"  class='paging' onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        }
                    } else {
                        if ($post) {
                            $pagingLink[] = " <li class='page-item'><a class='page-link' href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        } else {
                            $pagingLink[] = " <li class='page-item'><a class='page-link' href=\"$paging_url$page\" onclick=\" var page= " . $page . "; App.loadPage( page);  \"  title='$page'>$page</a> </li>";
                        }
                    }
                }
            }

            $pagingLink = implode('  ', $pagingLink);
            // return the page navigation link
            //$pagingLink = '<ul>'. $first. $prev . $pagingLink . $next.$last .'</ul>';
            $pagingLink = '<ul class="pagination">'. $prev . $pagingLink . $next .'</ul>';
            $pagingLink = str_replace('<li></li>', '', $pagingLink);
        }

        $currPage = '<input type="hidden" id="page" value="' . @$pageNumber . '" name="page" />';
        if($pageCurrent > 1){
            $startRecord = ($pageCurrent -1 )* $this->itemPerPage + 1;
        }else{
            if($this->total==0){
                $startRecord = 0;     
            }else{
                $startRecord = 1;     
            }
        }
        $toRecord = $pageCurrent * $this->itemPerPage;
        if($toRecord > $this->total){
            $toRecord = $this->total;
        }
        $this->pagingInfo = "<div class='total_page'>Đang hiển thị từ $startRecord đến $toRecord trên tổng số $this->total </div>";
        $this->pagingLink =  $pagingLink;
        
        return $this->pagingLink;
    }

    function getPagingLinkFrontNextBack( $post = false) {
        $page = $this->page;
        $pageCurrent = $page;
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = explode('?', $request_uri);
        //print_r($request_uri[1]);
        $query_string = @$_SERVER['QUERY_STRING'];

        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        $url = ($https ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'] . $request_uri['0'];
        $path = @$_SERVER['PATH_INFO'];
        //var_dump($query_string);

        if ($path) {
            $path = trim($path, '/');
        }
        if ($query_string) {
            $params = array();
            parse_str($query_string, $params);
            if ($params) {
                $this->params = $params;
                unset($this->params['page']);
            }
        }

        if ($this->params) {
            $paging_url = $url . $path . '?' . http_build_query($this->params) . '&page=';
        } else {
            $paging_url = $url . $path . '?page=';
        }
        $pagingLink = '';
        $totalPages = ceil($this->total / $this->itemPerPage);
        // how many link pages to show
        $numLinks = 5;
        // create the paging links only if we have more than one page of results
        if ($totalPages > 1) {
            $pageNumber = $page;
            // print 'previous' link only if we're not
            // on page one
            if ($pageNumber > 1) {
                $page = $pageNumber - 1;
                if ($pageNumber > 1) {
                    if ($post) {
                        $prev = " <li class=\"page-item\"><a class=\"page-link\" rel=\"prev\" aria-label=\"Previous\" href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage(page); \" title='<<'><span aria-hidden=\"true\" class=\"icomoon icon-chevron-left\"></span></a> </li>";
                        $first = " <li><a href=\"" . $paging_url . '1' . "\" onclick=\" var page= " . 1 . "; App.loadPage(page); \" title='Đầu'>Đầu</a> </li>";
                    } else {
                        $prev = " <li class=\"page-item\"><a class=\"page-link\" rel=\"prev\" aria-label=\"Previous\" href=\"$paging_url$page\"  title='<<'><span aria-hidden=\"true\" class=\"icomoon icon-chevron-left\"></span></a> </li>";
                        $first = " <li><a href=\"" . $paging_url . '1' . "\" onclick=\" var page= " . 1 . "; App.loadPage(page); \"  title='Đầu'>Đầu</a> </li>";
                    }
                } else {
                    $prev = " <li class=\"page-item\"><a class=\"page-link\" rel=\"prev\" aria-label=\"Previous\" href=\"#\"  title='<<'><span aria-hidden=\"true\" class=\"icomoon icon-chevron-left\"></span></a> </li>";
                }
            } else {
                $prev = ''; // we're on page one, don't show 'previous' link
                $first = ''; // nor 'first page' link
            }

            // print 'next' link only if we're not
            // on the last page
            if ($pageNumber < $totalPages) {
                $page = $pageNumber + 1;
                if ($post) {
                    $next = " <li class=\"page-item\"><a class=\"page-link\" rel=\"next\" aria-label=\"Next\" href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage(page);  \" title='>>' ><span aria-hidden=\"true\" class=\"icomoon icon-chevron-right\"></span></a> </li>";
                    $last = " <li><a href=\"javascript:;\" onclick=\" var page= " . $totalPages . "; App.loadPage(page); \" title='Cuối'>Cuối</a> </li>";
                } else {
                    $next = " <li class=\"page-item\"><a class=\"page-link\" rel=\"next\" aria-label=\"Next\" href=\"$paging_url$page\" onclick=\" var page= " . $page . "; App.loadPage(page);  \"   title='>>' ><span aria-hidden=\"true\" class=\"icomoon icon-chevron-right\"></span></a> </li>";
                    $last = " <li><a href=\"$paging_url$totalPages\" onclick=\" var page= " . $totalPages . "; App.loadPage(page); \" title='Cuối'>Cuối</a> </li> ";
                }
            } else {
                $next = ''; // we're on the last page, don't show 'next' link  Next&rsaquo;&rsaquo;
                $last = ''; // nor 'last page' link
            }
            $specialNum = (int)($numLinks/2);
            if($pageNumber > 3){
                $start = $pageNumber - $specialNum;
            }

            if ($pageNumber == $totalPages) {
                $start = $pageNumber - $numLinks + 1;
            }
            if ($start <= 0) {
                $start = 1;
            }
            $end = $start + $numLinks - 1;

            $end = min($totalPages, $end);

            $pagingLink = array();

            for ($page = $start; $page <= $end; $page++) {
                if ($page == $pageNumber) {

                    $pagingLink[] = "<li class='active'><a style =\"cursor:default\" title='$page'> $page </a></li>";   // no need to create a link to current page
                } else {
                    if ($page == 1) {
                        if ($post) {
                            $pagingLink[] = " <li><a href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        } else {
                            $pagingLink[] = " <li><a href=\"$paging_url$page\"  class='paging' onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        }
                    } else {
                        if ($post) {
                            $pagingLink[] = " <li><a href=\"javascript:;\" onclick=\" var page= " . $page . "; App.loadPage( page);  \" title='$page'>$page</a> </li>";
                        } else {
                            $pagingLink[] = " <li><a href=\"$paging_url$page\" onclick=\" var page= " . $page . "; App.loadPage( page);  \"  title='$page'>$page</a> </li>";
                        }
                    }
                }
            }

            $pagingLink = implode('  ', $pagingLink);
            // return the page navigation link
            //$pagingLink = '<ul>'. $first. $prev . $pagingLink . $next.$last .'</ul>';

        }

        $currPage = '<input type="hidden" id="page" value="' . @$pageNumber . '" name="page" />';
        if($pageCurrent > 1){
            $startRecord = ($pageCurrent -1 )* $this->itemPerPage + 1;
        }else{
            if($this->total==0){
                $startRecord = 0;     
            }else{
                $startRecord = 1;     
            }
        }
        $toRecord = $pageCurrent * $this->itemPerPage;
        if($toRecord > $this->total){
            $toRecord = $this->total;
        }
        $this->pagingInfo = "<div class='total_page'>Đang hiển thị từ $startRecord đến $toRecord trên tổng số $this->total </div>";

        $pageInfo = '<li class="page-item">
              <a class="page-link"><span>'.$startRecord.'</span>/ <span>'.$this->total.'</span></a>
            </li>';

        $pagingLink = ($prev || $next) ? $prev . $pageInfo . $next : '';
        $pagingLink = str_replace('<li></li>', '', $pagingLink);


        $this->pagingLink =  $pagingLink;
        
        return $this->pagingLink;
    }

    function getPagingLinkFrontNextBackGalleryAjax($pagingParams = array()) {
        //App.GalleryPagingAjax.loadPaging('".$ajaxUrl."', '".$galleryType."', '".$elAppend."', '".$page."');

        $ajaxUrl = $pagingParams['ajax_url'];
        $galleryType = $pagingParams['gallery_type'];
        $elAppend = $pagingParams['el_append'];

        $page = $this->page;
        $pageCurrent = $page;
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = explode('?', $request_uri);
        //print_r($request_uri[1]);
        $query_string = @$_SERVER['QUERY_STRING'];

        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
        $url = ($https ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'] . $request_uri['0'];
        $path = @$_SERVER['PATH_INFO'];
        //var_dump($query_string);

        if ($path) {
            $path = trim($path, '/');
        }
        if ($query_string) {
            $params = array();
            parse_str($query_string, $params);
            if ($params) {
                $this->params = $params;
                unset($this->params['page']);
            }
        }

        if ($this->params) {
            $paging_url = $url . $path . '?' . http_build_query($this->params) . '&page=';
        } else {
            $paging_url = $url . $path . '?page=';
        }
        $pagingLink = '';
        $totalPages = ceil($this->total / $this->itemPerPage);
        // how many link pages to show
        $numLinks = 5;
        // create the paging links only if we have more than one page of results
        if ($totalPages > 1) {
            $pageNumber = $page;
            // print 'previous' link only if we're not
            // on page one
            if ($pageNumber > 1) {
                $page = $pageNumber - 1;
                if ($pageNumber > 1) {
                    $prev = " <li class=\"page-item\"><a class=\"page-link\" rel=\"prev\" aria-label=\"Previous\" href=\"javascript:void(0);\" onclick=\"javascript:App.GalleryPagingAjax.loadPaging('".$ajaxUrl."', '".$galleryType."', '".$elAppend."', '".$page."'); return false;\"  title='<<'><span aria-hidden=\"true\" class=\"icomoon icon-chevron-left\"></span></a> </li>";
                    $first = " <li><a href=\"" . $paging_url . '1' . "\" onclick=\" var page= " . 1 . "; App.loadPage(page); \"  title='Đầu'>Đầu</a> </li>";
                    
                } else {
                    $prev = " <li class=\"page-item\"><a class=\"page-link\" rel=\"prev\" aria-label=\"Previous\" href=\"#\"  title='<<'><span aria-hidden=\"true\" class=\"icomoon icon-chevron-left\"></span></a> </li>";
                }
            } else {
                $prev = ''; // we're on page one, don't show 'previous' link
                $first = ''; // nor 'first page' link
            }

            // print 'next' link only if we're not
            // on the last page
            if ($pageNumber < $totalPages) {
                $page = $pageNumber + 1;
              
                $next = " <li class=\"page-item\"><a class=\"page-link\" rel=\"next\" aria-label=\"Next\" href=\"javascript:void(0);\" onclick=\"javascript:App.GalleryPagingAjax.loadPaging('".$ajaxUrl."', '".$galleryType."', '".$elAppend."', '".$page."'); return false;\"   title='>>' ><span aria-hidden=\"true\" class=\"icomoon icon-chevron-right\"></span></a> </li>";
                $last = " <li><a href=\"$paging_url$totalPages\" onclick=\" var page= " . $totalPages . "; App.loadPage(page); \" title='Cuối'>Cuối</a> </li> ";
                
            } else {
                $next = ''; // we're on the last page, don't show 'next' link  Next&rsaquo;&rsaquo;
                $last = ''; // nor 'last page' link
            }
            $specialNum = (int)($numLinks/2);
            if($pageNumber > 3){
                $start = $pageNumber - $specialNum;
            }

            if ($pageNumber == $totalPages) {
                $start = $pageNumber - $numLinks + 1;
            }
            if ($start <= 0) {
                $start = 1;
            }
            $end = $start + $numLinks - 1;

            $end = min($totalPages, $end);

            $pagingLink = array();

            for ($page = $start; $page <= $end; $page++) {
                if ($page == $pageNumber) {

                    $pagingLink[] = "<li class='active'><a style =\"cursor:default\" title='$page'> $page </a></li>";   // no need to create a link to current page
                } else {
                    if ($page == 1) {
                      
                        $pagingLink[] = " <li><a class='paging' href=\"javascript:void(0);\" onclick=\"javascript: App.GalleryPagingAjax.loadPaging('".$ajaxUrl."', '".$galleryType."', '".$elAppend."', '".$page."'); return false;\" title='$page'>$page</a> </li>";
                        
                    } else {
                        $pagingLink[] = " <li><a href=\"javascript:void(0);\" onclick=\"javascript:App.GalleryPagingAjax.loadPaging('".$ajaxUrl."', '".$galleryType."', '".$elAppend."', '".$page."'); return false;\"  title='$page'>$page</a> </li>";                    }
                }
            }

            $pagingLink = implode('  ', $pagingLink);
            // return the page navigation link
            //$pagingLink = '<ul>'. $first. $prev . $pagingLink . $next.$last .'</ul>';

        }

        $currPage = '<input type="hidden" id="page" value="' . @$pageNumber . '" name="page" />';
        if($pageCurrent > 1){
            $startRecord = ($pageCurrent -1 )* $this->itemPerPage + 1;
        }else{
            if($this->total==0){
                $startRecord = 0;     
            }else{
                $startRecord = 1;     
            }
        }
        $toRecord = $pageCurrent * $this->itemPerPage;
        if($toRecord > $this->total){
            $toRecord = $this->total;
        }
        $this->pagingInfo = "<div class='total_page'>Đang hiển thị từ $startRecord đến $toRecord trên tổng số $this->total </div>";

        $pageInfo = '<li class="page-item">
              <a class="page-link"><span>'.$pageCurrent.'</span>/ <span>'.$totalPages.'</span></a>
            </li>';

        $pagingLink = ($prev || $next) ? $prev . $pageInfo . $next : '';
        $pagingLink = str_replace('<li></li>', '', $pagingLink);


        $this->pagingLink =  $pagingLink;
        
        return $this->pagingLink;
    }
}