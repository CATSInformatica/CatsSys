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

    var loadedExams;
    var applicationId;
    var chosenExam;
    var loadedSubjects;
    var loadedPeople;
    var loadedAnswers = {};
    var loadedCsvData = null;
    var parallels;
    var fileGroups;
    var alreadyMounted = false;
    var mapToFileGroup;
    var peopleTable;
    var status = {
        empty: 'VAZIO',
        loaded: 'CARREGADO',
        saved: 'SALVO'
    };

    var UploadAnswersModule = (function () {

        var people = null;

        initListeners = function () {
            $("#application").change(getExams);
            $("#fetch-data").click(fetchData);

            $("#exam-answers").change(function (e) {

                var files = e.target.files; // FileList object
                var file = files[0];
                var reader = new FileReader();
                reader.readAsText(file);
                reader.onload = function (event) {
                    try {
                        loadedCsvData = $.csv
                                .toArrays(event.target.result, {separator: ";"});
                    } catch (e) {
                        bootbox.alert("Erro: O arquivo deve estar no formato <b>csv</b> e utilizar \"<b>;</b>\" como separador.");
                    }
                };
                reader.onerror = function () {
                    bootbox.alert("Não foi possível abrir o arquivo <b>" +
                            file.name + "<br>");
                };
            });

            $("#import-answers").click(function () {
                if (loadedCsvData === null) {
                    bootbox.alert("Erro: Nenhum arquivo foi adicionado.");
                    return;
                }

                if (!alreadyMounted) {
                    mountGroups();
                    drawGroups();
                    alreadyMounted = true;
                    $("#adjust-groups").prop('disabled', false);
                } else {
                    addDataToTable();
                }
            });

            $("#adjust-groups").click(function () {
                $(this).prop('disabled', true);
                adjustGroups();
                readAnswers();
                addDataToTable();
            });
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        addDataToTable = function () {
            var studentRow, parsDesc;
            Object.keys(loadedAnswers).forEach(function (filename) {
                studentRow = peopleTable.find('tr.student-' + loadedAnswers[filename].registration);

                if (parallels.length) {

                    parsDesc = loadedAnswers[filename].parallels.map(function (par, indx) {
                        return parallels[indx][par].name;
                    }).join(', ');

                    studentRow.find('td.language-option').text(parsDesc);
                }

                studentRow.find('td.filename').text(loadedAnswers[filename].filename);
                studentRow.find('td.op-status').text(status.loaded);
                if (!studentRow.hasClass('cats-selected-row')) {
                    studentRow.trigger('click');
                }

            });
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        readAnswers = function () {

            var objFg;
            var ans;
            var registration;
            var par;
            var enrollment;
            var questionCounter;

            // le as respostas de cada candidato/aluno
            for (var i = 1; i < loadedCsvData.length; i++) {

                // le a matricula/inscriçao
                objFg = fileGroups[mapToFileGroup.registration];

                if ($("#studentClass").length) {
                    enrollment = parseInt(loadedCsvData[i].slice(objFg.startAt, objFg.startAt + objFg.amount).map(function (item) {
                        return (item.toUpperCase().charCodeAt(0) - 'A'.charCodeAt(0));
                    }).join(''));

                    registration = peopleTable.find('tr[data-enrollment="' + enrollment + '"]').attr('data-registration');
                } else {
                    registration = parseInt(loadedCsvData[i].slice(objFg.startAt, objFg.startAt + objFg.amount).map(function (item) {
                        return (item.toUpperCase().charCodeAt(0) - 'A'.charCodeAt(0));
                    }).join(''));
                }

                // indica qual foi(ram) a(s) opçao(oes) escolhida(s) pelo candidato
                par = [];
                for (var j = 0; j < mapToFileGroup.parallel.length; j++) {
                    objFg = fileGroups[mapToFileGroup.parallel[j]];
                    par.push(loadedCsvData[i].slice(objFg.startAt, objFg.startAt + objFg.amount).join("").toUpperCase().charCodeAt() - 'A'.charCodeAt());
                }

                loadedAnswers[loadedCsvData[i][0]] = {
                    filename: loadedCsvData[i][0],
                    registration: registration,
                    answers: {},
                    parallels: par
                };

                ans = loadedAnswers[loadedCsvData[i][0]].answers;
                questionCounter = chosenExam.content.questionsStartAtNumber;

                loadedSubjects.forEach(function (subject) {
                    group = mapToFileGroup[subject.name];
                    objFg = fileGroups[group];

                    var ansArr = loadedCsvData[i].slice(objFg.startAt, objFg.startAt + objFg.amount);

                    for(var q = 0; q < ansArr.length; q++, questionCounter++) {
                        ans[questionCounter] = ansArr[q];
                    }
                });
            }

            console.log('loadedAnswers', loadedAnswers);

        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        adjustGroups = function () {

            mapToFileGroup = {};

            loadedSubjects.forEach(function (key) {
                mapToFileGroup[key.name] = $("#subject-external-group" + key.id).val();
            });

            mapToFileGroup.registration = $("#registration-external-group").val();

            mapToFileGroup.parallel = [];
            for (var i = 0; i < parallels.length; i++) {
                mapToFileGroup.parallel.push($("#parallel-external-group" + i + "").val());
            }
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        drawGroups = function () {

            var groups = $("#groups");
            var letterCode = 'A'.charCodeAt();
            groups.html('');

            //external options
            externalOptions = Object.keys(fileGroups).map(function (item) {
                return '<option value="' + item + '">' + item + '</option>';
            }).join('');

            // subject groups
            var subjectGroups = loadedSubjects.map(function (key) {
                return '<div class="col-md-6 col-xs-12"><div class="form-group"><label>' + key.name + '</label><select id="subject-external-group' + key.id + '" class="form-control">' +
                        externalOptions +
                        '</select></div></div>';
            }).join('');

            groups.append(subjectGroups);

            groups.find("select[id^='subject-external-group']").each(function (i) {
                $(this).find('option').eq(i).prop('selected', true);
            });

            // registration groupletter
            groups.append('<div class="col-md-6 col-xs-12"><div class="form-group"><label>Matricula ou Inscrição (0 = A, 2 = B, ..., 9 = J)</label>' +
                    '<select id="registration-external-group" class="form-control">' +
                    externalOptions +
                    '</select></div></div>'
                    );

            groups.find("#registration-external-group option").eq(loadedSubjects.length).prop('selected', true);

            // parallels group
            for (var i = 0; i < parallels.length; i++) {

                desc = parallels[i].map(function (p, index) {
                    return p.name + ' = ' + String.fromCharCode(letterCode + index);
                }).join(', ');

                groups.append(
                        '<div class="col-md-6 col-xs-12"><div class="form-group"><label>Grupo paralelo ' + (i + 1) + ': (' + desc + ')</label>' +
                        '<select id="parallel-external-group' + i + '" class="form-control">' +
                        externalOptions +
                        '</select></div></div>'
                        );
            }

            groups.find("select[id^='parallel-external-group']").each(function (i) {
                $(this).find('option').eq(loadedSubjects.length + i + 1).prop('selected', true);
            });
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        mountGroups = function () {

            fileGroups = {};
            header = loadedCsvData[0];

            var lastLabel = null;
            header.forEach(function (item, index) {
                if (index > 0) {
                    var label = item.split('.')[0];
                    if (label !== lastLabel) {
                        fileGroups[label] = {startAt: index, amount: 0};
                        lastLabel = label;
                    }
                    fileGroups[label].amount++;
                }
            });
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        getExams = function () {
            applicationId = $("#application").val();
            loadedExams = [];
            $.getJSON('/school-management/school-exam/get-exams/' + applicationId).then(function (exams) {
                loadedExams = exams;
                var options = exams.map(function (exam) {
                    return '<option value="' + exam.examId + '">' + exam.name + '</option>';
                }).join('');

                $("#exam").html(options);

            }, function (jqXHR) {
                $("#exam").html('');
                console.log('error status', jqXHR.status);
            });
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        fetchData = function () {

            loadedPeople = [];
            var groupId = $("#studentClass").val() || $("#studentRecruitment").val();
            applicationId = $("#application").val();
            var examId = parseInt($("#exam").val());

            chosenExam = loadedExams.find(function (element) {
                return element.examId === examId;
            });

            if (examId !== "") {

                if ($("#studentClass").length) {
                    getStudents(groupId).then(function (data) {
                        loadedPeople = data.students;
                        people = data.students;
                        createStudentTable();
                        readSubjects();

                    }, function (err) {
                        console.log(err);
                        people = [];
                    });
                } else {
                    getCandidates(groupId).then(function (data) {
                        loadedPeople = data.candidates;
                        people = data.candidates;
                        createCandidateTable();
                        readSubjects();

                    }, function (err) {
                        console.log(err);
                        people = [];
                    });
                }
            }

            $("#fetch-data").prop('disabled', true);
            $("#import-answers").prop('disabled', false);
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        readSubjects = function () {
            loadedSubjects = [];
            parallels = [];

            var config = chosenExam.content;
            var group, subgroup, parallel;

            for (var i = 0; i < config.groups.length; i++) {
                group = config.groups[i];
                loadedSubjects.push({
                    id: group.id,
                    name: group.groupName
                });
                for (var j = 0; j < group.subgroups.length; j++) {
                    subgroup = group.subgroups[j];
                    if (subgroup instanceof Array) {

                        parallel = [];

                        for (var g = 0; g < subgroup.length; g++) {
                            parallel.push({
                                id: subgroup[g].id,
                                name: subgroup[g].subgroupName
                            });
                        }

                        parallels.push(parallel);
                    }
                }
            }
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        createStudentTable = function () {

            peopleTable = $("#student-answers-table");
            var partialId;
            var tbody = people.map(function (student) {
                partialId = "" + student.enrollmentId;
                return '<tr class="student-' + student.registrationId + ' cats-row" data-enrollment="' +
                        student.enrollmentId + '" data-registration="' + student.registrationId + '"><td class="details-control"></td><td class="text-center">' +
                        ("00000" + partialId).substring(partialId.length) +
                        '</td><td>' + student.personFirstName + ' ' + student.personLastName +
                        '</td><td class="text-center filename"></td><td class="text-center language-option">---</td><td class="text-center op-status"> ' +
                        status.empty + ' </td></tr>';
            }).join('');

            peopleTable.find('tbody').html(tbody);

            peopleTable.on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                toggleDetailsRow(tr);
            });
        };

        /**
         * Done
         *
         * @param {type} $tr
         * @returns {undefined}
         */
        toggleDetailsRow = function ($tr) {

            var filename = $tr.find('td.filename').text();
            var questionFirstNumber = chosenExam.content.questionsStartAtNumber;
            var ans = null, registration = $tr.data('registration');
            var nextRow = $tr.next('tr');
            var len;

            if (filename !== "") {
                ans = loadedAnswers[filename].answers;
            }

            if (!nextRow.length || !nextRow.hasClass('details-row')) {
                // adiciona a linha de descriçao
                getPrevAnswers(registration, chosenExam.examId).then(function (prevAnswers) {

                    var resp = prevAnswers.examAnswers;
                    nextRow = '<tr class="details-row"><td colspan="6"><div class="content"><h4 class="text-center">Respostas</h4><hr>';
                    if (resp) {

                        // prev parallels
                        nextRow += '<h5 class="text-center"><b>Grupos Paralelos</b>: ' + resp.parallels.map(function (i, index) {
                            return parallels[index][i].name;
                        }).join(', ') + '</h5>';

                        nextRow += '<table class="table table-condensed table-bordered table-striped">' +
                                '<thead><tr><th class="text-center">Nº</th><th class="text-center">Resposta</th><th class="text-center">Resposta Anteriores</th></tr></thead><tbody>';

                        if (ans) {
                            len = Object.keys(ans).length;
                        } else if (resp) {
                            len = Object.keys(resp.answers).length;
                        } else {
                            len = 0;
                        }

                        console.log('len', len);

                        for (var i = 0; i < len; i++) {
                            nextRow += '<tr><td class="text-center">' + (questionFirstNumber + i) + '</td><td class="text-center">' +
                                    (ans ? ans[questionFirstNumber + i] : '-') + '</td><td class="text-center">' + (resp ? resp.answers[questionFirstNumber + i] : '-') + '</td></tr>';
                        }

                        nextRow += '</tbody></table>';

                    }

                    nextRow += '</div></td></tr>';
                    $tr.after(nextRow);
                });

            } else {
                nextRow.remove();
            }
        };

        /**
         *
         */
        getPrevAnswers = function (registration, examId) {
            return $.ajax({
                url: '/school-management/school-exam-result/get-answers',
                type: 'POST',
                data: {
                    registration: registration,
                    exam: examId
                }
            });
        };

        /**
         * Done
         *
         * @returns {undefined}
         */
        createCandidateTable = function () {

            peopleTable = $("#student-answers-table");
            var tbody = people.map(function (candidate) {
                return '<tr class="student-' + candidate.registrationId + ' cats-row" data-registration="' + candidate.registrationId + '"><td class="details-control"></td><td class="text-center">' + candidate.registrationNumber +
                        '</td><td>' + candidate.personName +
                        '</td><td class="text-center filename"></td><td class="text-center language-option">---</td><td class="text-center op-status"> ' +
                        status.empty + ' </td></tr>';
            }).join('');

            peopleTable.find('tbody').html(tbody);

            peopleTable.on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                toggleDetailsRow(tr);
            });
        };

        /**
         * Done
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
                }
            });
        };

        /**
         * Done
         *
         * @param {type} recId
         * @returns {jqXHR}
         */
        getCandidates = function (recId) {

            return $.ajax({
                url: '/recruitment/registration/get-confirmed',
                type: 'POST',
                data: {
                    id: recId
                }
            });
        };

        getSelectedIndividuals = function () {

            var data = {
                exam: chosenExam.examId,
                individuals: []
            };

            var filename;
            peopleTable.find('tbody tr.cats-selected-row').each(function () {
                filename = $(this).find('td.filename').text();
                data.individuals.push(loadedAnswers[filename]);
            });

            return data;
        };

        return {
            init: function () {
                initListeners();
                getExams();
            },
            getDataOf: function (element) {
                switch (element) {
                    case 'save-answers':
                        return getSelectedIndividuals();
                        break;
                    default:
                        return {};
                }
            },
            getCallbackOf: function (element) {

                switch (element) {
                    case 'save-answers':
                        return {
                            exec: function (data) {
                                if (data.individuals) {
                                    var row;
                                    data.individuals.forEach(function (ind) {
                                        row = peopleTable.find('tr.student-' + ind.registration);
                                        row.find('td.op-status').text(status.saved);
                                    });
                                }
                            }
                        };
                        break;
                    default:
                        return {
                            exec: function () {

                            }
                        };
                }
            }
        };

    }());

    return UploadAnswersModule;
});
