/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datetimepicker'], function () {
    var addCashFlow = (function () {
        
        initDatepicker = function () {
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                locale: 'pt-br'
            });
        };

        return {
            init: function () {
                initDatepicker();
            }
        };
    }());

    return addCashFlow;
});