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

        /**
         * @todo 
         *  Vincular as ações de adicionar e remover conjuntos de campos para:
         *  
         *  Membros da família 1+
         *  Problemas de saúde de membros da família 0+
         *  Bens móveis 0+
         *  Bens imóveis (propriedades) 0+
         *  Despesas da família 1+
         *  Receitas da família 1+
         * 
         * @returns {undefined}
         */
        initAddDelButtons = function () {

            $(".add-button").click(function () {
                
                var container = $(this).closest(".field-container");
                
//                console.log("id", container.attr("id"));
                
                var currentCount = container.find(".field-box").length;

                var outerTemplate = container.children("span").first().data("template");
                var innerTemplate = container.children("span").last().data("template");

                var newInnerElement = innerTemplate.replace(/__index__/g, currentCount);
                var newPartialElement = outerTemplate.replace(/__index__/g, currentCount + 1);

                var innerJQObj = $(newInnerElement);

                innerJQObj.find("div.form-group").each(function (e) {
                    newPartialElement = newPartialElement.replace("__TEMPLATE" + e + "__", $(this).get(0).outerHTML);
                });

                if(currentCount > 0) {
                    container.find(".field-box").last().after(newPartialElement);
                } else {
                    container.append(newPartialElement);
                }

                return false;
            });

            $(".del-button").click(function () {
                var container = $(this).closest(".field-container");
                var currentCount = container.find(".field-box").length;

                if (currentCount > container.data("min")) {
                    container.find(".field-box").last().remove();
                }

                return false;
            });

        };

        return {
            init: function () {
                regModule.init();
                initAddDelButtons();
            }
        };
    }());

    return PreInterviewModule;

});
