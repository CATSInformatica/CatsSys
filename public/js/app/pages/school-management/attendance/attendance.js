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

            attImportInput.change(function (e) {

                var files = e.target.files; // FileList object
                var file = files[0];

                var reader = new FileReader();
                reader.readAsText(file);

                reader.onload = function (event) {
                    lists = $.csv.toArrays(event.target.result);
                    showLists();
                };

                reader.onerror = function () {
                    bootbox.alert("Não foi possível abrir o arquivo <b>" + file.name + "<br>");
                };
            });
        };

        showLists = function () {


            // config

            $("#schoolClass")
                    .data("id", lists[0][1])
                    .next().find("em").text(lists[0][2]);

            $("#attendanceTypes")
                    .data("id", JSON.stringify(lists[1].slice(1)))
                    .next().find("em").text(lists[1].slice(1).join(", "));
            $("#attendanceDates")
                    .data("id", JSON.stringify(lists[2].slice(1)))
                    .next().find("em").text(lists[2].slice(1).join(", "));

            // lists

            for (var i = 6; i < lists.length; i++) {
                console.log(lists[i]);
            }
        };

        return {
            init: function () {

                if (add.length > 0 && rm.length > 0) {
                    initDateCopy();
                    initMasks();
                    applyDatepickers();
                }

                if (attImportInput.length > 0) {
                    require(['bootbox', 'jquerycsv'], function (bootbox) {
                        bindImportEvent(bootbox);
                    });

                }

            }
        };

    }());

    return generate;

});