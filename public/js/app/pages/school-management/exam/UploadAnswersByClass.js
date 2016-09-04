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
    var chosenExam;
    var loadedSubjects;
    var questionSubjects;
    var loadedStudents;
    var loadedAnswers = {};
    var loadedCsvData = null;
    var hasLanguage;
    var fileGroups;
    var alreadyMounted = false;
    var mapToFileGroup;
    var studentsTable;
    var ENGLISH = {text: 'INGLÊS', id: null};
    var SPANISH = {text: 'ESPANHOL', id: null};
    var status = {
        empty: 'VAZIO',
        loaded: 'CARREGADO',
        saved: 'SALVO'
    };

    var UploadAnswersModule = (function () {

        var students = null;

        initListeners = function () {
            $("#application").change(getExams);
            $("#fetch-data").click(fetchApplicationAndClassInfo);

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
                    hasLanguage = $("#has-language:checked").length;
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

        addDataToTable = function () {
            var studentRow;
            Object.keys(loadedAnswers).forEach(function (filename) {
                studentRow = studentsTable.find('tr.student-' + loadedAnswers[filename].enrollment);

                if (hasLanguage) {
                    if (loadedAnswers[filename].languageOption === ENGLISH.id) {
                        studentRow.find('td.language-option').text(ENGLISH.text);
                    } else if (loadedAnswers[filename].languageOption === SPANISH.id) {
                        studentRow.find('td.language-option').text(SPANISH.text);
                    }
                }

                studentRow.find('td.filename').text(loadedAnswers[filename].filename);
                studentRow.find('td.op-status').text(status.loaded);
            });
        };

        readAnswers = function () {

            var objFg;
            var ans;
            var enrollment;
            var languageOption = null;
            for (var i = 1; i < loadedCsvData.length; i++) {

                objFg = fileGroups[mapToFileGroup.enrollment];
                enrollment = parseInt(loadedCsvData[i].slice(objFg.startAt, objFg.startAt + objFg.amount).map(function (item) {
                    return (item.toUpperCase().charCodeAt(0) - 'A'.charCodeAt(0));
                }).join(''));

                if (hasLanguage) {
                    objFg = fileGroups[mapToFileGroup.languageOption];
                    languageOption = loadedCsvData[i].slice(objFg.startAt, objFg.startAt + objFg.amount).join("").toUpperCase() === 'A' ? ENGLISH.id : SPANISH.id;
                }

                loadedAnswers[loadedCsvData[i][0]] = {
                    filename: loadedCsvData[i][0],
                    enrollment: enrollment,
                    answers: [],
                    languageOption: languageOption
                };

                console.log('file groups', fileGroups);

                ans = loadedAnswers[loadedCsvData[i][0]].answers;
                // disciplinas

                console.log('answers', loadedCsvData[i].slice(1));

                loadedSubjects.forEach(function (subject) {
                    group = mapToFileGroup[subject.subjectName];
                    objFg = fileGroups[group];
                    $.merge(ans, loadedCsvData[i].slice(objFg.startAt, objFg.startAt + objFg.amount));
                });
            }

            console.log('student answers', loadedAnswers);
        };

        adjustGroups = function () {

            mapToFileGroup = {};

            loadedSubjects.forEach(function (key) {
                mapToFileGroup[key.subjectName] = $("#subject-external-group" + key.subjectId).val();
            });


            mapToFileGroup.enrollment = $("#enrollment-external-group").val();

            if (hasLanguage) {
                mapToFileGroup.languageOption = $("#language-external-group").val();
            }

            console.log('toFileGroup', mapToFileGroup);
        };

        drawGroups = function () {

            $("#groups").html('');

            //external options
            externalOptions = Object.keys(fileGroups).map(function (item) {
                return '<option value="' + item + '">' + item + '</option>';
            });

            // language group
            if (hasLanguage) {
                $("#groups").append(
                        '<div class="col-md-6 col-xs-12"><div class="form-group"><label>IDIOMA (INGLÊS = A, ESPANHOL = B)</label>' +
                        '<select id="language-external-group" class="form-control">' +
                        externalOptions +
                        '</select></div></div>'
                        );
            }

            // subject groups
            var subjectGroups = loadedSubjects.map(function (key) {
                return '<div class="col-md-6 col-xs-12"><div class="form-group"><label>' + key.subjectName + '</label><select id="subject-external-group' + key.subjectId + '" class="form-control">' +
                        externalOptions +
                        '</select></div></div>';
            });
            $("#groups").append(subjectGroups);

            // enrollment group
            $("#groups").append('<div class="col-md-6 col-xs-12"><div class="form-group"><label>MATRÍCULA (0 = A, 2 = B, ..., 9 = J)</label>' +
                    '<select id="enrollment-external-group" class="form-control">' +
                    externalOptions +
                    '</select></div></div>'
                    );
        };

        mountGroups = function () {

            fileGroups = {};
            header = loadedCsvData[0];

            var lastLabel = null;
            header.forEach(function (item, index) {
                if (index > 0) {
                    var label = item.split(']', 1)[0].substr(1);
                    if (label !== lastLabel) {
                        fileGroups[label] = {startAt: index, amount: 0};
                        lastLabel = label;
                    }
                    fileGroups[label].amount++;
                }
            });

            console.log('csv', loadedCsvData);
            console.log('grupos', fileGroups);
            console.log('loadedSubjects', loadedSubjects);

        };

        getExams = function () {
            var applicationId = $("#application").val();
            loadedExams = [];
            $.getJSON('/school-management/school-exam/get-exams/' + applicationId).then(function (exams) {
                loadedExams = exams;
                var options = exams.map(function (exam) {
                    return '<option value="' + exam.examId + '">' + exam.name + '</option>';
                });

                $("#exam").html(options);

            }, function (jqXHR) {
                $("#exam").html('');
                console.log('error status', jqXHR.status);
            });
        };

        fetchApplicationAndClassInfo = function () {

            loadedStudents = [];
            var classId = $("#studentClass").val();
            var applicationId = $("#application").val();
            var examId = parseInt($("#exam").val());

            chosenExam = loadedExams.find(function (element) {
                return element.examId === examId;
            });

            console.log('chosen exam', chosenExam);

            if (examId !== "") {

                // getStudents
                getStudents(classId).then(function (data) {

                    loadedStudents = data.students;
                    students = data.students;
                    createStudentsTable();
                    getQuestionSubjects();

                }, function (err) {
                    console.log(err);
                    students = [];
                });
            }

            $("#fetch-data").prop('disabled', true);
            $("#import-answers").prop('disabled', false);
        };

        getQuestionSubjects = function () {

            var questions = chosenExam.content.questions.map(function (q) {
                return q.questionId;
            });

            questionSubjects = null;
            loadedSubjects = [];
            var eachAux;
            var subjectExists;

            $.post('/school-management/school-exam/get-question-subjects', {
                questions: questions
            }).then(function (response) {
                questionSubjects = response;
                console.log('qs', questionSubjects);
                questions.forEach(function (questionId) {
                    eachAux = questionSubjects[questionId][0];

                    subjectExists = loadedSubjects.find(function (item) {
                        return item.subjectId === eachAux.subjectId;
                    });

                    if (typeof subjectExists === "undefined") {
                        loadedSubjects.push({
                            subjectId: eachAux.subjectId,
                            subjectName: eachAux.subjectName
                        });
                    }

                    if (questionSubjects[questionId][1].subjectName === SPANISH.text) {
                        SPANISH.id = questionSubjects[questionId][1].subjectId;
                    } else if (questionSubjects[questionId][1].subjectName === ENGLISH.text) {
                        ENGLISH.id = questionSubjects[questionId][1].subjectId;
                    }
                });

                console.log('loaded subjects', loadedSubjects);

                console.log('question subjects', response);
            }, function (jqXHR) {
                console.log('question subjects', jqXHR.status);
            });
        };

        createStudentsTable = function () {

            studentsTable = $("#student-answers-table");
            var partialId;
            var tbody = students.map(function (student) {
                partialId = "" + student.enrollmentId;
                return '<tr class="student-' + student.enrollmentId + '"><td class="text-center">' + ("00000" + partialId).substring(partialId.length) +
                        '</td><td>' + student.personFirstName + ' ' + student.personLastName +
                        '</td><td class="text-center filename"></td><td class="text-center language-option">---</td><td class="text-center op-status"> ' +
                        status.empty + ' </td></tr>';
            });

            studentsTable.find('tbody').html(tbody);
        };

        mountAdjustArea = function () {
            console.log('chosenExam: ', chosenExam);
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
                }
            });
        };


        return {
            init: function () {
                initListeners();
                getExams();
            }
        };

    }());

    return UploadAnswersModule;
});
