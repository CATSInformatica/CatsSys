/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'mathjax'], function () {
    var question = (function () {

        var questionTable = $('#question-table');

        initDataTable = function () {
            questionTable.DataTable({
                dom: 'lftip',
                ajax: {
                    url: "/school-management/school-exam/get-questions",
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
                                0: data[i].questionId,
                                1: data[i].questionEnunciation,
                                2: data[i].questionAnswersStr
                            });
                        }

                        return questions;
                    }
                }
            });
            $('button[name=submit]').click(function () {
                questionTable.DataTable().ajax.reload();
            });
        };

        return {
            init: function () {
                initDataTable();
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