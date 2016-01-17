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

            var recruitmentTable = $('#recruitment-table').DataTable({
                iDisplayLength: 10,
                dom: 'lftip',
//                paging: false,
                ajax: {
                    url: "/recruitment/registration/getStudentRegistrations",
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
                if ($(this).data('prev') !== $('#recruitment_id').val()) {
                    recruitmentTable.ajax.reload();
                }
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