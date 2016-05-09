/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datetimepicker'], function () {
    var openMonthBalance = (function () {

        initDatepicker = function () {
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                useCurrent: false,
                locale: 'pt-br',
                viewMode: 'days'
            });
        };

        return {
            init: function () {
                initDatepicker();
            }
        };
    }());

    return openMonthBalance;
});