/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'trumbowyg', 'trumbowygpt', 'trumbowygcolors', 'trumbowygupload'], function () {
    var addQuestion = (function () {
        //  Tipos de questão
        var CLOSED_QUESTION = "1";
        var OPEN_QUESTION = "2";
        var preview = null;

        /*
         * 
         *  Enumera as alternativas e adiciona os radio buttons para seleção da alternativa correta
         */
        initFieldControlBtns = function () {
            // Inicializa o campo onde ficarão os radio buttons para selecionar a alternativa correta
            var correctAnswerField = $('#alternative-control-btns').
                    before('<div class="form-group"><label for="correct-answer">Alternativa Correta</label><div id="correct-answer"></div></div><br>');
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
         *  Configura o comportamento do formulário 
         *      *   Em relação ao campo de seleção "Tipo de questão", mostrando 
         *          diferentes elementos baseados no tipo de questão selecionada
         *      *   Em relação aos botões de controle Adicionar/Remover Alternativa
         */
        setFormBehavior = function () {
            // Impede que o usuário selecione o tipo de questão como aberta se 
            // houver ao menos um campo de alternativas
            $("#question-type").change(function () {
                if ($("#question-type").val() === OPEN_QUESTION) {
                    //  Se já existem campos para as alternativas, não se pode selecionar
                    //  o tipo de questão como aberta
                    //  **TODO: Permitir essa opção mostrando um modal perguntando se 
                    //          se o usuário tem certeza.
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
            
            /*
             * 
             *  Adiciona uma alternativa para a questão (textarea e radio button) 
             */
            $('#add-alternative-btn').click(function () {
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

                    setTrumbowyg($('#alternatives-fieldset > fieldset').last().find('textarea'));
                }
            });
            
            /*
             * 
             *  Remove a última alternativa da questão (textarea e radio button) 
             */
            $('#remove-alternative-btn').click(function () {
                if ($("#question-type").val() === CLOSED_QUESTION && $('#alternatives-fieldset > fieldset').length > 0) {
                    $('#correct-answer div').last().remove();
                    $('#alternatives-fieldset fieldset').last().remove();
                }
            });
        };

        /*
         * 
         *  Validação parcial do formulário
         *      *   Verifica se uma resposta correta foi escolhida
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
                } else {
                    $('#add-question-btn').attr('type', 'submit');
                }
            });
        };

        initPreview = function () {

            var value = "";
            $("#add-exam-question").on("keyup", ".trumbowyg-editor", function () {
                $("#add-exam-question").find(".trumbowyg-editor").each(function (e) {
                    if (e === 0) {
                        value = "<h4>Questão <h4>";
                        value += $(this).html();
                        value += "<hr>";
                        value += "<ol type='A' class='text-center'>";
                    } else {
                        value += "<li>" + $(this).html();
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
        
        /*
         *  elem === null
         *      Transforma todo textarea da página em um Trumbowyg (editor wysiwyg HTML)
         *      com as configurações definidas abaixo
         *  elem === string (id, class...)
         *      Transforma os elementos encontrados em um Trumbowyg
         *      Exemplo: '.edit-textarea' Transforma todos os elementos da classe 
         *                                .edit-textarea em um Trumbowyg
         *      
         *  Configuração Trumbowyg:
         *  -   Trumbowyg padrão
         *  -   Plugin Colors - Permite editar a cor do texto
         *  -   Plugin Upload - Permite fazer upload de imagens
         *  -   Localização em português
         *      
         * @param mixed elem
         */
        setTrumbowyg = function (elem) {
            if (elem === null) { // ALL
                elem = 'textarea';
            }
            $(elem).trumbowyg({
                lang: 'pt',
                btnsDef: {
                    image: {
                        dropdown: ['insertImage', 'upload'],
                        ico: 'insertImage'
                    }
                },
                btns: ['viewHTML',
                    '|', 'formatting',
                    '|', 'btnGrp-design',
                    '|', 'image',
                    '|', 'btnGrp-justify',
                    '|', 'btnGrp-lists',
                    '|', 'horizontalRule',
                    '|', 'foreColor',
                    '|', 'backColor']
            });
        };

        return {
            init: function () {
                initFieldControlBtns();
                setTrumbowyg(null);
                setFormBehavior();
                initMathJax();
                partialValidation();
            }
        };
    }());
    return addQuestion;
});