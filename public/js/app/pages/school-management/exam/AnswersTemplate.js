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
                                1: exams[i].examId,
                                2: exams[i].name,
                                3: moment(exams[i].date.date).format("DD/MM/YYYY"),
                                4: moment(exams[i].startTime.date).format("HH:mm"),
                                5: moment(exams[i].endTime.date).format("HH:mm")
                            });
                        }

                        return result;
                    }
                },
                columnDefs: [
                    {
                        className: "text-center",
                        targets: [1, 3, 4, 5]
                    },
                    {
                        className: "details-control",
                        targets: [0]
                    }
                ]
            });


            $("#fetch-data").click(function () {
                dtExamsTable.ajax.url("/school-management/school-exam/get-exams/" + getChosenApplication()).load();
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
                    loadAnswers(tr.data('id')).then(function (ans) {
                        tr.next('tr').find('.spinner-feedback').remove();
                        var table = generateAnswersTable(ans);
                        tr.next('tr').find('.container').append(table);
                        tr.next('tr').find('table tr')
                                .off('click', 'td.clickable-answer')
                                .on('click', 'td.clickable-answer', function () {
                                    if ($(this).text() !== $(this).data('value')) {
                                        $(this).text($(this).data('value'));
                                    } else {
                                        $(this).text('ANULADA');
                                    }
                                });
                    });
                }
            });
        };

        generateAnswersTable = function (ans) {
            var table = '<table class="table table-condensed table-bordered table-striped">' +
                    '<thead><tr><th class="text-center">Nº</th><th class="text-center">Resposta</th><th class="text-center">Resp. Anterior</th></tr></thead><tbody>';

            var config = ans.config;
            var answers = ans.answers;
            var group, subgroup, question, prevAns;

            var qNum = config.questionsStartAtNumber;
            var tempNum;
            var parallelIdx = -1;

            for (var i = 0; i < config.groups.length; i++) {
                group = config.groups[i];
                table += '<tr><th colspan="3" class="text-center bg-green">' + group.groupName + '</th></tr>';
                for (var j = 0; j < group.subgroups.length; j++) {
                    subgroup = group.subgroups[j];
                    if (subgroup instanceof Array) {
                        parallelIdx++;
                        for (var g = 0; g < subgroup.length; g++) {
                            tempNum = qNum;
                            table += '<tr><td colspan="3" class="text-center bg-navy">' + subgroup[g].subgroupName + '</td></tr>';

                            for (var k = 0; k < subgroup[g].questions.length; k++) {
                                question = subgroup[g].questions[k];
                                prevAns = answers !== null ? (answers[tempNum].answers ? answers[tempNum].answers[g] : 'SUBGRUPO PARALELO TROCADO') : '-';
                                table += generateAnswerRow(tempNum, question.answer, prevAns, parallelIdx);
                                tempNum++;
                            }
                        }

                        qNum = tempNum;
                    } else {
                        table += '<tr><td colspan="3" class="text-center bg-navy">' + subgroup.subgroupName + '</td></tr>';
                        for (var k = 0; k < subgroup.questions.length; k++) {
                            question = subgroup.questions[k];
                            prevAns = answers !== null ? (answers[qNum].answers ? 'SUBGRUPO PARALELO TROCADO' : answers[qNum]) : '-';
                            table += generateAnswerRow(qNum, question.answer, prevAns, null);
                            qNum++;
                        }
                    }
                }
            }

            table += '</tbody></table>';
            return table;
        };

        generateAnswerRow = function (qNum, answ, prevAns, parallelIdx) {
            return '<tr class="answer-row" data-num="'+ qNum +'" data-parallel="'+ parallelIdx +'"><td class="text-center">' + qNum +
                    '</td><td class="text-center clickable-answer" data-value=' + answ + '>' + answ +
                    '</td><td class="text-center">' + prevAns + '</td></tr>';
        };

        getChosenApplication = function () {
            return $("#application").val();
        };

        loadAnswers = function (examId) {
            return $.ajax('/school-management/school-exam-result/get-template-answers/' + examId, {
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
            content += "<h4 class='text-center spinner-feedback'><i class='fa fa-refresh fa-spin fa-4x'></i></h4>";
            content += "</div>";

            return content;
        };

        return {
            init: function () {
                initListeners();
            },
            getDataOf: function (selectedItemId) {
                
                switch(selectedItemId) {
                    case 'save-template':
                        
                        var examTemplates = [];
                        
                        examsTable.find('tr.cats-selected-row').each(function(){
                            var examTemplate = {};
                            
                            $(this).next('tr').find('table tr.answer-row').each(function(){
                                
                                var num = $(this).data('num');
                                
                                ans = $(this).find('td.clickable-answer').text();
                                
                                if(examTemplate[num] === undefined) {
                                   examTemplate[num] = ans;
                                } else if(examTemplate[num] instanceof Object) {
                                    examTemplate[num].answer.push(ans);
                                } else {
                                   examTemplate[num] = {
                                       parallel: parseInt($(this).data('parallel')),
                                       answers: [examTemplate[num], ans]
                                   };
                                }
                            });
                            
                            examTemplates.push({
                                id: $(this).data('id'),
                                template: examTemplate
                            });
                        });
                        
                        return {
                            templates: examTemplates
                        };
                        
                        break;
                    default: 
                        return {  
                        };
                }
            }
        };
    })();

    return UploadAnswersTemplate;
});
