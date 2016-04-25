/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'chart'], function () {
    var index = (function () {

        initGraph = function () {
            var options = {
                bezierCurve: false,
                responsive: true,
            };
            var monthsCtx = $("#months-graph").get(0).getContext("2d");
            var monthsExpensesChart = null;
            var yearsExpensesChart = null;
            var allTimeExpensesChart = null;
            $("#all-time-graph").hide();
            $("#years-graph").hide();

            var graphColor = {
                dataset: [
                    {
                        fillColor: "rgba(202, 77, 77, 0.2)",
                        strokeColor: "rgba(202, 77, 77, 1)",
                        pointColor: "rgba(202, 77, 77, 1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(202, 77, 77, 1)"
                    },
                    {
                        fillColor: "rgba(151, 187, 205, 0.2)",
                        strokeColor: "rgba(151, 187, 205, 1)",
                        pointColor: "rgba(151, 187, 205, 1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(151, 187, 205, 1)"
                    }
                ]
            }

            var monthsData = {
                labels: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Augosto", "Setembro", "Outubro", "Dezembro"],
                datasets: [
                    {
                        label: "Expenses throughout the year",
                        fillColor: graphColor.dataset[0].fillColor,
                        strokeColor: graphColor.dataset[0].strokeColor,
                        pointColor: graphColor.dataset[0].pointColor,
                        pointStrokeColor: graphColor.dataset[0].pointStrokeColor,
                        pointHighlightFill: graphColor.dataset[0].pointHighlightFill,
                        pointHighlightStroke: graphColor.dataset[0].pointHighlightStroke,
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                    },
                    {
                        label: "Revenues throughout the year",
                        fillColor: graphColor.dataset[1].fillColor,
                        strokeColor: graphColor.dataset[1].strokeColor,
                        pointColor: graphColor.dataset[1].pointColor,
                        pointStrokeColor: graphColor.dataset[1].pointStrokeColor,
                        pointHighlightFill: graphColor.dataset[1].pointHighlightFill,
                        pointHighlightStroke: graphColor.dataset[1].pointHighlightStroke,
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                    }
                ]
            };
            monthsExpensesChart = new Chart(monthsCtx).Line(monthsData, options);
            $.ajax({
                url: "/financial-management/cash-flow/get-months-cash-flow",
                success: function (data) {
                    var monthExpense = data.monthExpense;
                    var monthRevenue = data.monthRevenue;
                    var monthlyExpense = data.monthlyExpense;
                    var monthlyRevenue = data.monthlyRevenue;
                    var totalExpense = 0;
                    var totalRevenue = 0;
                    for (var i = 0; i < monthExpense.length; ++i) {
                        monthsExpensesChart.datasets[0].points[i].value = monthExpense[i];
                        monthsExpensesChart.datasets[1].points[i].value = monthRevenue[i];
                        totalExpense += monthExpense[i]
                        totalRevenue += monthRevenue[i];
                        $("#monthly-revenue ul").append('<li class="list-group-item">' + monthlyRevenue['description'] + '</li>');
                        $("#monthly-expense ul").append('<li class="list-group-item">' + monthlyExpense['description'] + '</li>');
                    }
                    monthsExpensesChart.update();
                    $("#monthly-revenue ul").append('<li class="list-group-item text-right">Total: ' + monthlyRevenue['total'] + '</li>');
                    $("#monthly-expense ul").append('<li class="list-group-item text-right">Total: ' + monthlyExpense['total'] + '</li>');
                    $("#total-expense").text('Despesas totais: R$ ' + totalExpense + ',00');
                    $("#total-revenue").text('Receitas totais: R$ ' + totalRevenue + ',00');
                    $("#cash-in-hand").text('R$ ' + (totalRevenue - totalExpense).toString() + ',00');
                }
            });

            var getGraphData = function (revenue, expense) {
                var expenseData = [];
                var revenueData = [];
                var totalExpense = 0;
                var totalRevenue = 0;
                var labels = Object.keys(expense);
                var totalRange = labels.length;

                for (var i = 0; i < totalRange; ++i) {
                    expenseData[i] = expense[labels[i]];
                    revenueData[i] = revenue[labels[i]];
                    totalExpense += expense[labels[i]];
                    totalRevenue += revenue[labels[i]];
                }
                $("#total-expense").text('Despesas totais: R$ ' + totalExpense + ',00');
                $("#total-revenue").text('Receitas totais: R$ ' + totalRevenue + ',00');

                var data = {};
                data.labels = labels;
                data.expenseData = expenseData;
                data.revenueData = revenueData;
                return data;
            };


            /*
             * Range: 0 ->  Todos os anos
             *        1 ->  
             *        n ->  n anos passados não incluindo o atual 
             *              (atualmente definido como 5 no atributo value de uma option em #graph-time-range)
             */
            $("#graph-time-range").change(function () {
                var range = $("#graph-time-range").val();
                if (range === "1") {
                    $("#years-graph").hide();
                    $("#all-time-graph").hide();
                    $("#months-graph").show();
                } else if (range === "0" && allTimeExpensesChart !== null) {
                    $("#years-graph").hide();
                    $("#months-graph").hide();
                    $("#all-time-graph").show();
                } else if (range !== "0" && yearsExpensesChart !== null) {
                    $("#all-time-graph").hide();
                    $("#months-graph").hide();
                    $("#years-graph").show();
                } else if (!isNaN(parseInt(range))) {
                    $.ajax({
                        url: "/financial-management/cash-flow/get-years-cash-flow/" + range,
                        success: function (data) {
                            var graphData = getGraphData(data.yearRevenue, data.yearExpense);
                            var yearsData = {
                                labels: graphData.labels,
                                datasets: [
                                    {
                                        label: "Expenses throughout the years",
                                        fillColor: graphColor.dataset[0].fillColor,
                                        strokeColor: graphColor.dataset[0].strokeColor,
                                        pointColor: graphColor.dataset[0].pointColor,
                                        pointStrokeColor: graphColor.dataset[0].pointStrokeColor,
                                        pointHighlightFill: graphColor.dataset[0].pointHighlightFill,
                                        pointHighlightStroke: graphColor.dataset[0].pointHighlightStroke,
                                        data: graphData.expenseData
                                    },
                                    {
                                        label: "Revenues throughout the years",
                                        fillColor: graphColor.dataset[1].fillColor,
                                        strokeColor: graphColor.dataset[1].strokeColor,
                                        pointColor: graphColor.dataset[1].pointColor,
                                        pointStrokeColor: graphColor.dataset[1].pointStrokeColor,
                                        pointHighlightFill: graphColor.dataset[1].pointHighlightFill,
                                        pointHighlightStroke: graphColor.dataset[1].pointHighlightStroke,
                                        data: graphData.revenueData
                                    }
                                ]
                            };
                            $("#months-graph").hide();
                            if (range === "0") {
                                $("#years-graph").hide();
                                $("#all-time-graph").show();
                                var allTimeCtx = $("#all-time-graph").get(0).getContext("2d");
                                allTimeExpensesChart = new Chart(allTimeCtx).Line(yearsData, options);
                            } else {
                                $("#all-time-graph").hide();
                                $("#years-graph").show();
                                var yearsCtx = $("#years-graph").get(0).getContext("2d");
                                yearsExpensesChart = new Chart(yearsCtx).Line(yearsData, options);
                            }
                        }
                    });
                }
            });
        };

        return {
            init: function () {
                initGraph();
            }
        };

    }());

    return index;
});