/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['moment', 'jquery', 'datetimepicker'], function (moment) {
    var form = (function () {
        // your module code goes here
        // var config = null;
        /**
         * 
         * private functions
         */
        initDatepickers = function () {
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                minDate: moment().subtract(100, 'years'),
                useCurrent: false,
                maxDate: moment().subtract(15, 'years'),
                locale: 'pt-br',
                viewMode: 'years',
                viewDate: moment().subtract(21, 'years')
            });
        };
        initCaptchaOperations = function () {
            $('#captcha_input-refresh').click(function () {
                $.ajax({
                    url: '/recruitment/captcha/refresh',
                    dataType: 'json',
                    success: function (data) {
                        $('#captcha_input-image').attr('src', data.src);
                        $('#captcha_input-hidden').attr('value', data.id);
                    }
                });
            });
        };
        return {
            init: function () {
                initDatepickers();
                initCaptchaOperations();
            }
        };
    }());

    return form;
});