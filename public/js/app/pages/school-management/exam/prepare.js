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

define(['jquery', 'datatable', 'mathjax', 'jquerycolumnizer', 
        'jqueryprint', 'datetimepicker', 'app/pages/school-management/exam/exam-config'], function () {
    var prepare = (function () {

        //  Array de objetos com as questões carregadas por ajax
        //  índice = id da questão
        var examQuestions = [];
        //  Array de objetos com formatação para inserir no DataTable as questões de uma disciplina 
        //  índice = id da disciplina
        var sQuestionsDatatable = [];
        //  Array de bool com as questões selecionadas (adicionadas a prova)
        //  índice = id da questão
        var selectedQuestions = [];
        //  id do DataTable
        var questionTable = $('#question-table');
        //  Número de questões adicionadas        
        var questionCount = 0;

        setListeners = function () {
            /*
             * Atualiza o indicador ao lado das disciplinas quanto ao número desejado de questões
             * 
             */
            $('.amount-input').change(function () {
                var sId = $(this).parents('.quantity-block').data('s-id');
                var subjectAmount = +$(this).val();

                var oldValue = +$(this).data('old-value');
                if (isNaN(oldValue)) {
                    oldValue = 0;
                }
                if ($('#select-' + sId).find('.q-amount').html() !== '') {
                    subjectAmount += +$('#select-' + sId).find('.q-amount').html();
                    subjectAmount -= oldValue;
                }
                $('#select-' + sId).find('.q-amount').html(subjectAmount);
                $(this).data('old-value', subjectAmount);

                var count = 0;
                $('.amount-input').each(function () {
                    if ($(this).val() !== '') {
                        count += +$(this).val();
                    }
                });
                $('#question-count').html(count);
                updateRemainingQuestions();
            });

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
             * Se a configuração "Numerar questões a partir de" mudar, renumera as questões
             * 
             */
            $('#exam-numbering-start').change(function () {
                numberQuestions();
            });

            /*
             * Adiciona todas as questões selecionadas
             * 
             */
            $('#add-exam-question').click(function () {
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
            });

            /*
             * Salva todo o simulado (configuração + questões selecionadas).
             * 
             */
            $('#save-exam').click(function () {
                saveExam();
            });

            /*
             * Mostra o diálogo de impressão do gabarito
             * 
             */
            $('#print-answer-key').click(function () {
                generateAnswerKey();
                
                $('#answer-key-tables').print({
                    globalStyles: true,
                    mediaPrint: true,
                    stylesheet: '/css/exam-print.css',
                    noPrintSelector: null,
                    iframe: true,
                    append: null,
                    prepend: null,
                    manuallyCopyFormValues: true,
                    deferred: $.Deferred(),
                    timeout: 250,
                    title: null,
                    doctype: '<!doctype html>'
                });
            });

            /*
             * Mostra o diálogo de impressão do simulado
             * 
             */
            $('#print-exam').click(function () {
                var pageNumber = 1;

                var firstPage = jQuery();
                var instructionsPage = '';
                var wordingPage = '';

                if ($('#exam-instructions').is(":checked")) {
                    var examName = $('#exam-name-input').val() || "SIMULADO";
                    var examDate = $('#exam-day').val();
                    var examBeginTime = $('#exam-begin-time').val();
                    var examEndTime = $('#exam-end-time').val();

                    /*
                     * TODO: Usar template disponível na página
                     * 
                     * O código abaixo não está funcional, por algum motivo 
                     * os estilos não são aplicados corretamente
                     *
                     *
                    var instructionsTemplate = $($("#instructions-template").html());
                    instructionsTemplate.find('.id-goes-here').attr('id', 'instructions');
                    instructionsTemplate.find('.i-exam-name').html(examName);
                    instructionsTemplate.find('.i-exam-date').html(examDate);
                    instructionsTemplate.find('.i-begin-time').html(examBeginTime);
                    instructionsTemplate.find('.i-end-time').html(examEndTime);
                    
                    var instructionsPageTemplate = $(".exam-page")
                            .clone().removeClass('exam-page')
                            .addClass("page").css("display", "block")
                            .css("height", "297mm");
                    instructionsPageTemplate.find('.exam-content').html(instructionsTemplate.html());
                    */
                   
                    var instructions = '<div id="instructions"><div class="text-center"><h3>'
                            + '<strong>' + examName + '</strong></h3>'
                            + examDate + ' - ' + examBeginTime
                            + 'h às ' + examEndTime + 'h'
                            + '<div class="text-uppercase"><br><h4>Instruções gerais '
                            + 'para a realização da prova</h4></div><h4><br><strong>'
                            + 'Antes de iniciar a resolução da prova, verifique '
                            + 'se as regras abaixo estão sendo cumpridas.'
                            + '</strong></h4></div><br>'
                            + '<div class="text-justify"><ol> <li>Não abra a prova até que o fiscal lhe conceda '
                            + 'autorização</li><li>Em sua mesa de prova <strong>não '
                            + 'deve constar nada além de lápis, borracha e caneta.</strong></li>'
                            + '<li>Quaisquer materiais que não sejam os descritos acima'
                            + ' devem ser deixados à sua direita, no chão.</li>'
                            + '<li><strong>O candidato</strong> que precisar usar o banheiro'
                            + ' ou que, por ventura, <strong> venha a se sentir mal, deve'
                            + ' chamar o fiscal</strong> à sua mesa. <strong>Não</strong>'
                            + ' se levante da mesa sem comunicar o fiscal.</li>'
                            + '<li>Não haverá correção de erros nas questões. Caso '
                            + 'esses existam e comprometam o resultado das mesmas, '
                            + 'as questões erradas serão anuladas posteriormente.</li>'
                            + '<li>A prova tem duração máxima de 4 (quatro) horas. '
                            + 'O tempo mínimo de permanência na sala é de 2<strong> (duas)'
                            + '</strong> horas. Após este período, você receberá uma '
                            + 'folha de respostas, onde deverá marcar as repostas de '
                            + '<strong>caneta azul ou preta.</strong> Marque somente '
                            + 'uma resposta para cada questão, caso contrário, '
                            + 'tal questão será considerada anulada em sua prova. Logo abaixo'
                            + ', você pode verificar como deve ser marcada a resposta '
                            + 'que você julgar correta.</li>'
                            + '<div class="col-xs-6 col-xs-offset-3" style="line-height: 15mm;">'
                            + '<div class="col-xs-1" style="font-size: 30pt; line-height: 15mm;"></div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9398 </div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9399 </div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9400 </div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 1mm 0 0;"> <svg height="38" width="38"><circle cx="19" cy="19" r="19" fill="black" /></svg></div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9402 </div>'
                            + '<div class="col-xs-1" style="font-size: 30pt; line-height: 15mm;"></div>'
                            + '</div>'
                            + '<br><br><br><li>Caso exista uma questão que você julgue estar sem '
                            + 'sentido, sem resposta correta, mais de uma resposta '
                            + 'correta, ilegível ou algo do gênero, escreva atrás do '
                            + 'gabarito o número da questão e o erro encontrado.</li>'
                            + '<li>É recomendável que se deixem pelo menos 30 (trinta) '
                            + 'minutos para o preenchimento da folha de resposta. Em '
                            + 'hipótese alguma o fiscal irá trocá-la e somente ela '
                            + 'deverá ser entregue ao fiscal no fim de sua prova. '
                            + 'Não se esqueça de escrever o número de matrícula na '
                            + 'folha de resposta. O caderno de questões ficará com '
                            + 'você.</li><li>Antes de iniciar sua prova, espere autorização '
                            + 'do fiscal para conferir se todas as páginas estão em seu '
                            + 'caderno e se todas estão legíveis.</li><li>Inicie a prova '
                            + 'quando houver autorização do fiscal.</li>'
                            + '</ol></div></div>';

                    instructionsPage = $(".exam-page")
                            .clone().removeClass('exam-page')
                            .addClass("page").css("display", "block")
                            .css("height", "297mm");
                    instructionsPage.find('.exam-content').html(instructions);
                    instructionsPage.find('.page-number').html(pageNumber);
                    ++pageNumber;
                }

                if ($('.exam-questions .wording-block').length !== 0) {
                    wordingPage = $(".exam-page").clone().removeClass('exam-page')
                            .addClass("page");
                    wordingPage.css("display", "block").css("height", "297mm");

                    var wordingBlock = $('.exam-questions .wording-block').clone();
                    wordingBlock.find('.question-block *').css('text-align', 'justify');
                    wordingBlock.find('.do-not-print').each(function () {
                        $(this).remove();
                    });
                    wordingPage.find('.exam-content').html(wordingBlock.html());
                    wordingPage.find('.page-number').html(pageNumber);
                    ++pageNumber;
                }

                //  Prepara a div #print-page (columnizer) e imprimi no callback
                generateExam(pageNumber, function() {
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'print-page'], function () {
                        //  Abre o diálogo de impressão da div #print-page usando jqueryprint
                        $('#print-page').print({
                            globalStyles: true,
                            mediaPrint: true,
                            stylesheet: '/css/exam-print.css',
                            noPrintSelector: null,
                            iframe: true,
                            append: null,
                            prepend: (firstPage.add(instructionsPage)).add(wordingPage),
                            manuallyCopyFormValues: true,
                            deferred: $.Deferred(),
                            timeout: 250,
                            title: null,
                            doctype: '<!doctype html>'
                        });
                        $('#print-page').html(''); 
                    });
                });
                
            });

            /*
             * Remove a questão a qual está associado o ícone
             * 
             */
            $('.exam-questions').on('click', '.rm-question', function () {
                var question = $('#question-' + $(this).parents('.question-block').data('id'));
                question.find('td').removeClass('cats-selected-bg');
                question.addClass('cats-row');
                question.css('opacity', 1);
                removeQuestion(+$(this).parents('.question-block').data('id'));
            });

            /*
             * Troca de posição com a questão de cima, se existir e for da mesma disciplina.
             * 
             */
            $('.exam-questions').on('click', '.move-up', function () {
                var qBlock = $(this).parents('.question-block');
                var previous = qBlock.prev('.question-block');
                if (previous.length !== 0) {
                    var qNumber = qBlock.find('.q-number').html();
                    qBlock.find('.q-number').html(previous.find('.q-number').html());
                    previous.find('.q-number').html(qNumber);
                    qBlock.detach().insertBefore(previous);
                }
            });

            /*
             * Troca de posição com a questão de baixo, se existir e for da mesma disciplina.
             * 
             */
            $('.exam-questions').on('click', '.move-down', function () {
                var qBlock = $(this).parents('.question-block');
                var next = qBlock.next('.question-block');
                if (next.length !== 0) {
                    var qNumber = qBlock.find('.q-number').html();
                    qBlock.find('.q-number').html(next.find('.q-number').html());
                    next.find('.q-number').html(qNumber);
                    qBlock.detach().insertAfter(next);
                }
            });

            /*
             * Insere o nome preenchido no campo "Nome do Simulado" no rodapé do simulado
             * 
             */
            $('#exam-name-input').on('keyup', function () {
                $('.exam-name').html($('#exam-name-input').val());
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
         * Configura (cabeçalho, duas colunas, rodapé) e gera a versão de impressão 
         * na div #print-page
         * @param {int} pageNumber - número da primeira página de prova
         */
        generateExam = function (pageNumber, callback) {

            var contentHeight = 884.88;    // Número aproximado empiricamente
            var page = pageNumber;

            $('#exam').append('<div id="exam-temp"></div>');
            $('#exam-temp').html($("#preview-page").html());
            $('#exam-temp').find('.do-not-print').each(function () {
                $(this).remove();
            });
            $('#print-page').html('');

            function buildExamLayout() {
                if ($('#exam-temp > .exam-questions').contents().length > 0) {
                    // Impede que uma página vazia seja gerada no fim do simulado
                    if ($('#exam-temp > .exam-questions > div').length === 1 &&
                            $('#exam-temp > .exam-questions > div > div').length === 1 &&
                            $('#exam-temp > .exam-questions > div > div').html() === '<hr class="q-divider">') {
                        return;
                    }
                    $page = $(".exam-page").clone().removeClass('exam-page')
                            .addClass("page").css("display", "block").css("height", "297mm");
                    ;

                    $page.find(".page-number").first().append(page++);
                    $("#print-page").append($page);

                    $('#exam-temp > .exam-questions').columnize({
                        columns: 2,
                        target: ".page:last .exam-content",
                        overflow: {
                            height: contentHeight,
                            id: "#exam-temp > .exam-questions",
                            doneFunc: function () {
                                buildExamLayout();
                            }
                        }
                    });
                }
            }
            buildExamLayout();

            $('#exam-temp').remove();
            
            // Chama o callback
            if (callback && typeof callback === 'function') {
                callback();
            }
        };

        /*
         * Gera o JSON das questões do simulado no formato adequado para salvar o simulado
         *  
         * @returns {Array} - array de objetos
         *  Ex:
         *  [
         *      {
         *          questionId: <number>, 
         *          questionCorrectAnswer: <number>,
         *          questionNumber: <number>
         *      },
         *      ...
         *  ] 
         */
        generateExamQuestionsJson = function() {
            var questions = [];
            var majorSubjects = $('.exam-questions > div');
            majorSubjects.each(function () {
                $(this).find('.question-block').each(function () {
                    questions.push({
                        questionId: $(this).data('id'),
                        questionCorrectAnswer: $(this).data('answer'),
                        questionNumber: +$(this).find('.q-number').html()
                    });
                });
            });
            return questions;
        };

        /*
         * Salva o simulado
         * 
         */
        saveExam = function() {
            var questions = generateExamQuestionsJson();
            saveConfig();
            
            $.ajax({
                method: "POST",
                url: '/school-management/school-exam/save-exam-questions',
                data: {
                    examId: $('#examId').val(),
                    questions: questions
                }
            });
            
        };

        /*
         * Carrega todas as questões do simulado
         * 
         */
        loadQuestions = function() {
            $.ajax({
                method: "POST",
                url: '/school-management/school-exam/get-exam-questions',
                data: {
                    examId: $('#examId').val()
                }
            }).done(function(response) {
                var questions = response.questions;
                var total = questions.length;
                var execAddQuestion = function (i) {
                    setTimeout(function () {
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
                        $('#exam-load-progress').find('.sr-only').html(percentage);
                        $('#exam-load-progress').find('.progress-bar').css('width', percentage);
                        if (i === total - 1) {
                            $('#exam-load-progress').find('.sr-only').html('CARREGAMENTO COMPLETO');
                            setTimeout(function () {
                                $('#exam-load-progress').slideUp('slow');
                            }, 3000);
                        }  
                    }, 400);                 
                };
                
                $('#exam-load-progress').slideDown('fast');
                for (var i = 0; i < total; ++i) {
                    execAddQuestion(i);
                }
            });
            
        };

        /*
         * Gera o gabarito do simulado
         * 
         */
        generateAnswerKey = function () {
            //generateExamConfig();
            $('.answers-table').remove();
            
            var majorSubjects = $('.exam-questions > div');
            var majorSubjectName = '';
            var number = $('#exam-numbering-start').val();
            var columnCount = 1;            
            var CSVAnswers = "Numero,Resposta,Disciplina\n";
            
            majorSubjects.each(function () {
                if ($(this).hasClass('spanish-block')) {
                    number = $('#exam-numbering-start').val();
                }
                
                majorSubjectName = $(this).find('.title').html();
                $('#answer-key-tables').append(
                    '<table class="table table-striped table-condensed answers-table">' +
                        '<caption class="text-center"><strong>' +
                            majorSubjectName + 
                        '</strong></caption>' +
                    '</table>'
                );
                columnCount = 1;
                
                $(this).find('.question-block').each(function () {
                    if (columnCount++ % 26 === 0) {
                        $('#answer-key-tables').append(
                            '<table class="table table-striped table-condensed answers-table">' + 
                                '<caption class="text-center"><strong></strong></caption>' +
                            '</table>'
                        );
                    }
                    $('.answers-table').last().append(
                        '<tr>' +
                            '<td class="text-center">' + (number) + '</td>' +
                            '<td class="text-center"><span class="text-center">' +
                                String.fromCharCode($(this).data('answer') + 'A'.charCodeAt(0)) +
                            '</span></td>' +
                        '</tr>'
                    );
                    CSVAnswers += (number++) + ',' + 
                            String.fromCharCode($(this).data('answer') + 'A'.charCodeAt(0)) + ',' + 
                            majorSubjectName + "\n";
                });
            });
            $('#answer-key-tables').css("height", "210mm");

            $("#save-answers-csv").removeAttr("disabled");
            $("#save-answers-csv").attr("href", 'data:attachment/csv,' +  encodeURIComponent(CSVAnswers));
            $("#save-answers-csv").attr("target", '_blank');
            $("#save-answers-csv").attr("download", 'gabarito.csv');
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
         * Adiciona uma questão ao simulado
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

                    $('.exam-questions').prepend(wordingBlock);
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

                                var wordingBlock = $('.exam-questions').first()
                                        .find('.wording-block');
                                if (wordingBlock.length !== 0) {
                                    wordingBlock.after(subjectBlock);
                                } else {
                                    $('.exam-questions').prepend(subjectBlock);
                                }
                                break;
                            case "ESPANHOL":
                                subjectBlock = subjectBlock
                                        .replace('<div id', '<div class="spanish-block" id');

                                var englishBlock = $('.exam-questions').first()
                                        .find('.english-block');
                                var wordingBlock = $('.exam-questions').first()
                                        .find('.wording-block');
                                if (englishBlock.length !== 0) {
                                    englishBlock.after(subjectBlock);
                                } else if (wordingBlock.length !== 0) {
                                    wordingBlock.after(subjectBlock);
                                } else {
                                    $('.exam-questions').prepend(subjectBlock);
                                }
                                break;
                        }
                    }
                    
                //  Se ainda não existir um bloco de questões dessa matéria, cria-se
                } else if ($('#s-' + baseSubjectId).length === 0) { 
                    $('.exam-questions').append('<div id="s-' + baseSubjectId + '">'
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
                    var alternative = alternatives[i]
                            .replace(/(<div.*?>)/, '$1<span class="push-left">'
                                    + '&#' + (9398 + i) + ';  </span>');
                    if (alternative === alternatives[i]) {
                        alternative = '<span class="push-left">'
                                + '&#' + (9398 + i) + ';  </span>'
                                + alternative;
                    }
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

                $('.exam-questions').prepend(wordingBlock);
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
            var questions = $('.exam-questions > div');
            var number = $('#exam-numbering-start').val();

            questions.each(function () {
                if ($(this).hasClass('spanish-block')) {
                    number = $('#exam-numbering-start').val();
                }
                $(this).find('.question-block .q-number').each(function () {
                    $(this).html(number++);
                });
            });
        };

        /*
         * Remove uma questão do simulado
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
                initQuantities();
                setListeners();
                examConfigListeners();
                initDataTable();
                initQuestionAmount();
                initDatepicker();
                loadQuestions();
            }
        };

    }());

    return prepare;
});
