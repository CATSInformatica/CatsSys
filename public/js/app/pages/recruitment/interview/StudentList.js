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

define(['app/models/CriteriaGrade', 'datatable'], function (CriteriaGrade) {

    var registrationsTable = null;
    var detailContent = null;

    var targetTable = $('#target-table');

    var StudentListModule = (function () {

        initDataTable = function () {

            $('#student-list-table').find('tbody').find('tr').each(function () {
                var t = $(this);
                var soc = t.data('socioeconomic'), socf, vul = t.data('vulnerability'), stf, st = t.data('student'), stf;
                if (soc !== "" && vul !== "" && st !== "") {
                    socf = CriteriaGrade.calcCriteriaGrade(soc, CriteriaGrade.SOCIOECONOMIC);
                    vulf = CriteriaGrade.calcCriteriaGrade(vul, CriteriaGrade.VULNERABILITY);
                    stf = CriteriaGrade.calcCriteriaGrade(st, CriteriaGrade.STUDENT);

                    var result = ((socf + vulf + stf) / 3).toFixed(3);

                    $(this).children("td[id^=grades]").text(result);
                }
            });

            registrationsTable = $('#student-list-table').DataTable({
                iDisplayLength: 50,
                order: [[7, 'desc'], [3, 'asc']]
            });
        };

        /**
         * Exibir mais informações ao clicar na linha de algum aluno.
         * @returns {undefined}
         */
        initTableListeners = function () {

            $('#student-list-table').on("click", "td.details-control", function () {
                var tr = $(this).closest("tr");
                var registrationId = tr.data("id");
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
         * 
         * @param int registrationId - inscrição do candidato
         * @returns promise
         */
        getDetailsOf = function (registrationId) {

            return $.ajax({
                url: '/recruitment/interview/get-student-info/' + registrationId,
                type: 'GET',
                success: function (response) {
                    detailContent = createContent(response.info);
                },
                error: function (txtStatus) {
                    console.log(txtStatus);
                }
            });
        };

        /*
         * Retorna o layout da área de informações sobre o candidato
         * 
         * @param {object} info - Informações sobre o candidato
         * @returns {String}
         */
        createContent = function (info) {
            var socioeconomic = '';
            var vulnerability = '';
            var student = '';

            if (info['preInterview'] === null) {
                var socioeconomic = 'O candidato ainda não realizou a pré-entrevista.';
                var vulnerability = 'O candidato ainda não realizou a pré-entrevista.';
                var student = 'O candidato ainda não realizou a pré-entrevista.';
            } else {
                var table, data; // Variáveis auxiliares

                /* Aba - Socioeconômico */
                // receitas e despesas familiares
                var familyExpenses = info['preInterview']['familyExpenses'];
                var familyIncome = info['preInterview']['familyIncome'];
                var total2 = 0;
                var total1 = 0;
                data = [];
                var i;
                for (i = 0; i < familyIncome.length; ++i) {
                    data.push([
                        i + 1,
                        familyIncome[i]['familyIncomeExpDescription'],
                        '<strong class="text-green">' +
                                familyIncome[i]['familyIncomeExpValue'] +
                                '</strong>'
                    ]);
                    total2 += parseFloat(familyIncome[i]['familyIncomeExpValue']);
                }
                for (i = 0; i < familyExpenses.length; ++i) {
                    data.push([
                        familyIncome.length + i + 1,
                        familyExpenses[i]['familyIncomeExpDescription'],
                        '<strong class="text-red">' +
                                familyExpenses[i]['familyIncomeExpValue'] +
                                '</strong>'
                    ]);
                    total1 += parseFloat(familyExpenses[i]['familyIncomeExpValue']);
                }
                // penúltima linha: total de despesas e receitas
                data.push([
                    '<strong>Total</strong>',
                    '',
                    '<strong><span class="text-green">' + (+total2.toFixed(2)) + '</span> / ' +
                            '<span class="text-red">' + (+total1.toFixed(2)) + '</span></strong>'
                ]);
                // última linha: saldo
                data.push([
                    '<strong>Saldo</strong>',
                    '',
                    '<strong>' + (total2 - total1) + '</strong>'
                ]);
                table = createTable(['#', 'Receita/Despesa', 'Valor'], data, ['text-center', '', 'text-center']);
                socioeconomic += createBox('Receitas e Despesas da família', table, 'box-warning');

                // acesso a bens e serviços (em casa)
                table = createTable(['Item', 'Quantidade'],
                        [
                            ["Tv", info['preInterview']['itemTv']],
                            ["Banheiro", info['preInterview']['itemBathroom']],
                            ["Empregada mensalista", info['preInterview']['itemSalariedHousekeeper']],
                            ["Empregada diarista", info['preInterview']['itemDailyHousekeeper']],
                            ["Máquina de lavar roupa", info['preInterview']['itemWashingMachine']],
                            ["Geladeira", info['preInterview']['itemRefrigerator']],
                            ["TV a cabo", info['preInterview']['itemCableTv']],
                            ["Computador", info['preInterview']['itemComputer']],
                            ["Smartphones", info['preInterview']['itemSmartphone']],
                            ["Quartos", info['preInterview']['itemBedroom']]
                        ],
                        ['', 'text-center']);
                socioeconomic += createBox('Onde você reside existem:', table, 'box-warning');


                // elementos de infraestrutura do local de moradia
                var infrastructureElements = '';
                for (var i = 0; i < info['preInterview']['infrastructureElements'].length; ++i) {
                    infrastructureElements +=
                            info['preInterview']['infrastructureElements'][i]['infrastructureElementDescription'];
                    infrastructureElements += '; ';
                }
                socioeconomic += '<div class="box box-warning">' +
                        '<div class="box-body">' +
                        '<strong>Você mora:</strong><br>' +
                        info['preInterview']['live'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Quem é(são) o(os) responsável(is) pela manutenção financeira do grupo familiar?</strong><br>' +
                        info['preInterview']['responsibleFinancial'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>A casa onde mora tem:</strong><br>' +
                        infrastructureElements +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Você reside em:</strong><br>' +
                        info['preInterview']['liveArea'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Justificativa para a nota no critério Socioeconômico</strong><br>' +
                        ((info['studentInterview'] !== null) ?
                                info['studentInterview']['interviewerSocioecGradeJustification'] :
                                'O candidato ainda não realizou a entrevista.') +
                        '</div>' +
                        '</div>';


                /* Aba - Vulnerabilidade */
                // membros da família
                var familyMembers = info['preInterview']['familyMembers'];
                data = [];
                for (var i = 0; i < familyMembers.length; ++i) {
                    data.push([
                        i + 1,
                        familyMembers[i]['candidateFamilyName'],
                        '<strong>Idade:</strong> ' + familyMembers[i]['candidateFamilyAge'] + '<br>' +
                                '<strong>Parentesco:</strong> ' + familyMembers[i]['relationship'] + '<br>' +
                                '<strong>Situação de trabalho:</strong> ' + familyMembers[i]['workSituation'] + '<br>' +
                                '<strong>Estado civil:</strong> ' + familyMembers[i]['maritalStatus'] + '<br>' +
                                '<strong>Escolaridade:</strong> ' + familyMembers[i]['scholarity']
                    ]);
                }
                table = createTable(['#', 'Nome', 'Informações'], data, ['text-center', 'text-center', '']);
                vulnerability += createBox('Membros da família', table, 'box-danger');

                // saúde dos familiares
                var familyHealth = info['preInterview']['familyHealth'];
                data = [];
                for (var i = 0; i < familyHealth.length; ++i) {
                    data.push([
                        i + 1,
                        familyHealth[i]['familyHealthName'],
                        '<strong>Problema de saúde:</strong> ' +
                                familyHealth[i]['healthProblem'] + '<br>' +
                                '<strong>Impede a pessoa de trabalhar?:</strong> ' +
                                ((familyHealth[i]['disableForWork']) ? "Sim" : "Não") + '<br>' +
                                '<strong>A pessoa precisa de acompanhamento diário?:</strong> ' +
                                ((familyHealth[i]['dailyDependency']) ? "Sim" : "Não")
                    ]);
                }
                table = createTable(['#', 'Nome', 'Informações'], data, ['text-center', 'text-center', '']);
                vulnerability += createBox('Problemas de saúde de membros da família', table, 'box-danger');

                // bens móveis
                var familyGoods = info['preInterview']['familyGoods'];
                data = [];
                for (var i = 0; i < familyGoods.length; ++i) {
                    data.push([
                        i + 1,
                        familyGoods[i]['goodName'],
                        familyGoods[i]['goodDescription'],
                        familyGoods[i]['goodValue']
                    ]);
                }
                table = createTable(['#', 'Nome', 'Descrição', 'Valor'], data, ['text-center', 'text-center', '', 'text-center']);
                vulnerability += createBox('Bens móveis', table, 'box-danger');

                // bens imóveis
                var familyProperties = info['preInterview']['familyProperties'];
                data = [];
                for (var i = 0; i < familyProperties.length; ++i) {
                    data.push([
                        i + 1,
                        familyProperties[i]['propertyName'],
                        familyProperties[i]['propertyDescription'],
                        familyProperties[i]['propertyAddress']
                    ]);
                }
                table = createTable(['#', 'Nome', 'Descrição', 'Endereço'], data, ['text-center', 'text-center', 'Descrição', 'Endereço']);
                vulnerability += createBox('Bens imóveis', table, 'box-danger');

                vulnerability += '<div class="box box-danger">' +
                        '<div class="box-body">' +
                        '<strong>Você considera sua família:</strong><br>' +
                        info['preInterview']['familyEthnicity'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>A Instituição de ensino na qual cursou o ensino fundamental é?</strong><br>' +
                        info['preInterview']['elementarySchoolType'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Você cursou/cursa o ensino médio em escola(s):</strong><br>' +
                        info['preInterview']['highSchoolType'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Ano de ingresso no ensino médio?</strong><br>' +
                        info['preInterview']['highSchoolAdmissionYear'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Ano de conclusão/previsão de conclusão do ensino médio?</strong><br>' +
                        info['preInterview']['highSchoolConclusionYear'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Tem irmãos que cursaram/cursam o ensino superior?</strong><br>' +
                        ((info['preInterview']['siblingsUndergraduate']) ? "Sim" : "Não") +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Fala algum idioma estrangeiro? Se sim, como estudou?</strong><br>' +
                        info['preInterview']['otherLanguages'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Imovel em que reside é?</strong><br>' +
                        info['preInterview']['homeStatus'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Marque a característica que melhor descreve a sua casa?</strong><br>' +
                        info['preInterview']['homeDescription'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Transporte utilizado para comparecer às aulas:</strong><br>' +
                        info['preInterview']['transport'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Justificativa para a nota no critério Vulnerabilidade</strong><br>' +
                        ((info['studentInterview'] !== null) ?
                                info['studentInterview']['interviewerVulnerabilityGradeJustification'] :
                                'O candidato ainda não realizou a entrevista.') +
                        '</div>' +
                        '</div>';

                /* Aba - Perfil de Estudante */
                student += '<div class="box box-info">' +
                        '<div class="box-body">' +
                        '<strong>Fez algum curso extraclasse? Se sim, qual(is) curso(s)?</strong><br>' +
                        info['preInterview']['extraCourses'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Já fez curso pré-vestibular? Se sim, qual(is) curso(s) pré-vestibular(es)?</strong><br>' +
                        info['preInterview']['preparationCourse'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Já prestou algum vestibular ou concurso? Se sim, qual(is) vestibular(es)?</strong><br>' +
                        info['preInterview']['entranceExam'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Já ingressou no ensino superior? Se sim, ainda cursa?</strong><br>' +
                        info['preInterview']['undergraduateCourse'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>O que espera de nós e o que pretende alcançar caso seja aprovado?</strong><br>' +
                        info['preInterview']['waitingForUs'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Outras Informações</strong><br>' +
                        info['preInterview']['moreInformation'] +
                        '</div>' +
                        '<div class="box-body">' +
                        '<strong>Justificativa para a nota no critério perfil de estudante</strong><br>' +
                        ((info['studentInterview'] !== null) ?
                                (info['studentInterview']['interviewerStudentGradeJustification']) :
                                'O candidato ainda não realizou a entrevista.') +
                        '</div>' +
                        '</div>';
            }

            var socioeconomicGrade = '-', socioeconomicFinalGrade = '-';
            var vulnerabilityGrade = '-', vulnerabilityFinalGrade = '-';
            var studentGrade = '-', studentFinalGrade = '-';
            var finalGrade = '-';
            // Mostra as notas se o candidato tiver feito a entrevista
            if (info['studentInterview'] !== null) {
                socioeconomicGrade = +(info['studentInterview']['interviewSocioeconomicGrade']);
                socioeconomicFinalGrade = CriteriaGrade.calcCriteriaGrade(socioeconomicGrade, CriteriaGrade.SOCIOECONOMIC);
                vulnerabilityGrade = +(info['studentInterview']['interviewVulnerabilityGrade']);
                vulnerabilityFinalGrade = CriteriaGrade.calcCriteriaGrade(vulnerabilityGrade, CriteriaGrade.VULNERABILITY);
                studentGrade = +(info['studentInterview']['interviewStudentGrade']);
                studentFinalGrade = CriteriaGrade.calcCriteriaGrade(studentGrade, CriteriaGrade.STUDENT);
                finalGrade = ((socioeconomicFinalGrade + vulnerabilityFinalGrade + studentFinalGrade) / 3).toFixed(3);

                socioeconomicGrade = socioeconomicGrade.toFixed(3);
                socioeconomicFinalGrade = socioeconomicFinalGrade.toFixed(3);
                vulnerabilityGrade = vulnerabilityGrade.toFixed(3);
                vulnerabilityFinalGrade = vulnerabilityFinalGrade.toFixed(3);
                studentGrade = studentGrade.toFixed(3);
                studentFinalGrade = studentFinalGrade.toFixed(3);
            }

            var addresses = '';
            for (var i = 0; i < info['person']['addresses'].length; ++i) {
                addresses += info['person']['addresses'][i]['addressStreet'] + ', ' +
                        ((info['person']['addresses'][i]['addressNumber'] === null) ? 'S/N' : info['person']['addresses'][i]['addressNumber']) + ' - ' +
                        info['person']['addresses'][i]['addressNeighborhood'] + ' - ' +
                        info['person']['addresses'][i]['addressCity'] + ' - ' +
                        info['person']['addresses'][i]['addressState'] + ', CEP: ' +
                        info['person']['addresses'][i]['addressPostalCode'] + '<br>';
            }


            return '<div class="row">' +
                    '<div class="col-md-3">' +
                    '<div class="box box-primary">' +
                    '<div class="box-body box-student">' +
                    '<img class="student-user-img img-responsive img-circle center-block" src="/recruitment/registration/photo/' + info['person']['personId'] + '" alt="__NAME__">' +
                    '<h3 class="student-username text-center"> ' +
                    info['person']['personFirstName'] + ' ' + info['person']['personLastName'] +
                    '</h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="box box-primary">' +
                    '<div class="box-header with-border">' +
                    '<h3 class="box-title"> Sobre Mim </h3>' +
                    '</div>' +
                    '<div class="box-body">' +
                    '<strong><i class="fa fa-birthday-cake margin-r-5"></i> Data de Nascimento</strong>' +
                    '<p class="text-muted">' + info['person']['personBirthday'] + '</p>' +
                    '<strong><i class="fa fa-phone margin-r-5"></i> Telefone</strong>' +
                    '<p class="text-muted">' + info['person']['personPhone'] + '</p>' +
                    '<strong><i class="fa fa-at margin-r-5"></i> Email</strong>' +
                    '<p class="text-muted">' + info['person']['personEmail'] + '</p>' +
                    '<strong><i class="fa fa-map-marker margin-r-5"></i> Endereço</strong>' +
                    '<p class="text-muted">' + addresses + '</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-9">' +
                    '<div class="box box-primary">' +
                    '<div class="box-header with-border">' +
                    '<h3 class="box-title"> Nota </h3>' +
                    '<div class="box-tools pull-right">' +
                    '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '<div class="box-body">' +
                    '<table class="table table-condensed">' +
                    '<tr>' +
                    '<th class="text-center">Critério</th>' +
                    '<th class="text-center">Nota Obtida</th>' +
                    '<th class="text-center">Nota Calculada</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Socioeconômico</td>' +
                    '<td id="se-grade-' + info['registrationId'] + '" class="text-center">' + socioeconomicGrade + '</td>' +
                    '<td id="se-fgrade-' + info['registrationId'] + '" class="text-center">' + socioeconomicFinalGrade + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Vulnerabilidade</td>' +
                    '<td id="v-grade-' + info['registrationId'] + '" class="text-center">' + vulnerabilityGrade + '</td>' +
                    '<td id="v-fgrade-' + info['registrationId'] + '" class="text-center">' + vulnerabilityFinalGrade + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Perfil de Estudante</td>' +
                    '<td id="s-grade-' + info['registrationId'] + '" class="text-center">' + studentGrade + '</td>' +
                    '<td id="s-fgrade-' + info['registrationId'] + '" class="text-center">' + studentFinalGrade + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>Nota final</strong></td>' +
                    '<td colspan="2" class="text-center"><strong id="final-grade-' + info['registrationId'] + '">' + finalGrade + '</strong></td>' +
                    '</tr>' +
                    '</table>' +
                    '</div>' +
                    '</div>' +
                    '<div class="nav-tabs-custom">' +
                    '<ul class="nav nav-tabs">' +
                    '<li class="active"><a href="#socioeconomic" data-toggle="tab" aria-expanded="true">Socioeconômico</a></li>' +
                    '<li class=""><a href="#vulnerability" data-toggle="tab" aria-expanded="false">Vulnerabilidade</a></li>' +
                    '<li class=""><a href="#student" data-toggle="tab" aria-expanded="false">Perfil de Estudante</a></li>' +
                    '</ul>' +
                    '<div class="tab-content">' +
                    '<div class="tab-pane active" id="socioeconomic">' +
                    socioeconomic +
                    '</div>' +
                    '<div class="tab-pane" id="vulnerability">' +
                    vulnerability +
                    '</div>' +
                    '<div class="tab-pane" id="student">' +
                    student +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
        };

        /*
         * Retorna o código HTML de uma caixa estilizada com o conteúdo passado por parâmetro
         *  
         * @param {String} boxTitle - título da caixa
         * @param {String} boxBody - html do conteúdo da caixa
         * @param {String} boxClasses - classes da tabela da caixa
         * @returns {String}
         */
        createBox = function (boxTitle, boxBody, boxClasses) {
            return '<div class="box collapsed-box ' + boxClasses + '">' +
                    '<div class="box-header">' +
                    '<h3 class="box-title">' + boxTitle + '</h3>' +
                    '<div class="box-tools pull-right">' +
                    '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>' +
                    '</div>' +
                    '</div>' +
                    '<div class="box-body">' +
                    boxBody +
                    '</div>' +
                    '</div>';
        };

        /*
         * Retorna o código HTML de uma tabela com os dados passados por parâmetro       
         * 
         * @param {array} columns - array com os títulos das colunas. 
         *      O tamanho desse array define o número de colunas da tabela
         *      columns = [titulo-coluna1, titulo-coluna2, titulo-coluna3]
         * @param {array} data - array de arrays que contêm o conteúdo da tabela
         *      data = [
         *          [linha1-coluna1, linha1-coluna2, linha1-coluna3], 
         *          ...
         *          [linhaN-coluna1, linhaN-coluna2, linhaN-coluna3]
         *      ]
         * @param {array} columnCellClasses - array com as classes que devem ser 
         *      adicionadas as células de cada coluna
         *      columnCellClasses = [
         *          [classes-coluna1, classes-coluna2, classes-coluna3]
         *      ]
         * @returns {String}
         */
        createTable = function (columns, data, columnCellClasses) {
            var cellClasses = [];
            if (!columnCellClasses) {
                for (var i = 0; i < columns.length; ++i) {
                    cellClasses[i] = '';
                }
            } else {
                cellClasses = columnCellClasses;
            }

            var table = '<table class="table table-condensed table-hover"><tbody><tr>';
            for (var i = 0; i < columns.length; ++i) {
                table += '<th class="text-center">' + columns[i] + '</th>';
            }
            table += '</tr>';
            for (var i = 0; i < data.length; ++i) {
                table += '<tr>';
                for (var j = 0; j < columns.length; ++j) {
                    table += '<td class="' + cellClasses[j] + '">' + data[i][j] + '</td>';
                }
                table += '</tr>';
            }
            table += '</tbody></table>';
            return table;
        };

        /*
         * Aguarda uma mudança no registro de entrevistas, no localStorage do navegador, e 
         * então atualiza a nota do candidato e reordena a tabela. 
         * A mudança ocorre quando uma entrevista acaba de ser concluída.
         * 
         */
        interviewLogListener = function () {

            var table = $("#student-list-table");

            window.addEventListener('storage', function (e) {

                setTimeout(function () {
                    if (e.newValue !== null) { // e.newValue - registrationId
                        getCandidateGrades(e.newValue, function (grades) {
                            var finalGrade = (grades.socioeconomicFinal + grades.vulnerabilityFinal + grades.studentFinal) / 3;
                            // Se a área com detalhes sobre o candidato estiver aberta, atualiza lá também
                            if (table.find('#final-grade-' + (e.newValue)).length > 0) {

                                table.find('#se-grade-' + (e.newValue)).html((+grades.socioeconomic).toFixed(3));
                                table.find('#se-fgrade-' + (e.newValue)).html(grades.socioeconomicFinal.toFixed(3));

                                table.find('#v-grade-' + (e.newValue)).html((+grades.vulnerability).toFixed(3));
                                table.find('#v-fgrade-' + (e.newValue)).html(grades.vulnerabilityFinal.toFixed(3));

                                table.find('#s-grade-' + (e.newValue)).html((+grades.student).toFixed(3));
                                table.find('#s-fgrade-' + (e.newValue)).html(grades.studentFinal.toFixed(3));

                                table.find('#final-grade-' + (e.newValue)).html(finalGrade.toFixed(3));
                            }

                            $('#student-list-table').DataTable().cell($('#grades-' + (e.newValue))).data(finalGrade.toFixed(3));
                            $('#student-list-table').DataTable().order([5, "desc"]).draw();
                        });
                    }
                }, 2000);

            });
        };

        /*
         * Retorna, através de um callback, as notas do candidato de interesse, 
         * identificado pela inscrição passada por parâmetro.
         * 
         * @param int registrationId - inscrição do candidato
         * @param function callback - função que utiliza as notas retornadas
         */
        getCandidateGrades = function (registrationId, callback) {
            $.ajax({
                url: '/recruitment/interview/get-student-grades/' + registrationId,
                type: 'GET',
                success: function (response) {
                    var grades = response.grades;
                    grades.socioeconomicFinal = CriteriaGrade.calcCriteriaGrade(grades.socioeconomic, CriteriaGrade.SOCIOECONOMIC);
                    grades.vulnerabilityFinal = CriteriaGrade.calcCriteriaGrade(grades.vulnerability, CriteriaGrade.VULNERABILITY);
                    grades.studentFinal = CriteriaGrade.calcCriteriaGrade(grades.student, CriteriaGrade.STUDENT);

                    callback(response.grades);
                },
                error: function (txtStatus) {
                    console.log(txtStatus);
                }
            });
        };

        /**
         * Mantém a sessão para análise de candidatos ativa.
         * 
         * A cada 5 mins uma requisição é enviada ao servidor para manter a
         * sessão ativa.
         * 
         * @returns {undefined}
         */
        keepMeAlive = function () {

            setTimeout(function () {
                $.ajax({
                    url: '/recruitment/interview/keepAlive',
                    type: 'GET',
                    success: function (data) {
                        keepMeAlive();
                    },
                    error: function (texErr) {
                        console.log('Error', texErr);
                    }
                });
            }, 300000);

        };

        return {
            init: function () {
                var socTarget = targetTable.data('socioeconomic');
                var vulTarget = targetTable.data('vulnerability');
                var stTarget = targetTable.data('student');
                CriteriaGrade.init(socTarget, vulTarget, stTarget);
                initDataTable();
                initTableListeners();
                interviewLogListener();
                keepMeAlive();
            }
        };
    }());
    return StudentListModule;
});

