/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['moment', 'masks', 'datetimepicker', 'datatable'], function (moment, masks) {
    var index = (function () {
        // your module code goes here
        // var config = null;

        /**
         * 
         * private functions
         */

        initDatepickers = function () {
            $("input[name=timestamp]").closest(".input-group").datetimepicker({
                format: 'DD/MM/YYYY HH:mm',
                minDate: moment(),
                useCurrent: false,
                maxDate: moment().add(1, 'years'),
                locale: 'pt-br',
                viewDate: moment()
            });
        };

        initDataTable = function () {

            var recruitmentTable = $('#recruitment-table').DataTable({
                iDisplayLength: 10,
                dom: 'lftip',
//                paging: false,
                ajax: {
                    url: "/recruitment/registration/getRegistrations",
                    type: "POST",
                    data: function () {
                        var rid = $('#recruitment_id').val();
                        $('#recruitment-button').data('prev', rid);
                        return {
                            rid: rid
                        };
                    }
                },
                columnDefs: [{targets: 6, className: 'text-center'}]
            });

            $('#recruitment-button').click(function () {
                if ($(this).data('prev') !== $('#recruitment_id').val()) {
                    recruitmentTable.ajax.reload();
                }
            });
        };

        initMasks = function () {
            masks.bind({
                datetimeNoSeconds: 'input[name=timestamp]'
            });
        };

        return {
            init: function () {
                initDatepickers();
                initDataTable();
                initMasks();
            },
            getDataOf: function (statusAction) {

                return {
                    timestamp: $('input[name=timestamp]').val()
                };
            }
        };

    }());

    return index;
});