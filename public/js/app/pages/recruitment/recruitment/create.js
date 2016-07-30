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
                date: "input[name='recruitment_begindate'], input[name='recruitment_enddate']"
            });
        };

        initSliders = function () {
            $('.input-slider').slider({
                tooltip: 'always'
            });
            
            $('select[name=recruitment_type]').on('change', function(){
                disableSliderIfNeeded($(this).val());
            });
            
            disableSliderIfNeeded($('select[name=recruitment_type]').val());
        };
        
        disableSliderIfNeeded = function(value){
            if(parseInt(value) !== PSA) {
                console.log();
                $(".input-slider").slider('disable');
            } else {
                $(".input-slider").slider('enable');
            }
        };

        return {
            init: function () {
                initDatepickers();
                initMasks();
                initSliders();
            }
        };

    }());

    return create;
});
