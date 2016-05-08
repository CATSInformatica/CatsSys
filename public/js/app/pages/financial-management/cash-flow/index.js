/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['chart', 'jquery', 'datatable'], function () {
    var index = (function () {

        initChart = function () {
            $.ajax({
                method: "GET",
                url: "/financial-management/cash-flow/get-month-balances/12",
                success: function (data) {
                    var monthsName = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
                    
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
                    var lastYearBalance = new Chart(lastYearCtx, {
                        type: 'bar',
                        data: lastYearData
                    });
                }
            });

        };
        return {
            init: function () {
                initChart();
            }
        };
    }());
    return index;
});