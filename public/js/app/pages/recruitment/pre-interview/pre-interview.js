/* 
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
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

define(['app/pages/recruitment/registration/registration'], function (regModule) {

    var PreInterviewModule = (function () {

        var suggestions = ['Teste', 'Hello'];

        /**
         * Adiciona/Remove conjuntos de campos para
         * 
         * - Membros da família
         * - Doenças na família
         * - Bens móveis
         * - Bens imóveis
         * - Despesas
         * - Receitas
         * 
         * @returns {undefined}
         */
        initAddDelButtons = function () {

            $(".add-button").click(function () {

                var container = $(this).closest(".field-container");

                var currentCount = container.find(".field-box").length;

                var outerTemplate = container.children("span").first().data("template");
                var innerTemplate = container.children("span").last().data("template");

                var newInnerElement = innerTemplate.replace(/__index__/g, currentCount);
                var newPartialElement = outerTemplate.replace(/__index__/g, currentCount + 1);

                var innerJQObj = $(newInnerElement);

                innerJQObj.find("div.form-group").each(function (e) {
                    newPartialElement = newPartialElement.replace("__TEMPLATE" + e + "__", $(this).get(0).outerHTML);
                });

                container.children('span').last().before(newPartialElement);

//                updateSuggestions();

                return false;
            });

            $(".del-button").click(function () {
                var container = $(this).closest(".field-container");
                var currentCount = container.find(".field-box").length;

//                updateSuggestions();

                if (currentCount > container.data("min")) {
                    container.find(".field-box").last().remove();
                }

                return false;
            });

        };

        updateSuggestions = function () {

//            $('input[name *=familyHealthName]').autocomplete({
//                query: 'Unit',
//                suggestions: suggestions
//            });

        };

        /**
         * Mantém a sessão para o formulário de pré-entrevista ativa.
         * 
         * A cada 5 mins uma requisição é enviada ao servidor para manter a
         * sessão ativa.
         * 
         * @returns {undefined}
         */
        keepMeAlive = function () {

            setTimeout(function () {
                $.ajax({
                    url: '/recruitment/pre-interview/keepAlive',
                    type: 'GET',
                    success: function (data) {
                        keepMeAlive();
                    },
                    error: function (texErr) {
                        console.log('Error', texErr);
                    }
                });
            }, 300000);

        };

        return {
            init: function () {
                regModule.init();
                initAddDelButtons();
                keepMeAlive();
//                updateSuggestions();
            }
        };
    }());

    return PreInterviewModule;

});
