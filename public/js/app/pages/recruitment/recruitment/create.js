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
        };
        
        /**
         * Exibe os campos específicos de cada tipo de processo seletivo após
         * a seleção do campo recruitment[recruitmentType]
         * 
         */
        initSpecificFields = function () {
            var recruitmentTypeInput = $('[name="recruitment[recruitmentType]"]');
            var form = $('form');
            var studentType = +recruitmentTypeInput.data('student-type');
            var volunteerType = +recruitmentTypeInput.data('volunteer-type')
            
            recruitmentTypeInput.on('change', function () {
                var type = +recruitmentTypeInput.val();
                
                if (type === studentType) {
                    form.find('.type-student').show();
                    form.find('.type-volunteer').hide();
                    
                    form.find('.undefined-placeholder').each(function() {
                        $(this).attr('placeholder', $(this).data('student-placeholder'));
                    });
                } else if (type === volunteerType) {
                    form.find('.type-volunteer').show(); 
                    form.find('.type-student').hide();   
                    
                    form.find('.undefined-placeholder').each(function() {
                        $(this).attr('placeholder', $(this).data('volunteer-placeholder'));
                    });                                    
                } else {
                    form.find('.specific-field').hide();
                }
            });
            
            // para casos de edição
            recruitmentTypeInput.trigger('change');
        };
        
        initOpenJobsInput = function () {
            
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
                initSpecificFields();
                initOpenJobsInput();
            }
        };

    }());

    return create;
});
