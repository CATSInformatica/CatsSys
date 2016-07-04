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

define(['datatable'], function () {

    var registrationsTable = null;
    var detailContent = null;
    var StudentListModule = (function () {

        initDataTable = function () {
            registrationsTable = $('#student-list-table').DataTable({
                iDisplayLength: 50
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
        /**
         * @Todo 
         *  - Fazer requisição ajax para /recruitment/interview/get-candidate-info
         *  - Exibir de forma bacana as informações do candidato
         *     - Foto?
         *     - Nome, data de nascimento, etc..
         *     - Informações da pré-entrevista organizada em abas
         *          - socioeconômico
         *          - vulnerabilidade
         *          - perfil do estudante
         *     (utilizar como base o CsvViewer)
         *     - Informações da entrevista e pontuação.
         * 
         * @param int registrationId
         * @returns promise
         */
        getDetailsOf = function (registrationId) {

            return $.ajax({
                url: '/recruitment/interview/get-student-info/' + registrationId,
                type: 'GET',
                success: function (response) {
                    console.log(response);
                    console.log(registrationId);
                    detailContent = createContent(response.info);
                },
                error: function (txtStatus) {
                    console.log(txtStatus);
                }
            });
        };

        createContent = function (info) {
            var socioeconomic = '';
            var vulnerability = '';
            var profile = '';
            
            if (info['preInterview'] === null) {
                var socioeconomic = 'O candidato ainda não realizou a pré-entrevista.';
                var vulnerability = 'O candidato ainda não realizou a pré-entrevista.';
                var profile = 'O candidato ainda não realizou a pré-entrevista.';
            } else {
                var table, data; // Variáveis auxiliares

                /* Aba - Socioeconômico */
                // receitas e despesas familiares
                var familyExpenses = info['preInterview']['familyExpenses'];
                var familyIncome = info['preInterview']['familyIncome'];
                var total2 = 0;
                var total1 = 0;
                var smallerArray = Math.min(familyIncome.length, familyExpenses.length);
                data = [];
                var i;
                for (i = 0; i < smallerArray; ++i) {
                    data.push([
                        i + 1,
                        familyIncome[i]['familyIncomeExpDescription'],
                        '<strong class="text-green">' + 
                                familyIncome[i]['familyIncomeExpValue'] + '</strong>',
                        familyExpenses[i]['familyIncomeExpDescription'],
                        '<strong class="text-red">' + 
                                familyExpenses[i]['familyIncomeExpValue'] + '</strong>'
                    ]);
                    total2 += parseFloat(familyIncome[i]['familyIncomeExpValue']);
                    total1 += parseFloat(familyExpenses[i]['familyIncomeExpValue']);
                }
                for (/*i começa de onde parou*/; i < familyIncome.length; ++i) {
                    data.push([
                        i + 1,
                        familyIncome[i]['familyIncomeExpDescription'],
                        familyIncome[i]['familyIncomeExpValue'],
                        '',
                        ''
                    ]);
                    total2 += parseFloat(familyIncome[i]['familyIncomeExpValue']);
                }
                for (/*i começa de onde parou*/; i < familyExpenses.length; ++i) {
                    data.push([
                        i + 1,
                        '',
                        '',
                        familyExpenses[i]['familyIncomeExpDescription'],
                        familyExpenses[i]['familyIncomeExpValue']
                    ]);
                    total1 += parseFloat(familyExpenses[i]['familyIncomeExpValue']);
                }
                // penúltima linha: total de despesas e receitas
                data.push([
                    '<strong>Total</strong>',
                    '',
                    '<strong class="text-green">' + total2 + '</strong>',
                    '',
                    '<strong class="text-red">' + total1 + '</strong>'
                ]);
                table = createTable(['#', 'Receita', 'Valor', 'Despesa', 'Valor'], 
                        data, {text: "Saldo", value: total2 - total1});
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
                        null);
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
                table = createTable(['#', 'Nome', 'Informações'], data, null);
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
                table = createTable(['#', 'Nome', 'Informações'], data, null);
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
                table = createTable(['#', 'Nome', 'Descrição', 'Valor'], data, null);
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
                table = createTable(['#', 'Nome', 'Descrição', 'Endereço'], data, null);
                vulnerability += createBox('Bens imóveis', table, 'box-danger');

                vulnerability += '<div class="box box-danger">' +
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
                '</div>';
        

                /* Aba - Perfil de Estudante */
                profile += '<div class="box box-info">' +
                    '<div class="box-body">' +
                        '<strong>Fez algum curso extraclasse? Se sim, qual(is) curso(s)?</strong><br>' +
                        '<p>' + info['preInterview']['extraCourses'] + '</p>' +
                    '</div>' + 
                    '<div class="box-body">' +
                        '<strong>Já fez curso pré-vestibular? Se sim, qual(is) curso(s) pré-vestibular(es)?</strong><br>' +
                        '<p>' + info['preInterview']['preparationCourse'] + '</p>' +
                    '</div>' + 
                    '<div class="box-body">' +
                        '<strong>Já prestou algum vestibular ou concurso? Se sim, qual(is) vestibular(es)?</strong><br>' +
                        '<p>' + info['preInterview']['entranceExam'] + '</p>' +
                    '</div>' + 
                    '<div class="box-body">' +
                        '<strong>Já ingressou no ensino superior? Se sim, ainda cursa?</strong><br>' +
                        '<p>' + info['preInterview']['undergraduateCourse'] + '</p>' +
                    '</div>' + 
                    '<div class="box-body">' +
                        '<strong>O que espera de nós e o que pretende alcançar caso seja aprovado?</strong><br>' +
                        '<p>' + info['preInterview']['waitingForUs'] + '</p>' +
                    '</div>' + 
                    '<div class="box-body">' +
                        '<strong>Outras Informações</strong><br>' +
                        '<p>' + info['preInterview']['moreInformation'] + '</p>' +
                    '</div>' + 
                '</div>';
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
                        '<div class="box-body box-profile">' +
                            '<img class="profile-user-img img-responsive img-circle" src="/recruitment/registration/photo/' + info['person']['personId'] +'" alt="__NAME__">' +
                            '<h3 class="profile-username text-center"> ' +
                                info['person']['personFirstName'] + ' ' + info['person']['personLastName'] +
                            '</h3>' +
                            '<ul class="list-group list-group-unbordered">' + 
                                '<li class="list-group-item"><strong>Nota</strong>' + 
                                    '<a class="pull-right">' + '+9000' + '</a>' +
                                '</li>' +
                            '</ul>' +
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
                            '<h3 class="box-title"> Composição da Nota </h3>' +
                            '<div class="box-tools pull-right">' + 
                                '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>' +
                                '</button>' + 
                            '</div>' +
                        '</div>' +
                        '<div class="box-body">' +
                            '<table class="table table-condensed">' + 
                                '<tr>' +
                                    '<th>Critério</th>' +
                                    '<th>Nota</th>' +
                                    '<th>Peso</th>' +
                                '</tr>' + 
                                '<tr>' +
                                    '<td>Socioeconômico</td>' +
                                    '<td>' + '' + '</td>' +
                                    '<td>' + ((info['recruitmentTarget']['socioeconomic'] !== null) ? 
                                            info['recruitmentTarget']['socioeconomic'] 
                                            : '') + 
                                    '</td>' +
                                '</tr>' + 
                                '<tr>' +
                                    '<td>Vulnerabilidade</td>' +
                                    '<td>' + '' + '</td>' +
                                    '<td>' + ((info['recruitmentTarget']['vulnerability'] !== null) ? 
                                            info['recruitmentTarget']['vulnerability'] 
                                            : '') +
                                    '</td>' +
                                '</tr>' + 
                                '<tr>' +
                                    '<td>Perfil de Estudante</td>' +
                                    '<td>' + '' + '</td>' +
                                    '<td>' + ((info['recruitmentTarget']['student'] !== null) ?
                                            info['recruitmentTarget']['student']
                                            : '') +
                                    '</td>' +
                                '</tr>' + 
                                '<tr>' +
                                    '<td><strong>Nota final</strong></td>' +
                                    '<td>' + '<strong></strong>' + '</td>' +
                                    '<td></td>' +
                                '</tr>' + 
                            '</table>' +
                        '</div>' +
                    '</div>' +
                    '<div class="nav-tabs-custom">' +
                        '<ul class="nav nav-tabs">' +
                            '<li class="active"><a href="#socioeconomic" data-toggle="tab" aria-expanded="true">Socioeconômico</a></li>' +
                            '<li class=""><a href="#vulnerability" data-toggle="tab" aria-expanded="false">Vulnerabilidade</a></li>' +
                            '<li class=""><a href="#profile" data-toggle="tab" aria-expanded="false">Perfil de Estudante</a></li>' +
                        '</ul>' +
                        '<div class="tab-content">' +
                            '<div class="tab-pane active" id="socioeconomic">' +
                                socioeconomic +
                            '</div>' +
                            '<div class="tab-pane" id="vulnerability">' +
                                vulnerability + 
                            '</div>' +
                            '<div class="tab-pane" id="profile">' +
                                profile + 
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
        createBox = function(boxTitle, boxBody, boxClasses) {
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
         * @param {null ou object} lastLine - Usado para inserir uma última linha do tipo: 
         *      Texto: valor 
         *      lastLine = {
         *          text: "Soma"
         *          value: 50.5
         *      }
         * @returns {String}
         */
        createTable = function(columns, data, lastLine) {
            var table = '<table class="table table-condensed table-hover"><tbody><tr>';
            for (var i = 0; i < columns.length; ++i) {
                table += '<th>' + columns[i] + '</th>';
            }
            table += '</tr>';   
            for (var i = 0; i < data.length; ++i) {
                table += '<tr>';
                for (var j = 0; j < columns.length; ++j) {
                    table += '<td>' + data[i][j] + '</td>';
                }
                table += '</tr>';
            }
            if (lastLine !== null) {
                table += '<tr>';
                for (var j = 0; j < columns.length - 2; ++j) {
                    table += '<td></td>';
                }
                if (columns.length > 1) {
                    table += '<td class="pull-right"><strong>' + lastLine['text'] + ': </strong></td>';
                }
                table += '<td><strong>' + lastLine['value'] + '</strong></td>';
                table += '</tr>';
            }
            table += '</tbody></table>';
            return table;
        };

        return {
            init: function () {
                initDataTable();
                initTableListeners();
            }
        };
    }());
    return StudentListModule;
});

