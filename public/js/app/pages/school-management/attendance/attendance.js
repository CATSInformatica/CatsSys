/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['masks', 'moment', 'datetimepicker'], function (masks, moment) {

    var generate = (function () {

        var templateDate = moment();

        var add = $("#addAttendanceDate");
        var rm = $("#removeAttendanceDate");
        var attImportInput = $("#attendanceListInput");
        var lists;
        var attendanceLists = $("#attendanceLists");
        var listModels = [];
        var allowanceMonth = $("#allowanceMonth");
        var allowanceStudentsByMonth = {};
        var allowanceStudents = $("#allowanceStudents");

        initDateCopy = function () {
            add.click(addAttendanceDate);
            rm.click(removeAttendanceDate);
        };

        addAttendanceDate = function () {

            var currentValue = $("input[name*=attendanceDate]").last().val();

            if (currentValue !== "") {
                templateDate = moment(currentValue, "DD/MM/YYYY");
            }

            var currentCount = $('form fieldset > fieldset').length;
            var template = $('form fieldset > span').data('template');
            template = template.replace(/__index__/g, currentCount);

            var htmlTemplate = $(template);
            htmlTemplate.find('input')
                    .val(templateDate.add(1, 'days').format('DD/MM/YYYY'));

            $('form fieldset').first().append(htmlTemplate);
            applyDatepickers();
        };

        removeAttendanceDate = function () {
            var currentCount = $('form fieldset > fieldset').length;
            if (currentCount > 1) {
                $('form fieldset > fieldset').last().remove();
                templateDate.subtract(1, 'days').format('DD/MM/YYYY')
            }
        };

        initMasks = function () {
            masks.bind({
                date: "input[name*=attendanceDate]"
            });
        };

        applyDatepickers = function () {
            $("input[name*=attendanceDate]")
                    .closest('.input-group')
                    .datetimepicker({
                        format: 'DD/MM/YYYY',
                        minDate: moment().subtract(6, 'months'),
                        useCurrent: true,
                        locale: 'pt-br',
                        viewMode: 'days',
                        viewDate: moment()
                    });
        };

        bindImportEvent = function (bootbox) {

            attImportInput.click(function () {
                $(this).val("");
            });

            attImportInput.change(function (e) {

                var files = e.target.files; // FileList object
                var file = files[0];

                var reader = new FileReader();
                reader.readAsText(file);

                reader.onload = function (event) {
                    lists = $.csv.toArrays(event.target.result);
                    importReset();
                    createLists();
                    setAttendanceActionListeners();
                };

                reader.onerror = function () {
                    bootbox.alert("Não foi possível abrir o arquivo <b>" + file.name + "<br>");
                };
            });
        };

        importReset = function () {
            listModels = [];
            attendanceLists.html("");
        };

        createLists = function () {

            var index;

            index = lists[1].indexOf("");
            if (index > 0) {
                lists[1] = lists[1].slice(0, index);
            }
            index = lists[2].indexOf("");
            if (index > 0) {
                lists[2] = lists[2].slice(0, index);
            }
            index = lists[3].indexOf("");
            if (index > 0) {
                lists[3] = lists[3].slice(0, index);
            }

            var attendanceTypesIds = lists[1].slice(1);
            var attendanceTypesNames = lists[2].slice(1);
            var dates = lists[3].slice(1);

            // config
            $("#schoolClass")
                    .data("id", lists[0][1])
                    .next().find("em").text(lists[0][2]);

            $("#attendanceTypes")
                    .data("id", JSON.stringify(attendanceTypesIds))
                    .next().find("em").text(attendanceTypesNames.join(", "));
            $("#attendanceDates")
                    .data("id", JSON.stringify(dates))
                    .next().find("em").text(dates.join(", "));

            // foreach day
            for (var d = 0; d < dates.length; d++) {

                var sm = {
                    date: moment(dates[d], "DD/MM/YYYY").format("YYYY-MM-DD"),
                    typeNames: attendanceTypesNames,
                    students: []
                };

                // foreach student
                for (var i = 7; i < lists.length; i++) {

                    var student = {
                        id: lists[i][0], //enrollmentId
                        name: lists[i][1],
                        types: []
                    };
                    // foreach attendanceType
                    for (var a = 0; a < attendanceTypesIds.length; a++) {

                        student.types.push({
                            id: attendanceTypesIds[a],
                            status: lists[i][2 + a + d * (attendanceTypesIds.length + 1)].toUpperCase() === "P" ? 1 : 0
                        });
                    }

                    sm.students.push(student);
                }

                listModels.push(sm);
                showList(sm, d);
            }
        };

        showList = function (list, index) {
            var i, j;
            var container = $("<div class='panel box box-success col-md-6 col-xs-12 cats-row' style='display:none;'>" +
                    "<div class='box-header with-border'>" +
                    "<h4 class='box-title'>" +
                    "<a data-toggle='collapse' data-parent='#" + attendanceLists.attr("id") + "' " +
                    "href='#collapse-" + index + "'>" +
                    "Lista de " + moment(list.date, "YYYY-MM-DD")
                    .format("DD/MM/YYYY") +
                    "</a>" +
                    "</h4>" +
                    "</div>" +
                    "<div id='collapse-" + index + "' class='panel-collapse collapse'>" +
                    "<div class='box-body bg-white'>" +
                    "</div>" +
                    "</div>" +
                    "</div>");

            var table = "<div class='col-md-8'><table data-id='" + index + "' class='table table-condensed table-bordered table-striped table-hover attendanceListTable'>" +
                    "<thead><tr>";

            table += "<th>Aluno</th>";
            for (i = 0; i < list.typeNames.length; i++) {
                table += "<th class='text-center'>" + list.typeNames[i] + "</th>";
            }

            table += "</tr></thead><tbody>";


            for (i = 0; i < list.students.length; i++) {
                table += "<tr data-id='" + i + "'>";
                table += "<td>" + list.students[i].name + "</td>";
                for (j = 0; j < list.students[i].types.length; j++) {
                    table += "<td data-id='" + j + "'" +
                            "class='text-center btn-" + (list.students[i].types[j].status ? "success" : "danger") + " attendanceStatus'>" +
                            "<i class='fa " + (list.students[i].types[j].status ? "fa-check" : "fa-close") + "' ></i></td>";
                }
                table += "</tr>";
            }

            table += "</tbody></table></div>";
            container.find(".box-body").append(table);
            attendanceLists.append(container);
            container.slideDown('fast');
        };

        setAttendanceActionListeners = function () {

            // block cats-selected-row change on table
            attendanceLists.off("click", ".attendanceListTable");
            attendanceLists.on("click", ".attendanceListTable", function (e) {
                e.stopPropagation();
            });

            attendanceLists.off("click", ".attendanceListTable td.attendanceStatus");
            attendanceLists.on("click", ".attendanceListTable td.attendanceStatus", function (e) {
                var type = $(this).data("id");
                var student = $(this).closest("tr").data("id");
                var list = $(this).closest("table").data("id");

                var result = listModels[list].students[student].types[type].status ^= true;

                if (result) {
                    $(this)
                            .addClass("btn-success")
                            .removeClass("btn-danger")
                            .find("i")
                            .addClass("fa-check")
                            .removeClass("fa-close");
                } else {
                    $(this)
                            .addClass("btn-danger")
                            .removeClass("btn-success")
                            .find("i")
                            .addClass("fa-close")
                            .removeClass("fa-check");
                }
            });
        };

        initAllowanceDatepicker = function () {

            allowanceMonth.datetimepicker({
                format: 'MMMM',
                inline: true,
                viewMode: 'months',
                locale: 'pt-br',
                useCurrent: false,
                defaultDate: moment("1", "D")
            });

            allowanceMonth.on("dp.change", function (e) {
                var startDate = e.date.format('YYYY-MM-DD');
                var endDate = e.date.add(1, 'months').format('YYYY-MM-DD');

//                console.log({
//                    start: moment().format("YYYY-MM-01"),
//                    end: moment().add(1, 'months').format("YYYY-MM-01")
//                });

                searchAllowanceBetween(startDate, endDate);
            });
        };

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
                                attendanceId: allowance[i].enrollmentId,
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

                    monthTemplate += "<li class='cats-row' data-mindex='" + month +
                            "' data-dindex='" + l + "' data-sindex='" + i + "'>" +
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

        moveMonthAllowancesUp = function (month) {
            allowanceStudents
                    .find("#month-" + month)
                    .hide()
                    .detach()
                    .prependTo(allowanceStudents)
                    .slideDown("fast");
        };
        
        /**
         * @TODO
         * Criar botão de remoção de abonos.
         * O botão deve levar em consideração os valores do item selecionado:
         *  * data-mindex="i"
         *  * data-dindex="j"
         *  * data-sindex="k"
         *   
         *   Ex: allowanceStudentsByMonth[i][j][k] // abono do mês i, 
         *   na j-ésima data referente ao k-ésimo aluno
         *   allowanceStudentsByMonth[i][j][k].attendanceId 
         *   // id da linha do abono no banco
         */

        return {
            init: function () {
                moment.locale("pt-br");

                // generateList
                if (add.length > 0 && rm.length > 0) {
                    initDateCopy();
                    initMasks();
                    applyDatepickers();
                }

                // importList
                if (attImportInput.length > 0) {
                    require(['bootbox', 'jquerycsv'], function (bootbox) {
                        bindImportEvent(bootbox);
                    });
                }

                // allowance
                if (allowanceMonth.length > 0) {
                    initAllowanceDatepicker();
                }

            },
            getDataOf: function (selectedElementId) {

                var index = $(".cats-selected-row").find(".attendanceListTable").data("id");

                switch (selectedElementId) {
                    case 'attendance-list-save':
                        return listModels[index];
                        break;
                }
            }
        };

    }());

    return generate;
});