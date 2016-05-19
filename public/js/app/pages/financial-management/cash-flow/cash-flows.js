/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {
    var cashFlows = (function () {

        var cashFlowTable = $('#cash-flow-table');

        initDataTable = function () {
            
            cashFlowTable.DataTable({
                dom: 'lftip',
                pageLength: 50,
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"] ],
                order: [[ 0, 'desc' ]]
            });
        };

        return {
            init: function () {
                initDataTable();
            },
            getCallbackOf: function (element) {
                
                return {
                    exec: function (data) {
                        cashFlowTable
                                .DataTable()
                                .row('#cash-flow-' + data.cashFlowId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());    
    
    return cashFlows;
});