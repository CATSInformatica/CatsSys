/* 
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
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

define(['app/pages/recruitment/interview/keep-alive', 
    'app/pages/recruitment/interview/interviewers-evaluations', 
    'datatable', 'chart', 'mathjax'], function (keepAlive, interviewersEvaluations) {

    var registrationsTable = null;
    var detailContent = null;

    var volunteerList = (function () {
        
        /**
         * Retorna o argumento de ordenação da tabela de candidatos
         * 
         * @returns {Array}
         *  array do tipo:
         *      [
         *          [<column-number>, <ordering-mode>],
         *          .
         *          .
         *          .
         *      ]
         *      
         *  <ordering-mode> pode ser 'asc' ou 'desc'
         */
        getOrdering = function () {
            return [
                [6, 'asc'],     // cargo desejado
                [9, 'desc'],    // nota final
                [5, 'desc'],    // situação
                [1, 'asc']      // número de inscrição
            ];
        };
        
        /**
         * Inicializa a tabela de candidatos
         * 
         */
        initDataTable = function () {
            registrationsTable = $('#volunteer-list-table').DataTable({
                iDisplayLength: 50,
                order: getOrdering()
            });
        };
        
        /*
         * Busca as informações de um candidato e cria um layout para exibi-las.
         * Salva o layout com as informações na variável global detailContent.
         * 
         * @param int registrationId - inscrição do candidato
         * @returns promise
         */
        getDetailsOf = function (registrationId) {

            return $.ajax({
                url: '/recruitment/interview/get-volunteer-info/' + registrationId,
                type: 'GET',
                success: function (response) {
                    detailContent = createContent(response.info);
                },
                error: function (txtStatus) {
                    console.log(txtStatus);
                }
            });
        };

        /**
         * Exibir mais informações ao clicar na linha de algum candidato.
         * 
         */
        initTableListeners = function () {
            $('#job-select').on('change', function () {
                var selectedJobId = +$(this).val();
                if (selectedJobId > 0) {
                    $('#volunteer-list-table tr').hide();
                    $('.job-' + selectedJobId).show();
                } else {
                    $('#volunteer-list-table tr').show();                    
                }
            });
            
            $('#volunteer-list-table').on("click", "td.details-control", function () {
                var tr = $(this).closest("tr");
                var registrationId = +tr.data("id");
                var row = registrationsTable.row(tr);
                if (row.child.isShown()) {
                    tr.removeClass("details");
                    row.child.hide();
                } else {
                    tr.addClass("details");
                    detailContent = getSpinner();
                    row.child(detailContent).show();
                    getDetailsOf(registrationId).then(function () {
                        row.child(detailContent).show();
                    });
                }
            });
        };
        
