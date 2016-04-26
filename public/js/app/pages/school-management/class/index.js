/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['jquery', 'datatable'], function () {

    var index = (function () {
        // your module code goes here
        // var config = null;

        /**
         * 
         * private functions
         */

        initDataTable = function () {

            $('#class-table').DataTable({
                dom: 'lftip',
                paging: false
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