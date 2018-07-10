/* 
 * Copyright (C) 2017 Márcio Dias <marciojr91@gmail.com>
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


    var ApplicationResultModule = (function () {

        var currentApplicationId = null;
        var exams;
        var ApplicationElel = $("#application");
        var resultContainer = $("#result-container");
        var sortGroupContainer = $("#sort-group-container");
        var recruitment = $("#recruitment");
        var numberOfExams;
        var groups;
        var groupOrder;
        var answers;
        var sortedAnswers;
        var saveIndex;

        var CRITERIA = {
            AGE_CODE: -1,
            AGE_DESC: 'NASCIMENTO'
        };

        var NULLIFIED = 'ANULADA';

        initListeners = function () {

            currentApplicationId = ApplicationElel.val();
            ApplicationElel.change(function () {
                currentApplicationId = ApplicationElel.val();
                sortedAnswers = null;
            });
            $("#fetch-new-data").click(function () {
                getExams().then(function (e) {
                    if (e.length) {

                        numberOfExams = e.length;
                        exams = {};
                        groups = [];
                        resultContainer.html('');
                        sortGroupContainer.html('');

                        var defs = [];
                        var examId;

                        for (var i = 0; i < e.length; i++) {
                            examId = e[i].examId;
                            exams[examId] = e[i];
                            addGroupsOf(examId);
                            defs.push(loadAnswers(examId));
                        }

                        $.when(defs).then(function () {
                            $("#set-criteria").prop('disabled', false);
                            createSortList();
                        });
                    }
                });
            });

            $("#fetch-current-data").click(function () {
                sortedAnswers = null;
                exams = {};
                groups = [];
                groupOrder = [];
                answers = {};
                resultContainer.html('');
                sortGroupContainer.html('');
                $("#set-criteria").prop('disabled', true);

                getApplicationResult().then(function (results) {
                    if (results.length) {
                        // ordena o resultado antes de adicionar na tabela (1º, 2º, 3º, ...)
                        createLoadedResultTable(results.sort(function(prev, next){
                            intPrev = parseInt(prev.position);
                            intNext = parseInt(next.position);
                            if(intPrev < intNext) {
                                return -1;
                            } else if(intNext < intPrev) {
                                return 1;
                            }                            
                            return 0;
                        }));
                    }

                });
            });

            $("#set-criteria").on('click', function () {
                groupOrder = [];

                $("select[id^='criteria-group']").each(function () {
                    groupOrder.push(parseInt($(this).val()));
                });

                adjustAnswers();

                calcResult();

                console.log('exams', exams);
                console.log('groups', groups);
                console.log('order', groupOrder);
                console.log('answers', answers);
                console.log('sortedAnswers', sortedAnswers);
                saveIndex = 0;
                createResultTable();
            });
        };

        createLoadedResultTable = function (results) {
            var table = "<table class='table table-condensed table-striped table-bordered'>";

            table += "<thead><tr><th class='text-center'>POSIÇÃO</th><th class='text-center'>SITUAÇÃO</th><th class='text-center'>INSCRIÇÃO</th>";

            gs = results[0].groups;

            table += gs.map(function (g) {
                return "<th class='text-center'>" + g + "</th>";
            }).join('');

            table += "<th class='text-center'>TOTAL</th></tr><thead><tbody>";

            var tableContent = "";

            // para cada resultado
            for (var i = 0; i < results.length; i++) {
                tableContent += '<tr><td class="text-center">' + results[i].position +
                        'º</td><td class="text-center">' + results[i].currentStatus + '</td><td class="text-center">' + results[i].registrationNumber + '</td>';
                // para cada grupo, na ordem definida
                for (var j = 0; j < gs.length; j++) {
                    tableContent += '<td class="text-center">' + results[i].partialResult[j] + '</td>';
                }

                tableContent += '<td class="text-center">' + results[i].result + '</td></tr>';
            }

            table += tableContent;
            table += "</tbody></table>";

            resultContainer.html(table);
        };

        getApplicationResult = function () {
            return $.ajax("/school-management/school-exam-result/get-result/" + currentApplicationId, {
                type: 'GET',
                dataType: 'json'
            });
        };

        getExams = function () {
            return $.ajax("/school-management/school-exam/get-exams/" + currentApplicationId, {
                type: 'GET',
                dataType: 'json'
            });
        };

        loadAnswers = function (examId) {

            var def = $.Deferred();

            return $.ajax('/school-management/school-exam-result/getAllAnswers/' + examId, {
                type: 'GET',
                datatype: 'json'
            }).then(function (ans) {
                exams[ans.examId].peopleAnswers = ans.answers;
                def.resolve();
            }, function () {
                def.reject();
            });

            return def;
        };

        adjustAnswers = function () {

            answers = {};
            var ans;

            Object.keys(exams).forEach(function (examId) {
                ans = exams[examId].peopleAnswers;
                for (var i = 0; i < ans.length; i++) {

                    if (!answers[ans[i].registrationId]) {
                        answers[ans[i].registrationId] = {
                            enrollment: ans[i].enrollment,
                            registrationId: ans[i].registrationId,
                            registrationNumber: ans[i].registrationNumber,
                            birth: ans[i].birth,
                            currentStatus: ans[i].currentStatus,
                            partialResult: [],
                            result: 0,
                            groups: []
                        };
                    }

                    answers[ans[i].registrationId][examId] = ans[i].answers;
                }
            });
        };

        calcResult = function () {

            var currentGroup;
            var score;
            var correctAnswers;
            var person;
            var parallelIndx, parallel;
            var endAt;

            // para cada grupo, na ordem definida
            for (var i = 0; i < groupOrder.length; i++) {

                if (groupOrder[i] !== CRITERIA.AGE_CODE) {
                    currentGroup = groups[groupOrder[i]];
                    correctAnswers = exams[currentGroup.examId].answers;

                    Object.keys(answers).forEach(function (registrationId) {
                        person = answers[registrationId][currentGroup.examId];

                        // calcular a pontuaçao no grupo currentGroup
                        score = 0;

                        endAt = currentGroup.quantity + currentGroup.startAt;
                        for (var q = currentGroup.startAt; q < endAt; q++) {

                            // corrigir
                            if (correctAnswers[q] instanceof Object) {
                                parallelIndx = correctAnswers[q].parallel;
                                parallel = person.parallels[parallelIndx];
                                if (correctAnswers[q].answers[parallel] === NULLIFIED || person.answers[q] === correctAnswers[q].answers[parallel]) {
                                    score++;
                                }
                            } else {
                                if (correctAnswers[q] === NULLIFIED || person.answers[q] === correctAnswers[q]) {
                                    score++;
                                }
                            }
                        }

                        answers[registrationId].partialResult.push(score);
                        answers[registrationId].result += score;
                        answers[registrationId].groups.push(currentGroup.name);
                    });
                } else {
                    Object.keys(answers).forEach(function (registrationId) {
                        answers[registrationId].partialResult.push(-moment(answers[registrationId].birth, 'DD/MM/YYYY').unix());
                    });
                }
            }

            sortedAnswers = [];

            Object.keys(answers).forEach(function (registrationId) {
                sortedAnswers.push({
                    enrollment: answers[registrationId].enrollment,
                    registrationId: answers[registrationId].registrationId,
                    currentStatus: answers[registrationId].currentStatus,
                    registrationNumber: answers[registrationId].registrationNumber,
                    birth: answers[registrationId].birth,
                    partialResult: answers[registrationId].partialResult,
                    result: answers[registrationId].result,
                    groups: answers[registrationId].groups
                });
            });

            sortedAnswers.sort(function (answerA, answerB) {

                var ret;

                // resultado A maior que resultado B
                if (answerA.result > answerB.result) {
                    for (var i = 0; i < answerA.partialResult.length; i++) {

                        if (!answerB.partialResult[i] && answerA.partialResult[i]) {
                            return -1;
                        }

                        if (!answerA.partialResult[i] && answerB.partialResult[i]) {
                            return 1;
                        }
                    }
                    return -1;
                } else if (answerB.result > answerA.result) {

                    for (var i = 0; i < answerA.partialResult.length; i++) {

                        if (!answerA.partialResult[i] && answerB.partialResult[i]) {
                            return 1;
                        }

                        if (!answerB.partialResult[i] && answerA.partialResult[i]) {
                            return -1;
                        }

                    }

                    return 1;
                } else {
                    ret = 0;
                    // Resultados iguais
                    for (var i = 0; i < answerA.partialResult.length; i++) {
                        if (answerA.partialResult[i] > answerB.partialResult[i] && !ret) {
                            ret = -1;
                        } else if (answerB.partialResult[i] > answerA.partialResult[i] && !ret) {
                            ret = 1;
                        }

                        if (ret > 0 && !answerB.partialResult[i] && answerA.partialResult[i]) {
                            return -1;
                        } else if (ret < 0 && !answerA.partialResult[i] && answerB.partialResult[i]) {
                            return 1;
                        }
                    }

                    return ret;
                }
            });
        };

        addGroupsOf = function (examId) {

            var groupContent = exams[examId].content.groups;
            var subgroups;
            var quantity;
            var firstNum = exams[examId].content.questionsStartAtNumber;
            var startAt = firstNum;

            for (var i = 0; i < groupContent.length; i++) {

                quantity = 0;

                subgroups = groupContent[i].subgroups;
                for (var j = 0; j < subgroups.length; j++) {
                    if (subgroups[j] instanceof Array) {
                        quantity += subgroups[j][0].questions.length;
                    } else {
                        quantity += subgroups[j].questions.length;
                    }
                }

                groups.push({
                    examId: examId,
                    name: groupContent[i].groupName,
                    startAt: startAt,
                    quantity: quantity
                });

                startAt += quantity;
            }
        };

        createResultTable = function () {

            var table = "<table class='table table-condensed table-striped table-bordered'>";

            table += "<thead><tr><th class='text-center'>POSIÇÃO</th><th class='text-center'>SITUAÇÃO</th><th class='text-center'>INSCRIÇÃO</th>";

            table += groupOrder.map(function (i) {
                if (i !== CRITERIA.AGE_CODE) {
                    return "<th class='text-center'>" + groups[i].name + "</th>";
                } else {
                    return "<th class='text-center'>" + CRITERIA.AGE_DESC + "</th>";
                }
            }).join('');

            table += "<th class='text-center'>TOTAL</th></tr><thead><tbody>";

            var tableContent = "";

            // para cada resultado
            for (var i = 0; i < sortedAnswers.length; i++) {
                sortedAnswers[i].position = i + 1;
                tableContent += '<tr><td class="text-center">' + sortedAnswers[i].position +
                        'º</td><td class="text-center">' + sortedAnswers[i].currentStatus +
                        '</td><td class="text-center">' + sortedAnswers[i].registrationNumber + '</td>';
                // para cada grupo, na ordem definida
                for (var j = 0, partialResultIndex = 0; j < groupOrder.length; j++) {

                    if (groupOrder[j] !== CRITERIA.AGE_CODE) {
                        tableContent += '<td class="text-center">' + sortedAnswers[i].partialResult[partialResultIndex] + '</td>';
                        partialResultIndex++;
                    } else {
                        tableContent += '<td class="text-center">' + sortedAnswers[i].birth + '</td>';
                    }
                }

                tableContent += '<td class="text-center">' + sortedAnswers[i].result + '</td></tr>';
            }

            table += tableContent;
            table += "</tbody></table>";

            resultContainer.html(table);
        };

        createSortList = function () {


            sortGroupContainer.html('');

            var options;

            for (var i = 0; i < groups.length; i++) {
                options += '<option value="' + i + '">' + groups[i].name + '</option>';
            }

            options += '<option value="' + CRITERIA.AGE_CODE + '">' + CRITERIA.AGE_DESC + '</option>';

            var criteria = 0;
            for (; criteria < groups.length; criteria++) {
                sortGroupContainer.append("<div class='col-md-6'><div class='form-group'><label>Critério " + (criteria + 1) + "</label><select id='criteria-group" + i + "' class='form-control'>" + options + "</select></div></div>");
            }

            sortGroupContainer.append("<div class='col-md-6'><div class='form-group'><label>Critério " + (criteria + 1) + "</label><select id='criteria-group" + i + "' class='form-control'>" + options + "</select></div></div>");

        };

        getResult = function () {

            if (sortedAnswers) {
                
                var obj = {
                    recruitment: recruitment.val(),
                    application: currentApplicationId,
                    results: sortedAnswers.slice(saveIndex, saveIndex + 10)
                };
                
                saveIndex += 10;
                
                return obj;
                
            } else {
                return {
                };
            }
        };

        return {
            init: function () {
                initListeners();
            },
            getDataOf: function (element) {
                switch (element) {
                    case 'save-result':
                        return getResult();
                        break;
                    default:
                        return {};
                }
            }
        };

    }());
    return ApplicationResultModule;
});
