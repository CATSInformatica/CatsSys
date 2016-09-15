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

define(['moment'], function (moment) {
    UploadAnswersTemplate = (function () {

        var examsTable = $("#exams-table");
        var selectedExams;

        initListeners = function () {
            $("#fetch-data").click(loadData);
            loadData();
        };

        loadData = function () {
            loadExams().then(loadAnswers).then(function (examAnswers) {
                console.log('examAnswers', examAnswers);
                //createExamTable(selectedExams);
            });
        };

        /**
         * 
         * @returns {jqXHR}
         */
        loadExams = function () {
            var application = $("#application").val();
            return $.ajax('/school-management/school-exam/get-exams/' + application, {
                dataType: 'json'
            });
        };

        loadAnswers = function (exams) {
            selectedExams = exams;
            examIds = exams.map(function (exam) {
                return exam.examId;
            });
            return $.ajax('/school-management/school-exam-result/get-answers', {
                type: 'POST',
                data: {
                    exams: examIds
                },
                dataType: 'json'
            });
        };

        createExamTable = function (exams) {

            var tbody;

            exams.forEach(function (exam) {
                tbody += "<tr class='cats-row' data-id='" + exam.examId + "'>" +
                        "<td class='details-control'></td>" +
                        "<td class='text-center'>" + exam.name + "</td>" +
                        "<td class='text-center'>" + moment(exam.date.date).format("DD/MM/YYYY") + "</td>" +
                        "<td class='text-center'>" + moment(exam.startTime.date).format("HH:mm") + "</td>" +
                        "<td class='text-center'>" + moment(exam.endTime.date).format("HH:mm") + "</td>" +
                        "</tr>";
            });

            examsTable.find("tbody").html(tbody);
        };

        return {
            init: function () {
                initListeners();
            }
        };
    })();

    return UploadAnswersTemplate;
});
