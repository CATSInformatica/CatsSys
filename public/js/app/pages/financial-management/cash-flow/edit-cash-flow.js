/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datetimepicker'], function () {
    var editCashFlow = (function () {
        
        initDatepicker = function () {
            var initialDate = new Date($('.datepicker').val());
            initialDate.setTime(initialDate.getTime() + 1 * 86400000);
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                locale: 'pt-br'
            });
            $('.datepicker').closest('.input-group')
                    .data("DateTimePicker").date(initialDate);
        };

        return {
            init: function () {
                initDatepicker();
            }
        };
    }());

    return editCashFlow;
});