/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {
    var index = (function () {

        initDataTable = function () {

            var infoTable = $('#info-table').DataTable({
                iDisplayLength: 1,
                dom: 'lftip'
            });
        };

        return {
            init: function () {
                initDataTable();
            }
        };

    }());    
    
    return index;
});