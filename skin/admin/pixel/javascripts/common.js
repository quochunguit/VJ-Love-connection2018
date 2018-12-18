
var App = {};
/**
 * array config
 */
App.Config = {};
/**
 * goto new url
 * this function alternate window.location
 */
App.gotoURL = function(newurl){
    window.location = newurl;
}

App.validateForm = function(){
    if($('form.validate').length > 0){
        $('form.validate').validate();
    }
}
    
App.slug = function(){
    if($("#title").length > 0 && $('#slug').length > 0){
        $("#title").slug('title', 'slug');
    }
    if($("#name").length > 0 && $("#slug").length > 0){
        $("#name").slug('name', 'slug');
    }
    
    if($("#title").length > 0 && $("#alias").length > 0){
        $("#title").slug('title', 'alias');
    }      
}  

App.loadPage = function(page){
    if($('#page').length > 0){
        $('#page').val(page);
        $('form').submit();
    }
}

App.sort = function(title, order){
    jQuery('#sort').val(title);
    jQuery('#order').val(order);
    $('form').submit();
}

App.highlightTable = function(){
    $(".dataGrid tr").hover(
        function(){
            if($('.dataGrid tr:eq(0)')==$(this)){
                return false;
            }
            $(this).addClass("highlight");
        },
        function(){
            $(this).removeClass("highlight");
        }
    );
    $(".dataGrid tr:odd").removeClass('odd');
    $(".dataGrid tr:even").addClass('even');

}

App.checkItemValidate = function(){
    var choose = false;
    if($('input.checkitem').length==0){
        return true;
    }
    $('input.checkitem').each(function(){
        if($(this).attr('checked')==true){
            choose = true;
            return choose;
        }
    });
    return choose;

}

App.checkMe = function(){
    if($("#checkall").length>0){
        $("#checkall").click(function(){
            if($('#checkall').attr('checked')=="checked" || $('#checkall').attr('checked')==true )
            {
                $('input.checkitem').attr('checked',true);
                
            }else{
                $('input.checkitem').attr('checked',false);
                     
            }
        });

        $('input.checkitem').each(function(){
            $(this).click(function(){
                if($(this).is(':checked')==false){
                    $('#checkall').attr('checked',false);
                }
                if($(this).is(':checked')==true){
                    var checkall = true;
                    $('input.checkitem').each(function(){
                        if($(this).is(':checked')==false){
                            checkall = false;
                        }
                    });
                    if(checkall==true)
                        $('#checkall').attr('checked',true);
                }

            });
        });

        
    }
}

App.handleStatus = function(){
    if($('.change_status').length > 0){
        $('.change_status').click(function(){
            var dataAction = $(this).attr('data-action');

            //check current row
            $(this).parent('td').parent('tr').find('.checkitem').attr('checked', true);
            //submit form
            App.submitForm(dataAction);
        })
    }
}

App.submitForm = function(action){
    if(typeof(action) !== undefined && typeof(action) !== ''){
        var isValid = false;
        if((action === 'publish' || action === 'unpublish' || action==='delete' || action==='deleteitem')){
            var textAction = (action === 'delete' || action === 'deleteitem') ? 'delete' : action;
            isValid = confirm("Do you want to " + textAction + "?") ? true : false;
        }else{
            isValid = true;
        }

        if (isValid){
            $('#callaction').val(action);
            if($('form.validate').length > 0 ){
                if($('form.validate').valid()){
                    $('form').submit();
                }
            }else if($('form').length > 0){
                $('form').submit();
            }   
        } 
    }   
}

App.sort = function(field, direction){
    $("#filter_order_Dir").val(direction);
    $("#filter_order").val(field);
    $('form').submit();
}

App.addNew = function(url){
    App.gotoURL(url);
}

App.tab = function(){
    //$( "#tabs" ).tabs(); 
}

App.validateFormPost = function(){
    if(jQuery("#post").length>0){
        jQuery("#post").validate();
    }
}

App.resetFilterPost = function(){
    if(jQuery("#resetFilterPost").length > 0){
        jQuery("#resetFilterPost").click(function(){
            jQuery("#filter_search").val("");
            jQuery("#filter_status").val("");
            jQuery("#filter_featured").val("");
            jQuery("#filter_type").val("");
            jQuery("#filter_category").val("");
            jQuery("#filter_language").val("*");
            jQuery('#page').val('1');
            
            jQuery(this).parents("form").submit();
        });
    }
}

