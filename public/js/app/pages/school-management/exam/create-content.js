/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
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

define(['jquery', 'datetimepicker'], function () {
    var createContent = (function () {

        /*
         * Eventos que envolvem o formulário
         * 
         */
        createContentListeners = function() {
            /*
             * Atualiza o total de questões
             * 
             */
            $('.amount-input').change(function () {
                var count = 0;
                $('.amount-input').each(function () {
                    if ($(this).val() !== '') {
                        count += parseInt($(this).val());
                    }
                });
                $('#question-count').html(count);
            });
        };

        /*
         * Inicializa os campos de quantidade de questões de cada disciplina
         * 
         */
        initQuantities = function() {
            var QUESTIONS_PER_BASE_SUBJECT = 45;
            var quantityIsDefined =  ($('.base-subject').find('.quantity-block').data('quantity') !== '') ? true : false;
            $('.base-subject').each(function() {
                var questionQuantity = parseInt(QUESTIONS_PER_BASE_SUBJECT / parseInt($(this).find('.amount-input').length));
                
                $(this).find('.amount-input').each(function() {
                    if (quantityIsDefined) {
                        $(this).val($(this).parents('.quantity-block').data('quantity'));
                    } else {
                        $(this).val(questionQuantity);
                    }
                });
            });
        };

        /*
         * Provoca a contagem do número total de questões, definidas por padrão, e atualiza a interface
         * 
         */
        initQuestionAmount = function () {
            $(".amount-input").each(function () {
                $(this).trigger("change");
            });
        };

        return {
            init: function () {
                createContentListeners();
                initQuantities();
                initQuestionAmount();
            } 
        };

    }());

    return createContent;
});