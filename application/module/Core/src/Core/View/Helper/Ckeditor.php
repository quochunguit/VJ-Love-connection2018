<?php

namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;

class Ckeditor extends AbstractHelper {

    function editor($id = 'body', $type = 'full') {
        switch ($type) {
            case 'simple':
                return $this->simpleCke($id);
                break;
            case 'medium':
                return $this->mediumCke($id);
                break;
            default: //Full
                return $this->fullCke($id);
                break;
        }
    }

    function _link() {
        $ck = '<script src="' . CKEDITOR_PATH . '/skin/common/ckeditor/ckeditor.js" type="text/javascript"></script>';
        $ck .= '<script src="' . CKEDITOR_PATH . '/skin/common/ckfinder/ckfinder.js" type="text/javascript"></script>';
        $ck .= '<script src="' . CKEDITOR_PATH . '/skin/common/ckeditor/config.js" type="text/javascript"></script>';
        return $ck;
    }

    function simpleCke($id) {
        $code = "
                   if ( typeof CKEDITOR == 'undefined' )
                    {
                        document.write(
                            '<strong><span style=\"color: #ff0000\">Error</span>: CKEditor not found</strong>.') ;
                    }
                    else
                    {
                        var editor = CKEDITOR.replace( '$id',
                        {
  
                            toolbar :[
                                ['Undo','Redo'],
                                ['Bold','Italic'],
                                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']

                            ],
                            height:225,
                            resize_enabled:false

                        }
                        );
                       // editor.setData( '<p></p>' );

                        //CKFinder.setupCKEditor( editor, '" . CKEDITOR_PATH . "/skin/common/ckfinder/' ) ;
                        //CKFinder.setupCKEditor( editor, { BasePath :  '" . CKEDITOR_PATH . "/skin/common/ckfinder/', RememberLastFolder : false } ) ;

                    }
                  ";

        $ck = $this->_link();
        return $ck . '<script type="text/javascript">' . $code . '</script>';
    }

    function mediumCke($id) {
        $code = "
                   if ( typeof CKEDITOR == 'undefined' )
                    {
                        document.write(
                            '<strong><span style=\"color: #ff0000\">Error</span>: CKEditor not found</strong>.') ;
                    }
                    else
                    {
                        var editor = CKEDITOR.replace( '$id',                       {                                     
                            toolbar :[
                                ['Source','-','Templates'],
                                ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
                                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],                                            
                                ['Link','Unlink','Anchor'] 
                            ]
                        }
                        );
                       // editor.setData( '<p></p>' );

                        CKFinder.setupCKEditor( editor, '" . CKEDITOR_PATH . "/skin/common/ckfinder/' ) ;
                        CKFinder.setupCKEditor( editor, { BasePath :  '" . CKEDITOR_PATH . "/skin/common/ckfinder/', RememberLastFolder : false } ) ;

                    }
                  ";

        $ck = $this->_link();
        return $ck . '<script type="text/javascript">' . $code . '</script>';
    }

    function fullCke($id) {
        $code = "
                   if ( typeof CKEDITOR == 'undefined' )
                    {
                        document.write(
                            '<strong><span style=\"color: #ff0000\">Error</span>: CKEditor not found</strong>.') ;
                    }
                    else
                    {
                        var editor = CKEDITOR.replace( '$id',
                        {
                                       
                            toolbar :[
                                ['Source','-','Templates'],
                                ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
                                ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                                ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
                                ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
                                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                                ['Link','Unlink','Anchor'],
                                ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Maximize', 'ShowBlocks'],
                                ['Styles','Format','Font','FontSize'],
                                ['TextColor','BGColor'],
                            ]
                        }
                        );
                       // editor.setData( '<p></p>' );

                        CKFinder.setupCKEditor( editor, '" . CKEDITOR_PATH . "/skin/common/ckfinder/' ) ;
                        CKFinder.setupCKEditor( editor, { BasePath :  '" . CKEDITOR_PATH . "/skin/common/ckfinder/', RememberLastFolder : false } ) ;

                    }
                  ";

        $ck = $this->_link();
        return $ck . '<script type="text/javascript">' . $code . '</script>';
    }

}

?>