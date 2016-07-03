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
                    detailContent = createContent(response.info);
                    console.log(response);
                },
                error: function (txtStatus) {
                    console.log(txtStatus);
                }
            });
        };

        createContent = function (info) {
            var table, data; // Variáveis auxiliares
            var socioeconomic = '';
            var vulnerability = '';
            var profile = '';
            
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
            
            // habitantes da casa
            socioeconomic += createBox('Você mora:', info['preInterview']['live'], 'box-warning');
            // responsáveis financeiros da casa
            socioeconomic += createBox('Quem é(são) o(os) responsável(is) pela manutenção financeira do grupo familiar?', 
                    info['preInterview']['responsibleFinancial'], 'box-warning');
            
            // elementos de infraestrutura do local de moradia
            var infrastructureElements = '';
            for (var i = 0; i < info['preInterview']['infrastructureElements'].length; ++i) {
                infrastructureElements += 
                        info['preInterview']['infrastructureElements'][i]['infrastructureElementDescription'];
                infrastructureElements += '; ';
            }
            socioeconomic += createBox('A casa onde mora tem:', infrastructureElements, 'box-warning');
            
            // perímetro de moradia (zona central, periférica ou rural)
            socioeconomic += createBox('Você reside em:', info['preInterview']['liveArea'], 'box-warning');
                 
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
            
            vulnerability += createBox('A Instituição de ensino na qual cursou o ensino fundamental é?', 
                    info['preInterview']['elementarySchoolType'], 'box-danger');
            vulnerability += createBox('Você cursou/cursa o ensino médio em escola(s):', 
                    info['preInterview']['highSchoolType'], 'box-danger');
            vulnerability += createBox('Ano de ingresso no ensino médio?', 
                    info['preInterview']['highSchoolAdmissionYear'], 'box-danger');
            vulnerability += createBox('Ano de conclusão/previsão de conclusão do ensino médio?', 
                    info['preInterview']['highSchoolConclusionYear'], 'box-danger');
            vulnerability += createBox('Tem irmãos que cursaram/cursam o ensino superior?', 
                    info['preInterview']['siblingsUndergraduate'], 'box-danger');
            vulnerability += createBox('Fala algum idioma estrangeiro? Se sim, como estudou?', 
                    info['preInterview']['otherLanguages'], 'box-danger');
            vulnerability += createBox('Imovel em que reside é?', 
                    info['preInterview']['homeStatus'], 'box-danger');
            vulnerability += createBox('Marque a característica que melhor descreve a sua casa?', 
                    info['preInterview']['homeDescription'], 'box-danger');
            vulnerability += createBox('Transporte utilizado para comparecer às aulas:', 
                    info['preInterview']['transport'], 'box-danger');
            
            
            /* Aba - Perfil de Estudante */
            profile += createBox('Fez algum curso extraclasse? Se sim, qual(is) curso(s)?', 
                    info['preInterview']['extraCourses'], 'box-info');
            profile += createBox('Já fez curso pré-vestibular? Se sim, qual(is) curso(s) pré-vestibular(es)?', 
                    info['preInterview']['preparationCourse'], 'box-info');
            profile += createBox('Já prestou algum vestibular ou concurso? Se sim, qual(is) vestibular(es)?', 
                    info['preInterview']['entranceExam'], 'box-info');
            profile += createBox('Já ingressou no ensino superior? Se sim, ainda cursa?', 
                    info['preInterview']['undergraduateCourse'], 'box-info');
            profile += createBox('O que espera de nós e o que pretende alcançar caso seja aprovado?', 
                    info['preInterview']['waitingForUs'], 'box-info');
            profile += createBox('Outras Informações', 
                    info['preInterview']['moreInformation'], 'box-info');
            
            
            return '<div class="row">' +
                '<div class="col-md-3">' +
                    '<div class="box box-primary">' +
                        '<div class="box-body box-profile">' +
                            '<img class="profile-user-img img-responsive img-circle" src="/img/user.png" alt="__NAME__">' +
                            '<h3 class="profile-username text-center"> ' +
                                info['person']['personFirstName'] + ' ' + info['person']['personLastName'] +
                            '</h3>' +
                            '<p class="text-muted text-center">' + info['person']['personBirthday'] + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<div class="box box-primary">' +
                        '<div class="box-header with-border">' +
                            '<h3 class="box-title"> Sobre Mim </h3>' +
                        '</div>' +
                        '<div class="box-body">' +
                            '<strong> <i class="fa fa-book margin-r-5"></i> Education</strong>' +
                            '<p class = "text-muted">' +
                                'B.S.in Computer Science from the University of Tennessee at Knoxville' +
                            '</p>' +
                            '<hr>' +
                            '<strong><i class="fa fa-map-marker margin-r-5"></i>Location</strong>' +
                            '<p class="text-muted"> Malibu, California </p>' +
                            '<hr>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-9">' +
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

