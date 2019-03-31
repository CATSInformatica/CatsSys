/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'mathjax'], function () {
    var question = (function () {

        var questionTable = $('#question-table');

        /**
         * Configuração de pesquisa de questões. Define a quantidade de questões carregada
         * e paginação
         */
        var currentConfig = {
            totalPage: 25,
            total: 0,
            page: 1
        };

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

            /**
             * Ao mudar a quantidade de questões da lista força pesquisa
             */
            questionTable.on( 'length.dt', function (e, settings, len) {
                currentConfig.page = 1;
                currentConfig.totalPage = len;
                questionTable.DataTable().ajax.reload();
            });
        };

        /*
         * Cria e carrega a tabela de questões de acordo com os filtros
         * selecionados (disciplina e tipo de questão)
         */
        initDataTable = function () {
            questionTable.DataTable({
                dom: 'lftp',
                columnDefs: [{
                    targets: 0,
                    className: 'details-control',
                    orderable: false
                }],
                lengthMenu: [5, 10, 25, 50, 75, 100],
                pageLength: currentConfig.totalPage,
                order: [],
                ajax: {
                    url: "/school-management/school-exam/get-subject-questions",
                    type: "POST",
                    data: function () {
                        return {
                            subject: $("select[name=subject]").val(),
                            questionType: $("select[name=questionType]").val(),
                            totalPage: currentConfig.totalPage,
                            page: currentConfig.page
                        };
                    },
                    dataSrc: function (data) {
                        var questions = [];
                        currentConfig.total = data.total;
                        var dQuestions = data.questions;
                        for (var i = 0; i < dQuestions.length; ++i) {
                            questions.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "id": "question-" + dQuestions[i].questionId,
                                    "data-id": dQuestions[i].questionId
                                },
                                0: '',
                                1: dQuestions[i].questionEnunciation,
                                child: formatAlternatives(dQuestions[i].questionAlternatives,
                                    dQuestions[i].questionCorrectAlternative)
                            });
                        }

                        return questions;
                    }
                },
                drawCallback: function (settings) {
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                    updatePagination();
                }
            });
        };

        updatePagination = function () {

            if(currentConfig.total < 1) {
                return;
            }

            var pages = '', i, minPage, maxPage;
            var tableWrapper = $("#questionContentWrapper").find("#question-table_paginate .pagination");
            minPage = Math.max(1, currentConfig.page - 5);
            maxPage = Math.min(currentConfig.page + 5, Math.ceil(currentConfig.total / currentConfig.totalPage));

            for(i = minPage; i <= maxPage; i++) {
                pages += getPageTemplate(i, i == currentConfig.page);
            }

            tableWrapper.html(
                '<li class="paginate_button previous" id="question-table_previous"><a aria-controls="question-table" style="cursor:pointer" data-dt-idx="prev" tabindex="0">Previous</a></li>' +
                pages +
                '<li class="paginate_button next" id="question-table_next"><a aria-controls="question-table" style="cursor:pointer" data-dt-idx="next" tabindex="0">Next</a></li>'
            );

            tableWrapper.on('click', '.paginate_button a', function() {
                var attr = $(this).attr('data-dt-idx'), pageClicked;

                if(attr == "next") {
                    pageClicked = Math.min(maxPage, currentConfig.page + 1);
                } else if(attr == "prev") {
                    pageClicked = Math.max(1, currentConfig.page - 1);
                } else {
                    pageClicked = attr;
                }

                if(pageClicked != currentConfig.page) {
                    currentConfig.page = parseInt(pageClicked);
                    questionTable.DataTable().ajax.reload();
                }
            });
        };

        getPageTemplate = function (idx, isActive) {
            return '<li class="paginate_button '+ (isActive ? 'active' : '') +'">' +
                   '<a aria-controls="question-table" style="cursor:pointer" data-dt-idx="'+ idx +'" tabindex="0">'+ idx +'</a>' +
                   '</li>';
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