//        /**
//         * Exibe um gráfico do tipo radar para representar a autoavaliação feita
//         * pelo candidato no momento da inscrição
//         * 
//         * @param {DOM Element} ctx - contexto onde o gráfico será exibido (canvas)
//         */
//        initSelfEvaluationChart = function (ctx, data) {
//            var chart = new Chart(ctx, {
//                type: 'radar',
//                data: {
//                    labels: [
//                        "Responsabilidade", 
//                        "Proatividade", 
//                        "Espírito Voluntário", 
//                        "Comprometimento", 
//                        "Trabalho em Grupo", 
//                        "Eficiência", 
//                        "Cortesia"
//                    ],
//                    datasets: [{
//                        label: 'Avaliação do candidato',
//                        data: data,
//                        backgroundColor: [
//                            'rgba(255, 99, 132, 0.2)',
//                            'rgba(54, 162, 235, 0.2)',
//                            'rgba(255, 206, 86, 0.2)',
//                            'rgba(75, 192, 192, 0.2)',
//                            'rgba(153, 102, 255, 0.2)',
//                            'rgba(255, 159, 64, 0.2)'
//                        ],
//                        borderColor: [
//                            'rgba(255,99,132,1)',
//                            'rgba(54, 162, 235, 1)',
//                            'rgba(255, 206, 86, 1)',
//                            'rgba(75, 192, 192, 1)',
//                            'rgba(153, 102, 255, 1)',
//                            'rgba(255, 159, 64, 1)'
//                        ],
//                        borderWidth: 2
//                    }]
//                },
//                options: {
//                    scale: {
//                        ticks: {
//                            min: 0,
//                            suggestedMax: 5,
//                            stepSize: 1
//                        }
//                    }
//                }
//                        
//            });
//        };

        /**
         * Mostra um icone de carregamento.
         * 
         * @returns {String}
         */
        getSpinner = function () {
            return '<p class="text-center">' +
                    '<i class="fa fa-refresh fa-spin fa-4x"></i></p>';
        };

        /*
         * Retorna o layout da área de informações sobre o candidato
         * 
         * @param {object} info - Informações sobre o candidato
         * @returns {String}
         */
        createContent = function (info) {
            var addresses = '';
            for (var i = 0; i < info['person']['addresses'].length; ++i) {
                addresses += info['person']['addresses'][i]['addressStreet'] + ', ' +
                        ((info['person']['addresses'][i]['addressNumber'] === null) ? 'S/N' : info['person']['addresses'][i]['addressNumber']) + ' - ' +
                        info['person']['addresses'][i]['addressNeighborhood'] + ' - ' +
                        info['person']['addresses'][i]['addressCity'] + ' - ' +
                        info['person']['addresses'][i]['addressState'] + ', CEP: ' +
                        info['person']['addresses'][i]['addressPostalCode'];
            }
            
            // Template da área de informações
            var content = $('#template-container > .candidate-info').clone();
            
            // Insere o link para a foto
            content.find('.candidate-img').first()
                    .attr('src', '/recruitment/registration/photo/' + 
                    info['person']['personId']);
            
            // Insere o nome
            content.find('.candidate-name').first()
                    .text(info['person']['personFirstName'] + ' ' + 
                    info['person']['personLastName']);
            
            // Insere a data de nascimento
            content.find('.candidate-birthday').first()
                    .text(info['person']['personBirthday']);
            
            // Insere o telefone
            content.find('.candidate-phone').first()
                    .text(info['person']['personPhone']);
            
            // Insere o email
            content.find('.candidate-email').first()
                    .text(info['person']['personEmail']);
            
            // Insere os endereços
            content.find('.candidate-address').first()
                    .text(addresses);
            
            if (info.volunteerInterview !== null) {
                
                if (info.volunteerInterview.interviewersEvaluations) {
                    var evaluations = info.volunteerInterview.interviewersEvaluations;
                    var tabContentTemplate = $('#template-container > .rating-tabs-content').first();
                    var finalRatings = [];

                    for (var i = 0; i < evaluations.length; ++i) {
                        var tabContent = tabContentTemplate.clone();
                        tabContent.find('.evaluation-criteria-1').first()
                                .text(evaluations[i].volunteerProfileRating);
                        tabContent.find('.evaluation-criteria-1-comments').first()
                                .text(evaluations[i].volunteerProfile);

                        tabContent.find('.evaluation-criteria-2').first()
                                .text(evaluations[i].volunteerAvailabilityRating);
                        tabContent.find('.evaluation-criteria-2-comments').first()
                                .text(evaluations[i].volunteerAvailability);

                        tabContent.find('.evaluation-criteria-3').first()
                                .text(evaluations[i].volunteerResponsabilityAndCommitmentRating);
                        tabContent.find('.evaluation-criteria-3-comments').first()
                                .text(evaluations[i].volunteerResponsabilityAndCommitment);

                        tabContent.find('.evaluation-overall-rating').first()
                                .text(evaluations[i].volunteerOverallRating);
                        tabContent.find('.evaluation-overall-rating-comments').first()
                                .text(evaluations[i].volunteerOverallRemarks);

                        tabContent.find('.evaluation-final-rating').first()
                                .text(evaluations[i].volunteerFinalRating);

                        finalRatings.push(evaluations[i].volunteerFinalRating);

                        var tab = createTab(
                            evaluations[i].interviewerName, 
                            'evaluation-' + evaluations[i].interviewerEvaluationId,
                            tabContent,
                            i === 0
                        );

                        content.find('.rating-nav-tabs').append(tab.nav); 
                        content.find('.rating-content-tabs').append(tab.content);               
                    }
                        
                    content.find('.candidate-final-rating').first()
                            .text(interviewersEvaluations.getFinalRating(finalRatings)); 
                } else {
                    content.find('.candidate-final-rating').first()
                            .text('-');
                }
                
                /* Aba de perguntas da entrevista */
                var interviewTabContent = '';
                interviewTabContent += createExpandableBox(
                    "Comentários dos entrevistadores",
                    info.volunteerInterview.interviewersInitialComments
                );
                interviewTabContent += createExpandableBox(
                    "O que gosta de fazer nas horas livres?",
                    info.volunteerInterview.interests
                );
                interviewTabContent += createExpandableBox(
                    "Com que tipo de pessoa prefere trabalhar? Com que tipo tem dificuldade?",
                    info.volunteerInterview.interpersonalRelationship
                );
                interviewTabContent += createExpandableBox(
                    "Caso se depare com algo que não concorda, você estuda um jeito de sugerir uma mudança ou tenta se adaptar?",
                    info.volunteerInterview.proactivity
                );
                interviewTabContent += createExpandableBox(
                    "Fale uma qualidade. / Como essa qualidade pode ajudar no CATS?",
                    info.volunteerInterview.qualities
                );
                interviewTabContent += createExpandableBox(
                    "Fale um defeito. / Esse defeito poderia atrapalhar de alguma forma o CATS?",
                    info.volunteerInterview.flaws
                );
                interviewTabContent += createExpandableBox(
                    "Se você entrasse no CATS e fosse desligado um mês depois, por qual motivo seria?",
                    info.volunteerInterview.potentialIssues
                );
                interviewTabContent += createExpandableBox(
                    "Se você só tivesse uma escolha, preferiria fazer seu trabalho no horário ou corretamente?",
                    info.volunteerInterview.flexibilityAndResponsability
                );
                interviewTabContent += createExpandableBox(
                    "Caso seja aluno da UNIFEI: Você já cumpriu as horas complementares exigidas pelo seu curso? Se a pessoa dizer que não, perguntar se ela se interessaria pela vaga mesmo que não ganhasse horas complementares.",
                    info.volunteerInterview.coherenceTest
                );
                interviewTabContent += createExpandableBox(
                    "Quanto tempo pretende ficar no CATS?",
                    info.volunteerInterview.expectedContribution
                );
                interviewTabContent += createExpandableBox(
                    "De 0 a 10, quanto você quer entrar no CATS?",
                    info.volunteerInterview.interestRating 
                            + (info.volunteerInterview.interestJustification == null 
                                ? "" 
                                : "<br>Justificativa: " + info.volunteerInterview.interestJustification)
                );
        
                content.find('.interview-tab-content').append(interviewTabContent);
            } else {
                content.find('.interview-tab-content').append('O candidato ainda não realizou a entrevista.');
                content.find('.candidate-final-rating').first()
                        .text('-');
            }
            
            /* Aba de perguntas da entrevista */                
            var registrationTabContent = '';
            registrationTabContent += createExpandableBox(
                "Ocupação (acadêmica e/ou profissional)",
                info.occupation
            );
            registrationTabContent += createExpandableBox(
                "Fez algum curso (técnico, linguas, etc) ? Qual?",
                info.education
            );
            registrationTabContent += createExpandableBox(
                "O que pensa sobre trabalho voluntário? Já fez? Descreva",
                info.volunteerWork
            );
            registrationTabContent += createExpandableBox(
                "Como e quando conheceu o CATS?",
                info.howAndWhenKnowUs
            );
            registrationTabContent += createExpandableBox(
                "Participa de outro projeto de extensão?",
                info.extensionProjects
            );
            registrationTabContent += createExpandableBox(
                "Por que escolheu se inscrever no CATS? Tentou outros projetos?",
                info.whyWorkWithUs
            );
            registrationTabContent += createExpandableBox(
                "O que espera do trabalho voluntário no CATS?",
                info.volunteerWithUs
            );
            content.find('.registration-tab-content').append(registrationTabContent);
            
            content.find('.registration-tab-content')
                    .attr('id', 'registration-tab');
            content.find('.registration-tab-link').
                    attr('href', '#registration-tab');
            
            content.find('.interview-tab-content')
                    .attr('id', 'interview-tab');
            content.find('.interview-tab-link').
                    attr('href', '#interview-tab');
            
//            initSelfEvaluationChart(
//                    $(content.find('canvas.self-evaluation-chart').first()), 
//                    [
//                        info.responsibility, 
//                        info.proactive, 
//                        info.volunteerSpirit,
//                        info.commitment,
//                        info.teamWork,
//                        info.efficiency,
//                        info.courtesy
//                    ]
//            );
            
            return content;
            
            /**
             * Cria uma aba
             * 
             * @param {string} label - Nome da aba
             * @param {integer} tabId - id da div que conterá o conteúdo da aba
             * @param {jQuery Object} content - conteúdo da aba
             * @param {boolean} active - indica se a aba estará selecionada
             * @returns {object} 
             *      {
             *         nav: <string>, // código HTML da aba
             *         content: <string> // código HTML da div com o conteúdo da aba
             *      }
             */
            function createTab(label, tabId, content, active) {
                var activeClass = '';
                if (active) {
                    activeClass = 'active';
                }
                
                return {
                    nav: '<li class="' + activeClass + '"><a href="#' + tabId + '" data-toggle="tab" aria-expanded="false">' + label + '</a></li>',
                    content: '<div class="tab-pane ' + activeClass + '" id="' + tabId + '">' + content.prop('outerHTML') + '</div>'
                };
            }

            /*
             * Retorna o código HTML de uma caixa expansível estilizada com o 
             * conteúdo passado por parâmetro
             *  
             * @param {String} boxTitle - título da caixa
             * @param {String} boxBody - html do conteúdo da caixa
             * @param {String} boxClasses - classes da tabela da caixa
             * @returns {String} - Código HTML da caixa
             */
            function createExpandableBox(boxTitle, boxBody, boxClasses) {
                if (boxClasses === 'undefined' || !boxClasses) {
                    boxClasses = 'box-default';
                }
                
                var box = $('#template-container > .collapsed-box').first().clone();
                box.find('.box-title').html(boxTitle);
                box.find('.box-body').html(boxBody);
                box.addClass(boxClasses);
                
                return box.prop('outerHTML');
            }
        };

        /*
         * Aguarda uma mudança no registro de entrevistas, no localStorage do navegador, e 
         * então atualiza a nota do candidato e reordena a tabela. 
         * A mudança ocorre quando uma entrevista acaba de ser concluída.
         * 
         */
        updateTableCell = function () {

            var table = $("#volunteer-list-table");

            window.addEventListener('storage', function (e) {
                setTimeout(function () {
                    if (e.newValue !== null) { // e.newValue - registrationId
                        interviewersEvaluations.getInterviewersEvaluations(e.newValue, function (response) { 
                            if (response.length === 0) {
                                return;
                            }
                            
                            var finalRatings = [];
                            var interviewersEvaluationsCell = $('#interviewers-evaluations-' + e.newValue);
                            interviewersEvaluationsCell.html('');
                            var interviewerEvaluationTemplate = $('#template-container > .interviewer-evaluation').clone();

                            for (var interviewerName in response) {
                                var interviewerEvaluation = interviewerEvaluationTemplate.clone();
                                finalRatings.push(response[interviewerName].volunteerFinalRating);

                                interviewerEvaluation
                                        .find('.interviewer-name').html(interviewerName);
                                interviewerEvaluation
                                        .find('.interviewer-final-rating')
                                        .html(response[interviewerName].volunteerFinalRating.toFixed(3));                          

                                interviewersEvaluationsCell.append(interviewerEvaluation);
                            }

                            var finalRating = interviewersEvaluations.getFinalRating(finalRatings);
                            $('#volunteer-list-table')
                                    .DataTable()
                                    .cell($('#final-rating-' + (e.newValue)))
                                    .data(finalRating);
                            $('#volunteer-list-table').DataTable()
                                    .order(getOrdering())
                                    .draw();
                        });
                    }
                }, 2000);

            });
        };
        
        initRatingFormula = function () {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, 'rating-formula']);  
        };

        return {
            init: function () {
                initDataTable();
                initTableListeners();
                updateTableCell();
                initRatingFormula();
                keepAlive.init();
            }
        };
    }());
    
    return volunteerList;
});

