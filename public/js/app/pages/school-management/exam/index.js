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

define(['jquery', 'datatable', 'mathjax', 'jquerycolumnizer'], function () {
    var index = (function () {

        var examQuestions = [];
        var selectedQuestions = [];
        var sQuestionsDatatable = [];
        var questionTable = $('#question-table');
        var questionCount = 0;
        var examPage = 1;

        setListeners = function () {
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

            $('#add-exam-question').click(function () {
                $('.select-questions:checkbox:checked').each(function () {
                    addQuestion($(this).val());
                });
            });

            $('#print-notebook-1').click(function () {
                generateExam();

                var page = $('.print-page').first().clone();
                page.find('.do-not-print').each(function () {
                    $(this).attr('style', 'display: none');
                });

                var printPage = window.open('', '', 'width=840,height=1200');
                printPage.document.write(page.html());
                printPage.document.write('<link rel="stylesheet" href="/css/exam-print.css" type="text/css" />');
                printPage.document.write('<link rel="stylesheet" href="/vendor/AdminLTE/bootstrap/css/bootstrap.min.css" type="text/css" />');

                printPage.document.close();
                printPage.focus();



                /*var printContents = $('#print-page').html();
                 var originalContents = $('body').html();
                 $('body').html(printContents);
                 $(".do-not-print").css("display", "none");
                 window.print();
                 questionTable.DataTable().destroy();
                 $('body').html(originalContents);*/
            });

            $('.exam-questions').on('click', '.rm-question', function () {
                removeQuestion(parseInt(parseInt($(this).parents('.question-block').attr('id').substr(2))));
            });

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

            $('#exam-name-input').on('keyup', function () {
                $('.exam-name').html($('#exam-name-input').val());
            });
        };

        setDatatable = function (data) {
            questionTable.DataTable().clear();
            questionTable.DataTable().rows.add(data).draw();
        };

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

        generateExam = function () {
            var content_height = 884.88;
            var page = 1;   // Página Inicial
            function buildExamLayout() {
                if ($('.exam-questions').contents().length > 0) {
                    $page = $(".exam-page").first().clone().addClass("page").css("display", "block");

                    $page.find(".page-number").first().append(page);
                    $(".print-page").append($page);
                    page++;

                    $('.exam-questions').columnize({
                        columns: 2,
                        target: ".page:last .exam-content",
                        overflow: {
                            height: content_height,
                            id: ".exam-questions",
                            doneFunc: function () {
                                buildExamLayout();
                            }
                        }
                    });
                }
            }
            buildExamLayout();
        };

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
                return;
            }
        };

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