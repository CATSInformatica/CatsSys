/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function(){
    
    var index = (function () {
        // your module code goes here
        // var config = null;

        /**
         * 
         * private functions
         */

        initDataTable = function () {

            var recruitmentTable = $('#recruitment-table').DataTable({
                dom: 'lftip',
                paging: false,
                ajax: {
                    url: "/recruitment/registration/getApprovedStudents",
                    type: "POST",
                    data: function () {
                        var rid = $('#recruitment_id').val();
                        $('#recruitment-button').data('prev', rid);
                        return {
                            rid: rid
                        };
                    }
                }
            });

            $('#recruitment-button').click(function () {
                recruitmentTable.ajax.reload();
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
