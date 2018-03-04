/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


requirejs.config({
    baseUrl: '/vendor',
    paths: {
        jquery: 'jquery/dist/jquery.min',
        bootstrap: 'bootstrap/dist/js/bootstrap.min',
        jqueryui: 'AdminLTE/plugins/jQueryUI/jquery-ui.min',
        jquerycsv: 'jquery-csv/src/jquery.csv',
        jquerycolumnizer: 'jquery.columnizer/src/jquery.columnizer.min',
        bootstrapslider: 'AdminLTE/plugins/bootstrap-slider/bootstrap-slider',
        jmaskedinput: 'jquery.maskedinput/dist/jquery.maskedinput.min',
        slimscroll: 'jquery-slimscroll/jquery.slimscroll.min',
        pace: 'AdminLTE/plugins/pace/pace.min',
        adminlte: 'AdminLTE/dist/js/app.min',
        bootbox: 'bootbox.js/bootbox',
        'datatables.net': 'datatables.net/js/jquery.dataTables.min',
        datatable: 'datatables.net-bs/js/dataTables.bootstrap.min',
        datetimepicker: 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min',
        moment: 'moment/min/moment-with-locales.min',
        dropzone: 'dropzone/dist/min/dropzone-amd-module.min',
        mathjax: "MathJax/MathJax.js?config=TeX-AMS_HTML&amp;delayStartupUntil=configured",
        masks: '/js/app/masks/masks',
        app: '/js/app',
        trumbowyg: 'trumbowyg/dist/trumbowyg.min',
        trumbowygpt: 'trumbowyg/dist/langs/pt.min',
        trumbowygbase64: 'trumbowyg/dist/plugins/base64/trumbowyg.base64.min',
        chart: 'Chart.js/dist/Chart.min',
        filesaver: 'file-saver.js/FileSaver',
        jqueryprint: 'jQuery.print/jQuery.print',
        jszip: 'jszip/dist/jszip.min',
        ekkolightbox: 'ekko-lightbox/dist/ekko-lightbox.min'
    },
    shim: {
        bootstrap: {
            deps: ['jquery']
        },
        bootstrapslider: {
            deps: ['bootstrap']
        },
        datatable: {
            deps: ['jquery', 'bootstrap', 'datatables.net']
        },
        datetimepicker: {
            deps: ['jquery', 'bootstrap', 'moment']
        },
        slimscroll: {
            deps: ['jquery']
        },
        adminlte: {
            deps: ['bootstrap', 'jquery', 'slimscroll']
        },
        jmaskedinput: {
            deps: ['jquery']
        },
        jquerycsv: {
            deps: ['jquery']
        },
        jquerycolumnizer: {
            deps: ['jquery']
        },
        jqueryprint: {
            deps: ['jquery']
        },
        mathjax: {
            exports: "MathJax",
            init: function () {
                MathJax.Hub.Config({
                    tex2jax: {
                        inlineMath: [
                            ['$', '$'],
                            ['\\(', '\\)']
                        ],
                        processEscapes: true
                    }
                });
                MathJax.Hub.Startup.onload();
                return MathJax;
            }
        }
    }
});

define(['adminlte'], function () {

    // $('#leftMainMenu').tree();

    // for file inputs
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });

    $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
        $(this).siblings('.btn-file-name').text(label);
    });

    // show footer
    $(".main-footer").toggle("slide");

    $("section.content").on("click", "td.details-control", function (e) {
        e.stopPropagation();
    });

    //                $('.role').on('click', function () {
    //                    var role = $(this);
    //                    var jsonRole = JSON.stringify({
    //                        'role': role.text().toLowerCase()
    //                    });
    //                    $.ajax({
    //                        url: '/authorization/role/changeActiveUserRole',
    //                        type: 'POST',
    //                        dataType: 'json',
    //                        async: true,
    //                        data: jsonRole,
    //                        success: function (msg) {
    //
    //                            // change role in menu
    //                            if (msg.success) {
    //                                var newActiveRole = role.text();
    //                                role.text($('.active-role').text());
    //                                $('.active-role').text(newActiveRole);
    //                            }
    //
    //                            console.log(msg);
    //                        },
    //                        error: function (msg) {
    //                            console.log(msg);
    //                        }
    //                    });
    //                });

    require(['app/models/Main', 'pace'], function (Main) {

        $(document).ajaxStart(function () {
            Pace.restart();
        });

        var config = {
            toolbarElement: '.system-toolbar',
            toolbarItem: 'li',
            toolbarSelectedItem: '.cats-selected-row',
            toolbarContainer: '.control-sidebar',
            toolbarContainerOpen: 'control-sidebar-open'
        };

        if (typeof appConfig !== 'undefined') {
            require([appConfig.getScriptSrc()], function (pageScript) {
                Main.setPageConfig(pageScript);
                pageScript.init();
            });
        }

        Main.setConfig(config);
        Main.init();
    });
});
