/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['moment', 'jquery', 'datetimepicker'], function (moment) {

    var create = (function () {

        initDatepickers = function () {
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                minDate: moment(),
                useCurrent: true,
                maxDate: moment().add(2, 'years'),
                locale: 'pt-br',
                viewMode: 'months'
            });
        };

        return {
            init: function () {
                initDatepickers();
            }
        };

    }());

    return create;

});