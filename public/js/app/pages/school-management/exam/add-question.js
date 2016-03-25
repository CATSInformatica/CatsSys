/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'trumbowyg', 'trumbowygpt', 'trumbowygbase64'], function () {

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
            $('#correct-answer').append('<div class="radio-inline"></div>');

            if ($("#question-type").val() === CLOSED_QUESTION) {
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
            }

            // Se a questão for aberta, esconde os botões de controle e muda 
            // o nome do campo de resposta
            if ($("#question-type").val() === OPEN_QUESTION) {
                $("#alternative-control-btns").hide();
                $('#correct-answer').parent().hide();
                $('#alternatives-fieldset > fieldset > div > label').html('Resposta');
            }
        };

        /*
         * 
         *  Adiciona uma alternativa para a questão (textarea e radio button) 
         */
        addAlternative = function () {
            // Adiciona um campo para inserir a alternativa
            var currentCount = $('#alternatives-fieldset > fieldset').length;
            var template = $('#alternatives-fieldset span').data('template');
            template = template.replace(/__placeholder__/g, currentCount);

            if ($("#question-type").val() === CLOSED_QUESTION) {
                template = template.replace("Alternativa 1", "Alternativa " + (currentCount + 1));

                // Adiciona um radio button para selecionar esta alternativa como a correta
                var radioBtn = ''
                        + '<div class="radio-inline"><label class="control-label">'
                        + '<input type="radio" name="exam-question[correctAnswer]" id="correct-answer" value="' + currentCount + '">' + (currentCount + 1) + '</label></div>';
                $('#correct-answer').append(radioBtn);
            } else {
                template = template.replace("Alternativa 1", "Resposta");
            }
            $('#alternatives-fieldset').append(template);

            setTrumbowyg($('#alternatives-fieldset > fieldset').last().find('textarea'));
        };

        /*
         * 
         *  Remove a última alternativa da questão (textarea e radio button) 
         */
        removeAlternative = function () {
            if ($('#alternatives-fieldset > fieldset').length > 0) {
                if ($("#question-type").val() === CLOSED_QUESTION) {
                    $('#correct-answer div').last().remove();
                }
                $('#alternatives-fieldset fieldset').last().remove();
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
                $('#modal-question-type').modal('show');
                
                //  Volta o tipo da questão para o anterior, até que se 
                //  confirme o desejo de mundança
                if ($("#question-type").val() === OPEN_QUESTION) {
                    $("#question-type").val(CLOSED_QUESTION);
                } else {
                    $("#question-type").val(OPEN_QUESTION);
                }

                //  Mensagem do modal dependendo do tipo anterior da questão
                if ($("#question-type").val() === OPEN_QUESTION) {
                    $('#change-type-text').html('O conteúdo da resposta será perdido.<br>Tem certeza que deseja mudar o tipo da questão para FECHADA?');
                } else {
                    $('#change-type-text').html('O conteúdo das alternativas será perdido.<br>Tem certeza que deseja mudar o tipo da questão para ABERTA?');
                }

                $('#btn-change-type').unbind().click(function () {
                    $('#modal-question-type').modal('hide');

                    if ($("#question-type").val() === CLOSED_QUESTION) {
                        while ($('#alternatives-fieldset > fieldset').length > 0) {
                            removeAlternative();
                        }
                        $("#question-type").val(OPEN_QUESTION);
                        addAlternative();
                        $("#alternative-control-btns").hide();
                        $('#correct-answer').parent().hide();
                    } else { 
                        removeAlternative();
                        $("#question-type").val(CLOSED_QUESTION);
                        while ($('#alternatives-fieldset > fieldset').length < 5) {
                            addAlternative();
                        }
                        $("#alternative-control-btns").show();
                        $('#correct-answer').parent().show();
                    }
                });

            });

            $('#add-alternative-btn').click(function () {
                addAlternative();
            });

            $('#remove-alternative-btn').click(function () {
                removeAlternative();
            });
        };

        /*
         * 
         *  Validação parcial do formulário quando a pergunta é do tipo fechada
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
                    imagem: {
                        dropdown: ['insertImage', 'base64'],
                        ico: 'insertImage'
                    }
                },
                btns: ['formatting',
                    '|', 'btnGrp-design',
                    '|', 'imagem',
                    '|', 'btnGrp-justify',
                    '|', 'btnGrp-lists',
                    '|', 'horizontalRule']
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