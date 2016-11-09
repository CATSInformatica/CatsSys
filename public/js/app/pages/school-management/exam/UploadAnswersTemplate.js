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
    UploadAnswersTemplate = (function () {

        var examsTable = $("#exams-table");
        var dtExamsTable;
        var selectedExams;
        var cachedAnswers;

        initListeners = function () {

            dtExamsTable = examsTable.DataTable({
                dom: '',
                paging: false,
                ajax: {
                    url: "/school-management/school-exam/get-exams/" + getChosenApplication(),
                    type: "GET",
                    dataType: "json",
                    dataSrc: function (exams) {
                        var result = [];
                        for (var i = 0; i < exams.length; i++) {
                            result.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "data-id": exams[i].examId
                                },
                                0: '',
                                1: exams[i].name,
                                2: moment(exams[i].date.date).format("DD/MM/YYYY"),
                                3: moment(exams[i].startTime.date).format("HH:mm"),
                                4: moment(exams[i].endTime.date).format("HH:mm")
                            });
                        }

                        return result;
                    }
                },
                columnDefs: [
                    {
                        className: "text-center",
                        targets: [2, 3, 4]
                    },
                    {
                        className: "details-control",
                        targets: [0]
                    }
                ]
            });
            
            
            $("#fetch-data").click(function () {
                dtExamsTable.ajax.reload();
            });

            examsTable.on("click", "tr td.details-control", function () {
                var tr = $(this).closest("tr");
                var row = dtExamsTable.row(tr);
                if (row.child.isShown()) {
                    tr.removeClass("details");
                    row.child.hide();
                } else {
                    tr.addClass("details");
                    var detailContent = getDetailsOf();
                    row.child(detailContent).show();
                    loadAnswers(tr.data('id')).then(function(answers) {
                        setTimeout(function(){
                            tr.next('tr').find('.spinner-feedback').remove();
                        }, 500);
                    });
                }
            });
        };
        
        getChosenApplication = function() {
            return $("#application").val();
        };

        loadAnswers = function (examId) {
            return $.ajax('/school-management/school-exam-result/get-answers/' + examId, {
                type: 'GET',
                dataType: 'json'
            });
        };

        /**
         * Calcula o gabarito das questões da prova selecionada
         * 
         * @returns string
         */
        getDetailsOf = function () {

            var content = "<h3 class='text-center'>Gabarito</h3><hr><div class='container'>";
            content += "<h4 class='text-center spinner-feedback'><i class='fa fa-refresh fa-spin fa-5x'></i></h4>";
            content += "</div>";

            return content;
        };

        return {
            init: function () {
                initListeners();
            }
        };
    })();

    return UploadAnswersTemplate;
});
