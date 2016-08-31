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

define(['jquery', 'mathjax', 'jquerycolumnizer', 'jqueryprint'], function () {
    var prepareApplication = (function () {
        
        prepareApplicationListeners = function () {

            /*
             * Salva todo a prova
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
            $('.print-exam').click(function () {
                var pageNumber = 1;

                var firstPage = jQuery();
                var instructionsPage = '';
                var wordingPage = '';

                if ($(this).closest('.exam').find('.exam-instructions').is(":checked")) {
                    var examName = $('#exam-name-input').text().trim() || "PROVA";
                    var examDate = $('#exam-day').text().trim();
                    var examBeginTime = $('#exam-start-time').text().trim();
                    var examEndTime = $('#exam-end-time').text().trim();

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
                    
                    var instructionsPageTemplate = $(".exam-page").first()
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
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9398; </div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9399; </div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9400; </div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 1mm 0 0;"> <svg height="38" width="38"><circle cx="19" cy="19" r="19" fill="black" /></svg></div>'
                            + '<div class="col-xs-2 text-center" style="font-size: 30pt; padding: 0mm;"> &#9402; </div>'
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
                            .first()
                            .clone();
                    instructionsPage
                            .removeClass('exam-page')
                            .addClass("page")
                            .css("display", "block")
                            .css("height", "297mm");
                    instructionsPage.find('.exam-content').html(instructions);
                    instructionsPage.find('.page-number').html(pageNumber);
                    ++pageNumber;
                }

                if ($(this).parent().siblings('.preview-page > .content-questions .wording-block').length !== 0) {
                    wordingPage = $(".exam-page").first().clone();
                    wordingPage.removeClass('exam-page')
                            .addClass("page")
                            .css("display", "block")
                            .css("height", "297mm");

                    var wordingBlock = $(this)
                            .parent()
                            .siblings('.preview-page > .content-questions .wording-block')
                            .clone();
                    wordingBlock.find('.question-block *').css('text-align', 'justify');
                    wordingBlock.find('.do-not-print').each(function () {
                        $(this).remove();
                    });
                    wordingPage.find('.exam-content').html(wordingBlock.html());
                    wordingPage.find('.page-number').html(pageNumber);
                    ++pageNumber;
                }
                
                //  Prepara a div .print-page (columnizer) e imprimi no callback
                var printDivId = "print-page-" + $(this).closest('.application-content').data('content-id');
                generateExam(pageNumber, printDivId, function() {
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub, printDivId], 
                    function () {
                        //  Abre o diálogo de impressão da div .print-page usando jqueryprint
                        $("#" + printDivId).print({
                            globalStyles: true,
                            mediaPrint: true,
                            stylesheet: '/css/exam-print.css',
                            noPrintSelector: null,
                            iframe: true,
                            append: null,
                            prepend: (firstPage.add(instructionsPage)).add(wordingPage),
                            manuallyCopyFormValues: true,
                            deferred: $.Deferred(),
                            timeout: 1000,
                            title: null,
                            doctype: '<!doctype html>'
                        });
                        $("#" + printDivId).html('');
                    }); 
                });
               
            });
        };

        /*
         * Configura (cabeçalho, duas colunas, rodapé) e gera a versão de impressão 
         * na div #print-div
         * @param {int} pageNumber - número da primeira página de prova
         */
        generateExam = function (pageNumber, printDivId, printExam) {

            var contentHeight = 884.88;    // Número aproximado empiricamente
            var page = pageNumber;

            $("#" + printDivId).closest('.exam').append('<div id="exam-temp"></div>');
            $('#exam-temp').html($("#" + printDivId).siblings(".preview-page").html());
            $('#exam-temp').find('.do-not-print').each(function () {
                $(this).remove();
            });
            $("#" + printDivId).html('');
            
            function buildExamLayout() {
                if ($('#exam-temp > .content-questions').contents().length > 0) {
                    // Impede que uma página vazia seja gerada no fim do simulado
                    if ($('#exam-temp > .content-questions > div').length === 1 &&
                            $('#exam-temp > .content-questions > div > div').length === 1 &&
                            $('#exam-temp > .content-questions > div > div').html() === '<hr class="q-divider">') {
                        return;
                    }
                    $page = $(".exam-page")
                            .first()
                            .clone();
                    $page
                            .removeClass('exam-page')
                            .addClass("page")
                            .css("display", "block")
                            .css("height", "297mm");

                    $page.find(".page-number").first().append(page++);
                    $("#" + printDivId).append($page);

                    $('#exam-temp > .content-questions').columnize({
                        columns: 2,
                        target: ".page:last .exam-content",
                        overflow: {
                            height: contentHeight,
                            id: "#exam-temp > .content-questions",
                            doneFunc: function () {
                                buildExamLayout();
                            }
                        }
                    });
                }
            }
            buildExamLayout();
            
            $('#exam-temp').remove();
            // Chama o printExam
            if (printExam && typeof printExam === 'function') {
                printExam();
            }
        };

        loadExams = function () {
            $('.application-content').each(function () {
                getContentQuestions($(this), addFetchedQuestions);
            });
        };

        /*
         * 
         * 
         */
        getContentQuestions = function(applicationContent, callback) {
            $.ajax({
                method: "POST",
                url: '/school-management/school-exam/get-content-questions',
                data: {
                    contentId: applicationContent.data('content-id')
                }
            }).done(function(response) {
                callback(response.questions, applicationContent);
            });
            
        };
        
        addFetchedQuestions = function (questions, applicationContent) {
            for (var i = 0; i < questions.length; ++i) {
                addQuestion(questions[i], applicationContent);
            }
            numberQuestions();
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
        addQuestion = function (question, applicationContent) {
            var subjectId = question.subjectId;
            var subjectName = question.subjectName;
            var baseSubjectId = question.baseSubjectId;
            var baseSubjectName = question.baseSubjectName;
            var questionEnunciation = question.enunciation;  
            var alternatives = question.alternatives;
            var questionAnswer = question.answer;
            
            var contentQuestions = applicationContent.find('.content-questions');

            // TODO: Mudar a maneira como isto é feito para não utilizar o nome hardcoded           
            if (subjectName === "TEMAS PARA REDAÇÃO") {
                baseSubjectId = subjectId;
                if ($('#s-' + subjectId).length === 0) {
                    var wordingBlock = '<div id="s-' + subjectId + '"'
                            + 'class="wording-block do-not-print">'
                            + '<h3 class="text-center no-margin">'
                            + '<strong class="title"> PROPOSTA DE REDAÇÃO'
                            + '</strong></h3></div>';

                    contentQuestions.prepend(wordingBlock);
                }

                var q = '<div id="q-' + question.id + '" class="question-block"'
                        + 'data-id="' + question.id + '" data-subject-id="' + subjectId + '">'
                        + questionEnunciation;
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

                                var wordingBlock = contentQuestions
                                        .first()
                                        .find('.wording-block');
                                if (wordingBlock.length !== 0) {
                                    wordingBlock.after(subjectBlock);
                                } else {
                                    contentQuestions.prepend(subjectBlock);
                                }
                                break;
                            case "ESPANHOL":
                                subjectBlock = subjectBlock
                                        .replace('<div id', '<div class="spanish-block" id');

                                var englishBlock = contentQuestions
                                        .first()
                                        .find('.english-block');
                                var wordingBlock = contentQuestions
                                        .first()
                                        .find('.wording-block');
                                if (englishBlock.length !== 0) {
                                    englishBlock.after(subjectBlock);
                                } else if (wordingBlock.length !== 0) {
                                    wordingBlock.after(subjectBlock);
                                } else {
                                    contentQuestions.prepend(subjectBlock);
                                }
                                break;
                        }
                    }
                    
                //  Se ainda não existir um bloco de questões dessa matéria, cria-se
                } else if ($('#s-' + baseSubjectId).length === 0) { 
                    contentQuestions.append('<div id="s-' + baseSubjectId + '">'
                            + '<h3 class="text-center no-margin">'
                            + '<strong class="title">' + baseSubjectName
                            + '</strong></h3></div>');
                }

                var q = '<div id="q-' + question.id + '" class="question-block" '
                        + 'data-id="' + question.id + '" data-subject-id="' + subjectId + '" '
                        + 'data-answer="' + questionAnswer + '">' 
                        + '<p class="no-margin"><strong>QUESTÃO '
                        + '<span class="q-number"></span>'
                        + '</strong></p>' + questionEnunciation;

                for (var i = 0; i < alternatives.length; ++i) {
                    var alternative = '<span class="pull-left" style="padding-right: 1mm">'
                            + '&#' + (9398 + i) + ';  </span>'
                            + alternatives[i];
                    q += '<div>' + alternative + '</div>';
                }
                q += '<hr class="q-divider"></div>';
            }
            $('#s-' + baseSubjectId).append(q);
        };

        /*
         * Gera o gabarito do simulado
         * 
         */
        generateAnswerKey = function () {
            $('.answers-table').remove();
            
            var number = 1;//$('#exam-numbering-start').val();
            var columnCount = 1;
            var CSVAnswers = "Numero,Resposta,Disciplina\n";
            $('.application-content').each(function () {
                var applicationContentBeginNumber = number;
                
                var majorSubjects = $(this).find('.content-questions > div');
                var majorSubjectName = '';
                majorSubjects.each(function () {
                    if ($(this).hasClass('spanish-block')) {
                        number = applicationContentBeginNumber;
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
            });
            $('#answer-key-tables').css("height", "210mm");

            $("#save-answers-csv").removeAttr("disabled");
            $("#save-answers-csv").attr("href", 'data:attachment/csv,' +  encodeURIComponent(CSVAnswers));
            $("#save-answers-csv").attr("target", '_blank');
            $("#save-answers-csv").attr("download", 'gabarito.csv');
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
           */
        };

        /*
         * Numera as questões 
         * 
         */
        numberQuestions = function () {
            var number = 1;
            $('.q-number').each(function () {
                if ($(this).closest('.spanish-block').length !== 0 
                        && $(this).closest('.question-block').prev('div').length === 0) {
                    number = $(this)
                            .closest('.content-questions')
                            .find('.q-number')
                            .first()
                            .html();
                }
                $(this).html(number++);
            });
        };

        return {
            init: function () {
                prepareApplicationListeners();
                loadExams();
            }
        };

    }());

    return prepareApplication;
});
