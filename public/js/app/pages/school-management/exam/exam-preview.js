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


define(['bootbox', 'jquerycsv'], function (bootbox) {

    var students = [];
    var csvData = {
        answers: null,
        template: null
    };

    startListeners = function () {

        bindImportEvent();
        $("#calculate").click(function () {
            getStudents($("#studentClass").val()).then(function () {
                console.log(students);
                console.log(csvData);
            });
        });

    };

    /**
     * @TODO ver como utilizar o delimiter ";"
     * 
     * @returns {undefined}
     */
    bindImportEvent = function () {

        $("#answers, #template").click(function () {
            $(this).val("");
        });

        $("#answers, #template").change(function (e) {

            var prop = $(this).attr("id");
            var files = e.target.files; // FileList object
            var file = files[0];

            var reader = new FileReader();
            reader.readAsText(file);

            reader.onload = function (event) {
                csvData[prop] = $.csv
                        .toArrays(event.target.result, {separator: ";"});
            };

            reader.onerror = function () {
                bootbox.alert("Não foi possível abrir o arquivo <b>" +
                        file.name + "<br>");
            };
        });
    };



    /**
     * 
     * @param {type} classId
     * @returns {jqXHR} promise
     */
    getStudents = function (classId) {

        return $.ajax({
            url: '/school-management/student-class/get-students',
            type: 'POST',
            data: {
                id: classId
            },
            success: function (data) {
                students = data.students;
            },
            error: function (err) {
                console.log(err);
                students = [];
            }
        });
    };


    var preview = (function () {
        return {
            init: function () {
                startListeners();
            }
        };
    }());

    return preview;
});