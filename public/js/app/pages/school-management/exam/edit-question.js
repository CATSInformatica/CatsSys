/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'app/pages/school-management/exam/add-question'], function () {
    var edit = (function () {
        
        /*
         *  Impede que o conteúdo da textarea seja exibido pelo MathJax, impedindo a edição
         */
        initTextArea = function() {
            $('.trumbowyg-editor').addClass('tex2jax_ignore');
        };
        
        /*
         *  Inicializa o campo de seleção de disciplina com o id passado através 
         *  do dado data-subject-id da div #data
         */
        selectSubject = function () {
            var sId = $('#data').attr("data-subject-id");
            if (jQuery.isNumeric(sId) && parseInt(sId) > 0) {
                $('#subject').val(sId);
            }
        };

        /*
         *  Inicializa o campo de seleção da resposta correta com o índice passado através 
         *  do dado data-answer-id da div #data
         */
        selectCorrectAnswer = function () {
            var aId = $('#data').attr("data-answer-id");
            if (jQuery.isNumeric(aId) && parseInt(aId) >= 0) {
                $("#alternative-" + parseInt(aId)).prop("checked", true);
            }
        };
        
        return {
            init: function () {
                initFieldControlBtns();
                setTrumbowyg(null);
                setFormBehavior();
                initTextArea();
                initMathJax();
                selectSubject();
                selectCorrectAnswer();
                partialValidation();
            }
        };

    }());

    return edit;
});