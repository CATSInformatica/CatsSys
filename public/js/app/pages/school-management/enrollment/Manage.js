/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


define(['moment', 'datatable'], function (moment) {
    var Manage = (function () {

        var enrollmentTable;

        initDataTable = function () {

            enrollmentTable = $('#manage-enrollment-table').DataTable({
                iDisplayLength: 110,
                dom: 'lftip',
                ajax: {
                    url: "/school-management/student-class/getStudentsByClass",
                    type: "POST",
                    data: function () {
                        return {
                            id: $('#class-search-field').val()
                        };
                    },
                    dataSrc: function (data) {
                        var students = data.students;
                        var result = [];
                        for (var i = 0; i < students.length; i++) {
                            result.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "data-id": students[i].enrollmentId
                                },
                                0: students[i].enrollmentId,
                                1: moment(students[i].enrollmentBeginDate.date).format('DD/MM/YYYY'),
                                2: moment(students[i].personBirthday.date).format('DD/MM/YYYY'),
                                3: students[i].personFullName,
                                4: students[i].personEmail,
                                5: students[i].personPhone,
                                6: students[i].personCpf,
                                7: students[i].personRg
                            });
                        }

                        return result;
                    }
                }
            });
            $('#class-search-button').click(function () {
                enrollmentTable.ajax.reload();
            });
        };

        return {
            init: function () {
                initDataTable();
            },
            getDataOf: function (action) {
                if (action === "fn-unenroll" || action === "fn-close-enroll") {
                    return {
                        studentClass: $("select[name=studentClasses]").val()
                    };
                }
            },
            getCallbackOf: function (action) {

                var obj = {
                };

                switch (action) {
                    case 'fn-unenroll':
                    case 'fn-close-enroll':
                        obj.exec = function (params) {
                            var row = enrollmentTable.row('[data-id=' + params.id + ']');
                            row.remove().draw(false);
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
    return Manage;
});

