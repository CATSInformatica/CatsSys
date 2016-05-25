/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['chart', 'jquery', 'datatable'], function () {
    var index = (function () {
        var monthsName = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        var lastYearBalance = null;
        var cashFlowChart = null;

        var CashFlowCtx = $("#filtered-cash-flows");
        var cashFlowChartStorage = {};
        var sCashFlowType = null;
        var sDepartment = null;
        var sTimePeriod = null;

        setListeners = function () {
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
                    var CashFlowData = {
                        labels: storedData['labels'],
                        datasets: [
                            {
                                label: storedData['label'],
                                backgroundColor: "rgba(0, 128, 255, 0.6)",
                                borderColor: "rgba(0, 128, 255, 1)",
                                borderWidth: 1,
                                hoverBackgroundColor: "rgba(0, 128, 255, 0.8)",
                                hoverBorderColor: "rgba(0, 128, 255, 1)",
                                data: storedData['data']
                            }
                        ]
                    };
                    if (cashFlowChart !== null) {
                        cashFlowChart.destroy();
                    }
                    cashFlowChart = new Chart(CashFlowCtx, {
                        type: 'bar',
                        data: CashFlowData
                    });
                } else {
                    loadCashFlowChart(sCashFlowType, sDepartment, sTimePeriod);
                }
            });
        };

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
                                data: amountData
                            }
                        ]
                    };
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
                                data: amountData
                            }
                        ]
                    };
                    if (cashFlowChart !== null) {
                        cashFlowChart.destroy();
                    }
                    cashFlowChart = new Chart(CashFlowCtx, {
                        type: 'bar',
                        data: CashFlowData
                    });
                }
            });
        };

        initCharts = function () {
            $.ajax({
                method: "GET",
                url: "/financial-management/cash-flow/get-month-balances/12",
                success: function (data) {
                    var revenueData = data['monthBalances'].revenue;
                    var expenseData = data['monthBalances'].expense;
                    var projectedRevenueData = data['monthBalances'].projectedRevenue;
                    var projectedExpenseData = data['monthBalances'].projectedExpense;

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
                                data: revenueData
                            },
                            {
                                label: "Receita Prevista",
                                backgroundColor: "rgba(0, 128, 255, 0.1)",
                                borderColor: "rgba(0, 128, 255, 0.6)",
                                borderWidth: 0.5,
                                data: projectedRevenueData
                            },
                            {
                                label: "Despesa",
                                backgroundColor: "rgba(255, 0, 0, 0.6)",
                                borderColor: "rgba(255, 0, 0, 1)",
                                borderWidth: 1,
                                hoverBackgroundColor: "rgba(255, 0, 0, 0.8)",
                                hoverBorderColor: "rgba(255, 0, 0, 1)",
                                data: expenseData
                            },
                            {
                                label: "Despesa Prevista",
                                backgroundColor: "rgba(255, 0, 0, 0.1)",
                                borderColor: "rgba(255, 0, 0, 0.6)",
                                borderWidth: 0.5,
                                data: projectedExpenseData
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