/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {
    var index = (function () {

        var warningTable = $('#warning-table');

        initDataTable = function () {

            warningTable.DataTable({
                dom: 'lftip',
                paging: false
            });
        };

        return {
            init: function () {
                initDataTable();
            },
            getCallbackOf: function (element) {
                
                return {
                    exec: function (data) {
                        warningTable
                                .DataTable()
                                .row('#given-warning-' + data.givenWarningId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());    
    
    return index;
});