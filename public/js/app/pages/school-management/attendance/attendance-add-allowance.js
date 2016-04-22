/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['masks', 'moment', 'datetimepicker', 'datatable'], function (masks, moment) {

    var attendance = (function () {

        var templateDate = moment();

        var add = $("#addAttendanceDate");
        var rm = $("#removeAttendanceDate");
        var attImportInput = $("#attendanceListInput");
        var lists;
        var attendanceLists = $("#attendanceLists");
        var listModels = [];

        var studentClass = $("#studentClass");
        var chosenDate = null;
        var chosenAllowanceList = $("#chosenAllowanceList");

        // generateListV2
        var AttListsV2 = [];

        // generateList
        initDateCopy = function () {
            add.click(addAttendanceDate);
            rm.click(removeAttendanceDate);
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
                templateDate.subtract(1, 'days').format('DD/MM/YYYY');
            }
        };

        // generateListV2
        applyLoadListsListener = function () {

            $("#generatev2").click(function () {

                var dates = [];
                $("input[name^='dates['").each(function () {
                    dates.push(moment($(this).val(), "DD/MM/YYYY").format("YYYY-MM-DD"));
                });

                var types = [];
                $("input[name='attendanceType[]']:checked").each(function () {
                    types.push($(this).val());
                });

                if (dates.length > 0 && types.length > 0) {

                    var promise = getLists({
                        classId: $("select[name=schoolClasses]").val(),
                        dates: dates,
                        types: types
                    });
                    promise.then(function () {
                        console.log(AttListsV2);
                    });

                }

            });
        };

        /**
         * 
         * @param {type} classId
         * @returns {jqXHR} promise
         */
        getLists = function (data) {

            return $.ajax({
                url: '/school-management/school-attendance/getLists',
                type: 'POST',
                data: data,
                success: function (response) {
                    AttListsV2 = response.lists;
                },
                error: function (err) {
                    console.log(err);
                    AttListsV2 = [];
                }
            });
        };

        // importList
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
        setAttendanceActionListeners = function () {

            attendanceLists.off("click", ".listTitle");
            attendanceLists.on("click", ".listTitle", function () {
                $(this)
                        .prev("input[name=attendanceListRadio]")
                        .prop("checked", true);
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
        showList = function (list, index) {
            var i, j;
            var container = $("<div class='panel box box-success col-md-6 col-xs-12' style='display:none;'>" +
                    "<div class='box-header with-border'>" +
                    "<input name='attendanceListRadio' type='radio'> " +
                    "<h4 class='box-title listTitle'>" +
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

            var table = "<div class='col-md-8'><table data-id='" + index + "' class='table table-bordered attendanceListTable'>" +
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


        // add allowance
        initElements = function () {

            var template = "";

            // Students
            var stdClass = studentClass.val();
            studentClass.change(function () {
                getStudents(stdClass);
            });
            getStudents(stdClass);

            // Calendar
            $("#allowanceDate").datetimepicker({
                format: 'DD/MM/YYYY',
                inline: true,
                viewMode: 'days',
                locale: 'pt-br',
                useCurrent: false
            });

            $("#allowanceDate").on("dp.change", function (e) {
                chosenDate = e.date;
            });

            // button
            $("#addAllowance").on("click", function () {
                var selectedAllowance = $("input[name=attendanceType]:checked");
                if (chosenDate !== null && selectedAllowance.length > 0) {


                    template = "<div data-student='" + $("#students").val()
                            + "' data-allowancetype='" + selectedAllowance.val()
                            + "' data-date='" + chosenDate.format("YYYY-MM-DD")
                            + "' class='chosenAllowanceItem alert alert-success alert-dismissible fade in' role='alert'>" +
                            "<button type='button' class='close pull-left' data-dismiss='alert' aria-label='Close'>" +
                            "<span aria-hidden='true'><i class='fa fa-close'></i></span>" +
                            "</button>" +
                            "<p class='text-center'><strong>" + chosenDate.format("dddd, LL") + "</strong><br>" +
                            $("#students").find("option:selected").text() +
                            "<br>(" + selectedAllowance.data("name") + ")" +
                            "</p></div>";

                    chosenAllowanceList.append(template);
                }

                /**
                 * getDataOf must get all data-* of chosenAllowanceItem
                 * @param {type} id
                 * @returns {undefined}
                 */

            });
        };
        getStudents = function (id) {
            $.ajax({
                url: '/school-management/student-class/getStudents',
                type: 'POST',
                data: {
                    id: id
                },
                success: function (data) {
                    insertStudents(data.students);
                },
                error: function (textStatus) {
                    console.log(textStatus);
                }
            });
        };

        insertStudents = function (students) {
            $("#students").html("");
            var enrId;
            var template;
            for (var i = 0; i < students.length; i++) {
                enrId = "" + students[i].enrollmentId;

                template = "<option value='" + students[i].enrollmentId + "'>" +
                        ("0000" + enrId).substring(enrId.length) +
                        " - " +
                        students[i].personFirstName + " " +
                        students[i].personLastName +
                        "</option>";

                $("#students").append(template);
            }
        };

        getChosenAllowances = function () {

            var data = {
                allowances: []
            };

            chosenAllowanceList.find(".chosenAllowanceItem").each(function () {
                data.allowances.push({
                    enrollment: $(this).data('student'),
                    date: $(this).data('date'),
                    allowanceType: $(this).data('allowancetype')
                });
            });

            return data;
        };

        return {
            init: function () {
                moment.locale("pt-br");

                // generateListV2
                if ($("#generatev2").length > 0) {
                    initDateCopy();
                    initMasks();
                    applyDatepickers();
                    applyLoadListsListener();

                    // generateList
                } else if (add.length > 0 && rm.length > 0) {
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

                // add allowance
                if (studentClass.length > 0) {
                    initElements();
                }
            },
            getDataOf: function (selectedItemId) {

                switch (selectedItemId) {
                    case 'attendance-list-save':
                        var index = attendanceLists
                                .find("[name=attendanceListRadio]:checked")
                                .closest(".panel")
                                .find(".attendanceListTable")
                                .data("id");
                        return listModels[index];
                        break;
                    case 'allowance-save':
                        return getChosenAllowances();
                        break;
                }
            },
            getCallbackOf: function (selectedItemId) {

            }
        };

    }());

    return attendance;
});