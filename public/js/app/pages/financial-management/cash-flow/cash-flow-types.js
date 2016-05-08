/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {
    var cashFlowTypes = (function () {

        var cashFlowTypeTable = $('#cash-flow-type-table');

        initDataTable = function () {
            
            cashFlowTypeTable.DataTable({
                dom: 'lftip'
            });
        };

        return {
            init: function () {
                initDataTable();
            },
            getCallbackOf: function (element) {
                
                return {
                    exec: function (data) {
                        cashFlowTypeTable
                                .DataTable()
                                .row('#cash-flow-type-' + data.cashFlowTypeId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());    
    
    return cashFlowTypes;
});