/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {

    var contact = (function () {

        initDataTable = function () {
            $('#contact-table').DataTable({
                dom: 'lftip',
                paging: false,
                order: [[ 0, 'desc' ]]
            });
        };

        return {
            init: function () {
                initDataTable();
            }
        };

    }());

    return contact;

});