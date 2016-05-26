/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['chart', 'jquery'], function () {
    var index = (function () {
        //  Mapeamento índice-mês para criar as legendas dos gráficos
        var monthsName = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        //  Gráfico das receitas, despesas, receitas previstas 
        //  e despesas previstas  dos últimos 12 meses
        var lastYearBalance = null;
        //  Gráfico dos fluxos de caixa filtrados pelos parâmetros tipo 
        //  de fluxo de caixa, departamento e período de tempo
        var cashFlowChart = null;

        // Guarda os dados de gráficos já carregados para evitar uma segunda requisição
        var cashFlowChartStorage = {};

        //  Os dados dos gráficos são armazenados no objeto cashFlowChartStorage
        //  utilizando os ids dos filtros. Eles são acessados da seguinte 
        //  forma: cashFlowChartStorage[sCashFlowType][sDepartment][sTimePeriod]
        var sCashFlowType = null;   // Tipo de fluxo de caixa selecionado
        var sDepartment = null;     // Departamento selecionado
        var sTimePeriod = null;     // Período de tempo selecionado

        /**
         * 
         * Define os listeners da página
         */
        setListeners = function () {
            //  Ao mudar qualquer filtro do gráfico cashFlowChart, recarrega-o 
            //  com os novos parâmetros
            $('#cash-flow-type-input,#department-input,#time-period-input').change(function () {
                sCashFlowType = $('#cash-flow-type-input').val();
                sDepartment = $('#department-input').val();
                sTimePeriod = $('#time-period-input').val();
                //  ARRANJOS POSSÍVEIS
                //  [Tipo de fluxo de caixa][Departamento][Período de tempo]
                if (typeof (cashFlowChartStorage) !== 'undefined'
                        && typeof (cashFlowChartStorage[sCashFlowType]) !== 'undefined'
                        && typeof (cashFlowChartStorage[sCashFlowType][sDepartment]) !== 'undefined'
                        && typeof (cashFlowChartStorage[sCashFlowType][sDepartment][sTimePeriod]) !== 'undefined') {
                    var storedData = cashFlowChartStorage[sCashFlowType][sDepartment][sTimePeriod];
                }
                if (typeof (storedData) !== 'undefined') {
                    cashFlowChart = createCashFlowChart(storedData['labels'], 
                    storedData['label'], storedData['data']);
                } else {
                    loadCashFlowChart(sCashFlowType, sDepartment, sTimePeriod);
                }
            });
        };

        createCashFlowChart = function (labels, label, data) {
            var CashFlowCtx = $("#filtered-cash-flows");
            var CashFlowData = {
                labels: labels,
                datasets: [
                    {
                        label: label,
                        backgroundColor: "rgba(0, 128, 255, 0.6)",
                        borderColor: "rgba(0, 128, 255, 1)",
                        borderWidth: 1,
                        hoverBackgroundColor: "rgba(0, 128, 255, 0.8)",
                        hoverBorderColor: "rgba(0, 128, 255, 1)",
                        data: data
                    }
                ]
            };
            if (cashFlowChart !== null) {
                cashFlowChart.destroy();
            }
            return new Chart(CashFlowCtx, {
                type: 'bar',
                data: CashFlowData
            });
        };

        /*
         * 
         * Carrega o gráfico cashFlowChart e guarda seus dados em cashFlowChartStorage.
         * O gráfico exibe as receitas/despesas de acordo com os filtros passados 
         * nos parâmetros
         * 
         * @param {int} cashFlowType - Id do tipo de fluxo de caixa ou:
         *          -1: Todos os tipos de receita
         *          -2: Todos os tipos de despesa
         * @param {int} department - Id do departamento ou 
         *          -1: Todos os departamentos
         * @param {int} timePeriod - Período de tempo, em meses.
         */
        loadCashFlowChart = function (cashFlowType, department, timePeriod) {
            $.ajax({
                method: "POST",
                data: {
                    cashFlowType: cashFlowType,
                    department: department,
                    timePeriod: timePeriod
                },
                url: "/financial-management/cash-flow/get-filtered-cash-flows",
                success: function (data) {
                    var amountData = [];
                    var labels = [];
                    var label = $('#cash-flow-type-input')
                            .children(':selected').text().trim();
                    for (var i = data['beginAt']; i <= 12; ++i) { // i = [1, 12]
                        amountData.push(data['cashFlows'][i]);
                        labels.push(monthsName[i - 1]);
                    }
                    for (var i = 1; i < data['beginAt']; ++i) {
                        amountData.push(data['cashFlows'][i]);
                        labels.push(monthsName[i - 1]);
                    }
                    
                    cashFlowChart = createCashFlowChart(labels, label, amountData);

                    if (typeof (cashFlowChartStorage[cashFlowType]) === 'undefined') {
                        cashFlowChartStorage[cashFlowType] = {};
                    }
                    if (typeof (cashFlowChartStorage[cashFlowType][department]) === 'undefined') {
                        cashFlowChartStorage[cashFlowType][department] = {};
                    }
                    if (typeof (cashFlowChartStorage[cashFlowType][department][timePeriod]) === 'undefined') {
                        cashFlowChartStorage[cashFlowType][department][timePeriod] = {
                            labels: labels,
                            label: label,
                            data: amountData
                        };
                    } else {
                        cashFlowChartStorage[cashFlowType][department][timePeriod].push({
                            labels: labels,
                            label: label,
                            data: amountData
                        });

                    }
                }
            });
        };

        /*
         *         
         * Inicializa os gráficos lastYearBalance e cashFlowChart
         */
        initCharts = function () {
            $.ajax({
                method: "GET",
                url: "/financial-management/cash-flow/get-month-balances/12",
                success: function (data) {
                    var labels = [];
                    for (var i = 0; i < data['monthBalances']['month'].length; ++i) {
                        labels.push(monthsName[data['monthBalances']['month'][i] - 1]);
                    }

                    var lastYearCtx = $("#last-year-balance");
                    var lastYearData = {
                        labels: labels,
                        datasets: [
                            {
                                label: "Receita",
                                backgroundColor: "rgba(0, 128, 255, 0.6)",
                                borderColor: "rgba(0, 128, 255, 1)",
                                borderWidth: 1,
                                hoverBackgroundColor: "rgba(0, 128, 255, 0.8)",
                                hoverBorderColor: "rgba(0, 128, 255, 1)",
                                data: data['monthBalances'].revenue
                            },
                            {
                                label: "Receita Prevista",
                                backgroundColor: "rgba(0, 128, 255, 0.1)",
                                borderColor: "rgba(0, 128, 255, 0.6)",
                                borderWidth: 0.5,
                                data: data['monthBalances'].projectedRevenue
                            },
                            {
                                label: "Despesa",
                                backgroundColor: "rgba(255, 0, 0, 0.6)",
                                borderColor: "rgba(255, 0, 0, 1)",
                                borderWidth: 1,
                                hoverBackgroundColor: "rgba(255, 0, 0, 0.8)",
                                hoverBorderColor: "rgba(255, 0, 0, 1)",
                                data: data['monthBalances'].expense
                            },
                            {
                                label: "Despesa Prevista",
                                backgroundColor: "rgba(255, 0, 0, 0.1)",
                                borderColor: "rgba(255, 0, 0, 0.6)",
                                borderWidth: 0.5,
                                data: data['monthBalances'].projectedExpense
                            }
                        ]
                    };
                    lastYearBalance = new Chart(lastYearCtx, {
                        type: 'bar',
                        data: lastYearData
                    });

                    loadCashFlowChart($('#cash-flow-type-input').val(),
                            $('#department-input').val(), $('#time-period-input').val());
                }
            });
        };
        return {
            init: function () {
                initCharts();
                setListeners();
            }
        };
    }());
    return index;
});