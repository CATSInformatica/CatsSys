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

define(["moment"], function (moment) {
    var ApplicationResultModule = (function () {
        var currentApplicationId = null
        var exams
        var ApplicationElel = $("#application")
        var resultContainer = $("#result-container")
        var sortGroupContainer = $("#sort-group-container")
        var recruitment = $("#recruitment")
        var studentClass = $("#examClass")
        var groups
        var groupOrder
        var answers
        var sortedAnswers
        var isStudent = null
        var saveIndex
        var APPLICATION_TYPES = {
            PSA: "psa",
            SIMULADO: "simulado"
        }

        var CRITERIA = {
            AGE_CODE: -1,
            AGE_DESC: "NASCIMENTO"
        }

        var NULLIFIED = "ANULADA"

        var comments

        initListeners = function () {
            comments = []
            currentApplicationId = ApplicationElel.val()
            ApplicationElel.change(function () {
                currentApplicationId = ApplicationElel.val()
                sortedAnswers = null
            })
            $("#fetch-new-data").click(function () {
                getExams().then(function (e) {
                    if (e.length) {
                        exams = {}
                        groups = []
                        resultContainer.html("")
                        sortGroupContainer.html("")

                        var defs = []
                        var examId

                        for (var i = 0; i < e.length; i++) {
                            examId = e[i].examId
                            exams[examId] = e[i]
                            addGroupsOf(examId)
                            defs.push(loadAnswers(examId))

                            // comentários
                            if (!e[i].answers) {
                                comments.push({
                                    text:
                                        "É necessário salvar o gabarito da prova <b>" +
                                        e[i].name +
                                        "</b> antes de calcular o resultado. Item do menu <b>Exam Result > Answers template</b>",
                                    type: "danger"
                                })
                            }
                        }

                        $.when(defs).then(function () {
                            $("#set-criteria").prop("disabled", false)
                            createSortList()
                        })
                    } else {
                        comments.push({
                            text:
                                "A aplicação de prova indicada não possui provas associadas",
                            type: "danger"
                        })
                    }
                })
            })

            $("#fetch-current-data").click(function () {
                sortedAnswers = null
                exams = {}
                groups = []
                groupOrder = []
                answers = {}
                resultContainer.html("")
                sortGroupContainer.html("")
                $("#set-criteria").prop("disabled", true)

                getApplicationResult().then(function (results) {
                    if (results.length) {
                        // ordena o resultado antes de adicionar na tabela (1º, 2º, 3º, ...)
                        sortedAnswers = results.sort(function (prev, next) {
                            intPrev = parseInt(prev.position)
                            intNext = parseInt(next.position)
                            if (intPrev < intNext) {
                                return -1
                            } else if (intNext < intPrev) {
                                return 1
                            }
                            return 0
                        })
                        createResultTable(
                            sortedAnswers,
                            sortedAnswers[0].groups
                        )
                    }
                })
            })

            $("#set-criteria").on("click", function () {
                groupOrder = []
                $("select[id^='criteria-group']").each(function () {
                    groupOrder.push(parseInt($(this).val()))
                })

                adjustAnswers()

                console.log("exams", exams)
                console.log("answers", answers)
                console.log("groups", groups)
                console.log("order", groupOrder)

                addComments()
                calcResult()
                console.log("sortedAnswers", sortedAnswers)
                createResultTable(sortedAnswers, sortedAnswers[0].groups)
                releaseSaveResult()
                $("#resultProgress").text('0/' + sortedAnswers.length);
            })

            $("#classExamOrRecruitmentExam").change(function () {
                switch ($(this).val()) {
                    case APPLICATION_TYPES.PSA:
                        isStudent = 0
                        $("#fetch-new-data").prop("disabled", false)
                        $("#fetch-current-data").prop("disabled", false)
                        $("#simuladoSelect").hide()
                        $("#psaSelect").show()
                        break
                    case APPLICATION_TYPES.SIMULADO:
                        isStudent = 1
                        $("#fetch-new-data").prop("disabled", false)
                        $("#fetch-current-data").prop("disabled", false)
                        $("#psaSelect").hide()
                        $("#simuladoSelect").show()
                        break
                    default:
                        isStudent = null
                        $("#fetch-new-data").prop("disabled", true)
                        $("#fetch-current-data").prop("disabled", true)
                }
            })

            $("#save-result").click(function () {
                console.log('sortedAnswers', sortedAnswers);
                saveResults(0);
            })
        }

        addComments = function () {
            var formattedComments = comments.map(function (comment) {
                return (
                    "<li><span class='label label-" +
                    comment.type +
                    "'>Aviso</span> " +
                    comment.text +
                    ".</li>"
                )
            })

            $("#import-comments").html(formattedComments.join(""))
        }

        getApplicationResult = function () {
            return $.ajax(
                "/school-management/school-exam-result/get-result/" +
                currentApplicationId,
                {
                    type: "GET",
                    dataType: "json",
                    data: {
                        isStudent: isStudent
                    }
                }
            )
        }

        getExams = function () {
            return $.ajax(
                "/school-management/school-exam/get-exams/" +
                currentApplicationId,
                {
                    type: "GET",
                    dataType: "json"
                }
            )
        }

        loadAnswers = function (examId) {
            var def = $.Deferred()

            return $.ajax(
                "/school-management/school-exam-result/getAllAnswers/" + examId,
                {
                    type: "GET",
                    data: {
                        isStudent: isStudent
                    },
                    datatype: "json"
                }
            ).then(
                function (ans) {
                    if (!ans.answers.length) {
                        comments.push({
                            text:
                                "É necessário salvar as respostas dos alunos/candidatos da prova <b>" +
                                exams[ans.examId].name +
                                "</b> antes de calcular o resultado. Item do menu <b>Exam Result > Upload answers (class) ou Upload answers (rec)</b>",
                            type: "danger"
                        })
                    }

                    exams[ans.examId].peopleAnswers = ans.answers
                    def.resolve()
                },
                function () {
                    def.reject()
                }
            )

            return def
        }

        /**
         * Organiza as respostas para cada prova da aplicação
         */
        adjustAnswers = function () {
            answers = {}

            Object.keys(exams).forEach(function (examId) {
                ans = exams[examId].peopleAnswers
                for (var i = 0; i < ans.length; i++) {
                    if (!answers[ans[i].registrationOrEnrollment]) {
                        answers[ans[i].registrationOrEnrollment] = createAnswer(
                            ans[i].registrationOrEnrollment,
                            ans[i].fullname,
                            ans[i].registrationNumber,
                            ans[i].birth,
                            ans[i].currentStatus
                        )
                    }

                    answers[ans[i].registrationOrEnrollment][examId] =
                        ans[i].answers
                }
            })
        }

        createAnswer = function (
            registrationOrEnrollment,
            fullname,
            registrationNumber,
            birth,
            currentStatus
        ) {
            return {
                registrationOrEnrollment: registrationOrEnrollment,
                fullname: fullname,
                registrationNumber: registrationNumber || "",
                birth: birth,
                currentStatus: currentStatus || "",
                partialResult: [],
                result: 0,
                groups: []
            }
        }

        /**
         * Calcula a pontuação do aluno em um grupo de questões. Ex: Matemática e suas Tecnologias.
         *
         *
         * Para grupos comuns (Ex: Matemática)
         *
         * Soma ponto se:
         *  1. questão anulada
         *  2. o aluno acertou a resposta
         *
         * Para grupos que possuem opções paralelas. Ex: Linguagens (Inglês e Espanhol)
         *
         * Soma ponto se:
         *  1. questão anulada
         *  2. o aluno, no grupo paralelo escolhido, (por exemplo, inglês) acertou a resposta
         *
         * Não soma ponto se a questão não está anulada e:
         *  1. o aluno preencheu o grupo paralelo corretamente, mas errou a resposta
         *  2. o aluno não preencheu o grupo corretamente (não pintou se queria inglês ou espanhol ou
         *  pintou multiplas vezes, ex: pintou inglês e espanhol)
         */
        calcScore = function (person, currentGroup, correctAnswers) {
            var parallelIndx, parallel
            var endAt
            var score = 0

            endAt = currentGroup.quantity + currentGroup.startAt
            // para cada questão do grupo
            for (var q = currentGroup.startAt; q < endAt; q++) {
                // se é um grupo parelelo (ex: inglês e espanhol)
                if (correctAnswers[q] instanceof Object) {
                    parallelIndx = correctAnswers[q].parallel
                    parallel = person.parallels[parallelIndx]
                    console.log(
                        "parallel",
                        parallel,
                        person.answers[q],
                        correctAnswers[q].answers[parallel]
                    )

                    if (
                        correctAnswers[q].answers[parallel] === NULLIFIED ||
                        (correctAnswers[q].answers[parallel] &&
                            person.answers[q] ===
                            correctAnswers[q].answers[parallel])
                    ) {
                        score++
                    }
                    // se é um grupo normal
                } else {
                    if (
                        correctAnswers[q] === NULLIFIED ||
                        person.answers[q] === correctAnswers[q]
                    ) {
                        score++
                    }
                }
            }

            return score
        }

        calcResult = function () {
            var currentGroup
            var score
            var correctAnswers
            var person

            // para cada grupo, na ordem definida
            for (var i = 0; i < groupOrder.length; i++) {
                if (groupOrder[i] !== CRITERIA.AGE_CODE) {
                    currentGroup = groups[groupOrder[i]]
                    correctAnswers = exams[currentGroup.examId].answers

                    // para cada aluno
                    Object.keys(answers).forEach(function (
                        registrationOrEnrollment
                    ) {
                        person =
                            answers[registrationOrEnrollment][
                            currentGroup.examId
                            ]
                        score = 0

                        if (person) {
                            score = calcScore(
                                person,
                                currentGroup,
                                correctAnswers
                            )
                        } else {
                            answers[registrationOrEnrollment][
                                currentGroup.examId
                            ] = null
                        }

                        // define a pontuação do aluno registrationOrEnrollment como sendo score no grupo currentGroup
                        answers[registrationOrEnrollment].partialResult.push(
                            score
                        )
                        // acrecenta na pontuação geral do aluno
                        answers[registrationOrEnrollment].result += score
                        // salva o nome do grupo no objeto de respostas do aluno
                        answers[registrationOrEnrollment].groups.push(
                            currentGroup.name
                        )
                    })
                } else {
                    // salva a data de nascimento do aluno para casos de desempate
                    Object.keys(answers).forEach(function (
                        registrationOrEnrollment
                    ) {
                        answers[registrationOrEnrollment].partialResult.push(
                            -moment(
                                answers[registrationOrEnrollment].birth,
                                "DD/MM/YYYY"
                            ).unix()
                        )

                        // salva o nome do grupo no objeto de respostas do aluno
                        answers[registrationOrEnrollment].groups.push(
                            CRITERIA.AGE_DESC
                        )
                    })
                }
            }

            sortedAnswers = []

            Object.keys(answers).forEach(function (registrationOrEnrollment) {
                sortedAnswers.push({
                    registrationOrEnrollment: registrationOrEnrollment,
                    fullname: answers[registrationOrEnrollment].fullname,
                    currentStatus:
                        answers[registrationOrEnrollment].currentStatus,
                    registrationNumber:
                        answers[registrationOrEnrollment].registrationNumber,
                    birth: answers[registrationOrEnrollment].birth,
                    partialResult:
                        answers[registrationOrEnrollment].partialResult,
                    result: answers[registrationOrEnrollment].result,
                    groups: answers[registrationOrEnrollment].groups
                })
            })

            // ordenação que considera ordem e zeros em grupos
            sortedAnswers.sort(function (answerA, answerB) {
                var ret

                // Aluno A tem resultado final maior que Aluno B
                if (answerA.result > answerB.result) {
                    // Procura por zeros no aluno A
                    for (var i = 0; i < answerA.partialResult.length; i++) {
                        // se o aluno A zerou e o aluno B não, o aluno A perde posição
                        if (
                            !answerA.partialResult[i] &&
                            answerB.partialResult[i]
                        ) {
                            return 1
                        }
                    }
                    // caso contrário o aluno A está na frente
                    return -1
                    // se o aluno B está na frente do aluno A
                } else if (answerB.result > answerA.result) {
                    // Procura por zeros no aluno B
                    for (var i = 0; i < answerB.partialResult.length; i++) {
                        // se o aluno B zerou e o aluno A não, o aluno B perde posição
                        if (
                            !answerB.partialResult[i] &&
                            answerA.partialResult[i]
                        ) {
                            return -1
                        }
                    }

                    // caso contrário o aluno B está na frente
                    return 1
                    // se o aluno A está empatado com o aluno B
                } else {
                    // considera inicialmente empate completo
                    ret = 0
                    // verifica quem tem maior pontuação em cada grupo
                    for (var i = 0; i < answerA.partialResult.length; i++) {
                        // estão empatados
                        if (!ret) {
                            // o aluno A tem maior pontuação, A fica na frente de B
                            if (
                                answerA.partialResult[i] >
                                answerB.partialResult[i]
                            ) {
                                ret = -1
                                // o aluno B tem maior pontuação, B fica na frente de A
                            } else if (
                                answerB.partialResult[i] >
                                answerA.partialResult[i]
                            ) {
                                ret = 1
                            }
                        }

                        // B está na frente, mas zerou em um grupo, A passa na frente
                        if (
                            ret > 0 &&
                            !answerB.partialResult[i] &&
                            answerA.partialResult[i]
                        ) {
                            return -1
                            // A está na frente, mas zerou em um grupo, B passa na frente
                        } else if (
                            ret < 0 &&
                            !answerA.partialResult[i] &&
                            answerB.partialResult[i]
                        ) {
                            return 1
                        }
                    }

                    return ret
                }
            })

            formatEmptyScores()
        }

        addGroupsOf = function (examId) {
            var groupContent = exams[examId].content.groups
            var subgroups
            var quantity
            var firstNum = exams[examId].content.questionsStartAtNumber
            var startAt = firstNum

            for (var i = 0; i < groupContent.length; i++) {
                quantity = 0

                subgroups = groupContent[i].subgroups
                for (var j = 0; j < subgroups.length; j++) {
                    if (subgroups[j] instanceof Array) {
                        quantity += subgroups[j][0].questions.length
                    } else {
                        quantity += subgroups[j].questions.length
                    }
                }

                groups.push({
                    examId: examId,
                    name: groupContent[i].groupName,
                    startAt: startAt,
                    quantity: quantity
                })

                startAt += quantity
            }
        }

        createResultTable = function (results, orderedGroups) {
            var table =
                "<table class='table table-condensed table-striped table-bordered'>"

            table += "<thead><tr><th class='text-center'>POSIÇÃO</th>"

            if (isStudent) {
                table +=
                    '<th class="text-center">Matrícula</th><th class="text-center">Nome</th>'
            } else {
                table +=
                    '<th class="text-center">SITUAÇÃO</th><th class="text-center">INSCRIÇÃO</th>'
            }

            table += orderedGroups
                .map(function (g) {
                    return "<th class='text-center'>" + g + "</th>"
                })
                .join("")

            table += "<th class='text-center'>TOTAL</th></tr><thead><tbody>"

            var tableContent = ""

            // para cada resultado
            for (var i = 0; i < results.length; i++) {
                results[i].position = i + 1
                tableContent +=
                    '<tr><td class="text-center">' +
                    results[i].position +
                    "º</td>"

                if (isStudent) {
                    tableContent +=
                        '<td class="text-left">' +
                        results[i].registrationOrEnrollment +
                        '</td><td class="text-center">' +
                        results[i].fullname +
                        "</td>"
                } else {
                    tableContent +=
                        '<td class="text-center">' +
                        results[i].currentStatus +
                        '</td><td class="text-center">' +
                        results[i].registrationNumber +
                        "</td>"
                }

                // para cada grupo, na ordem definida
                for (var j = 0; j < orderedGroups.length; j++) {
                    if (orderedGroups[j] !== CRITERIA.AGE_DESC) {
                        tableContent +=
                            '<td class="text-center">' +
                            results[i].partialResult[j] +
                            "</td>"
                    } else {
                        tableContent +=
                            '<td class="text-center">' +
                            moment
                                .unix(-results[i].partialResult[j])
                                .format("DD/MM/YYYY") +
                            "</td>"
                    }
                }

                tableContent +=
                    '<td class="text-center">' +
                    results[i].result +
                    "</td></tr>"
            }

            table += tableContent
            table += "</tbody></table>"

            resultContainer.html(table)
        }

        /**
         * Caso o aluno não tenha feito uma das provas transforma o score 0 dos grupos em string vazia ''
         */
        formatEmptyScores = function () {
            var registrationOrEnrollment, group, person, groupIndex

            for (var i = 0; i < sortedAnswers.length; i++) {
                registrationOrEnrollment =
                    sortedAnswers[i].registrationOrEnrollment

                for (var j = 0; j < groupOrder.length; j++) {
                    groupIndex = groupOrder[j]
                    if (groupIndex !== CRITERIA.AGE_CODE) {
                        group = groups[groupIndex]
                        person = answers[registrationOrEnrollment][group.examId]
                        if (!person) {
                            sortedAnswers[i].partialResult[j] = ""
                        }
                    }
                }
            }
        }

        createSortList = function () {
            sortGroupContainer.html("")
            var options
            for (var i = 0; i < groups.length; i++) {
                options +=
                    '<option value="' + i + '">' + groups[i].name + "</option>"
            }

            options +=
                '<option value="' +
                CRITERIA.AGE_CODE +
                '">' +
                CRITERIA.AGE_DESC +
                "</option>"
            var criteria = 0
            for (; criteria < groups.length; criteria++) {
                sortGroupContainer.append(
                    "<div class='col-md-6'><div class='form-group'><label>Critério " +
                    (criteria + 1) +
                    "</label><select id='criteria-group" +
                    criteria +
                    "' class='form-control'>" +
                    options +
                    "</select></div></div>"
                )
            }

            sortGroupContainer.append(
                "<div class='col-md-6'><div class='form-group'><label>Critério " +
                (criteria + 1) +
                "</label><select id='criteria-group" +
                (criteria + 1) +
                "' class='form-control'>" +
                options +
                "</select></div></div>"
            )
            // seleciona critérios padrões
            for (criteria = 0; criteria < groups.length; criteria++) {
                sortGroupContainer
                    .find("#criteria-group" + criteria)
                    .val(criteria)
            }
            sortGroupContainer
                .find("#criteria-group" + (criteria + 1))
                .val(CRITERIA.AGE_CODE)
        }

        saveResults = function (currentIndex) {
            var results = sortedAnswers.slice(currentIndex, currentIndex + 4)
            setSaveProgress(currentIndex);

            if (!results.length) {
                releaseSaveResult()
                return
            }

            sendSave(results, currentIndex);
        }

        sendSave = function (results, currentIndex) {
            $.ajax({
                url: "/school-management/school-exam-result/save-result",
                type: "POST",
                data: {
                    index: currentIndex,
                    recruitmentOrClass: isStudent ? studentClass.val() : recruitment.val(),
                    isStudent: isStudent,
                    application: currentApplicationId,
                    results: results
                }
            }).then(
                function (resp) {
                    saveResults(currentIndex + 4)
                },
                function (err) {
                    console.log("error", err)
                    releaseSaveResult()
                }
            )
        }

        setSaveProgress = function (currentIndex) {
            $("#resultProgress").text(Math.min(currentIndex, sortedAnswers.length) + "/" + sortedAnswers.length)
        }

        releaseSaveResult = function () {
            $("#save-result").prop("disabled", false)
        }

        return {
            init: function () {
                initListeners()
            },
            getDataOf: function (element) {
                switch (element) {
                    default:
                        return {}
                }
            }
        }
    })()
    return ApplicationResultModule
})
