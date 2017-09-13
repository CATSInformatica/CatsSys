/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['moment', 'masks', 'app/models/Service', 'jquery', 'datetimepicker'], function (moment, masks, service) {
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

        initMasks = function () {
            masks.bind({
                phone: "input[name='registration[person][personPhone]']",
                cpf: "input[name='registration[person][personCpf]']",
                date: "input[name='registration[person][personBirthday]']",
                zip: "input[name*=addressPostalCode]"
            });
        };

        initServices = function () {
            service.bindZipService({
                dataHolder: $('input[name*=addressPostalCode]')
            });
        };
       
        /**
         * Permite que o usuário faça múltiplos cliques para selecionar os cargos 
         * desejados, ao invés de precisar utilizar a tecla Ctrl para manter
         * as seleções já feitas.
         * A funcionalidade é aplicada a qualquer campo do tipo 'select' que possua
         * a classe 'allow-multiple-clicks'
         */
        initDesiredJobsInput = function () {  
            $('.allow-multiple-clicks option').mousedown(function(e) {
                e.preventDefault();
                $(this).prop('selected', !$(this).prop('selected'));
                return false;
            });
        };

        return {
            init: function () {
                initDatepickers();
                initMasks();
                initServices();
                initDesiredJobsInput();
            },
            initDesiredJobsInput: function() {
                initDesiredJobsInput();
            }
        };
    }());

    return form;
});