/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['moment', 'masks', 'jquery', 'datetimepicker', 'bootstrapslider'], function (moment, masks) {
    var create = (function () {

        var PSA = 1;

        initDatepickers = function () {

            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                minDate: moment(),
                useCurrent: false,
                maxDate: moment().add(18, 'months'),
                locale: 'pt-br',
                viewMode: 'years',
                viewDate: moment()
            });
        };

        initMasks = function () {
            masks.bind({
                date: 'input[name$="Date]"]'
            });
        };

        initSliders = function () {
            var val;

            $('input[name$="Target]"]').each(function () {
                val = $(this).val();
                if (val !== "") {
                    $(this).attr('data-slider-value', val);
                }
            });

            $('.input-slider').slider({
                tooltip: 'always'
            });

            $('select[name$="recruitmentType]"]').on('change', function () {
                disableSliderIfNeeded($(this).val());
            });

            disableSliderIfNeeded($('select[name$="recruitmentType]"]').val());
        };

        disableSliderIfNeeded = function (value) {
            if (parseInt(value) !== PSA) {
                $(".input-slider").slider('disable');
            } else {
                $(".input-slider").slider('enable');
            }
        };
        
        /**
         * Exibe a seleção de cargos apenas quando o usuário selecionar o tipo
         * do processo seletivo como sendo de voluntários
         * 
         */
        initOpenJobsInput = function () {
            var openJobsInput = $('[name="recruitment[openJobs][]"]');
            var recruitmentTypeInput = $('[name="recruitment[recruitmentType]"]');
            var openJobsContainer = openJobsInput.closest('.open-jobs-container');
            
            recruitmentTypeInput.on('change', function () {
                if (+recruitmentTypeInput.val() === +openJobsInput.data('type-must-be')) {
                    openJobsContainer.removeClass('hide');
                } else {
                    openJobsContainer.addClass('hide');                    
                }
            });
            
            // em caso de edição de um PSV, exibe a seleção de cargos.
            recruitmentTypeInput.trigger('change');
            
            /**
             * Permite que o usuário faça múltiplos cliques para selecionar os cargos 
             * abertos, ao invés de precisar utilizar a tecla Ctrl para manter
             * as seleções já feitas.
             */
            $('[name="recruitment[openJobs][]"] option').mousedown(function(e) {
                e.preventDefault();
                $(this).prop('selected', !$(this).prop('selected'));
                return false;
            });
        };

        return {
            init: function () {
                initDatepickers();
                initMasks();
                initSliders();
                initOpenJobsInput();
            }
        };

    }());

    return create;
});
