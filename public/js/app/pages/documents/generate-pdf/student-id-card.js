/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['moment', 'jquery', 'datetimepicker', 'datatable'], function (moment) {

    var create = (function () {

        var studentsData = [];

        initDataTables = function() {            
            $('#students-table').DataTable({
                dom: 'lftip',
                paging: false,
                ajax: {
                    method: 'POST',
                    url: '/school-management/student-class/get-students-by-class',
                    data: function() {
                        return {
                            id: $('#class-select').val()
                        };
                    },
                    dataSrc: function (response) {
                        var students = response.students;

                        studentsData = [];
                        for (var i = 0; i < students.length; ++i) {
                            var date = new Date(students[i].enrollmentBeginDate.date);
                            var year = date.getFullYear() ;
                            var month = (date.getMonth() < 10) ? '0' + date.getMonth() : date.getMonth();
                            var day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                            
                            studentsData.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "data-id": students[i].personId
                                },
                                0: students[i].personFullName,
                                1: students[i].personRg,
                                2: year +  '-' + month + '-' + day
                            });
                        }
                        return studentsData;
                    }
                }
            });
        };

        initDatepickers = function () {
            $('#expiry-date').datetimepicker({
                minDate: moment(),
                maxDate: moment().add(2, 'years'),
                useCurrent: true,
                viewMode: 'years',
                format: 'DD/MM/YYYY',
                inline: true,
                locale: 'pt-br'
            });
        };
        
        initStudentBgConfigTable = function() {
            require(['/js/app/pages/documents/student-bg-config/student-bg-configs.js'], function(studentBgConfig) {
                studentBgConfig.init();
            });
            
            $('.content').on('click', '#config-table .cats-row > td', function(e) {                
                $(this).parent().siblings('.cats-selected-row').click();
            });
        };
        
        initStudentsFetching = function() {
            $('#fetch-class-students').on('click', function() {
                $('#students-table').DataTable().ajax.reload();
            });
            
            $('#select-all-students').on('click', function() {
                $('#students-table .cats-row:not(.cats-selected-row)').click();
            });
            
            $('#unselect-all-students').on('click', function() {
                $('#students-table .cats-selected-row').click();
            });
        };
        
        allowSubmit = function() {
            $('form').submit(function() {
                $('#students-table .cats-selected-row').each(function() {
                    $('form').append('<input type="hidden" name="studentIds[]" id="students-input" value="' + $(this).data('id') + '">');
                });
                
                $('#bg-config-id-input').val($('#config-table .cats-selected-row').first().data('id'));
                $('#expiry-date-input').val($('#expiry-date .day.active').first().data('day'));
            });
        };
        

        return {
            init: function () {
                initDataTables();
                initDatepickers();
                initStudentBgConfigTable();
                initStudentsFetching();
                allowSubmit();
            }
        };

    }());

    return create;

});