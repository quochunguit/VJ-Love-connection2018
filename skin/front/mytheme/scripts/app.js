var UserValidate = function() {
    this.validateRegister = function() {
        var formRegister = $('form#register');
        if (formRegister.length < 1) {
            return;
        }
        formRegister.validate({
            rules: {
                password: {
                    required: true,
                },
                cfpassword: {
                    equalTo: "#password"
                }
            }
        });
    };
    this.validateLogin = function() {
        var formLogin = $('form#login');
        if (formLogin.length < 1) {
            return;
        }
        formLogin.validate();
    };
    this.validateForgot = function() {
        var formForgot = $('form#forgot');
        if (formForgot.length < 1) {
            return;
        }
        formForgot.validate();
    };
    this.validateRecover = function() {
        var formRecover = $('form#recover');
        if (formRecover.length < 1) {
            return;
        }
        formRecover.validate({
            rules: {
                password: {
                    required: true,
                },
                cfpassword: {
                    equalTo: "#password"
                }
            }
        });
    };
    this.validateChangePassword = function() {
        var formChangePassword = $('form#changepassword');
        if (formChangePassword.length < 1) {
            return;
        }
        formChangePassword.validate({
            rules: {
                password: {
                    required: true
                },
                cfpassword: {
                    equalTo: "#password"
                }
            }
        });
    };
    this.validateEditAccount = function() {
        var formEditAccount = $('form#editaccount');
        if (formEditAccount.length < 1) {
            return;
        }
        formEditAccount.validate();
    };
};
var ChangePicture = function($) {
    function init() {
        if ($('form#changepicture').length > 0) {
            $('#fileupload').fileupload({
                url: baseurl + '/media/uploadimage',
                dataType: 'json',
                autoUpload: true,
                done: uploadCompleted
            });

            $('#save-avatar').click(handleSubmit);
        }
    }
    function uploadCompleted(e, data) {
        $.each(data.result.files, function(index, file) {

            $('img#media-avatar').attr('src', 'media/tmp/contest/' + file.name);
            $('input#avatar').val(file.name);

        });
        $('#fileupload-container').addClass('hidden');
        $('#changepicture-action').removeClass('hidden');
    }

    function handleSubmit() {
        if ($('input#avatar').val().length > 0) {
            $('form#changepicture').submit();
        } else {
            alert('Please select file image to upload')
        }
    }


    init();
}(jQuery);

var Contest = function($) {

    function init() {

        if ($('#n_contest_item').length > 0) {

            $('#n_contest_item').validate();

            $('#fileupload').fileupload({
                url: baseurl + '/media/uploadimage',
                dataType: 'json',
                autoUpload: true,
                done: uploadCompleted
            });

            $('#contestSubmit').click(handleSubmit);

        }

    }

    function uploadCompleted(e, data) {
        $.each(data.result.files, function(index, file) {
            $('#files_show_for_user').text(file.name);
            $('#files').val(file.name);
            $('#preview').val(file.name);
        });
    }
    function handleSubmit() {
        if ($('#n_contest_item').valid()) {
            //check file files and preview
            if ($('#files').val().length > 0) {
                $('#n_contest_item').submit();
            } else {
                alert('Please select file image to upload')
            }
        }
    }
    return {
        init: init
    };
}(jQuery);

var Contact = {
    init: function() {
        if ($('#n_contact').length > 0) {
            $('#n_contact').validate();
            $('#contact-submit').click(function() {
                if ($('#n_contact').valid()) {
                    $('#n_contact').submit();
                }
            });
        }
    }
};

