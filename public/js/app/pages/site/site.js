/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['app/pages/administrative-structure/department/departments', 'moment'], function (departments, moment) {

    var recruitmentTypes = {
        VOLUNTEER: 2,
        STUDENT: 1
    };
    var Site = (function () {

        pastExamsAjax = function () {
            //  Carrega os vestibulinhos passados automaticamente por AJAX
            var pastExams = ''; // HTML que será inserido sob #past-exams
            var i = 0;
            $.ajax({
                type: "POST",
                url: "/school-management/study-resources/get-past-exams",
                success: function (data) {
                    for (i = 0; i < data.psa['source'].length; ++i) {
                        pastExams += '<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 catssys-test">'
                                + '<a href="' + data.psa_dir + '/' + data.psa['source'][i] + '" target="_blank">'
                                + '<i class="fa fa-file-text-o"></i><h4>' + data.psa['number'][i]
                                + 'º' + ' PSA ' + data.psa['year'][i];
                        if (data.psa['part'][i] !== "") {
                            pastExams += ' pt. ' + data.psa['part'][i];
                        }
                        pastExams += '</h4></a></div>';
                    }
                    $("#past-exams").html(pastExams);
                }
            });
        };
        /**
         * Busca por processos seletivos abertos ou que estão por abrir nos
         * próximos 5 dias.
         * @returns {undefined}
         */
        recruitmentFinder = function () {

            $.ajax({
                type: "GET",
                url: "/recruitment/recruitment/get-last-opened",
                success: function (data) {

                    if (data.recruitments !== null) {

                        var studentData = data.recruitments.student;
                        var volunteerData = data.recruitments.volunteer;
                        // define a forma como os processos seletivos serão
                        // organizados na tela
                        var studentWrapper = null;
                        var volunteerWrapper = null;
                        var type = 0;
                        if (studentData.content !== null) {
                            studentWrapper = $("#student-recruitment");

                            studentWrapper
                                    .find(".recruitment-period")
                                    .prepend("<b>Inscrições abertas de <br>" +
                                            moment(studentData.content.recruitmentBeginDate.date, "YYYY-MM-DD").format("LL")
                                            + " a " +
                                            moment(studentData.content.recruitmentEndDate.date, "YYYY-MM-DD").format("LL")
                                            + "</b>");

                            studentWrapper
                                    .find(".recruitment-public-notice")
                                    .attr("href", "/recruitment/recruitment/public-notice/" + studentData.content.recruitmentId)
                                    .html(studentData.content.recruitmentNumber + "º " +
                                            "Processo Seletivo de Alunos " + studentData.content.recruitmentYear + " (<i class='fa fa-file-pdf-o'></i>)");
                            studentWrapper
                                    .find(".recruitment-form-wrapper-closed")
                                    .html("O formulário de inscrição estará disponível no período de " +
                                            moment(studentData.content.recruitmentBeginDate.date, "YYYY-MM-DD").format("L") + " a " +
                                            moment(studentData.content.recruitmentEndDate.date, "YYYY-MM-DD").format("L")
                                            );
                            if (studentData.offset) {
                                studentWrapper
                                        .find(".recruitment-form-wrapper-closed").show();
                            } else {
                                
                                if(studentData.showSubscriptionLink) {
                                    studentWrapper
                                        .find(".recruitment-form-wrapper-opened").show();
                                }
                                
                                studentWrapper
                                        .find(".recruitment-form-wrapper-candidate").show();
                                
                                
                            }

                            type += recruitmentTypes.STUDENT;
                        }

                        if (volunteerData.content !== null) {
                            volunteerWrapper = $("#volunteer-recruitment");

                            volunteerWrapper
                                    .find(".recruitment-period")
                                    .prepend("<b>Inscrições abertas de <br>" +
                                            moment(volunteerData.content.recruitmentBeginDate.date, "YYYY-MM-DD").format("LL")
                                            + " a " +
                                            moment(volunteerData.content.recruitmentEndDate.date, "YYYY-MM-DD").format("LL")
                                            + "</b>");


                            volunteerWrapper
                                    .find(".recruitment-public-notice")
                                    .attr("href", "/recruitment/recruitment/public-notice/" + volunteerData.content.recruitmentId)
                                    .html(volunteerData.content.recruitmentNumber + "º " +
                                            "Processo Seletivo de Voluntários " + volunteerData.content.recruitmentYear + " (<i class='fa fa-file-pdf-o'></i>)");
                            volunteerWrapper
                                    .find(".recruitment-form-wrapper-closed")
                                    .html("O formulário de inscrição estará disponível no período de " +
                                            moment(volunteerData.content.recruitmentBeginDate.date, "YYYY-MM-DD").format("L") + " a " +
                                            moment(volunteerData.content.recruitmentEndDate.date, "YYYY-MM-DD").format("L")
                                            );
                            if (volunteerData.offset) {
                                volunteerWrapper
                                        .find(".recruitment-form-wrapper-closed").show();
                            } else {
                                volunteerWrapper
                                        .find(".recruitment-form-wrapper-opened").show();
                            }

                            type += recruitmentTypes.VOLUNTEER;
                        }

                        switch (type) {
                            case 1:
                                studentWrapper.addClass("col-lg-12 col-md-12 col-sm-12 col-xs-12");
                                studentWrapper.show();
                                break;
                            case 2:
                                volunteerWrapper.addClass("col-lg-12 col-md-12 col-sm-12 col-xs-12");
                                volunteerWrapper.show();
                                break;
                            case 3:
                                studentWrapper.addClass("col-lg-6 col-md-6 col-md-push-6 col-sm-12 col-xs-12");
                                volunteerWrapper.addClass("col-lg-6 col-md-6 col-md-pull-6 col-sm-12 col-xs-12");
                                studentWrapper.show();
                                volunteerWrapper.show();
                                break;
                        }

                        $("#recruitments").slideDown();
                    }
                }
            });
        };
        return {
            init: function () {
                moment.locale("pt-br");
                pastExamsAjax();
                departments.init();
                recruitmentFinder();
            }
        };
    }());
    return Site;
});
