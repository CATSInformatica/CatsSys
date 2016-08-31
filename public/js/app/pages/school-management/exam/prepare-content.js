/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
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

define(['jquery', 'datatable', 'jquerycolumnizer', 
        'jqueryprint', 'datetimepicker'], function () {
    var prepare = (function () {

        //  Array de objetos com as questões carregadas por ajax
        //  índice = id da questão
        var examQuestions = [];
        //  Array de objetos com formatação para inserir no DataTable as questões de uma disciplina 
        //  índice = id da disciplina
        var sQuestionsDatatable = [];
        //  Array de bool com as questões selecionadas (adicionadas ao conteúdo)
        //  índice = id da questão
        var selectedQuestions = [];
        //  id do DataTable
        var questionTable = $('#question-table');
        //  Número de questões adicionadas
        var questionCount = 0;

        setListeners = function () {            
            /*
             * Carrega as questões da disciplina selecionada no DataTable
             * 
             */
            $('select[name=subject]').change(function () {
                var optionSelected = $("option:selected", this);
                $('#last-selected').prop('selected', false);
                $('#last-selected').removeAttr('id');
                optionSelected.attr('id', 'last-selected');
                if (typeof sQuestionsDatatable[+$("#last-selected").val()] !== 'undefined') {
                    setDatatable(sQuestionsDatatable[+$("#last-selected").val()]);
                } else {
                    questionTable.DataTable().ajax.reload();
                }
            });

            /*
             * Recarrega as questões da disciplina selecionada no DataTable
             * 
             */
            $('#refresh-button').click(function () {
                questionTable.DataTable().ajax.reload();
            });

            /*
             * Adiciona todas as questões selecionadas
             * 
             */
            $('.add-question').click(function () {
                $('#question-table > tbody > .cats-selected-row').each(function () {
                    $(this).removeClass('cats-selected-row');
                    $(this).removeClass('cats-row');
                    $(this).css('opacity', 0.4);
                    var qId = $(this).data('id')
                    addQuestion({
                        id: qId,
                        subjectId: examQuestions[qId]['subject'],
                        baseSubjectId: $('#last-selected').parents('.base-subject-info').data('id'),
                        enunciation: examQuestions[qId]['enunciation'],
                        alternatives: examQuestions[qId]['alternatives'],
                        answer: examQuestions[qId]['correctAlternative']
                    });
                });
                
                if ($('#autosaving').is(':checked')) {
                    saveContent();
                }
            });

            /*
             * Salva o conteúdo.
             * 
             */
            $('#save-content').click(function () {
                saveContent();
            });

            /*
             * Remove a questão a qual está associado o ícone
             * 
             */
            $('.content-questions').on('click', '.rm-question', function () {
                var question = $('#question-' + $(this).parents('.question-block').data('id'));
                question.find('td').removeClass('cats-selected-bg');
                question.addClass('cats-row');
                question.css('opacity', 1);
                removeQuestion(+$(this).parents('.question-block').data('id'));
                
                if ($('#autosaving').is(':checked')) {
                    saveContent();
                }
            });

            /*
             * Troca de posição com a questão de cima, se existir e for da mesma disciplina.
             * 
             */
            $('.content-questions').on('click', '.move-up', function () {
                var qBlock = $(this).parents('.question-block');
                var previous = qBlock.prev('.question-block');
                if (previous.length !== 0) {
                    var qNumber = qBlock.find('.q-number').html();
                    qBlock.find('.q-number').html(previous.find('.q-number').html());
                    previous.find('.q-number').html(qNumber);
                    qBlock.detach().insertBefore(previous);
                }
                
                if ($('#autosaving').is(':checked')) {
                    saveContent();
                }
            });

            /*
             * Troca de posição com a questão de baixo, se existir e for da mesma disciplina.
             * 
             */
            $('.content-questions').on('click', '.move-down', function () {
                var qBlock = $(this).parents('.question-block');
                var next = qBlock.next('.question-block');
                if (next.length !== 0) {
                    var qNumber = qBlock.find('.q-number').html();
                    qBlock.find('.q-number').html(next.find('.q-number').html());
                    next.find('.q-number').html(qNumber);
                    qBlock.detach().insertAfter(next);
                }
                
                if ($('#autosaving').is(':checked')) {
                    saveContent();
                }
            });
        };

        updateRemainingQuestions = function () {
            $('#added-questions').html(questionCount);
            $('#remaining-questions')
                    .html(+$('#question-count').html() - questionCount);
        };

        /*
         * Remove os dados do DataTable e insere os contidos em 'data'
         * 
         * @param {array} data - Array de objetos contendo as informações a serem carregadas na tabela
         * 
         * data contém:
         *      DT_RowAttr: (Objeto que determina os atributos de cada linha da tabela)
         *      0: (Primeira coluna - checkbox com o id da questão)
         *      1: (Segunda coluna - Descrição da questão)
         * }
         */
        setDatatable = function (data) {
            questionTable.DataTable().clear();
            questionTable.DataTable().rows.add(data).draw();
        };

        /*
         * Inicializa a tabela e define a forma de obtenção de dados por ajax em 'dataSrc'
         * 
         */
        initDataTable = function () {
            questionTable.DataTable({
                dom: 'tp',
                autoWidth: false,
                createdRow: function(row, data, dataIndex) {
                    if (selectedQuestions[($(row).data('id'))]) {
                        $(row).find('td').addClass('cats-selected-bg');
                        $(row).removeClass('cats-row');
                        $(row).css('opacity', 0.4);
                    }
                },
                ajax: {
                    url: "/school-management/school-exam/get-questions",
                    type: "POST",
                    data: function () {
                        return {
                            subject: +$("#last-selected").val(),
                            questionType: -1
                        };
                    },
                    dataSrc: function (data) {
                        var questions = [];
                        var sId = +$("#last-selected").val();
                        for (var i = 0; i < data.length; ++i) {
                            examQuestions[data[i].questionId] = {
                                enunciation: data[i].questionEnunciation,
                                alternatives: data[i].questionAlternatives,
                                correctAlternative: data[i].questionCorrectAlternative,
                                subject: sId
                            };
                            questions.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "id": "question-" + data[i].questionId,
                                    "data-id": data[i].questionId
                                },
                                0: data[i].questionEnunciation
                            });
                        }
                        sQuestionsDatatable[sId] = questions;

                        return questions;
                    }
                }
            });
        };

        /*
         * Gera o JSON das questões do conteúdo no formato adequado para salvá-lo
         *  
         * @returns {Array} - array de objetos
         *  Ex:
         *  [
         *      {
         *          questionId: <number>,
         *          questionNumber: <number>
         *      },
         *      ...
         *  ] 
         */
        generateContentQuestionsJson = function() {
            var questions = [];
            var majorSubjects = $('.content-questions > div');
            majorSubjects.each(function () {
                $(this).find('.question-block').each(function () {
                    questions.push({
                        questionId: $(this).data('id'),
                        //questionCorrectAnswer: $(this).data('answer'),
                        questionNumber: +$(this).find('.q-number').html()
                    });
                });
            });
            return questions;
        };

        /*
         * Salva o conteúdo
         * 
         */
        saveContent = function() {
            var questions = generateContentQuestionsJson();
            
            $.ajax({
                method: "POST",
                url: '/school-management/school-exam/save-content-questions',
                data: {
                    contentId: $('#contentId').val(),
                    questions: questions
                }
            });
            
        };

        /*
         * Carrega todas as questões do conteúdo
         * 
         */
        loadQuestions = function(contentId) {
            $.ajax({
                method: "POST",
                url: '/school-management/school-exam/get-content-questions',
                data: {
                    contentId: contentId
                }
            }).done(function(response) {
                addLoadedQuestions(response.questions);
            });
            
        };
        
        addLoadedQuestions = function (questions) {
            var total = questions.length;
            var invokeAddQuestion = function (i) {
                // Adiciona questão
                examQuestions[questions[i].id] = {
                    enunciation: questions[i].enunciation,
                    alternatives: questions[i].alternatives,
                    correctAlternative: questions[i].answer,
                    subject: questions[i].subjectId
                };
                addQuestion(questions[i]);

                // Barra de progresso
                var percentage = +((100 * (i + 1)) / total) + '%';
                $('#content-load-progress').find('.sr-only').html(percentage);
                $('#content-load-progress').find('.progress-bar').css('width', percentage);
                if (i === total - 1) {
                    $('#content-load-progress').find('.sr-only').html('CARREGAMENTO COMPLETO');
                    setTimeout(function () {
                        $('#content-load-progress').slideUp('slow');
                    }, 3000);
                }                  
            };

            $('#content-load-progress').slideDown('fast');
            for (var i = 0; i < total; ++i) {
                invokeAddQuestion(i);
            }

            if ($('#preview-page').hasClass('view-only')) {
                $('.control-icons').remove();
            }
        };

        /*
         * Inicializa o indicador ao lado das disciplinas quanto ao número desejado de questões
         * 
         */
        initSubjectCounters = function () {
            $('.quantity-block').each(function () {
                var quantity = +$(this).data('quantity');
                var subjectId = +$(this).data('s-id');
                $('#select-' + subjectId).find('.q-amount').html(quantity);
            });
        };

        /*
         * Incrementa o valor do contador de questões da disciplina de id=subjectId
         * 
         * @param {int} subjectId - id da disciplina
         */
        incrementSubjectCounter = function(subjectId) {
            $('#select-' + subjectId).find('.q-added')
                .html(+$('#select-' + subjectId).find('.q-added').html() + 1);
        };

        /*
         * Adiciona uma questão ao conteúdo
         * 
         * @param {object} question - objeto com os dados da questão
         *  question = {
         *      id: <number>
         *      subjectId: <number>,
         *      baseSubjectId: <number>,
         *      enunciation: <string>, 
         *      alternatives: [
         *           <string>,
         *           ...
         *      ]
         *      answer: <number>,
         *  }
         * 
         */
        addQuestion = function (question) {
            //  questão já adicionada
            if (selectedQuestions[question.id]) {
                return;
            }
            var subjectId = question.subjectId;
            var subjectName = $('#select-' + subjectId).data("name");
            var baseSubjectId = question.baseSubjectId;
            var questionEnunciation = question.enunciation;  
            var alternatives = question.alternatives;
            var questionAnswer = question.answer;
            var baseSubjectName = $('#base-subject-' + baseSubjectId).data("name");

            selectedQuestions[question.id] = true;
            ++questionCount;
            incrementSubjectCounter(subjectId);
            
            // TODO: Mudar a maneira como isto é feito para não utilizar o nome            
            if (subjectName === "TEMAS PARA REDAÇÃO") {
                baseSubjectId = subjectId;
                if ($('#s-' + subjectId).length === 0) {
                    var wordingBlock = '<div id="s-' + subjectId + '"'
                            + 'class="wording-block do-not-print">'
                            + '<h3 class="text-center no-margin">'
                            + '<strong class="title"> PROPOSTA DE REDAÇÃO'
                            + '</strong></h3></div>';

                    $('.content-questions').prepend(wordingBlock);
                }

                --questionCount;

                var q = '<div id="q-' + question.id + '" class="question-block"'
                        + 'data-id="' + question.id + '" data-subject-id="' + subjectId + '">'
                        + '<span class="do-not-print control-icons pull-right">'
                        + '<i class="rm-question fa fa-times"></i><br>'
                        + '</span>' + questionEnunciation;
            } else {

                if (subjectName === "INGLÊS" || subjectName === "ESPANHOL") {
                    baseSubjectId = subjectId;
                    if ($('#s-' + subjectId).length === 0) {
                        var subjectBlock = '<div id="s-' + subjectId + '">'
                                + '<h3 class="text-center no-margin">'
                                + '<strong class="title">OPÇÃO ' + subjectName
                                + '</strong></h3></div>';
                        switch (subjectName) {
                            case "INGLÊS":
                                subjectBlock = subjectBlock
                                        .replace('<div id', '<div class="english-block" id');

                                var wordingBlock = $('.content-questions').first()
                                        .find('.wording-block');
                                if (wordingBlock.length !== 0) {
                                    wordingBlock.after(subjectBlock);
                                } else {
                                    $('.content-questions').prepend(subjectBlock);
                                }
                                break;
                            case "ESPANHOL":
                                subjectBlock = subjectBlock
                                        .replace('<div id', '<div class="spanish-block" id');

                                var englishBlock = $('.content-questions').first()
                                        .find('.english-block');
                                var wordingBlock = $('.content-questions').first()
                                        .find('.wording-block');
                                if (englishBlock.length !== 0) {
                                    englishBlock.after(subjectBlock);
                                } else if (wordingBlock.length !== 0) {
                                    wordingBlock.after(subjectBlock);
                                } else {
                                    $('.content-questions').prepend(subjectBlock);
                                }
                                break;
                        }
                    }
                    
                //  Se ainda não existir um bloco de questões dessa matéria, cria-se
                } else if ($('#s-' + baseSubjectId).length === 0) { 
                    $('.content-questions').append('<div id="s-' + baseSubjectId + '">'
                            + '<h3 class="text-center no-margin">'
                            + '<strong class="title">' + baseSubjectName
                            + '</strong></h3></div>');
                }

                var q = '<div id="q-' + question.id + '" class="question-block" '
                        + 'data-id="' + question.id + '" data-subject-id="' + subjectId + '" '
                        + 'data-answer="' + questionAnswer + '">'
                        + '<span class="do-not-print control-icons pull-right">'
                        + '<i class="rm-question fa fa-times"></i><br>'
                        + '<i class="move-up fa fa-sort-asc"></i><br>'
                        + '<i class="move-down fa fa-sort-desc"></i>'
                        + '</span>' + '<p class="no-margin"><strong>QUESTÃO '
                        + '<span class="q-number">' + questionCount + '</span>'
                        + '</strong></p>' + questionEnunciation;

                for (var i = 0; i < alternatives.length; ++i) {
                    var alternative = '<span class="pull-left" style="padding-right: 1mm">'
                            + '&#' + (9398 + i) + ';  </span>'
                            + alternatives[i];
                    q += '<div>' + alternative + '</div>';
                }
                updateRemainingQuestions();
                q += '<hr class="q-divider"></div>';
            }
            $('#s-' + baseSubjectId).append(q);
            numberQuestions();
        };

        /*
         * TODO: Adicionar redação separadamente deixar o código mais legível
         * 
         */
        addExamWording = function (qId) {
            /*var subjectId = '';
            var subjectName = $('#select-' + subjectId).data("name");
            var baseSubjectId = '';
            var questionEnunciation = '';
            var questionAnswer = '';
            var alternatives = [];
            if (typeof question !== 'undefined') {
                subjectId = question.subjectId;
                baseSubjectId = question.baseSubjectId;
                questionEnunciation = question.enunciation;  
                alternatives = question.alternatives;
                questionAnswer = question.answer;
            } else {
                subjectId = examQuestions[qId]['subject'];
                baseSubjectId = $('#last-selected').parents('.base-subject-info').data('id');
                questionEnunciation = examQuestions[qId]['enunciation'];
                alternatives = examQuestions[qId]['alternatives'];
                questionAnswer = examQuestions[qId]['correctAlternative'];
            }

            selectedQuestions[qId] = true;
            ++questionCount;
            incrementSubjectCounter(subjectId);
            
            if ($('#s-' + subjectId).length === 0) {
                var wordingBlock = '<div id="s-' + subjectId + '"'
                        + 'class="wording-block do-not-print">'
                        + '<h3 class="text-center no-margin">'
                        + '<strong class="title"> PROPOSTA DE REDAÇÃO'
                        + '</strong></h3></div>';

                $('.content-questions').prepend(wordingBlock);
            }

            --questionCount;

            var q = '<div id="q-' + qId + '" class="question-block"'
                    + 'data-id="' + qId + '" data-subject-id="' + subjectId + '">'
                    + '<span class="do-not-print control-icons pull-right">'
                    + '<i class="rm-question fa fa-times"></i><br>'
                    + '</span>' + questionEnunciation;
        
            $('#s-' + baseSubjectId).append(q);
            numberQuestions();*/
        };
         

        /*
         * Numera as questões 
         * 
         */
        numberQuestions = function () {
            var STARTING_COUNT = 1;
            
            var questions = $('.content-questions > div');
            var number = STARTING_COUNT;

            questions.each(function () {
                if ($(this).hasClass('spanish-block')) {
                    number = STARTING_COUNT;
                }
                $(this).find('.question-block .q-number').each(function () {
                    $(this).html(number++);
                });
            });
        };

        /*
         * Remove uma questão do conteúdo
         * 
         * @param {int} qId - id da questão
         */
        removeQuestion = function (qId) {
            selectedQuestions[qId] = false;

            var subjectName = $('#select-' + examQuestions[qId]['subject']).data("name");
            if (subjectName !== "TEMAS PARA REDAÇÃO") {
                --questionCount;
            }

            var qBlock = $('#q-' + qId);
            var qNumber = qBlock.find('.q-number').html();

            $('#select-' + examQuestions[qId]['subject']).find('.q-added')
                    .html(+$('#select-' + examQuestions[qId]['subject'])
                            .find('.q-added').html() - 1);
            updateRemainingQuestions();

            qBlock.nextAll().each(function () {
                $(this).find('.q-number').html(qNumber++);
            });
            qBlock.parent().nextAll().each(function () {
                $(this).find('.question-block .q-number').each(function () {
                    $(this).html(qNumber++);
                });
            });
            var parent = qBlock.parent();
            qBlock.remove();
            if (parent.find('.question-block').length === 0) {
                parent.remove();
            }
            numberQuestions();
        };

        return {
            init: function () {
                require(['app/pages/school-management/exam/create-content'], function(createContent) {
                    createContent.init();

                    initSubjectCounters();
                    updateRemainingQuestions();
                });
                setListeners();
                initDataTable();
                loadQuestions($('#contentId').val());
            }
        };

    }());

    return prepare;
});
