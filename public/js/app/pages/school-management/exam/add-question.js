/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {
    var addQuestion = (function () {
        
        //  Tipos de questão
        var CLOSED_QUESTION = "1";
        var OPEN_QUESTION = "2";
        var preview = null;

        /*
         * 
         *  Inicializa os botões e campos que controlam a inserção dinâmica de respostas 
         *  para perguntas do tipo CLOSED_QUESTION
         */
        initFieldControlBtns = function () {
            // Botões Adiciona/Remove Alternativa
            var btnGroup = ''
                    + '<div class="btn-group btn-group-justified" role="group" id="alternative-control-btns">'
                    + '<div class="btn-group" role="group">'
                    + '<button type="button" id="add-alternative-btn" class="btn btn-success" onclick="return addAlternative()">Adicionar Alternativa</button>'
                    + '</div>'
                    + '<div class="btn-group" role="group">'
                    + '<button type="button" id="remove-alternative-btn" class="btn btn-danger" onclick="return removeAlternative()" onclick="return addQuestionAlternativeBtn()">Remover Alternativa</button>'
                    + '</div>'
                    + '</div>';
            $('#alternatives-fieldset').after(btnGroup + '<br><br>');

            // Inicializa o campo onde ficarão os radio buttons para selecionar a alternativa correta
            var correctAnswerField = $('#add-exam-question > fieldset > div.form-group').last();
            correctAnswerField.find('div').attr('id', 'correct-answer');
            $('#correct-answer div').first().remove();
            $('#correct-answer').append('<div class="radio-inline"><label class="control-label">'
                    + '<input type="radio" name="exam-question[correctAnswer]" id="none-radio" value="-1" disabled> Nenhum </label></div>');

            // No caso de edição ou na exibição de erros vindos do servidor 
            // é necessário numerar os campos de alternativas e mostrar os radio buttons
            var asnwerFields = $('#alternatives-fieldset > fieldset');
            asnwerFields.each(function (i, elem) {
                $(elem).find('div > label').html('Alternativa ' + (i + 1));
                var radioBtn = ''
                        + '<div class="radio-inline"><label class="control-label">'
                        + '<input type="radio" name="exam-question[correctAnswer]" value="' + i + '" id="alternative-' + i + '">' + (i + 1) + '</label></div>';
                $('#correct-answer').append(radioBtn);
            });

            // Se a questão for aberta, esconde os botões de controle
            if ($("#question-type").val() === OPEN_QUESTION) {
                $("#alternative-control-btns").hide();
                $('#correct-answer').parent().hide();
            }
        };

        /*
         * 
         *  Configura o comportamento do formulário em relação ao campo de seleção 
         *  "Tipo de questão", mostrando diferentes elementos baseados no tipo de questão selecionada
         */
        setFormBehavior = function () {
            // Impede que o usuário selecione o tipo de questão como aberta se 
            // houver ao menos um campo de alternativas
            $("#question-type").change(function () {
                if ($("#question-type").val() === OPEN_QUESTION) {
                    //  Se já existem campos para as alternativas, não se pode selecionar
                    //  o tipo de questão como aberta
                    if ($('#alternatives-fieldset > fieldset').length > 0) {
                        $("#question-type").val(CLOSED_QUESTION);
                    } else {
                        $("#alternative-control-btns").hide();
                        $('#correct-answer').parent().hide();
                    }
                } else {
                    $("#alternative-control-btns").show();
                    $('#correct-answer').parent().show();
                }
            });
        };

        /*
         * 
         *  Validação parcial do formulário
         *  -   Verifica se uma resposta correta foi escolhida e;
         *  -   Verifica se há mais de uma alternativa para a questão
         *  Se uma das verificações falhar, o formulário não será enviado
         */
        partialValidation = function () {
            $('#add-question-btn').click(function () {
                if ($("#question-type").val() === CLOSED_QUESTION &&
                        (!$("input[name='exam-question[correctAnswer]']:checked").val() || $("input[name='exam-question[correctAnswer]']:checked").val() === '-1')) {
                    if ($('ul#no-selection-err').length === 0) {
                        $('#correct-answer').append('<ul id="no-selection-err" class="help-block text-red"><li>Você deve selecionar a alternativa correta</li></ul>');
                        $('ul#no-selection-err').delay(5000).fadeOut(400, function () {
                            $('ul#no-selection-err').remove();
                        });
                    }
                } else if ($("#question-type").val() === CLOSED_QUESTION && $('#alternatives-fieldset > fieldset').length < 2) {
                    if ($('ul#no-alternative-err').length === 0) {
                        $('#alternatives-fieldset').append('<ul id="no-alternative-err" class="help-block text-red"><li>Uma questão fechada deve ter ao menos duas alternativas</li></ul>');
                        $('ul#no-alternative-err').delay(5000).fadeOut(400, function () {
                            $('#ul#no-alternative-err').remove();
                        });
                    }
                } else {
                    $('#add-question-btn').attr('type', 'submit');
                }
            });
        };

        /*
         * 
         *  Adiciona uma alternativa para a questão (textarea e radio button) 
         */
        addAlternative = function () {
            if ($("#question-type").val() === CLOSED_QUESTION) {
                // Adiciona um campo para inserir a alternativa
                var currentCount = $('#alternatives-fieldset > fieldset').length;
                var template = $('#alternatives-fieldset span').data('template');
                template = template.replace(/__placeholder__/g, currentCount);
                template = template.replace("Alternativa 1", "Alternativa " + (currentCount + 1));
                $('#alternatives-fieldset').append(template);

                // Adiciona um radio button para selecionar esta alternativa como a correta
                var radioBtn = ''
                        + '<div class="radio-inline"><label class="control-label">'
                        + '<input type="radio" name="exam-question[correctAnswer]" id="correct-answer" value="' + currentCount + '">' + (currentCount + 1) + '</label></div>';
                $('#correct-answer').append(radioBtn);
            }
        };

        /*
         * 
         *  Remove a última alternativa da questão
         */
        removeAlternative = function () {
            if ($("#question-type").val() === CLOSED_QUESTION && $('#alternatives-fieldset > fieldset').length > 0) {
                $('#correct-answer div').last().remove();
                $('#alternatives-fieldset fieldset').last().remove();
            }
        };

        initPreview = function () {

            var value = "";
            $("#add-exam-question").on("keyup", "textarea", function () {
                $("#add-exam-question").find("textarea").each(function (e) {
                    if (e === 0) {
                        value = "<h4>Questão <h4>";
                        value += $(this).val();
                        value += "<hr>";
                        value += "<ol type='A' class='text-center'>";
                    } else {
                        value += "<li>" + $(this).val();
                        +"</li>";
                    }
                });
                if (value !== "") {
                    value += "</ol>";
                }

                preview.setInputValue(value);
                preview.Update();
                value = "";
            });
        };

        initMathJax = function () {
            require(['mathjax'], function () {
                setTimeout($("#testEquation").fadeIn("slow"), 1000);
                require(['app/models/MathJaxPreview'], function (Preview) {
                    preview = Preview;
                    preview.Init();
                    initPreview();
                });
            });
        };

        return {
            init: function () {
                initFieldControlBtns();
                setFormBehavior();
                initMathJax();
                partialValidation();
            }
        };

    }());

    return addQuestion;
});