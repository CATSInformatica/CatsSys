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
                minDate: moment().subtract(6, 'months'),
                useCurrent: false,
                maxDate: moment().add(1, 'years'),
                locale: 'pt-br',
                viewDate: moment()
            });
        };
        initDataTable = function () {

            var recruitmentTable = $('#recruitment-table').DataTable({
                iDisplayLength: 100,
                dom: 'lftip',
//                paging: false,
                ajax: {
                    url: "/recruitment/registration/getRegistrations",
                    type: "POST",
                    data: function () {
                        return {
                            recruitment: $("select[name=recruitment]").val(),
                            registrationStatus: $("select[name=registrationStatus]").val()
                        };
                    },
                    dataSrc: function (data) {
                        var result = [];
                        for (var i = 0; i < data.length; i++) {
                            result.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "data-id": data[i].registrationId
                                },
                                0: data[i].registrationNumber,
                                1: data[i].registrationDate,
                                2: data[i].personName,
                                3: data[i].personCpf,
                                4: data[i].personRg,
                                5: data[i].personEmail,
                                6: data[i].status.type + '<br>' + data[i].status.timestamp
                            });
                        }

                        return result;
                    }
                },
                columnDefs: [{targets: 6, className: 'text-center'}]
            });
            $('button[name=submit]').click(function () {
                recruitmentTable.ajax.reload();
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
            getDataOf: function (action) {

                if (action === "fn-enroll") {
                    return {
                        studentClass: $("select[name=studentClasses]").val()
                    };
                }
                return {
                    timestamp: $('input[name=timestamp]').val()
                };
            },
            getCallbackOf: function (action) {

                var obj = {
                };

                switch (action) {
                    case 'fn-interview-convocation':
                    case 'fn-interview-waitlist':
                    case 'fn-interview-approved':
                    case 'fn-interview-disapproved':
                    case 'fn-interview-volunteer':
                    case 'fn-testclass-convocation':
                    case 'fn-testclass-waitlist':
                    case 'fn-canceled-registration':
                    
                    // student
                    case 'fn-confirmation':
                    case 'fn-convocation':
                    case 'fn-acceptance':
                    
                        obj.exec = function (params) {
                            $(".cats-selected-row").find("td:last")
                                    .text(params.status +
                                            "\n" + params.timestamp);
                        };
                        break;
                    default:
                        obj.exec = function (params) {
                        };
                }

                return obj;
            }
        };
    }());
    return index;
});