var Comment = function($) {

    var elementCommentId, objectId, objectType;

    function init(options) {
        elementCommentId = options.id;
        //load comment list
        if ($(elementCommentId).length <= 0) {
            return false;
        }

        objectId = $(elementCommentId).attr('data-object');
        objectType = $(elementCommentId).attr('data-type');

        loadData(objectId, objectType, 1);
    }
    ;
    function initCommentForm() {
        $(elementCommentId).find('form').validate();
        $(elementCommentId).find('.add_comment').click(function() {
            add();
        });
    }
    function add() {
        if ($(elementCommentId).find('form').valid()) {

            $(elementCommentId).find('.please-wait').fadeIn();

            var commentData = $(elementCommentId).find('form').serialize();
            console.log(commentData);
            jQuery.ajax({
                url: baseurl + "/comment/add",
                dataType: 'json',
                data: commentData,
                type: 'post',
                success: addCompleted
            });
        }
    }
    ;
    function addCompleted(response) {
        $(elementCommentId).find('.please-wait').fadeOut();
        if (response.status == true) {
            var cmid = $(elementCommentId).find('.comment-wrapper');
            $(response.comment).hide().prependTo(cmid).slideDown();
            $(elementCommentId).find('#message').focus().val('');
        } else {
            alert(response.message);

        }
    }
    ;

    function loadDataCompleted(response) {
        console.log(response);
        $(elementCommentId).html(response);
        initCommentForm();
    }
    ;
    function loadData(objectId, objectType, p) {
        jQuery.ajax({
            url: baseurl + "/comment?page=" + p + "&objectId=" + objectId + "&objectType=" + objectType,
            dataType: 'html',
            type: 'get',
            success: loadDataCompleted
        });
    }
    ;
    return {
        init: init
    }

}(jQuery);

var Vote = function() {

    var voteWrapperElement = '.vote-panel';
    var voteButtonElement = '.btn-vote';
    var voteCurrentElement = '.current-vote';


    function init() {
        if ($(voteButtonElement).length <= 0) {
            return false;
        }
        $(voteButtonElement).click(function() {
            var dataId = $(this).attr('data-id');
            var dataType = $(this).attr('data-type');
            vote(dataId, dataType);
        })
    }
    ;
    function vote(dataId, dataType) {
        $(voteWrapperElement).find('.please-wait').fadeIn();
        jQuery.ajax({
            url: baseurl + "/vote",
            dataType: 'json',
            data: {
                dataId: dataId,
                dataType: dataType
            },
            type: 'post',
            success: voteCompleted
        });
    }
    ;
    function voteCompleted(res) {
        var timeShowMessage = 3000;
        $(voteWrapperElement).find('.please-wait').html(res.message);
        if (res.status == true) {
            updateVote();
        } else {
            timeShowMessage = 6000;
        }
        setTimeout(function() {
            $(voteWrapperElement).find('.please-wait').add(voteButtonElement).fadeOut();

        }, timeShowMessage);


    }
    ;
    function updateVote() {
        var currentVoteVal = $(voteCurrentElement).text();
        currentVoteVal = parseInt(currentVoteVal);
        currentVoteVal++;
        $(voteCurrentElement).text(currentVoteVal);
    }


    init();
}();

var Menu = function($) {
    function init() {
        var url = window.location.href;

        $li = $('.navbar-nav a[href="' + url + '"]').parent('li');
        $li.addClass('active');
        if ($li.parent('ul.dropdown-menu').length > 0) {
            $li.parent('ul.dropdown-menu').parent('.dropdown').addClass('active');
        }

        $li = $('.navbar-nav a').filter(function() {
            return this.href == url;
        }).parent('li');

        $li.addClass('active');
        if ($li.parent('ul.dropdown-menu').length > 0) {
            $li.parent('ul.dropdown-menu').parent('.dropdown').addClass('active');
        }
    }
    init();

}(jQuery);
var pc = 1;

$(function() {
    jQuery.validator.messages.required = "";
    jQuery.validator.messages.email = "";
    jQuery.validator.messages.equalTo = "";

    var userValidate = new UserValidate();
    userValidate.validateRegister();
    userValidate.validateLogin();
    userValidate.validateForgot();
    userValidate.validateRecover();
    userValidate.validateChangePassword();
    userValidate.validateEditAccount();


    Contest.init();

    Contact.init();

    Comment.init({id: '#comments'});



});