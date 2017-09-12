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


define(['bootbox', 'datatable', 'jquerycsv'], function (bootbox) {

    var students = [];
    var csvData = {
        answers: null,
        template: null
    };

    /**
     * É construído na forma
     * studentAnswers = {
     *  <enrollmentId with 5 digits>: {
     *     name: <student fullname>,
     *    answers: ["A", "C", ...]
     *   },
     *   ...
     * }
     * Exemplo:
     * studentAnswers = {
     *      00301: {
     *          name: "Matheus Azevedo Martins",
     *          answers: ["A", "B", "E", ..., "", "A|E", "C", ...]
     *      },
     *      0045: {
     *          ...
     *      },
     * ...
     * }
     *
     * @type object
     */
    var studentAnswers = null;

    /**
     * é construído na forma
     * finalTemplate = {
     *      groups: ["Group 1", "Group 2", ...],
     *      answers: [
     *          {group: "Group 1", answer: "A"},
     *          {group: "Group 1", answer: "D"},
     *          ...,
     *          {group: "Group 2", answer: "C"},
     *          ...
     *      ]
     * }
     *
     * Exemplo
     * finalTemplate = {
     *      groups: [
     *          "Linguagens Códigos e Suas Tecnologias",
     *          "Ciências da Natureza e Suas Tecnologias"
     *      ],
     *      answers: [
     *          {group: "Linguagens Códigos e Suas Tecnologias", answer: "A"},
     *          {group: "Linguagens Códigos e Suas Tecnologias", answer: "D"},
     *          ...,
     *          {group: "Ciências da Natureza e Suas Tecnologias", answer: "C"},
     *          ...
     *      ]
     * }
     *
     * @type object
     */
    var finalTemplate = {
        groups: null,
        answers: null
    };
    /**
     * @todo createResultTable()
     * @returns {undefined}
     */
    startListeners = function () {

        bindImportEvent();
        $("#calculate").click(function () {
            getStudents($("#studentClass").val()).then(function () {
                processTemplate();
                processStudents();
                createAnswersTable();
                createTemplateTable();
                createResultTable();
                createHitRatioTable();
            });
        });
    };


    /**
     * Tabela com a taxa de acertos por questão
     * @returns {undefined}
     */
    createHitRatioTable = function () {
        $("#hit-ratio-container").slideUp("fast").html("");
        var table = "<table class='table table-condensed " +
                "table-striped attendanceListTable'>";
        table += "<thead><tr><th class='text-center'>Questão</th>";
        table += "<th class='text-center'>Acertos</th>" +
                "<th class='text-center'>Total</th>" +
                "<th class='text-center'>Percentual (%)</th>" +
                "</tr></thead><tbody>";

        var lastGroup = null;
        var hitCounter;
        var totalCounter;

        // para cada resposta
        for (var i = 0; i < finalTemplate.answers.length; i++) {

            if (lastGroup !== finalTemplate.answers[i].group) {
                lastGroup = finalTemplate.answers[i].group;
                table += "<tr><th colspan='4' class='text-center bg-green'>" +
                        lastGroup + "</th></tr>";
            }

            table += "<tr><td class='text-center'>" + (i + 1) + "</td>";

            // cálculo da quantidade de acertos por questão
            totalCounter = hitCounter = 0;
            $.each(studentAnswers, function (key, value) {
                if (typeof value.answers !== "undefined") {

                    if (value.answers[i] === finalTemplate.answers[i].answer) {
                        hitCounter++;
                    }

                    totalCounter++;
                }
            });

            table += "<td class='text-center'>" + hitCounter + "</td>" +
                    "<td class='text-center'>" + totalCounter + "</td>" +
                    "<td class='text-center'>" +
                    (100 * hitCounter / totalCounter).toFixed(2) + "</td>";
        }

        table += "</tbody></table>";

        $("#hit-ratio-container")
                .append(table)
                .slideDown("fast");
    };

    /**
     * Tabela com o total de acertos de cada aluno por área do gabarito
     * @returns {undefined}
     */
    createResultTable = function () {
        $("#result-container").slideUp("fast").html("");

        var table = "<table class='table table-condensed " +
                "table-striped attendanceListTable'>";
        table += "<thead><tr><th class='text-center'>Matrícula</th>";
        table += "<th class='text-center'>Aluno</th>";

        for (var i = 0; i < finalTemplate.groups.length; i++) {
            table += "<th class='text-center'>" +
                    finalTemplate.groups[i] + "</th>";
        }

        table += "</tr></thead><tbody>";

        var lastGroup;
        var sum;
        var tmpAns = null;

        $.each(studentAnswers, function (key, value) {
            lastGroup = finalTemplate.groups[0];
            sum = 0;
            table += "<tr><td class='text-center'>" + key + "</td>";
            if (typeof value.name !== "undefined") {
                table += "<td style='white-space:nowrap;'>" + value.name +
                        "</td>";
            } else {
                table += "<td style='white-space:nowrap;'>[DESLIGADO / NÃO ENCONTRADO]</td>";
            }

            if (typeof value.answers !== "undefined") {
                for (var j = 0; j < finalTemplate.answers.length; j++) {

                    if (lastGroup !== finalTemplate.answers[j].group) {
                        table += "<td class='text-center'>" + sum + "</td>";
                        sum = 0;
                        lastGroup = finalTemplate.answers[j].group;
                    }

                    tmpAns = finalTemplate.answers[j].answer.toUpperCase();

                    if (tmpAns === "X" || tmpAns === value.answers[j]) {
                        sum++;
                    }
                }
                table += "<td class='text-center'>" + sum + "</td></tr>";
            } else {
                for (var j = 0; j < finalTemplate.groups.length; j++) {
                    table += "<td></td>";
                }
            }

        });

        table += "</tbody></table>";
        $("#result-container")
                .append(table);

        $("#result-container").find("table").DataTable({
            dom: 'lftip',
            paging: false
        });

        $("#result-container")
                .slideDown("fast");
    };

    /**
     * Tabela de respostas dos alunos em cada questão
     * @returns {undefined}
     */
    createAnswersTable = function () {
        $("#answer-container").slideUp("fast").html("");
        var table = "<table class='table table-condensed table-striped attendanceListTable'>";
        table += "<thead><tr><th class='text-center'>Matrícula</th>";
        table += "<th class='text-center'>Aluno</th>";
        for (var i = 0; i < finalTemplate.answers.length; i++) {
            table += "<th class='text-center'>" + (i + 1) + "</th>";
        }

        table += "</tr></thead><tbody>";
        $.each(studentAnswers, function (key, value) {

            table += "<tr><td class='text-center'>" + key + "</td>";
            if (typeof value.name !== "undefined") {
                table += "<td style='white-space:nowrap;'>" + value.name +
                        "</td>";
            } else {
                table += "<td style='white-space:nowrap;'>[DESLIGADO / NÃO ENCONTRADO]</td>";
            }

            if (typeof value.answers !== "undefined") {
                for (var j = 0; j < finalTemplate.answers.length; j++) {
                    table += "<td class='text-center'>" + value.answers[j] +
                            "</td>";
                }
            }

            table += "</tr>";
        });
        table += "</tbody></table>";
        $("#answer-container")
                .append(table)
                .slideDown("fast");
    };

    /**
     * Gabarito
     *
     * @returns {undefined}
     */
    createTemplateTable = function () {
        $("#template-container").slideUp("fast").html("");
        var table = "<table class='table table-condensed table-striped attendanceListTable'>";
        table += "<thead><tr><th class='text-center'>Questão</th>";
        table += "<th class='text-center'>Resposta</th></tr></thead><tbody>";

        var lastGroup = null;

        for (var i = 0; i < finalTemplate.answers.length; i++) {

            if (lastGroup !== finalTemplate.answers[i].group) {
                lastGroup = finalTemplate.answers[i].group;
                table += "<tr><th colspan='2' class='text-center bg-green'>" +
                        lastGroup + "</th></tr>";
            }

            table += "<tr><td class='text-center'>" + (i + 1) +
                    "</td><td class='text-center'>" +
                    finalTemplate.answers[i].answer +
                    "</td></tr>";
        }

        table += "</tbody></table>";
        $("#template-container")
                .append(table)
                .slideDown("fast");
    };
    /**
     * Cria o vetor de respostas combinando com os nomes dos alunos
     *
     * @returns {undefined}
     */
    processStudents = function () {
        var ans = null;
        var enrollmentId;
        var partialId;

        studentAnswers = {};

        for (var i = 1; i < csvData.answers.length; i++) {
            ans = csvData.answers[i];
            enrollmentId = ans.slice(1, 6).join('');
            studentAnswers[enrollmentId] = {};
            studentAnswers[enrollmentId].answers = ans.slice(6);
        }

        for (var i = 0; i < students.length; i++) {
            partialId = "" + students[i].enrollmentId;
            enrollmentId = ("00000" + partialId).substring(partialId.length);
            if (typeof studentAnswers[enrollmentId] === "undefined") {
                studentAnswers[enrollmentId] = {};
            }
            studentAnswers[enrollmentId].name = students[i].personFirstName +
                    " " + students[i].personLastName;
        }
    };
    /**
     * Organiza o gabarito.
     *
     * @returns {undefined}
     */
    processTemplate = function () {

        var lastGroup = null;
        finalTemplate.groups = [];
        finalTemplate.answers = [];
        var group = null;
        for (var i = 1; i < csvData.template[1].length; i++) {
            group = csvData.template[0][i].split(".")[0];
            if (lastGroup !== group) {
                lastGroup = group;
                finalTemplate.groups.push(group);
            }

            finalTemplate.answers.push({
                group: group,
                answer: csvData.template[1][i]
            });

        }
    };
    /**
     * Lê os arquivos csv
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
