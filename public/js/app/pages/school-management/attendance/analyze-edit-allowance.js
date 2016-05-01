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

define(['moment', 'datetimepicker', 'datatable'], function (moment) {
    analyzeEditAllowance = (function () {

        var attendanceMonth = $("#attendanceMonth");
        var attendanceStudentsByMonth = {};
        var attendanceContainer = $("#attendanceContainer");

        var allowanceMonth = $("#allowanceMonth");
        var allowanceStudentsByMonth = {};
        var allowanceStudents = $("#allowanceStudents");

        var anTables = {};
        var studentData = {};

        var ATTENDANCE_TYPES = {
            ATTENDANCE_BEGIN: 1,
            ATTENDANCE_END: 2,
            ATTENDANCE_ALLOWANCE_BEGIN: 3,
            ATTENDANCE_ALLOWANCE_END: 4,
            ATTENDANCE_ALLOWANCE_FULL: 5
        };


        /**
         * Calendário para controle da assiduidade
         * 
         * @param {type} monthElement
         * @returns {undefined}
         */
        initAttendanceMonthpicker = function (monthElement) {

            monthElement.datetimepicker({
                format: 'MMMM',
                inline: true,
                viewMode: 'months',
                locale: 'pt-br',
                useCurrent: false,
                defaultDate: moment("1", "D")
            });

            monthElement.on("dp.change", function (e) {
                var startDate = e.date.format('YYYY-MM-DD');
                var endDate = e.date
                        .add(1, 'months')
                        .subtract(1, 'days')
                        .format('YYYY-MM-DD');

                if (monthElement.is(allowanceMonth)) {
                    searchAllowanceBetween(startDate, endDate);
                } else if (monthElement.is(attendanceMonth)) {
                    searchAttendanceBetween(startDate, endDate);
                }
            });

            var start = moment().format("YYYY-MM-01");
            var end = moment(start, "YYYY-MM-DD")
                    .add(1, 'months')
                    .subtract(1, 'days')
                    .format("YYYY-MM-DD");

            if (monthElement.is(allowanceMonth)) {
                searchAllowanceBetween(start, end);
            } else if (monthElement.is(attendanceMonth)) {
                searchAttendanceBetween(start, end);
            }
        };

        /**
         * Busca as faltas e abonos no intervalo especificado (intervalo = mês).
         * @param {type} start
         * @param {type} end
         * @returns {undefined}
         */
        searchAttendanceBetween = function (start, end) {
            var attr = start.split('-')[1];
            if (typeof attendanceStudentsByMonth[attr] === "undefined") {
                attendanceStudentsByMonth[attr] = true;

                var sclass = $("#sclass").val();

                $.ajax({
                    url: "/school-management/school-attendance/analysis",
                    type: "POST",
                    data: {
                        sclass: sclass,
                        beginDate: start,
                        endDate: end
                    },
                    success: function (response) {
                        studentData[attr] = response.data;
                        showMonthAttendance(attr);
                    },
                    error: function (textStatus) {
                        console.log(textStatus);
                    }
                });
            } else {
                $("#nav_tab_" + attr).tab("show");
            }
        };

        /**
         * Exibe a tabela de assiduidade dos alunos.
         * 
         * @param {type} month
         * @param {type} data
         * @returns {undefined}
         */
        showMonthAttendance = function (month) {

            var tab = "tab_" + month;

            attendanceContainer
                    .find(".nav-tabs")
                    .append("<li><a id='nav_" + tab + "' href='#" + tab +
                            "' data-toggle='tab' aria-expanded='true'>" +
                            moment(month, "MM").format("MMMM") + "</a></li>");

            var table = mountAnalysisTable(month);

            attendanceContainer
                    .find(".tab-content")
                    .append("<div id='" + tab + "' class='tab-pane'><div class='table-responsive'>" +
                            table +
                            "</div></div>");

            $("#nav_" + tab).tab("show");

            anTables["table-" + month] = $("#table_" + month).DataTable({
                dom: 'flript',
                paging: false,
                order: [[10, 'asc']]
            });
        };

        /**
         * Faz os cálculos dos valores de presença dos alunos.
         * 
         * @param String month
         * @returns {String}
         */
        mountAnalysisTable = function (month) {

            var ret = getDaysArrayByMonth(month);

            var days = ret.days;
            var arrMax = ret.daysOfTheWeek;
            var table = "";
            var tr = "";
            var max = sum(arrMax);
            var achieved;

            $.each(studentData[month], function (enroll, content) {

                tr += "<tr data-student='" + enroll + "'>";
                tr += "<td class='details-control'></td>";
                tr += "<td class='text-right'>" +
                        ("0000" + enroll).substring(enroll.length) + "</td>";
                tr += "<td>" + content.name + "</td>";

                var arrCurrent = [0, 0, 0, 0, 0, 0, 0];

                studentData[month][enroll].sortedByWeekDays = {};

                var sit;
                // foreach day of the month
                $.each(days, function (day, dayOfWeek) {

                    if (dayOfWeek !== "0") {

                        if (typeof studentData[month][enroll].sortedByWeekDays[dayOfWeek] === "undefined") {
                            studentData[month][enroll].sortedByWeekDays[dayOfWeek] = [];
                        }

                        /**
                         * Existe falta ou abono do dia 'day'
                         */
                        if (content.hasOwnProperty(day)) {

                            sit = content[day];
                            studentData[month][enroll].sortedByWeekDays[dayOfWeek].push({
                                date: day,
                                situation: sit
                            });

                            /**
                             * Se possui abono integral ganha 
                             * presença completa do dia
                             * 
                             */
                            if (sit.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_FULL)) {
                                arrCurrent[dayOfWeek] += 1;
                            } else {

                                /**
                                 * Se possui abono do início do dia ou 
                                 * não possui falta do início do dia
                                 * ganha presença parcial
                                 */
                                if (sit.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_BEGIN)
                                        || !sit.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_BEGIN)) {
                                    arrCurrent[dayOfWeek] += 0.5;
                                }

                                /**
                                 * Se possui abono do final do dia ou 
                                 * não possui falta do final do dia
                                 * ganha presença parcial
                                 */
                                if (sit.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_END)
                                        || !sit.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_END)) {
                                    arrCurrent[dayOfWeek] += 0.5;
                                }
                            }
                        } else {
                            //Não existe falta nem abono, aluno presente.
                            arrCurrent[dayOfWeek] += 1;
                            studentData[month][enroll].sortedByWeekDays[dayOfWeek].push({
                                date: day,
                                situation: null
                            });
                        }
                    }
                });
                achieved = sum(arrCurrent);

                tr +=
                        "<td class='text-center'>" + arrCurrent[1] + "</td>" +
                        "<td class='text-center'>" + arrCurrent[2] + "</td>" +
                        "<td class='text-center'>" + arrCurrent[3] + "</td>" +
                        "<td class='text-center'>" + arrCurrent[4] + "</td>" +
                        "<td class='text-center'>" + arrCurrent[5] + "</td>" +
                        "<td class='text-center'>" + arrCurrent[6] + "</td>" +
                        "<td class='text-center'>" + achieved + "/" + max +
                        "<td class='text-center'>" + ((achieved / max) * 100).toFixed(2) + "% " +
                        "</tr>";

            });

            table = "<table id='table_" + month
                    + "' data-month='" + month + "' class='table table-condensed table-bordered table-hover'><thead>" +
                    "<tr>" +
                    "<th></th>" +
                    "<th class='text-center'>Matrícula</th>" +
                    "<th class='text-center'>Aluno</th>" +
                    "<th class='text-center'>Segunda (" + arrMax[1] + ")</th>" +
                    "<th class='text-center'>Terça (" + arrMax[2] + ")</th> " +
                    "<th class='text-center'>Quarta (" + arrMax[3] + ")</th> " +
                    "<th class='text-center'>Quinta (" + arrMax[4] + ")</th> " +
                    "<th class='text-center'>Sexta (" + arrMax[5] + ")</th> " +
                    "<th class='text-center'>Sábado (" + arrMax[6] + ")</th> " +
                    "<th class='text-center'>Total (" + max + ")</th> " +
                    "<th class='text-center'> % </th> " +
                    "</tr>" +
                    "</thead><body>";

            table += tr;
            table += "</body></table>";

            return table;
        };

        /**
         * Utilizado pelo editar abono.
         * 
         * @param {type} start
         * @param {type} end
         * @returns {undefined}
         */
        searchAllowanceBetween = function (start, end) {

            /**
             * send the dates with ajax
             * the return must be organized by dates
             * date, type, personName
             * foreach date print the date in a friendly format and  print a hr
             * print all the students in that date. Each student must be a cats-row
             * create a button to refresh the data
             * new month clicks must do nothing, maybe move the loaded content to the top
             * @returns {undefined}
             */

            var attr = start.split('-')[1];
            if (typeof allowanceStudentsByMonth[attr] === "undefined") {
                allowanceStudentsByMonth[attr] = [];

                $.ajax({
                    url: "/school-management/school-attendance/getAllowance",
                    type: "POST",
                    data: {
                        start: start,
                        end: end
                    },
                    success: function (data) {
                        var allowance = data.allowance;
                        var content = [];
                        var lastDate = "";
                        var lastIndx = -1;
                        for (var i = 0; i < allowance.length; i++) {

                            var mdate = moment(allowance[i].date.date);

                            if (lastDate !== mdate.format("DDMMYYYY")) {
                                lastDate = mdate.format("DDMMYYYY");
                                lastIndx++;
                                content.push({
                                    date: mdate,
                                    students: []
                                });
                            }

                            content[lastIndx].students.push({
                                attendanceId: allowance[i].attendanceId,
                                enrollmentId: allowance[i].enrollmentId,
                                attendanceType: allowance[i].attendanceType,
                                attendanceTypeId: allowance[i].attendanceTypeId,
                                className: allowance[i].className,
                                name: allowance[i].name,
                                personId: allowance[i].personId
                            });
                        }

                        showMonthAllowances(attr, content);
                        allowanceStudentsByMonth[attr] = content;
                    },
                    error: function (textStatus) {
                        console.log(textStatus);
                    }
                });
            } else {
                moveMonthAllowancesUp(attr);
            }
        };

        /**
         * Arrasta os abonos do mês para o topo ao clicar no calendário.
         * 
         * @param {type} month
         * @returns {undefined}
         */
        moveMonthAllowancesUp = function (month) {
            allowanceStudents
                    .find("#month-" + month)
                    .hide()
                    .detach()
                    .prependTo(allowanceStudents)
                    .slideDown("fast");
        };

        /**
         * Exibe os abonos do mês.
         * 
         * @param {type} month
         * @param {type} content
         * @returns {undefined}
         */
        showMonthAllowances = function (month, content) {

            var monthTemplate = "<div class='box box-solid' style='display:none;' id='month-" + month + "'>" +
                    "<div class='box-header with-border'>" +
                    "<h3 class='box-title'>" + moment(month, "MM").format("MMMM") + "</h3>" +
                    "<div class='box-tools pull-right'>" +
                    "<button type='button' class='btn btn-box-tool' data-widget='collapse'>" +
                    "<i class='fa fa-minus'></i>" +
                    "</button>" +
                    "</div>" +
                    "</div>" +
                    "<div class='box-body'>" +
                    "<div class='box-group' id='accordion-" + month + "'>";

            var enrId;

            for (var l = 0; l < content.length; l++) {
                var md = content[l].date;
                var stds = content[l].students;
                monthTemplate += "<div class='panel box box-primary'>" +
                        "<div class='box-header with-border'>" +
                        "<h4 class='box-title'>" +
                        "<a data-toggle='collapse' data-parent='#accordion-" + l +
                        "' href='#collapse-" + md.format("DDMMYYYY") +
                        "' aria-expanded='false' class='collapsed'>" +
                        md.format("LL") +
                        "</a>" +
                        "</h4>" +
                        "</div>" +
                        "<div id='collapse-" + md.format("DDMMYYYY") +
                        "' class='panel-collapse collapse' aria-expanded='false' style='height: 0px;'>" +
                        "<div class='box-body'>";

                monthTemplate += "<ul class='users-list clearfix'>";
                for (var i = 0; i < stds.length; i++) {
                    enrId = "" + stds[i].enrollmentId;

                    monthTemplate += "<li id='entity-" + stds[i].attendanceId + "' class='cats-row' data-mindex='" + month +
                            "' data-dindex='" + l + "' data-sindex='" + i + "' data-id='" + stds[i].attendanceId + "'>" +
                            "<img src='/recruitment/registration/photo/" + stds[i].personId + "' alt='" +
                            stds[i].name + "' width='64'>" +
                            "<p class='users-list-name'> " + ("0000" + enrId).substring(enrId.length) +
                            " - " + stds[i].name + " <br>(" + stds[i].attendanceType + ") <br>" +
                            stds[i].className +
                            "</p>" +
//                            "<span class='users-list-date'><b> " + stds[i].className + "</b></span>" +
                            "</li>";
                }
                monthTemplate += "</ul></div></div></div>";
            }

            monthTemplate += "</div></div></div>";
            monthTemplate = $(monthTemplate);

            allowanceStudents.prepend(monthTemplate);
            monthTemplate.slideDown("fast");
        };

        /**
         * Busca os dias do mês para a listagem dos abonos de cada dia.
         * 
         * @param {type} month
         * @returns {analyze-edit-allowance_L18.analyze-edit-allowance_L19.getDaysArrayByMonth.analyze-edit-allowanceAnonym$8}
         */
        getDaysArrayByMonth = function (month) {

            var daysInMonth = moment(month, "MM").daysInMonth();
            var days = {};
            var daysOfTheWeek = [0, 0, 0, 0, 0, 0, 0];

            while (daysInMonth) {
                var current = moment(month, "MM").date(daysInMonth);
                days[current.format("YYYYMMDD")] = current.format("e");
                daysOfTheWeek[current.format("e")]++;
                daysInMonth--;
            }

            return {
                days: days,
                daysOfTheWeek: daysOfTheWeek
            };
        };

        /**
         * Soma os valores do array, utilzado na análise.
         * @param {type} arr
         * @returns {Number}
         */
        sum = function (arr) {
            var sum = 0;
            for (var i = 1; i < arr.length; i++) {
                sum += arr[i];
            }
            return sum;
        };

        /**
         * Exibir mais informações ao clicar na linha de algum aluno.
         * @returns {undefined}
         */
        initTableListeners = function () {
            $("#attendanceContainer").on("click", "table tr td.details-control", function () {
                var tr = $(this).closest("tr");
                var student = tr.data("student");
                var month = tr.closest("table").data("month");
                var row = anTables["table-" + month].row(tr);
                var detailContent = null;

                if (row.child.isShown()) {
                    tr.removeClass("details");
                    row.child.hide();
                } else {
                    tr.addClass("details");
                    detailContent = getDetailsOf(studentData[month][student].sortedByWeekDays);
                    row.child(detailContent).show();
                }
            });
        };

        /**
         * Monta a visão detalhada da assiduidade de um aluno
         * 
         * @returns {undefined}
         */
        getDetailsOf = function (att) {

            var content = "<h3 class='text-center'>Detalhamento</h3><hr><div class='container'>";
            var doubleCounter;
            // Segunda à Sábado
            $.each(att, function (weekDay, attAll) {
                content += "<div class='col-lg-4 col-md-6 col-sm-6 col-xs-12'><h4>" + moment(weekDay, "e").format("dddd") + "</h4>";
                content += "<div class='catssys-list-box'>";
                doubleCounter = 0;
                for (var i = 0; i < attAll.length; i++) {
                    content += "<label>" + moment(attAll[i].date, "YYYYMMDD").format("L") + ":</label> ";
                    if (attAll[i].situation === null) {
                        content += "PRESENÇA | PRESENÇA";
                    } else if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_FULL)) {
                        content += "ABONO | ABONO";
                    } else {
                        if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_BEGIN)) {
                            content += "ABONO | ";
                            if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_END)) {
                                content += "ABONO";
                            } else if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_END)) {
                                content += "FALTA";
                            } else {
                                content += "PRESENÇA";
                            }
                        } else if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_BEGIN)) {
                            content += "FALTA | ";
                            if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_END)) {
                                content += "ABONO";
                            } else if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_END)) {
                                content += "FALTA &crarr;";
                                doubleCounter++;
                            } else {
                                content += "PRESENÇA";
                            }
                        } else {
                            content += "PRESENÇA | ";
                            if (attAll[i].situation.hasOwnProperty(ATTENDANCE_TYPES.ATTENDANCE_ALLOWANCE_END)) {
                                content += "ABONO";
                            } else {
                                content += "FALTA";
                            }
                        }
                    }
                    content += "<br>";
                }

                content += "<label>Total: " + doubleCounter + "</label>";
                content += "</div></div>";
            });


            content += "</div>";

            return content;
        };

        return {
            init: function () {

                moment.locale("pt-br");

                // edit allowance
                if (allowanceMonth.length > 0) {
                    initAttendanceMonthpicker(allowanceMonth);
                }

                // analyze
                if (attendanceMonth.length > 0) {
                    initAttendanceMonthpicker(attendanceMonth);
                    initTableListeners();
                }
            },
            getCallbackOf: function (selectedItemId) {

                switch (selectedItemId) {
                    case 'allowance-delete':
                        return {
                            exec: function (params) {
                                allowanceStudents.find("#entity-" + params.id)
                                        .slideUp("fast", function () {
                                            $(this).remove();
                                        });
                            }
                        };
                }
            }
        };

    }());

    return analyzeEditAllowance;
});