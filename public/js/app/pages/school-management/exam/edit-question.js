/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery'], function () {
    var edit = (function () {

        /*
         *  Impede que o conteúdo da textarea seja exibido pelo MathJax, impedindo a edição
         */
        initTextArea = function () {
            $('.trumbowyg-editor').addClass('tex2jax_ignore');
        };

        return {
            init: function () {
                require(['app/pages/school-management/exam/add-question'], function (QuestionModule) {
                    QuestionModule.init();
                    initTextArea();
                });
            }
        };

    }());

    return edit;
});