App.filterChange = function(){
    var $filterChange = $('.filter_change');
    if($filterChange.length > 0){
        $filterChange.change(function(){
            $('#page').val(1);
            $filterChange.parents('form').submit();  
            
        });
    }
}

App.focusFirstInput = function(){
    if(jQuery('input[type=text]').length > 0){
        jQuery('input[type=text]:first').focus();
        jQuery('input[type=text]:first').select();
        
    }
}

App.getCenteredCoords = function(width, height){
    var xPos = null;
    var yPos = null;
    if(window.ActiveXObject) {
        xPos = window.event.screenX - (width / 2) + 100;
        yPos = window.event.screenY - (height / 2) - 100;
    } else {
        var parentSize = [window.outerWidth, window.outerHeight];
        var parentPos = [window.screenX, window.screenY];
        xPos = parentPos[0] + Math.max(0, Math.floor((parentSize[0] - width) / 2));
        yPos = parentPos[1] + Math.max(0, Math.floor((parentSize[1] - (height * 1.25)) / 2));
    }
    return [xPos, yPos];
}

App.initDatePicker = function(){
    if($('.datepicker').length > 0){
        var opts = {};
        $('#from_date').datepicker(opts);
        $('#to_date').datepicker(opts);
    }  
}

var Post = {
    fileupload: function() {
        $('#upload_image').fileupload({
            url: baseurl + '/media/uploadimagecontent',
            dataType: 'json',
            autoUpload: true,
            done: function(e, data) {
                $.each(data.result.files, function(index, file) {
                    $('#files_show_for_user_image').text(file.name);
                    $('#image').val(file.name);
                    $('#image').val(file.name);
                });
            },
        });

        $('#upload_large_image').fileupload({
            url: baseurl + '/media/uploadimagecontent',
            dataType: 'json',
            autoUpload: true,
            done: function(e, data) {
                $.each(data.result.files, function(index, file) {
                    $('#files_show_for_user_large_image').text(file.name);
                    $('#large_image').val(file.name);
                    //$('#image').val(file.name);
                });
            },
        });
    } 
}

App.Slug = function(){
    var slug = function(slugStr, charRep = '_'){

        var vn = new Array(
            "à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă",
            "ằ","ắ","ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề"
            ,"ế","ệ","ể","ễ",
            "ì","í","ị","ỉ","ĩ",
            "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"
            ,"ờ","ớ","ợ","ở","ỡ",
            "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
            "ỳ","ý","ỵ","ỷ","ỹ",
            "đ",
            "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
            ,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
            "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
            "Ì","Í","Ị","Ỉ","Ĩ",
            "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
            ,"Ờ","Ớ","Ợ","Ở","Ỡ",
            "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
            "Ỳ","Ý","Ỵ","Ỷ","Ỹ",
            "Đ","Q"," ","--","---","----"
        );
                
        var en = new Array(
                "a","a","a","a","a","a","a","a","a","a","a","a",
                "a","a","a","a","a","e","e","e","e","e","e","e"
                ,"e","e","e","e",
                "i","i","i","i","i",
                "o","o","o","o","o","o","o","o","o","o","o","o"
                ,"o","o","o","o","o",
                "u","u","u","u","u","u","u","u","u","u","u",
                "y","y","y","y","y",
                "d",
                "A","A","A","A","A","A","A","A","A","A","A","A"
                ,"A","A","A","A","A",
                "E","E","E","E","E","E","E","E","E","E","E",
                "I","I","I","I","I",
                "O","O","O","O","O","O","O","O","O","O","O","O"
                ,"O","O","O","O","O",
                "U","U","U","U","U","U","U","U","U","U","U",
                "Y","Y","Y","Y","Y",
                "D","q",charRep,charRep,charRep,charRep
        );
        
        slugStr = slugStr.toLowerCase();
        slugStr = str_replace(vn, en, slugStr);
        slugStr = slugStr.replace(/[^a-zA-Z0-9\-_]/g,'');
        slugStr = str_replace("--",charRep, slugStr);
        slugStr = str_replace("---", charRep, slugStr);
        
        return slugStr;    
    }

    var trim = function (str) {
        var whitespace, l = 0, i = 0;
        str += '';
        whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
        l = str.length;
        for (i = 0; i < l; i++) {if (whitespace.indexOf(str.charAt(i)) === -1) {str = str.substring(i);break;}}
        l = str.length;
        for (i = l - 1; i >= 0; i--) {if (whitespace.indexOf(str.charAt(i)) === -1) {str = str.substring(0, i + 1);break;}}
        return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
    }
    var str_replace = function(f, r, s){
        var ra = r instanceof Array, sa = s instanceof Array, l = (f = [].concat(f)).length, r = [].concat(r), i = (s = [].concat(s)).length;
        while(j = 0, i--)
            while(s[i] = s[i].split(f[j]).join(ra ? r[j] || "" : r[0]), ++j < l);
        return sa ? s : s[0];
    }

    return {
        slug:slug
    }
}();

