/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {
    var index = (function () {

        var configTable = $('#config-table');

        initTable = function () {
            require(['/js/app/pages/documents/student-bg-config/student-bg-configs.js'], function(studentBgConfigs) {
                studentBgConfigs.init();
            });
        };

        return {
            init: function () {
                initTable();
            },
            getCallbackOf: function (element) {
                
                return {
                    exec: function (data) {
                        configTable
                                .DataTable()
                                .row('#bg-config-' + data.bgConfigId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());    
    
    return index;
});