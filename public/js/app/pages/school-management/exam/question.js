/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'mathjax'], function () {
    var question = (function () {

        var questionTable = $('#question-table');

        /*
         * Retorna o template em HTML da área onde serão exibidas as alternativas da 
         * questão
         * 
         * @param {array} alternatives - alternativas da questão
         * @param {int} correctAlternative - índice da alternativa correta
         * @returns {string}
         */
        formatAlternatives = function (alternatives, correctAlternative) {
            var html = '<div class="row">' +
                '<div class="col-xs-12" style="padding-left: 2%">' + 
                    '<strong>Alternativas</strong><br>';
                    for (var i = 0; i < alternatives.length; ++i) {
                        html += '<span class="' + ((correctAlternative === i) ? 'text-green' : '') + 
                                ' pull-left" style="padding-right: 5px"> &#' + (9398 + i) + ';</span> ' + 
                                alternatives[i] + '<br>';
                    }
                html += '</div>' +
            '</div>';
            return html;
        };

        /*
         *  Listeners da tabela de questões
         */
        initDatatableListeners = function () {
            // Exibe as alternativas da questão quando o ícone (?) é clicado
            $('#question-table tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = questionTable.DataTable().row(tr);
                
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(row.data().child).show();
                    tr.addClass('shown');
                }
            });
            
            // Recarrega a tabela de questões quando se clica no botão de buscar
            $('button[name=submit]').click(function () {
                questionTable.DataTable().ajax.reload();
            });
        };

        /*
         * Cria e carrega a tabela de questões de acordo com os filtros 
         * selecionados (disciplina e tipo de questão)
         */
        initDataTable = function () {
            questionTable.DataTable({
                dom: 'lftip',
                columnDefs: [{
                    targets: 0,
                    className: 'details-control',
                    orderable: false
                }],
                order: [],
                ajax: {
                    url: "/school-management/school-exam/get-subject-questions",
                    type: "POST",
                    data: function () {
                        return {
                            subject: $("select[name=subject]").val(),
                            questionType: $("select[name=questionType]").val()
                        };
                    },
                    dataSrc: function (data) {
                        var questions = [];
                        for (var i = 0; i < data.length; ++i) {
                            questions.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "id": "question-" + data[i].questionId,
                                    "data-id": data[i].questionId
                                },
                                0: '',
                                1: data[i].questionEnunciation,
                                child: formatAlternatives(data[i].questionAlternatives, 
                                        data[i].questionCorrectAlternative)
                            });
                        }

                        return questions;
                    }
                },
                drawCallback: function (settings) {
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                }
            });
        };

        return {
            init: function () {
                initDataTable();
                initDatatableListeners();
            },
            getCallbackOf: function (element) {

                return {
                    exec: function (data) {
                        questionTable
                                .DataTable()
                                .row('#question-' + data.questionId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());

    return question;
});