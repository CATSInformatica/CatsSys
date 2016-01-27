/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


requirejs.config({
    baseUrl: '/vendor',
    paths: {
        jquery: 'AdminLTE/plugins/jQuery/jQuery-2.1.4.min',
        bootstrap: 'AdminLTE/bootstrap/js/bootstrap.min',
        jmaskedinput: 'jquery.maskedinput/dist/jquery.maskedinput.min',
        slimscroll: 'AdminLTE/plugins/slimScroll/jquery.slimscroll.min',
        adminlte: 'AdminLTE/dist/js/app.min',
        bootbox: 'bootbox.js/bootbox',
        jquerydatatable: 'AdminLTE/plugins/datatables/jquery.dataTables.min',
        datatable: 'AdminLTE/plugins/datatables/dataTables.bootstrap.min',
        datetimepicker: 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min',
        moment: 'moment/min/moment-with-locales.min',
        dropzone: 'dropzone/dist/min/dropzone-amd-module.min',
        masks: '/js/app/masks/masks',
        app: '/js/app'
    },
    shim: {
        bootstrap: {
            deps: ['jquery']
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
        }
    }
});

define(['jquery', 'bootstrap', 'adminlte'], function () {

});