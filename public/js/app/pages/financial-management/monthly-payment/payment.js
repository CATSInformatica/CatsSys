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


define(['moment', 'datetimepicker', 'datatable'], function (moment) {
    paymentModule = (function () {

        var PAYMENT_TYPE_TOTAL = 'TOTAL';
        var PAYMENT_TYPE_PARTIAL = 'PARCIAL';
        var PAYMENT_TYPE_FREE = 'ISENTO';
        var NO_PAYMENT = -1;
        var paymentMonth = $("#paymentMonth");
        var students = null;
        var payments = {};
        var selectedMonthPayments;
        var defaultValue;
        var loadedMonths = [];
        var PAID = 'PAGO';
        var WAITING_PAYMENT = 'AGUARDANDO PAGAMENTO';
        /**
         * Calendário para visualização da mesalidades
         * 
         * @param {type} monthElement
         * @returns {undefined}
         */
        initAttendanceMonthpicker = function (monthElement) {

            monthElement.datetimepicker({
                format: 'MMMM',
                inline: true,
                viewMode: 'months',
                locale: 'pt-br',
                useCurrent: false,
                defaultDate: moment("1", "D")
            });
            monthElement.on("dp.change", function (e) {

                var sclass = $("#sclass").val();
                var month = e.date.format("M");
                defaultValue = $("#defaultValue").val();
                if (loadedMonths.indexOf(month) < 0) {
                    $.when(getStudents(sclass), searchPaymentsOfMonth(sclass, month))
                            .then(function () {
                                loadedMonths.push(month);
                                sortPaymentData(month);
                            });
                } else {
                    showTab(month);
                }
            });
            var sclass = $("#sclass").val();
            var month = moment().format("M");
            defaultValue = $("#defaultValue").val();

            $.when(getStudents(sclass), searchPaymentsOfMonth(sclass, month))
                    .then(function () {
                        loadedMonths.push(month);
                        sortPaymentData(month);
                    });

        };
        getStudents = function (id) {
            return $.ajax({
                url: '/school-management/student-class/getStudents',
                type: 'POST',
                data: {
                    id: id
                },
                success: function (data) {
                    students = data.students;
                },
                error: function (textStatus) {
                    students = null;
                    console.log(textStatus);
                }
            });
        };
        /**
         * Busca as mensalidades no mês month da turma sclass.
         * 
         * @param int month
         * @returns {undefined}
         */
        searchPaymentsOfMonth = function (sclass, month) {
            return $.ajax({
                url: "/financial-management/monthly-payment/get-payments",
                type: "POST",
                data: {
                    sclass: sclass,
                    month: month
                },
                success: function (response) {
                    selectedMonthPayments = {};
                    var payments = response.payments;
                    for (var i = 0; i < payments.length; i++) {
                        selectedMonthPayments[payments[i].enrollment_id] = createNewPaymentObject(payments[i]);
                    }
                },
                error: function (textStatus) {
                    selectedMonthPayments = [];
                    console.log(textStatus);
                }
            });
        };
        /**
         * Organiza os nomes dos alunos e seus pagamentos.
         * 
         * @param int month
         * @returns {undefined}
         */
        sortPaymentData = function (month) {
            for (var i = 0; i < students.length; i++) {
                if (typeof payments[students[i].enrollmentId] === "undefined") {

                    payments[students[i].enrollmentId] = {
                        name: students[i].personFirstName + " " +
                                students[i].personLastName,
                        months: {
                            1: null,
                            2: null,
                            3: null,
                            4: null,
                            5: null,
                            6: null,
                            7: null,
                            8: null,
                            9: null,
                            10: null,
                            11: null,
                            12: null
                        }
                    };
                }

                if (typeof selectedMonthPayments[students[i].enrollmentId] !== "undefined") {
                    payments[students[i].enrollmentId].months[month] = selectedMonthPayments[students[i].enrollmentId];
                } else {
                    payments[students[i].enrollmentId].months[month] = createNewPaymentObject(null);
                }
            }
            showOrReplaceMonthTable(month);
        };
        /**
         * Exibe a tabela de mensalidades do mês escolhido.
         * 
         * @param {type} month
         * @returns {undefined}
         */
        showOrReplaceMonthTable = function (month) {

            var table = "<table class='table table-bordered table-condensed table-hover table-striped'><thead><tr>" +
                    "<th class='text-center' style='border-bottom: thin solid #777777;'>Mat.</th>" +
                    "<th class='text-center' style='border-bottom: thin solid #777777;'>Aluno</th>" +
                    "<th class='text-center' style='border-bottom: thin solid #777777;'> Situação </th>" +
                    "<th style='width:60%; border-bottom: thin solid #777777;' class='text-center'>Pagamento</th>" +
                    "</tr></thead></tbody>";
            $("#paymentMonthTables")
                    .find("ul.nav-tabs")
                    .find("li[data-month='" + month + "']")
                    .remove();
            $("#paymentMonthTables")
                    .find("ul.nav-tabs")
                    .append("<li data-month='" + month + "'><a id='nav_" + month + "' href='#month-" +
                            month + "-content' data-toggle='tab'>" +
                            moment(month, "M").format("MMMM") + "</a></li>");
            $("#paymentMonthTables")
                    .find("div.tab-content")
                    .find("#month-" + month + "-content")
                    .remove();
            
            var partial;            
            for (var i in payments) {
                partial = "" + i;
                table += "<tr>" +
                        "<td style='vertical-align:middle; border-bottom: thin solid #777777;' class='text-center'>" +
                        '<div class="checkbox">' +
                        '<label><input type="checkbox" name="student-payments[]" value="' + partial +
                        '" data-month="' + month +
                        '" data-id="' + payments[i].months[month].monthly_payment_id + '"> ' +
                        ("0000" + partial).substring(partial.length) + '</label>' +
                        '</div>'
                        + "</td>" +
                        "<td style='vertical-align:middle; border-bottom: thin solid #777777; class='text-center'>" +
                        payments[i].name
                        + "</td>" +
                        "<td style='vertical-align:middle; border-bottom: thin solid #777777;' class='text-center payment-status'>" +
                        (payments[i].months[month].monthly_payment_id === NO_PAYMENT ? WAITING_PAYMENT : PAID)
                        + "</td>" +
                        "<td style='border-bottom: thin solid #777777;'>" +
                        "<div class='col-sm-6'>" +
                        createPaymentDateInput(month, payments[i].months[month].monthly_payment_date.format("DD/MM/YYYY")) +
                        createPaymentTypeSelect(payments[i].months[month].monthly_payment_type) +
                        createPaymentValueInput(payments[i].months[month].monthly_payment_id, payments[i].months[month].monthly_payment_value) +
                        '</div>' +
                        "<div class='col-sm-5'>" +
                        createObservationTextarea(payments[i].months[month].monthly_payment_observation) +
                        "</div>" +
                        "</td>" +
                        "</tr>";
            }

            table += "</tbody></table>";
            $("#paymentMonthTables")
                    .find("div.tab-content")
                    .append("<div class='tab-pane' id='month-" + month +
                            "-content'>" + table + "</div>");

            showTab(month);
            $("#paymentMonthTables")
                    .find("#month-" + month + "-content")
                    .find("table").DataTable({
                dom: 'flript',
                paging: false,
                bFilter: false,
                bInfo: false,
                order: [[3, 'asc'], [1, 'asc']]
            });

            // adiciona o listener que altera o valor do pagamento
            // ao modificar o tipo de pagamento.
            $("#paymentMonthTables").on("change", "#month-" + month + "-content .monthly-payment-type", function () {

                var type = $(this).val();
                var element = $(this).closest("td").find(".monthly-payment-value");
                var value = element.val();

                switch (type) {
                    case PAYMENT_TYPE_TOTAL:
                        element.val(defaultValue);
                        break;
                    case PAYMENT_TYPE_PARTIAL:
                        if (value == 0 || value == defaultValue) {
                            element.val(defaultValue / 2);
                        }
                        break;
                    case PAYMENT_TYPE_FREE:
                        element.val(0);
                        break;
                }
            });

            $(".datepicker-month-" + month).datetimepicker({
                format: 'DD/MM/YYYY',
                viewMode: 'days',
                locale: 'pt-br'
            });
        };

        showTab = function (month) {
            $("#nav_" + month).tab("show");
        };

        getSelectedMonthlyPayments = function () {
            var selectedPayments = [];

            $("input[name='student-payments[]']:checked").each(function () {

                var tr = $(this).closest("tr");
                var enrollment = $(this).val();
                var id = $(this).attr('data-id');

                var month = $(this).data('month');
                var observation = tr.find(".monthly-payment-observation").val();
                var type = tr.find(".monthly-payment-type").val();
                var date = tr.find(".monthly-payment-date").val();
                var value = tr.find(".monthly-payment-value").val();

                selectedPayments.push({
                    id: id,
                    enrollment: enrollment,
                    month: month,
                    observation: observation,
                    type: type,
                    date: moment(date, "DD/MM/YYYY").format("YYYY-MM-DD"),
                    value: value
                });
            });

            return selectedPayments;
        };

        createObservationTextarea = function (observation) {

            return '<label>Observação</label><br>' +
                    '<div class="input-group col-sm-12">' +
                    '<span class="input-group-addon"><i class="fa fa-font"></i></span>' +
                    '<textarea class="form-control monthly-payment-observation" rows="7" cols="25">' +
                    (observation === null ? '' : observation) +
                    '</textarea>' +
                    "</div>";
        };

        createPaymentValueInput = function (id, value) {
            return '<label>Valor</label><br>' +
                    '<div class="input-group col-sm-12">' +
                    '<span class="input-group-addon">R$</span>' +
                    '<input class="text-center form-control monthly-payment-value" type="number" step="any" value="' +
                    (id === NO_PAYMENT ? defaultValue : value)
                    + '"></div>';
        };

        createPaymentTypeSelect = function (type) {
            return '<label>Tipo</label><br>' +
                    '<div class="input-group col-sm-12">' +
                    '<span class="input-group-addon"><i class="fa fa-bars"></i> </span>' +
                    "<select class='form-control monthly-payment-type'>" +
                    "<option value='" + PAYMENT_TYPE_TOTAL + "'" +
                    (type === PAYMENT_TYPE_TOTAL ? ' selected' : '') + ">" +
                    PAYMENT_TYPE_TOTAL +
                    "</option>" +
                    "<option value='" + PAYMENT_TYPE_PARTIAL + "'" +
                    (type === PAYMENT_TYPE_PARTIAL ? ' selected' : '') + ">" +
                    PAYMENT_TYPE_PARTIAL +
                    "</option>" +
                    "<option value='" + PAYMENT_TYPE_FREE + "'" +
                    (type === PAYMENT_TYPE_FREE ? ' selected' : '') + ">" +
                    PAYMENT_TYPE_FREE +
                    "</option>" +
                    "</select>" +
                    "</div>";
        };

        createPaymentDateInput = function (month, date) {
            return '<label>Data</label><br>' +
                    '<div class="input-group col-sm-12 datepicker-month-' + month + '">' +
                    '<span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> </span>' +
                    '<input class="text-center form-control monthly-payment-date" type="text" value="' + date
                    + '"></div>';
        };

        /**
         * Cria uma instância do objeto de pagamento.
         * 
         * @param Object data
         * @returns Object
         */
        createNewPaymentObject = function (data) {

            if (data === null) {
                return {
                    monthly_payment_id: NO_PAYMENT,
                    monthly_payment_date: moment(),
                    monthly_payment_month: moment(),
                    monthly_payment_value: 0,
                    monthly_payment_observation: null,
                    monthly_payment_type: null
                };
            }

            return {
                monthly_payment_id: data.monthly_payment_id !== null ? data.monthly_payment_id : NO_PAYMENT,
                monthly_payment_date: data.monthly_payment_date === null ? moment() : moment(data.monthly_payment_date, "YYYY-MM-DD"),
                monthly_payment_month: data.monthly_payment_month === null ? moment() : moment(data.monthly_payment_month, "YYYY-MM-DD"),
                monthly_payment_value: data.monthly_payment_value,
                monthly_payment_observation: data.monthly_payment_observation,
                monthly_payment_type: data.monthly_payment_type
            };
        };


        /**
         * Callback update
         * 
         * Altera 
         *  - atributo data-id dos checkboxes utilizados
         *  - status do pagamento
         *  - valor pago
         * 
         * @param {type} paymts
         * @param {type} status
         * @returns {undefined}
         */
        updatePayments = function (paymts, status) {

            var input = null;
            var tr = null;

            for (var i = 0; i < paymts.length; i++) {
                input = $("input[name='student-payments[]'][data-month='" +
                        paymts[i].month + "'][value='" +
                        paymts[i].enrollment + "']");

                input.attr("data-id", paymts[i].id);
                tr = input.closest("tr");
                tr.find(".monthly-payment-value").val(paymts[i].value);
                tr.find(".payment-status").text(status);
            }

            $("input[name='student-payments[]']").prop("checked", false);
        };

        return {
            init: function () {
                moment.locale("pt-br");
                // payment
                if (paymentMonth.length > 0) {
                    initAttendanceMonthpicker(paymentMonth);
                }
            },
            getDataOf: function (selectedItemId) {

                var payments = getSelectedMonthlyPayments();

                switch (selectedItemId) {
                    case 'save-payments':
                        return {
                            payments: payments
                        };
                    case 'delete-payments':
                        return  {
                            payments: payments
                        };
                }
            },
            getCallbackOf: function (selectedItemId) {
                // implementar modificações nas tabelas para alterar o status
                // de pagamento e remover seleção do checkbox
                // foreach checkbox:checked atualizar data-* e uncheck

                switch (selectedItemId) {
                    case 'save-payments':
                        return {
                            exec: function (paymts) {
                                updatePayments(paymts, PAID);
                            }
                        };
                    case 'delete-payments':
                        return {
                            exec: function (paymts) {
                                updatePayments(paymts, WAITING_PAYMENT);
                            }
                        };
                }
            }
        };
    }());
    return paymentModule;
});