/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['moment', 'masks', 'datetimepicker', 'jquery'], function (moment, masks) {
    var student = (function () {

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

        initMasks = function () {
            masks.bind({
                phone: "input[name='registration[person][personPhone]']",
                cpf: "input[name='registration[person][personCpf]']",
                date: "input[name='registration[person][personBirthday]']",
                zip: "input[name*=addressPostalCode]",
                number4: "input[name*=addressNumber]"
            });
        };

        return {
            init: function () {
                initDatepickers();
                initMasks();
            }
        };
    }());

    return student;

});
