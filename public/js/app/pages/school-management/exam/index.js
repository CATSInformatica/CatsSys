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

define(['jquery', 'datatable', 'mathjax', 'jquerycolumnizer', 'jqueryprint'], function () {
    var index = (function () {

        //  Array de objetos com as questões carregadas por ajax
        //  índice = id da questão
        var examQuestions = [];
        //  Array de objetos com formatação para inserir no DataTable as questões de uma disciplina 
        //  índice = id da disciplina
        var sQuestionsDatatable = [];
        //  Array de bool com as questões selecionadas
        //  índice = id da questão
        var selectedQuestions = [];
        //  id do DataTable
        var questionTable = $('#question-table');
        //  Número de questões adicionadas        
        var questionCount = 0;

        setListeners = function () {
            /*
             * Atualiza o indicador ao lado das disciplinas quanto ao número desejado de questões
             */
            $('.amount-input').change(function () {
                var sId = $(this).data('s-id');
                var subjectAmount = parseInt($(this).val());

                var oldValue = parseInt($(this).data('old-value'));
                if (isNaN(oldValue)) {
                    oldValue = 0;
                }
                if ($('#s-' + sId + '-amount').html() !== '') {
                    subjectAmount += parseInt($('#s-' + sId + '-amount').html());
                    subjectAmount -= oldValue;
                }
                $('#s-' + sId + '-amount').html(subjectAmount);

                $(this).data('old-value', subjectAmount);

                var count = 0;
                $('.amount-input').each(function () {
                    if ($(this).val() !== '') {
                        count += parseInt($(this).val());
                    }
                });
                $('#question-count').html('Total: ' + count);
            });

            /*
             * Carrega as questões da disciplina selecionada no DataTable
             */
            $('select[name=subject]').change(function () {
                var optionSelected = $("option:selected", this);
                $('#last-selected').prop('selected', false);
                $('#last-selected').removeAttr('id');
                optionSelected.attr('id', 'last-selected');
                if (typeof sQuestionsDatatable[parseInt($("#last-selected").val())] !== 'undefined') {
                    setDatatable(sQuestionsDatatable[parseInt($("#last-selected").val())]);
                } else {
                    questionTable.DataTable().ajax.reload();
                }
            });

            /*
             * Adiciona todas as questões selecionadas (checkbox)
             */
            $('#add-exam-question').click(function () {
                $('.select-questions:checkbox:checked').each(function () {
                    addQuestion($(this).val());
                });
            });

            /*
             * Mostra o diálogo de impressão do simulado
             */
            $('#print-exam').click(function () {
                //  Prepara a div #print-page
                generateExam();                

                MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'print-page'], function () {
                    //  Abre o diálogo de impressão da div #print-page usando jqueryprint
                    $('#print-page').print({
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
            });

            /*
             * Remove a questão a qual está associado o ícone
             */
            $('.exam-questions').on('click', '.rm-question', function () {
                removeQuestion(parseInt(parseInt($(this).parents('.question-block').attr('id').substr(2))));
            });

            /*
             * Troca de posição com a questão de cima, se existir e for da mesma disciplina.
             */
            $('.exam-questions').on('click', '.move-up', function () {
                var qBlock = $(this).parent().parent();
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
             */
            $('.exam-questions').on('click', '.move-down', function () {
                var qBlock = $(this).parent().parent();
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
             */
            $('#exam-name-input').on('keyup', function () {
                $('.exam-name').html($('#exam-name-input').val());
            });
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
         */
        initDataTable = function () {
            questionTable.DataTable({
                dom: 'tp',
                autoWidth: false,
                ajax: {
                    url: "/school-management/school-exam/get-questions",
                    type: "POST",
                    data: function () {
                        return {
                            subject: parseInt($("#last-selected").val()),
                            questionType: -1
                        };
                    },
                    dataSrc: function (data) {
                        var questions = [];
                        var sId = parseInt($("#last-selected").val());
                        for (var i = 0; i < data.length; ++i) {
                            examQuestions[data[i].questionId] = {
                                'enunciation': data[i].questionEnunciation,
                                'alternatives': data[i].questionAnswers,
                                'subject': sId
                            };
                            questions.push({
                                DT_RowAttr: {
                                    "class": "table-row",
                                    "id": "question-" + data[i].questionId,
                                    "data-id": data[i].questionId
                                },
                                0: '<input type="checkbox" class="select-questions" value="' + data[i].questionId + '">',
                                1: data[i].questionEnunciation
                            });
                        }
                        sQuestionsDatatable[sId] = questions;

                        return questions;
                    }
                }
            });
        };

        /*
         * Configura (cabeçalho, duas colunas, rodapé) e gera a versão de impressão na div #print-page
         */
        generateExam = function () {

            var content_height = 900;
            var page = 1;   // Número da primeira página
            
            
            $('#exam-part-1').append('<div id="exam-temp"></div>');
            $('#exam-temp').html($("#preview-page").first().html());
            $('#exam-temp').find('.do-not-print').each(function () {
                $(this).remove();
            });
            $('#print-page').html('');

            function buildExamLayout() {
                if ($('#exam-temp > .exam-questions').contents().length > 0) {
                    $page = $(".exam-page").first().clone().addClass("page").css("display", "block");

                    $page.find(".page-number").first().append(page++);
                    $("#print-page").append($page);

                    $('#exam-temp > .exam-questions').columnize({
                        columns: 2,
                        target: ".page:last .exam-content",
                        overflow: {
                            height: content_height,
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

        };

        /*
         * Adiciona uma questão ao simulado
         * 
         * @param {int} qId - id da questão
         */
        addQuestion = function (qId) {
            if (typeof selectedQuestions[qId] === 'undefined' || selectedQuestions[qId] === false) {
                selectedQuestions[qId] = true;
                ++questionCount;
                var subjectId = examQuestions[qId]['subject'];
                if ($('#s-' + subjectId).length === 0) {
                    var subjectName = $('option[value="' + subjectId + '"]').html();
                    $('.exam-questions').append('<div id="s-' + examQuestions[qId]['subject'] + '">'
                            + '<h3 class="text-center no-margin dontend"><strong class="title">' + subjectName + '</strong></h3></div>');
                }
                var q = '<div id=q-' + qId + ' class="question-block">'
                        + '<span class="do-not-print control-icons pull-right">'
                        + '<i class="rm-question fa fa-times"></i><br>'
                        + '<i class="move-up fa fa-sort-asc"></i><br>'
                        + '<i class="move-down fa fa-sort-desc"></i>'
                        + '</span>'
                        + '<div><p class="text-justify"><span class="q-number">' + questionCount + '</span>) ' + examQuestions[qId]['enunciation'] + '</p></div>';

                var alternativeListStyle = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
                for (var i = 0; i < examQuestions[qId]['alternatives'].length; ++i) {
                    q += '<div><p class="text-justify">' + alternativeListStyle[i] + ') ' + examQuestions[qId]['alternatives'][i] + '</div>';
                }
                q += '</div>';

                $('#s-' + examQuestions[qId]['subject']).append(q);
            }
        };

        /*
         * Remove uma questão do simulado
         * 
         * @param {int} qId - id da questão
         */
        removeQuestion = function (qId) {
            var qBlock = $('#q-' + qId);
            var qNumber = qBlock.find('.q-number').html();

            qBlock.nextAll().each(function () {
                $(this).find('.q-number').html(qNumber++);
            });
            qBlock.parent().nextAll().each(function () {
                $(this).find('.question-block .q-number').html(qNumber++);
            });

            if (qBlock.siblings('.question-block').length === 0) {
                qBlock.parent().remove();
            }

            qBlock.remove();
            selectedQuestions[qId] = false;
            --questionCount;
        };

        return {
            init: function () {
                setListeners();
                initDataTable();
            }
        };

    }());

    return index;
});