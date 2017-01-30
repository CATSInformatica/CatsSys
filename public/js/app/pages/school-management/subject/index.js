/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'jqueryui'], function () {
    var index = (function () {
        
        /*
         * Permite a criação de disciplinas.
         */
        initCreateFunctionality = function () {
            
            showCreateButtons();
            
            /*
             * Evento: clique em um botão de criar diciplina (.create-subject)
             * Abre o modal com o formulário de criação de disciplina. A disciplina 
             * criada será uma filha da disciplina base que abriga o botão.
             * 
             */
            $('.content').on('click', '.create-subject', function() {           
                var form = $('#subject-form');
                var baseSubjectBlock = $(this).closest('.base-subjects');
                var alertBlock = baseSubjectBlock.find('.subject-alert').first();
                
                var selectedParentSubjectBlock = baseSubjectBlock.find('.cats-selected-row').first();
                var parentSubjectId = 0;
                if (selectedParentSubjectBlock.length > 0) {
                    parentSubjectId = selectedParentSubjectBlock.data('id');
                    selectedParentSubjectBlock.click();
                } else {
                    parentSubjectId = baseSubjectBlock.data('id');
                }
                
                form.find('.subject-parent-input').first().val(parentSubjectId);

                clearForm(form);
                openFormDialog('Criar disciplina', 'Criar disciplina', function() { 
                    
                    requestSubjectCreation(form, alertBlock, function (response) {
                        var subjectBlock = createSubjectBlock(
                                response.subjectName, 
                                response.subjectDescription, 
                                response.subjectId, 
                                parentSubjectId);
                        
                        if (selectedParentSubjectBlock.length > 0) {
                            if ($('.parent-' + parentSubjectId).length > 0) {
                                $('.parent-' + parentSubjectId).last().after(subjectBlock);   
                            } else {
                                selectedParentSubjectBlock.after(subjectBlock);
                            }
                        } else {
                            baseSubjectBlock.find('.base-subject-children').first().append(subjectBlock);                        
                        }
                    }); 
                }); 
            });
            
            /*
             * Evento: clique no botão de criar diciplina base (.create-base-subject)
             * Abre o modal com o formulário de criação de disciplina. A disciplina 
             * criada será não terá uma mãe.
             * 
             */
            $('.content').on('click', '.create-base-subject', function() {
                var form = $('#subject-form');                
                var alertBlock = $('#base-subject-alert');
                
                form.find('.subject-parent-input').first().val(0);// 0 - NULL

                clearForm(form);
                openFormDialog('Criar disciplina base', 'Criar disciplina', function() { 
                    requestSubjectCreation(
                            form, 
                            alertBlock, 
                            function(response) {
                                var baseSubjectBlockTemplate = createBaseSubjectBlock(
                                        response.subjectName,
                                        response.subjectDescription,
                                        response.subjectId
                                        );
                                
                                $('#base-subjects-block').append(baseSubjectBlockTemplate);
                            }); 
                });  
            });
            
            /*
             * Mostra os botões de criar disciplina.
             */
            function showCreateButtons() {
                $('.create-subject, .create-base-subject').removeClass('hide');
            }
            
            /*
             * Faz a requisição de criação ao servidor e encarrega-se de tratar a 
             * resposta.
             * 
             * @param {object} form - jQuery object do formulário
             * @param {object} alertBlock - jQuery object do elemento onde 
             *      as mensagens de erro devem ser exibidas
             * @param {function} successFunc - função que será executada se a
             *      requisição for bem sucedida e a disciplina foi criada
             */
            function requestSubjectCreation(form, alertBlock, successFunc) {
                var request = $.ajax({
                    url: '/school-management/school-subject/create',
                    dataType: "json",
                    method: "POST",
                    data: form.serialize()
                });

                var failFunc = function(response) {
                    $("#form-modal").on('hidden.bs.modal', function() {
                        $("#form-modal").off('hidden.bs.modal');
                        $('#form-modal').modal('show');
                    });
                };

                requestHandler(request, form, alertBlock, successFunc, failFunc);
            };
            
        };

        /*
         * Permite a remoção de disciplinas.
         *  
         */
        initDeleteFunctionality = function () {
            
            showDeleteButtons();
            
            /*
             * Evento: Clique no botão de remover disciplina (.delete-subject)
             * Remove a disciplina relacionada ao botão.
             */
            $('.content').on('click', '.delete-subject', function() {
                var subjectBlock = $(this).closest('.subjects'); 
                
                openConfirmDeletionDialog(function() {
                    var baseSubjectBlock = subjectBlock.closest('.base-subjects');
                    var alertBlock = getAlertBlock(baseSubjectBlock);
                    
                    requestSubjectDeletion(subjectBlock, alertBlock);
                });
            });
            
            /*
             * Evento: clique no botão de remover disciplina base (.delete-base-subject)
             * Remove a disciplina relacionada ao botão.
             */
            $('.content').on('click', '.delete-base-subject', function() {
                var baseSubjectBlock = $(this).closest('.base-subjects'); 
                
                openConfirmDeletionDialog(function() {
                    var alertBlock = getAlertBlock(baseSubjectBlock);        

                    requestSubjectDeletion(baseSubjectBlock, alertBlock);
                });
            });
            
            /*
             * Exibe os botões de remover disciplina.
             */
            function showDeleteButtons() {    
                $('.delete-base-subject').removeClass('hide');
                $('.delete-subject').removeClass('hide');
            }
            
            /*
             * Abre um modal para o usuário confirmar sua intenção de remover uma
             * disciplina. Se o usuário confirmar, o modal é fechado e a função
             * handlerFunction é executada.
             * 
             * @param {function} handlerFunction - função que será executada se
             *      o usuário confirmar sua intenção de remover a disciplina
             */
            function openConfirmDeletionDialog(handlerFunction) {
                var confirmDeletionModal = $("#confirm-deletion-modal");
                
                confirmDeletionModal.modal('show');
                
                $('#confirm-deletion-button').off('click').on('click', function() {
                    confirmDeletionModal.modal('hide');
                    
                    handlerFunction();
                });
            }
            
            /*
             * Retorna o campo de mensagens de erro, dado um bloco de disciplina 
             * base.
             * 
             * @param {object} baseSubjectBlock - jQuery object de um bloco de 
             *      disciplina base.
             * @returns {object} jQuery object do bloco onde as mensagens de erro
             *      deverão ser exibidas.
             */
            function getAlertBlock(baseSubjectBlock) {
                return baseSubjectBlock.find('.subject-alert').first();
            }
            
            /*
             * Faz a requisição de remoção ao servidor e encarrega-se de tratar a 
             * resposta.
             * 
             * @param {object} subjectBlock - jQuery object de um bloco de 
             *      disciplina.
             * @param {object} alertBlock - jQuery object do elemento onde 
             *      as mensagens de erro devem ser exibidas
             */
            function requestSubjectDeletion(subjectBlock, alertBlock) {
                var subjectId = subjectBlock.data('id');
                
                if (!Number.isInteger(subjectId)) {
                    return;
                }
                
                // remove as disciplinas filhas
                $('.parent-' + subjectId).each(function() {
                    requestSubjectDeletion($(this), alertBlock);
                });
                
                // o intervalo é necessário para garantir que as disciplinas 
                // filhas sejam removidas antes da mãe.                
                var intervalToRemoval = setInterval(removeSubject, 400);
                
                /*
                 * Remove, de fato, a disciplina.
                 */
                function removeSubject() {
                    if ($('.parent-' + subjectId).length === 0) {
                        clearInterval(intervalToRemoval);
                        
                        var request = $.ajax('/school-management/school-subject/delete/' + subjectId);
                        request.done(function(response) {
                            alertBlock.text(response.message);
                            if (!response.error) {
                                subjectBlock.remove(); 
                            }
                        });

                        request.fail(function(jqXHR, textStatus) {
                            alertBlock.text("Ocorreu um erro. A disciplina não foi removida.<br>");
                        });
                    }
                }
            }
        };

        /*
         * Permite a edição de disciplinas.
         */
        initEditFunctionality = function() {
            
            showEditButtons();
            
            /*
             * Evento: clique no botão de editar disciplina base (.edit-base-subject)
             * Abre o modal com o formulário de edição de disciplina.
             */
            $('.content').on('click', '.edit-base-subject', function() {
                var baseSubjectBlock = $(this).closest('.base-subjects');

                var baseSubjectNameBlock = baseSubjectBlock.find('.base-subject-name').first();
                var baseSubjectDescriptionBlock = baseSubjectBlock.find('.base-subject-description').first();
                var form = $('#subject-form');
                
                clearForm(form);
                form.find('.subject-name-input').first().val(baseSubjectNameBlock.text().trim());
                form.find('.subject-description-input').first().val(baseSubjectDescriptionBlock.text().trim());
                form.find('.subject-parent-input').first().val(0);// 0 - NULL
                
                openFormDialog('Editar disciplina', 'Editar disciplina', function() {
                    requestSubjectEdit(
                            baseSubjectBlock.data('id'), 
                            form, 
                            baseSubjectBlock.find('.subject-alert').first(), 
                            function(response) {                                
                                baseSubjectNameBlock.text(response.subjectName);                          
                                baseSubjectDescriptionBlock.text(response.subjectDescription);
                            });
                });
            });
            
            /*
             * Evento: clique no botão de editar disciplina (.edit-subject)
             * Abre o modal com o formulário de edição de disciplina.
             */
            $('.content').on('click', '.edit-subject', function() {
                var baseSubjectBlock = $(this).closest('.base-subjects');
                var alertBlock = baseSubjectBlock.find('.subject-alert').first();
                var form = $('#subject-form');
                
                var subjectBlock = $(this).closest('.subjects');
                var subjectNameBlock = subjectBlock.find('.subject-name').first();
                var subjectDescriptionBlock = subjectBlock.find('.subject-description').first();
                var subjectParentBlock = subjectBlock.find('.subject-parent').first();
                
                clearForm(form);
                form.find('.subject-name-input').first().val(subjectNameBlock.text().trim());
                form.find('.subject-description-input').first().val(subjectDescriptionBlock.text().trim());
                form.find('.subject-parent-input').first().val(subjectBlock.data('parent-id'));
                
                openFormDialog('Editar disciplina', 'Editar disciplina', function() {
                    requestSubjectEdit(
                            subjectBlock.data('id'), 
                            form, 
                            baseSubjectBlock.find('.subject-alert').first(), 
                            function(response) {                                
                                subjectNameBlock.text(response.subjectName);                          
                                subjectDescriptionBlock.text(response.subjectDescription);
                            });
                });
            });
            
            /*
             * Evento: clique no botão de editar disciplina mãe (.edit-parent-subject)
             * Permite a seleção de outra disciplina como mãe.
             */
            $('.content').on('click', '.edit-parent-subject', function() {
                if ($('.parent-edit').length === 0 || $(this).hasClass('parent-edit')) { // inicia a troca
                    toggleAvailableParentsIcons($(this));
                } else if ($(this).find('i.fa-exchange').length > 0) {
                    toggleAvailableParentsIcons($('.parent-edit').first());
                    toggleAvailableParentsIcons($(this));                    
                } else if ($(this).find('i.fa-caret-square-o-down').length > 0) { // realiza a troca
                    var subjectBlock = getSubjectBlock($('.parent-edit').first());
                    var subjectId = subjectBlock.data('id'); 
                    var subjectName = '';                 
                    var subjectDescription = '';          
                    
                    if (subjectBlock.hasClass('subjects')) {
                        subjectName = subjectBlock.find('.subject-name').first().html().trim();
                        subjectDescription = subjectBlock.find('.subject-description').first().html().trim();
                    } else {
                        subjectName = subjectBlock.find('.base-subject-name').first().html().trim();
                        subjectDescription = subjectBlock.find('.base-subject-description').first().html().trim(); 
                    }  
                    
                    var parentSubjectBlock = getSubjectBlock($(this));
                    var parentSubjectId = 0;  
                    if (parentSubjectBlock !== null) {
                        parentSubjectId = parentSubjectBlock.data('id');                          
                    }  
                    
                    var form = $('#subject-form');   
                    form.find('.subject-name-input').first().val(subjectName);
                    form.find('.subject-description-input').first().val(subjectDescription);
                    form.find('.subject-parent-input').first().val(parentSubjectId);                        
                    
                    var alertBlock = $(this).closest('.base-subjects').find('.subject-alert').first();
                    
                    requestSubjectEdit(subjectId, form, alertBlock, successfulEdit);
                            
                    
                    /*
                     * Em caso de uma edição bem sucedida, faz as mudanças necessárias, 
                     * movendo a disciplina e suas filhas.
                     * 
                     * @param {object} response - resposta do servidor
                     */
                    function successfulEdit(response) {
                        toggleAvailableParentsIcons($('.parent-edit').first());
                        var baseSubjectChildren = subjectBlock.closest('.base-subject-children');
                        
                        if (parentSubjectBlock === null) {
                            subjectBlock.remove();
                            
                            var baseSubjectBlockTemplate = createBaseSubjectBlock(
                                    subjectName,
                                    subjectDescription,
                                    subjectId
                                    ); 

                            $('#base-subjects-block').append(baseSubjectBlockTemplate);
                            
                        } else if (parentSubjectBlock.hasClass('base-subjects') || parentSubjectBlock.hasClass('subjects')) {
                            if (subjectBlock.hasClass('base-subjects')) {
                                subjectBlock.detach();
                                baseSubjectChildren = subjectBlock;
                            } else {
                                subjectBlock.remove();                                
                            }
                            appendChildToSubject(parentSubjectBlock, subjectId, response.subjectName, response.subjectDescription);
                        }  
                        
                        moveSubjectChildren(subjectId, baseSubjectChildren);
                    }
                }
                
                /*
                 * Anexa as disciplinas filhas abaixo da disciplina, onde quer 
                 * que ela esteja agora.
                 * 
                 * @param {int} parentSubjectId - id da disciplina mãe
                 * @param {object} childrenParentBlock - jQuery object do bloco 
                 *      que abriga as disciplinas filhas, antes da mudança
                 */
                function moveSubjectChildren(parentSubjectId, childrenParentBlock) {
                    var parentSubjectBlock = $('#subject-' + parentSubjectId);
                    
                    childrenParentBlock.find('.parent-' + parentSubjectId).each(function() {
                        appendChildToSubject(
                                parentSubjectBlock, 
                                $(this).data('id'),
                                $(this).find('.subject-name').first().text().trim(),
                                $(this).find('.subject-description').first().text().trim());
                        $(this).remove();
                    });  
                }
            });
            
            /*
             * Exibe os ícones que permitem a edição das disciplinas.
             */
            function showEditButtons() {    
                $('.edit-base-subject').removeClass('hide');
                $('.edit-subject').removeClass('hide');
                $('.edit-parent-subject').removeClass('hide');
            }
            
            /*
             * Faz a requisição de edição ao servidor e encarrega-se de tratar a 
             * resposta.
             * 
             * @param {type} subjectId - id da disciplina
             * @param {type} form - jQuery object do formulário
             * @param {type} alertBlock - jQuery object do elemento onde 
             *      as mensagens de erro devem ser exibidas
             * @param {type} successFunc - função que será executada se a
             *      requisição for bem sucedida e a disciplina foi editada
             * @returns {undefined}
             */
            function requestSubjectEdit(subjectId, form, alertBlock, successFunc) {
                if (!Number.isInteger(subjectId)) {
                    return;
                }
                
                var request = $.ajax({
                    url: '/school-management/school-subject/edit/' + subjectId,
                    dataType: "json",
                    method: "POST",
                    data: form.serialize()
                });
                
                var failFunc = function(response) {
                    $("#form-modal").on('hidden.bs.modal', function() {
                        $("#form-modal").off('hidden.bs.modal');
                        $('#form-modal').modal('show');
                    });
                };

                requestHandler(request, form, alertBlock, successFunc, failFunc);
            };            
        };
        
        /*
         * @returns {String} - classe de cor de fundo aleatória
         */
        getRandomColorClass = function() {
            var colors = ['teal', 'purple', 'orange', 'navy', 'maroon', 'black', 'primary'];
            return 'bg-' + colors[Math.floor(Math.random() * (colors.length - 1))];
        };
        
        /*
         * Impede comportamentos indesejados.
         */
        preventUnwantedBehavior = function() {
            /*
             * Impede que, ao clicar em um botão, a disciplina seja selecionada.
             */
            $('.content').on('click', '.cats-row > td > button', function(e) {
                e.stopPropagation();
            });
            
            /*
             * Impede a seleção de múltiplas linhas de disciplina, dentro de uma 
             * única discplina base.
             */
            $('.content').on('click', '.cats-row > td', function(e) {                
                $(this).parent().siblings('.cats-selected-row').click();
            });
            
            /*
             * Ao abrir o formulário de confirmação de remoção, foca o botão 
             * de cancelar remoção.
             */
            $("#confirm-deletion-modal").on('shown.bs.modal', function() {
                $('#confirm-deletion-modal .cancel-button').first().focus();
            });       
            
            /*
             * Ao abrir o formulário de criação/edição, foca o campo de nome da 
             * disciplina.
             */
            $("#form-modal").on('shown.bs.modal', function() {
                $('#form-modal').find('.subject-name-input').first().focus();
            });
        };     
        
        /*
         * Define atalhos do teclado.
         */
        initKeyboardShortcuts = function() {      
            /*
             * Permite remover a seleção de uma disciplina com a tecla ESC.
             */
            $(document).keyup(function(e) {
                var ESC = 27;
                var activatedEditButton = $('.parent-edit').first();
                
                if (e.keyCode === ESC && activatedEditButton.length > 0) {
                    toggleAvailableParentsIcons(activatedEditButton);
                } 
            });
        };
          
        /*
         * Procura todas as disciplinas que podem ser mãe da disciplina 
         * ligada ao botão selecionado. Para indicar a disponibilidade da disciplina,
         * seu botão de 'trocar disciplina mãe' é trocado pelo de 'selecionar como
         * mãe'.
         * 
         * @param {type} selectedButton - botão de 'trocar disciplina mãe' selecionado
         */
        toggleAvailableParentsIcons = function(selectedButton) { 
            var subjectBlock = getSubjectBlock(selectedButton);
            
            if (selectedButton.hasClass('parent-edit')) {
                selectedButton.removeClass('bg-yellow').removeClass('parent-edit');
                var otherEditParentIcons = $('#base-subjects-block .fa-caret-square-o-down');

                otherEditParentIcons.each(function() {
                    $(this).removeClass('fa-caret-square-o-down').addClass('fa-exchange');
                });
                
                if (!subjectBlock.hasClass('base-subjects')) {
                    $('.null-parent-button').slideUp();
                }
            } else {
                selectedButton.addClass('bg-yellow').addClass('parent-edit');

                var subjectHasChildren = ($('.parent-' + subjectBlock.data('id')).length > 0) ? true : false;
                var subjectHasGrandchildren = (subjectBlock.find('.topics').length > 0) ? true : false;

                var otherEditParentIcons = '';
                if (subjectHasGrandchildren) {          
                    return;
                } else if (subjectHasChildren) {
                    otherEditParentIcons = $('.base-subject-header').find('.edit-parent-subject').not('.parent-edit');
                } else {
                    otherEditParentIcons = $('.subjects:not(.topics), .base-subject-header').find('.edit-parent-subject').not('.parent-edit');
                }

                otherEditParentIcons.each(function() {
                    $(this).find('i').first().removeClass('fa-exchange').addClass('fa-caret-square-o-down');
                });
                
                if (!subjectBlock.hasClass('base-subjects')) {
                    $('.null-parent-button').slideDown();
                }
            }
        };
         
        /*
         * Limpa os campos e as mensagens de erro do formulário.
         * 
         * @param {type} form - jQuery object do formulário
         */
        clearForm = function(form) {
            form.find('.subject-name-error').first().text('');
            form.find('.subject-description-error').first().text('');

            form.find('.subject-name-input').first().val('');
            form.find('.subject-description-input').first().val('');
        };
         
        /*
         * Encarrega-se de gerenciar a resposta do servidor a uma requisição, 
         * exibindo as mensagens de erro e executando as funções definidas pelo
         * usuário.
         * 
         * @param {jqXHR object} request - requisição
         * @param {type} form - jQuery object do formulário
         * @param {type} alertBlock - jQuery object do elemento onde 
         * @param {type} successFunc - função que será executada se a requisição 
         *      for bem sucedida e não retornar erros
         * @param {type} failFunc - função que será executada se a requisição 
         *      for bem sucedida mas retornar erros
         */
        requestHandler = function (request, form, alertBlock, successFunc, failFunc) {
            // requisição bem sucedida - resposta do servidor
            request.done(function(response) {
                // disciplina criada
                if (!response.error) {
                    // exibe a mensagem retornada
                    alertBlock.html(response.message + '<br>');

                    if (typeof successFunc === "function") {
                        successFunc(response);
                    }
                } else {
                    // a disciplina não foi criada, exibe todas as mensagens de erro 
                    var name = '';
                    for(name in response.formErrors.subjectName) {
                        form
                                .find('.subject-name-error')
                                .first()
                                .html(response.formErrors.subjectName[name] + '<br>');
                    }
                    for(name in response.formErrors.subjectDescription) {
                        form
                                .find('.subject-description-error')
                                .first()
                                .html(response.formErrors.subjectDescription[name] + '<br>');
                    }
                    
                    if (typeof failFunc === "function") {
                        failFunc(response);
                    }
                }
            });

            // requisição mal sucedida
            request.fail(function(jqXHR, textStatus) {
                alertBlock.html("Ocorreu um erro. A disciplina não foi criada!<br>");
            });
        };

        /*
         * Abre o modal com o formulário de adição/edição de disciplinas.
         * 
         * @param {String} dialogTitle - título do modal
         * @param {String} confirmButtonMessage - título do botão de confirmação
         * @param {function} handlerFunction - função que será executada se o
         *      usuário clicar no botão de confirmação
         */
        openFormDialog = function(dialogTitle, confirmButtonMessage, handlerFunction) {
            var form = $('#form-modal');
            
            $('#formModalName').text(dialogTitle);
            $('#confirm-button').text(confirmButtonMessage);
            
            form.modal('show');

            $('#confirm-button').off('click').on('click', function() {
                form.modal('hide');

                handlerFunction();
            });
        };

        /*
         * Dado um elemento qualquer, retorna o jQuery object do bloco de disciplina
         * (disciplina base, disciplina, tópico) a qual o elemento faz parte. Se
         * não fizer parte de nenhum, retorna null.
         * 
         * @param {object} element - jQuery object que representa um ou mais
         *      elementos do DOM.
         * @returns {object} - jQuery object - jQuery object do bloco de disciplina
         *      a qual o objeto 'element' pertence
         */
        getSubjectBlock = function(element) {
            var topic = element.closest('.topics');
            if (topic.length > 0) {
                return topic;
            }

            var subject = element.closest('.subjects');
            if (subject.length > 0) {
                return subject;
            }

            var baseSubject = element.closest('.base-subjects');
            if (baseSubject.length > 0) {
                return baseSubject;
            }
            
            return null;
        };
            
            
        /*
         * Cria um bloco de disciplina base.
         * 
         * @param {String} name - nome da disciplina base
         * @param {String} description - descrição da disciplina base
         * @param {String|int} id - id da disciplina base
         * @returns {object} - jQuery object do bloco de disciplina base criado
         */
        createBaseSubjectBlock = function(name, description, id) {
            // template
            var baseSubjectBlockTemplate = $('#base-subject-block-template')
                    .clone()
                    .removeClass('hide');

            // cor de fundo aleatória
            var bgColorClass = getRandomColorClass();

            if (id === 'undefined' || id === '') { // sem atributos data-id e id
                baseSubjectBlockTemplate.removeAttr('id');
                baseSubjectBlockTemplate.removeAttr('data-id');
            } else {
                baseSubjectBlockTemplate.attr('id', 'subject-' + id);
                baseSubjectBlockTemplate.attr('data-id', id);                    
            }
            
            baseSubjectBlockTemplate.attr('data-parent-id', 0);

            // insere nome, descrição e classe da cor no bloco
            baseSubjectBlockTemplate.find('.base-subject-name').first().html(name);
            baseSubjectBlockTemplate.find('.base-subject-description').first().html(description);
            baseSubjectBlockTemplate.find('.bg-color-class').first().addClass(bgColorClass);

            return baseSubjectBlockTemplate;
        };

        /*
         * Cria um bloco de disciplina.
         * 
         * @param {String} name - nome da disciplina
         * @param {String} description - descrição da disciplina
         * @param {String|int} id - id da disciplina
         * @param {String|int} parentId - id da disciplina mãe
         * @returns {object} - jQuery object do bloco de disciplina criado
         */
        createSubjectBlock = function(name, description, id, parentId) {
            // template
            var subjectBlockTemplate = $('.subject-block-template')
                    .first()
                    .clone()
                    .addClass('parent-' + parentId)  
                    .removeClass('hide')  
                    .removeClass('subject-block-template');                

            subjectBlockTemplate
                    .attr('id', 'subject-' + id)
                    .attr('data-parent-id', parentId)  
                    .attr('data-id', id);

            // insere nome, descrição e classe da cor no bloco
            subjectBlockTemplate.find('.subject-name').first().html(name);
            subjectBlockTemplate.find('.subject-description').first().html(description);

            if ($('#subject-' + parentId).hasClass('subjects')) {
                subjectBlockTemplate
                        .addClass('bg-gray')
                        .addClass('topics')
                        .removeClass('cats-row');
                subjectBlockTemplate
                        .find('.subject-name')
                        .first()
                        .removeClass('col-xs-12')
                        .addClass('col-xs-10')
                        .before($('#child-subject-icon-template > div').clone());
            }

            return subjectBlockTemplate;
        };
        
        /*
         * Dado o bloco de uma disciplina mãe, anexa uma disciplina filha.
         * 
         * @param {object} parentSubjectBlock - jQuery object do bloco da disciplina mãe
         * @param {String|int} id - id da disciplina filha
         * @param {String} name - nome da disciplina filha
         * @param {String} description - descrição da disciplina filha
         */
        appendChildToSubject = function(parentSubjectBlock, id, name, description) {
            var parentSubjectId = parentSubjectBlock.data('id');
            var childSubjectBlock = createSubjectBlock(
                    name,
                    description,
                    id,
                    parentSubjectId);
            if (parentSubjectBlock.hasClass('base-subjects')) {
                var baseSubjectChildren = parentSubjectBlock.find('.base-subject-children').first();
                baseSubjectChildren.append(childSubjectBlock);    
            } else if (parentSubjectBlock.hasClass('subjects')) { 
                var baseSubjectChildren = parentSubjectBlock.closest('.base-subject-children');
                if (baseSubjectChildren.find('.parent-' + parentSubjectId).length > 0) {
                    baseSubjectChildren.find('.parent-' + parentSubjectId).last().after(childSubjectBlock);   
                } else {
                    parentSubjectBlock.after(childSubjectBlock);  
                }
            }
        };

        return {
            init: function () {
                preventUnwantedBehavior();
                initKeyboardShortcuts();
                initCreateFunctionality();
                initDeleteFunctionality();
                initEditFunctionality();
            }
        };

    }());    
    
    return index;
});