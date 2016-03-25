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

define(['jquery', 'datatable', 'mathjax'], function () {
    var index = (function () {

        var selectedQuestions = [];
        var sQuestions = [];
        var sQuestionsDatatable = [];
        var questionCount = 0;
        var questionTable = $('#question-table');

        var setListeners = function () {
            $('.amount-input').change(function () {
                questionCount = 0;
                $('.amount-input').each(function () {
                    if ($(this).val() !== '') {
                        questionCount += parseInt($(this).val());
                    }
                });
                $('#question-count').html('Total: ' + questionCount);
            });

            $('input[type=checkbox]').change(function () {
                if (this.checked) {
                    alert('checked');
                }
            });

            $('input[type=checkbox]').change(function () {
                console.log('val = ' + $(this).val());
                if ($(this).prop("checked")) {
                    //do the stuff that you would do when 'checked'

                    return;
                }
                //Here do the stuff you want to do when 'unchecked'
            });
        };

        var initDataTable = function () {
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
                        sQuestions[sId] = [];
                        for (var i = 0; i < data.length; ++i) {
                            questions.push({
                                DT_RowAttr: {
                                    "class": "table-row",
                                    "id": "question-" + data[i].questionId,
                                    "data-id": data[i].questionId
                                },
                                0: '<input type="checkbox" value="' + sId + '">',
                                1: data[i].questionEnunciation
                            });
                            sQuestions[sId].push({
                                'enunciation': data[i].questionEnunciation,
                                'alternatives': data[i].questionAnswers
                            });
                        }
                        sQuestionsDatatable[sId] = questions;

                        return questions;
                    }
                }
            });

            var setDatatable = function (data) {
                questionTable.DataTable().clear();
                questionTable.DataTable().rows.add(data).draw();
            };

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