App.Site = function(){
    var autohideMessage = function(){
       var elMsg = $('.alert');
       if(elMsg.length > 0){
            setTimeout(function(){
                elMsg.slideUp();
            },5000);
       }
    };

    var common = function(){
        $('#filter').click(function(){
            $(this).parents('form').submit();
        });
        
        $('#reset').click(function(){
            $(this).parents('form').find('input, select').val('');   
            $(this).parents('form').submit();
        });

        $('input[type="button"]').add('input[type="submit"]').addClass('btn');
    };

    var datePicker = function(elFromdate, elTodate){
        $(elFromdate).datepicker({
            format: 'yyyy-mm-dd',
            minDate: '-3',
            maxDate: 'moment',
            autoclose: true
        }).on('changeDate', function(ev){
            console.log(ev);
            $(elTodate).datepicker({minDate: ev.date});
        });

        $(elTodate).datepicker({
            format: 'yyyy-mm-dd',
            minDate: '-3',
            maxDate: 'moment',
            autoclose: true
        }).on('changeDate', function(ev){
            console.log(ev);
            $(elTodate).datepicker({minDate: ev.date});
        });
    };

    var init = function(){
        autohideMessage();
        common();
        datePicker('#filter_from_submit_date','#filter_to_submit_date');
    };

    return {
        init:init
    }
}();

App.ContentType = function(){
    
    var common = function(){
        var $identity = $('#contenttype_type');
        if($identity.length > 0){
            $identity.attr('readonly','true');
            if($identity.val()===''){
                processCreateType();
            }

            $('.contenttype_type_btn_edit').click(function(){
                $identity.removeAttr('readonly');
            })
        }  
    };

    var processCreateType = function(){
        var $content_type_group = $('#content_type_group');
        if($content_type_group.length > 0){
            $content_type_group.change(function(){
                var valGroup = $(this).val();
                var valName = '';

                var $title = $("#contenttype_title");
                if($title.length > 0){
                    valName = $title.val();
                    valName = App.Slug.slug(valName,'');
                }
   
                var $identity = $('#contenttype_type');
                if($identity.length > 0){
                    $identity.val(valGroup+'_'+valName);   
                }  
            });
        }

        var $title = $('#contenttype_title');
        if($title.length > 0){
            $title.on('keypress click blur mouseout', function(){
                var valGroup = $('#content_type_group').val();
                var valName = $(this).val();
                valName = App.Slug.slug(valName,'');

                var $identity = $('#contenttype_type');
                if($identity.length > 0){
                    $identity.val(valGroup+'_'+valName);   
                }  
            });
        }
    };

    var init = function(){
        common();
    };

    return {
        init:init
    }
}();

$(document).ready(function(){
    /*
    jQuery.validator.messages.required = "";
    jQuery.validator.messages.email = "";
    jQuery.validator.messages.equalTo = "";
    */
    App.validateFormPost();
    App.slug();
    App.checkMe();
    App.highlightTable();
    App.tab();
    App.resetFilterPost();
    App.filterChange();
    App.focusFirstInput();
    App.initDatePicker();
    App.handleStatus();    
    Post.fileupload();
    App.Site.init();  
    App.ContentType.init();
});