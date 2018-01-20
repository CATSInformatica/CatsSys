/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define(['app/pages/recruitment/interview/keep-alive', 'app/pages/recruitment/registration/registration', 'datetimepicker', 'bootstrapslider'], function (keepAlive, registration) {
    
    var volunteerForm = (function () {

        /**
         * Inicializa os seletores de data e hora
         * 
         */
        initDatepickers = function () {
            
            $('.interview-time').closest('.input-group').datetimepicker({
                format: 'HH:mm',
                useCurrent: true,
                locale: 'pt-br'
            });
            
            $('#interview-date').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                locale: 'pt-br'
            });
            
        };
       
        /**
         * Fecha a caixa de texto quando o usuário clica no checkbox sobre o texto.
         * A cor da caixa, quando fechada, é alterada.
         * 
         */
        initBoxToggleTriggers = function() {
            
            $('.interview-checkbox').change(function() {
                var checkbox = $(this);
                checkbox.attr('disabled', true);
                
                checkbox.parent().siblings('button').click();
                
                var box = checkbox.parents('.box').first();
                var isChecked = box.hasClass('checked-box');
                box.toggleClass('checked-box', !isChecked);
                toggleBoxStyle(box, 'box-primary', isChecked);
                
                setTimeout(function() {
                    checkbox.attr('disabled', false);
                }, 400);                
            });
            
            function toggleBoxStyle(box, boxColorClass, isChecked) {
                var DEFAULT_BOX_COLOR_CLASS = 'box-default';
                
                if (isChecked) { // checkbox será desmarcado
                    box.removeClass(boxColorClass);
                    box.addClass(DEFAULT_BOX_COLOR_CLASS);
                } else {
                    box.removeClass(DEFAULT_BOX_COLOR_CLASS);
                    box.addClass(boxColorClass);
                }      
            }
        };

        /**
         * Iniciliza o slider para a seleção de um número.
         * Atribui cor ao slider.
         * 
         */
        initSlider = function () {            
            $('.input-slider').each(function() {
                var value = $(this).val();
                if (value === '') {
                    value = $(this).data('default');
                }
                
                $(this).slider({
                    tooltip: 'always',
                    min: +$(this).data('min'),
                    max: +$(this).data('max'),
                    step: +$(this).data('step'),
                    value: +value,
                    ticks: [
                        +$(this).data('min'), 
                        +$(this).data('max')
                    ],
                    ticks_labels: [
                        '<strong class="slider-tick-label">Pouco</strong>', 
                        '<strong class="slider-tick-label">Muito</strong>'
                    ],
                    focus: true
                });
            });
                      
            $(".interview-slider .slider-handle").css({
                "border-bottom-color": "blue"
            });  
            $(".interview-slider .slider-selection").css({
                "background": "#23c56b"
            });    
            $(".slider-tick-label").css({
                "margin-top": "1em"
            });    
        };

        return {
            init: function () {
                initDatepickers();
                initBoxToggleTriggers();
                initSlider();
                keepAlive.init();
            },
            initSlider: function() {
                initSlider();
            },
            initDatepickers: function() {
                initDatepickers();
            }
        };
    }());
    
    return volunteerForm;
});

