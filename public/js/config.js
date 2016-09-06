/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


requirejs.config({
    baseUrl: '/vendor',
    paths: {
        jquery: 'AdminLTE/plugins/jQuery/jquery-2.2.3.min',
        jquerycsv: 'jquery-csv/src/jquery.csv',
        jquerycolumnizer: 'jquery.columnizer/src/jquery.columnizer.min',
        bootstrap: 'AdminLTE/bootstrap/js/bootstrap.min',
        bootstrapslider: 'AdminLTE/plugins/bootstrap-slider/bootstrap-slider',
        jmaskedinput: 'jquery.maskedinput/dist/jquery.maskedinput.min',
        slimscroll: 'AdminLTE/plugins/slimScroll/jquery.slimscroll.min',
        pace: 'AdminLTE/plugins/pace/pace.min',
        adminlte: 'AdminLTE/dist/js/app.min',
        bootbox: 'bootbox.js/bootbox',
        jquerydatatable: 'AdminLTE/plugins/datatables/jquery.dataTables.min',
        datatable: 'AdminLTE/plugins/datatables/dataTables.bootstrap.min',
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
            deps: ['jquery', 'bootstrap', 'jquerydatatable']
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
        mathjax: {
            exports: "MathJax",
            init: function () {
                MathJax.Hub.Config({
                    tex2jax: {
                        inlineMath: [
                            ['$', '$'],
                            ['\\(', '\\)']
                        ]
                    }
                });
                MathJax.Hub.Startup.onload();
                return MathJax;
            }
        },
        jqueryprint: {
            deps: ['jquery']
        }
    }
});

define(['jquery', 'bootstrap', 'adminlte'], function () {

});