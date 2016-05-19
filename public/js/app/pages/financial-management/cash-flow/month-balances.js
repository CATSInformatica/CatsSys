/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {
    var monthBalances = (function () {

        var monthBalanceTable = $('#month-balance-table');

        initDataTable = function () {
            
            monthBalanceTable.DataTable({
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
                        monthBalanceTable
                                .DataTable()
                                .row('#month-balance-' + data.monthBalanceId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());    
    
    return monthBalances;
});