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
        var alternativeListStyle = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        /*
         *  Adiciona uma alternativa para a questão (textarea e radio button) 
         */
        addAlternative = function () {
            // Adiciona um campo para inserir a alternativa
            var currentCount = $("#question-panel")
                    .find(".answer-tab")
                    .length;

            if (currentCount > alternativeListStyle.length - 1) {
                return;
            }

            var template = $("#question-panel > span").data("template");

            template = template.replace(/__placeholder__/g, currentCount);

            // Adiciona um radio button para selecionar esta alternativa como a correta
            var radioBtn = '<label class="radio-inline">' +
                    '<input type="radio" name="exam-question[correctAnswer]" value="' +
                    currentCount + '">' + alternativeListStyle[currentCount] + '</label>';

            $("#correct-answer")
                    .append(radioBtn);

            var header = $('<li class="answer-tab-header">' +
                    '<a href="#answer_' + currentCount + '" ' +
                    'data-toggle="tab"> Resp. ' +
                    alternativeListStyle[currentCount] +
                    '</a></li>');

            var tab = $('<div class="tab-pane answer-tab" id="answer_' +
                    currentCount + '">' + template + '</div>');

            $("#question-panel")
                    .find("ul.nav")
                    .append(header);

            $("#question-panel")
                    .find("div.tab-content")
                    .append(tab);

            setTrumbowyg($("#question-panel")
                    .find(".answer-tab")
                    .last()
                    .find('textarea')
                    );
        };

        /*
         *  Remove a última alternativa da questão (radio button e tab)
         */
        removeAlternative = function () {

            if ($("#question-panel")
                    .find(".answer-tab")
                    .length > 0) {

                $('#correct-answer').find('.radio-inline').last().remove();

                $("#question-panel")
                        .find(".answer-tab-header")
                        .last()
                        .remove();

                $("#question-panel")
                        .find(".answer-tab")
                        .last()
                        .remove();
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
                        $("#question-panel")
                                .find(".answer-tab")
                                .each(removeAlternative);
                        $("#question-type").val(OPEN_QUESTION);
                        addAlternative();
                        $('#answers-blah').hide();
                    } else {
                        removeAlternative();
                        $("#question-type").val(CLOSED_QUESTION);
                        while ($("#question-panel")
                                .find(".answer-tab").length < 5) {
                            addAlternative();
                        }
                        $("#alternative-control-btns").show();
                        $('#answers-blah').show();
                    }
                });

            });

            $('#add-alternative-btn').click(addAlternative);
            $('#remove-alternative-btn').click(removeAlternative);
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
            $("#add-exam-question").on("keyup", ".trumbowyg-editor", showPreview);
            showPreview();
        };

        showPreview = function () {
            var value = "";

            $("#add-exam-question").find(".trumbowyg-editor").each(function (e) {
                if (e === 0) {
                    value += $(this).html();
                    value += "<hr>";
                    value += "<ol type='A'>";
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
        };

        initMathJax = function () {
            require(['mathjax'], function () {
                setTimeout(function () {
                    $("#testEquation").fadeIn("fast");
                }, 500);
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
                        dropdown: ['base64'],
                        ico: 'insertImage'
                    }
                },
                btns: ['formatting',
                    '|', 'btnGrp-design',
                    '|', 'image',
                    '|', 'btnGrp-justify',
                    '|', 'btnGrp-lists',
                    '|', 'horizontalRule']
            });
        };

        return {
            init: function () {
                require(['trumbowyg'], function () {
                    require(['trumbowygpt', 'trumbowygbase64'], function () {
                        setTrumbowyg(null);
                        setFormBehavior();
                        initMathJax();
                        partialValidation();
                    });
                });
            }
        };
    }());
    return addQuestion;
});