var FileMan = {
    init: function() {

        $('#othercmd a').click(function() {
            var a = $(this).attr('data-a');
            console.log(a);
            $('#a').val(a);
            //do command
            FileMan.doCommand(filemanUrl);
        });

        $('#myModal').on('hidden.bs.modal', function(e) {
            if ($('#modalactionbtn').length > 0) {
                $('#modalactionbtn').addClass('hidden');
                $('#modalactionbtn').unbind('click');
            }
        });
        $('#myModal').on('show.bs.modal', function(e) {

        });
        //handler click on row table 
        $('.brf').click(function(e) {
            e.preventDefault();
            var data = $("#formmain").serialize();
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: $(this).attr('href'),
                success: function(res) {
                    console.log(res);
                    if (res.status == "true") {
                        var body = res.body;
                        var d = res.d;
                        $('#d').val(d);
                        FileMan.updateMainContent(body);

                    }
                }
            });
        });
        //handler click on update row button
        $('.updaterow').click(function(e) {
            var p = $(this).attr('data-path');
            var a = $(this).attr('data-a');
            $('#d').val(p);
            $('#a').val(a);
            //do command
            FileMan.doCommand(filemanUrl);
        });

        $('.editfile').click(function(e) {
            var p = $(this).attr('data-path');
            var a = $(this).attr('data-a');
//            $('#d').val(p);
//            $('#a').val(a);
            //do command
            var data = {
                a: a,
                p: p
            };
            var $editElement = $(this);
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: filemanUrl,
                data: data,
                success: function(res) {
                    console.log(res);
                    if (res.status == "true") {

                        var body = res.body;
                        if (typeof (body) !== 'undefined' && body != '') {
                            FileMan.updateMainContent(body);
                        }
                        var message = "<textarea style='height: 500px; width: 100%' id='code'>" + res.message + "</textarea>";
                        FileMan.showLargeDialog(message);


                        $('#savefilebtn').click(function() {
                            var code = $('#code').val();
                            var data = {
                                a: 'savefile',
                                p: p,
                                code: code
                            };

                            $.ajax({
                                type: "POST",
                                dataType: 'json',
                                url: filemanUrl,
                                data: data,
                                success: function(res) {
                                    if (res.status == "true") {
                                        var message = res.message;
                                        FileMan.showDialog(message);
                                        if (res.redirect) {
                                            setTimeout(function() {
                                                window.location.href = res.redirect;
                                            }, 3000);
                                        }
                                    } else {
                                        var message = res.message;
                                        FileMan.showDialog(message);
                                    }

                                }
                            });
                        });

                    }
                }
            });
        });
        $('.delete').click(function(e) {
            if (confirm("Are you sure want to delete? ")) {
                var p = $(this).attr('data-path');
                var a = $(this).attr('data-a');
                $('#d').val(p);
                $('#a').val(a);
                //do command
                FileMan.doCommand(filemanUrl);
            }
        });

        $('.download').click(function() {
            var p = $(this).attr('data-path');

            FileMan.download(p);
        });
        $('.view').click(function() {
            var p = $(this).attr('data-path');
            FileMan.view(p);
        });
        $('.commit').click(function() {
            var p = $(this).attr('data-path');
            $('#a').val('status');
            $('#d').val(d);
            var url = $('#formmain').attr('action');
            FileMan.doCommand(url);
        });




    },
    initPopover: function() {
        $('#newfolderbtn').popover(
                {
                    placement: 'bottom',
                    html: true,
                    content: $('.newfolder').html()
                }
        );
        $('#newfolderbtn').on('shown.bs.popover', function() {

            $('#cancelcreatefolder').click(function() {
                $('#newfolderbtn').popover('hide');


            });
            $('#createfolder').click(function() {
                var foldername = $('#foldername').val();
                foldername = foldername.trim();
                var d = $('#d').val();
                if (foldername != "") {
                    var data = {
                        foldername: foldername,
                        a: 'createfolder',
                        d: d
                    };
                    $.ajax({
                        type: "GET",
                        dataType: 'json',
                        url: filemanUrl,
                        data: data,
                        success: function(res) {
                            console.log(res);
                            if (res.status == "true") {
                                var body = res.body;
                                var message = res.message;
                                FileMan.updateMainContent(body);
                                $('#newfolderbtn').popover('hide');
                                FileMan.showDialog(message);


                            }
                        }
                    });

                } else {
                    $('#foldername').focus();
                }

            });
        });

        $('#newfilebtn').popover(
                {
                    placement: 'bottom',
                    html: true,
                    content: $('.newfile').html()
                }
        );
        $('#newfilebtn').on('shown.bs.popover', function() {
            $('#cancelcreatefile').click(function() {
                $('#newfilebtn').popover('hide');
            });
            $('#createfile').click(function() {
                var filename = $('#filename').val();
                filename = filename.trim();
                var d = $('#d').val();
                if (filename != "") {
                    var data = {
                        filename: filename,
                        a: 'createfile',
                        d: d
                    };
                    $.ajax({
                        type: "GET",
                        dataType: 'json',
                        url: filemanUrl,
                        data: data,
                        success: function(res) {
                            console.log(res);
                            if (res.status == "true") {
                                var body = res.body;
                                var message = res.message;
                                FileMan.updateMainContent(body);
                                $('#newfilebtn').popover('hide');
                                FileMan.showDialog(message);

                            }
                        }
                    });

                } else {
                    $('#filename').focus();
                }
            });
        });
        var uploadHtml = $('.newupload').html();
        $('.newupload').html('');
        $('#uploadfilebtn').popover(
                {
                    placement: 'bottom',
                    html: true,
                    content: uploadHtml
                }
        );

        $('#uploadfilebtn').on('shown.bs.popover', function() {
            $('#canceluploadfile').click(function() {
                $('#uploadfilebtn').popover('hide');
            });
            $('#uploadfile').click(function() {
                $('#uploadpath').val($('#d').val());
                $('#uploadfr').load(function() {
                    var res = $('#uploadfr').contents().find('body').html();

                    var res = $.parseJSON(res);

                    if (res.status == "true") {
                        var body = res.body;
                        var message = res.message;
                        FileMan.updateMainContent(body);
                        $('#uploadfilebtn').popover('hide');


                        FileMan.showDialog(message);
                        FileMan.refreshFileListing();

                    }

                });
                $('#uploadform').submit();

            });
        });
    },
    refreshFileListing: function() {
        FileMan.doCommand(filemanUrl);
    },
    saveSetting: function() {
        var $formSetting = $('#formsetting');
        var data = $formSetting.serialize();
        var url = filemanUrl + '?savesetting=1';
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: url,
            data: data,
            success: function(res) {
                if (res.status == "true") {
                    var message = res.message;
                    FileMan.showDialog(message);
                    if (res.redirect) {
                        setTimeout(function() {
                            window.location.href = res.redirect;
                        }, 3000);
                    }
                } else {
                    var message = res.message;
                    FileMan.showDialog(message);
                }

            }
        });
    },
    doCommand: function(url) {
        var data = $('#formmain').serialize();
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: url,
            data: data,
            success: function(res) {
                console.log(res);
                if (res.status == "true") {

                    var body = res.body;
                    if (typeof (body) !== 'undefined' && body != '') {
                        FileMan.updateMainContent(body);
                    }
                    var message = res.message;
                    FileMan.showDialog(message);
                    if (res.jscallback && FileMan[res.jscallback]) {
                        var cb = res.jscallback;
                        var jsob = res.jsob;
                        if (typeof (jsob) !== 'undefined') {
                            FileMan[cb](jsob);
                        } else {
                            FileMan[cb]();
                        }


                    }



                }
            }
        });
    },
    showDialog: function(content) {
        if (typeof (content) !== "undefined" && content != "") {
            $('#myModal .modal-body').html(content);
            $('#myModal').modal();
        }
    },
    showLargeDialog: function(content) {
        if (typeof (content) !== "undefined" && content != "") {
            $('#editFileModal .modal-body').html(content);
            $('#editFileModal').modal();
        }
    },
    showLoading: function() {

        $('#loading').show();
    },
    hideLoading: function() {
        $('#loading').hide();

    },
    zip: function() {
        //show dialog status
        $('#a').val('zip');
        var url = $('#formmain').attr('action');
        FileMan.doCommand(url);
    },
    unzip: function() {
        $('#a').val('unzip');
        var url = $('#formmain').attr('action');
        FileMan.doCommand(url);
    },
    setting: function() {
        window.location.href = filemanUrl + '?a=setting';
    },
    del: function() {
        $('#a').val('delete');
        var url = $('#formmain').attr('action');
        FileMan.doCommand(url);
    },
    updateMainContent: function(newContent) {
        if (newContent == "") {
            return;
        }
        $('#mainContent').html(newContent);
        FileMan.init();
    },
    download: function(p) {

        window.location.href = filemanUrl + '?download=1&p=' + p;
    },
    view: function(p) {
        var data = {
            view: 1,
            p: p
        };
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: filemanUrl,
            data: data,
            success: function(res) {
                console.log(res);
                if (res.status == "true") {
                    var message = res.message;
                    FileMan.showDialog(message);
                }
            }
        });

    },
    createFile: function() {

    },
    createFolder: function() {

    },
    uploadFile: function() {

    },
};
jQuery(document).ready(function() {

    $(document).ajaxSend(function(event, request, settings) {
        FileMan.showLoading();
    });
    $(document).ajaxComplete(function(event, request, settings) {
        FileMan.hideLoading();
    });
    $(document).ajaxSuccess(function(event, request, settings) {
        FileMan.hideLoading();
    });
    FileMan.init();
    FileMan.initPopover();
    $('#filemandelete').click(function() {
        FileMan.update();
    });
    $('#filemanzip').click(function() {
        FileMan.zip();
    });
    $('#filemanunzip').click(function() {
        FileMan.unzip();
    });
    $('#delete').click(function() {
        FileMan.del();
    });
    $('.download').click(function() {
        var p = $(this).attr('data-path');

        FileMan.download(p);
    });
    $('.view').click(function() {
        var p = $(this).attr('data-path');
        FileMan.view(p);
    });

    $('#setting').click(function() {
        FileMan.setting();
    });
    $('#savesetting').click(function() {
        FileMan.saveSetting();
    });






});