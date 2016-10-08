/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable'], function () {

    var addQuestion = (function () {
        var LETTERS_IN_THE_ALPHABET = 26;

        //  Tipos de questão
        var CLOSED_QUESTION = "1";
        var OPEN_QUESTION = "2";
        
        var preview = null;

        /*
         *  Adiciona uma alternativa para a questão (textarea e radio button) 
         */
        addAlternative = function () {
            var currentCount = $("#question-panel")
                    .find(".answer-tab")
                    .length;
            
            if (currentCount >= LETTERS_IN_THE_ALPHABET) {
                return;
            }
            
            var alternativeLetter = String.fromCharCode('A'.charCodeAt(0) + +currentCount);
            var template = $("#question-panel > span").data("template");
            template = template.replace(/__placeholder__/g, currentCount);

            // Adiciona um radio button para selecionar esta alternativa como a correta
            var radioBtn = '<label class="radio-inline">' +
                    '<input type="radio" name="exam-question[correctAnswer]" value="' +
                    currentCount + '">' + alternativeLetter + '</label>';
            
            $("#correct-answer")
                    .append(radioBtn);

            // Adiciona uma aba para a alternativa
            var header = $('<li class="answer-tab-header">' +
                    '<a href="#answer_' + currentCount + '" ' +
                    'data-toggle="tab"> Resp. ' +
                    alternativeLetter +
                    '</a></li>');

            var tab = $('<div class="tab-pane answer-tab" id="answer_' +
                    currentCount + '">' + template + '</div>');

            $("#question-panel")
                    .find("ul.nav")
                    .append(header);

            $("#question-panel")
                    .find("div.tab-content")
                    .append(tab);

            // Ativa o Trumbowyg no textarea da alternativa
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
         *  Configura o comportamento do formulário 
         *      *   Em relação ao campo de seleção "Tipo de questão", mostrando 
         *          diferentes elementos baseados no tipo de questão selecionada
         *      *   Em relação aos botões de controle Adicionar/Remover Alternativa,
         *          relevantes somente para questões fechadas
         */
        setFormBehavior = function () {            
            // O usuário é alertado ao tentar mudar o tipo de questão. Se desejada, 
            // a ação pode ser executada mediante a confirmação do usuário, por um modal.
            $("#question-type").change(function () {
                // Abre o modal
                $('#modal-question-type').modal('show');

                //  Volta o select do tipo de questão para o anterior, até que se 
                //  confirme o desejo de mundança
                if ($("#question-type").val() === OPEN_QUESTION) {
                    $("#question-type").val(CLOSED_QUESTION);
                } else {
                    $("#question-type").val(OPEN_QUESTION);
                }

                //  Exibe a mensagem para o usuário
                if ($("#question-type").val() === OPEN_QUESTION) {
                    $('#change-type-text').html('O conteúdo da resposta será perdido.<br>Tem certeza que deseja mudar o tipo da questão para FECHADA?');
                } else {
                    $('#change-type-text').html('O conteúdo das alternativas será perdido.<br>Tem certeza que deseja mudar o tipo da questão para ABERTA?');
                }

                // Fecha o modal e executa as mudanças
                $('#btn-change-type').unbind().click(function () {
                    $('#modal-question-type').modal('hide');

                    if ($("#question-type").val() === CLOSED_QUESTION) {
                        // Efetiva a mudança de tipo de questão
                        $("#question-type").val(OPEN_QUESTION);
                        
                        // Remove todas as alternativas
                        $("#question-panel")
                                .find(".answer-tab")
                                .each(removeAlternative);
                        
                        // Adiciona o campo de resposta 
                        // e esconde os botões Adicionar/Remover Alternativa
                        addAlternative();
                        $('#answers-blah').hide();
                    } else {
                        // Efetiva a mudança de tipo de questão
                        $("#question-type").val(CLOSED_QUESTION);
                        
                        // Remove o campo de resposta
                        removeAlternative();
                                            
                        // Adiciona 5 alternativas (padrão) 
                        // e mostra os botões Adicionar/Remover Alternativa
                        while ($("#question-panel")
                                .find(".answer-tab").length < 5) {
                            addAlternative();
                        }
                        $("#alternative-control-btns").show();
                        $('#answers-blah').show();
                    }
                });

            });

            // Dá a funcionalidade desejada aos botões Adicionar/Remover Alternativa
            $('#add-alternative-btn').click(addAlternative);
            $('#remove-alternative-btn').click(removeAlternative);
        };

        /**
         *  Cria o campo de pré-visualização da questão e o listener que 
         *  o atualiza a cada mudança
         */
        initPreview = function () {
            $("#exam-question-form").on("keyup", ".trumbowyg-editor", showPreview);
            showPreview();
        };

        /*
         *  Atualiza o campo de pré-visualização
         */
        showPreview = function () {
            var value = "";

            $("#exam-question-form").find(".trumbowyg-editor").each(function (e) {
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

        /*
         *  Carrega o MathJax e o inicializa na equação teste e na pré-visualização
         */
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
         *  Ativa o Trumbowyg nos campos selecionados
         *  Configuração Trumbowyg:
         *  -   Plugin Base64 - Permite fazer o upload de imagens como base64
         *  -   Localização em português  
         *  
         *  @param string elem - selector
         *      Exemplo: '.edit-textarea' Transforma todos os elementos da classe 
         *                                .edit-textarea em um campo Trumbowyg
         */
        setTrumbowyg = function (elem) {
            $.trumbowyg.langs.pt.base64 = "Inserir Imagem";
            
            $(elem).trumbowyg({
                removeformatPasted: true,
                lang: 'pt',
                btnsDef: {
                    justify: {
                        dropdown: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],                        
                        ico: 'justifyCenter'
                    }
                },
                btnsGrps: {
                    semantic: ['strong', 'em', 'underline']
                },
                btns: [
                    ['undo', 'redo'],
                    'formatting',
                    'btnGrp-semantic',
                    ['removeformat'],
                    'base64',
                    'btnGrp-justify',
                    'btnGrp-lists',
                    'horizontalRule',
                    ['fullscreen']
                ]
            });
        };

        return {
            init: function () {
                setFormBehavior();
                
                require(['trumbowyg'], function () {
                    require(['trumbowygpt', 'trumbowygbase64'], function () {                
                        setTrumbowyg('textarea');
                        $(".trumbowyg-editor").addClass("tex2jax_ignore");
                        initMathJax();
                    });
                });
            }
        };
    }());
    return addQuestion;
});