/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'jqueryui'], function () {
    var index = (function () {
        
        initCreateFunctionality = function () {            
            /*
             * 
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
                                        response.subjectId,
                                        getRandomColorClass()
                                        );
                                
                                $('#base-subjects-block').append(baseSubjectBlockTemplate);
                            }); 
                });  
            });
            
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

        initDeleteFunctionality = function () {
            
            $('.content').on('click', '.delete-subject', function() {
                var subjectBlock = $(this).closest('.subjects'); 
                
                openConfirmDeletionDialog(function() {
                    var baseSubjectBlock = subjectBlock.closest('.base-subjects');
                    var alertBlock = getAlertBlock(baseSubjectBlock);
                    
                    requestSubjectDeletion(subjectBlock, alertBlock);
                });
            });
            
            $('.content').on('click', '.delete-base-subject', function() {
                var baseSubjectBlock = $(this).closest('.base-subjects'); 
                
                openConfirmDeletionDialog(function() {
                    var alertBlock = getAlertBlock(baseSubjectBlock);        

                    requestSubjectDeletion(baseSubjectBlock, alertBlock);
                });
            });
            
            function openConfirmDeletionDialog(handlerFunction) {
                var confirmDeletionModal = $("#confirm-deletion-modal");
                
                confirmDeletionModal.modal('show');
                
                $('#confirm-deletion-button').off('click').on('click', function() {
                    confirmDeletionModal.modal('hide');
                    
                    handlerFunction();
                });
            }
            
            function getAlertBlock(baseSubjectBlock) {
                return baseSubjectBlock.find('.subject-alert').first();
            }
            
            function requestSubjectDeletion(subjectBlock, alertBlock) {
                var subjectId = subjectBlock.data('id');
                
                if (!Number.isInteger(subjectId)) {
                    return;
                }
                
                $('.parent-' + subjectId).each(function() {
                    requestSubjectDeletion($(this), alertBlock);
                });
                
                var intervalToRemoval = setInterval(removeSubject, 400);
                
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

        initEditFunctionality = function() {
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
                            
                            
                    function successfulEdit(response) {
                        toggleAvailableParentsIcons($('.parent-edit').first());
                        var baseSubjectChildren = subjectBlock.closest('.base-subject-children');
                        
                        //base subject | subject | topic
                        if (parentSubjectBlock === null) {
                            // subjectBlock -> !subject c/ filhos | subject s/ filhos | topic
                            subjectBlock.remove();
                            
                            var baseSubjectBlockTemplate = createBaseSubjectBlock(
                                    subjectName,
                                    subjectDescription,
                                    subjectId,
                                    getRandomColorClass()
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
        
        getRandomColorClass = function() {
            var colors = ['teal', 'purple', 'orange', 'navy', 'maroon', 'black', 'primary'];
            return 'bg-' + colors[Math.floor(Math.random() * (colors.length - 1))];
        };
        
        preventUnwantedBehavior = function() {
            $('.content').on('click', '.cats-row > td > button', function(e) {
                e.stopPropagation();
            });
            
            $('.content').on('click', '.cats-row > td', function(e) {                
                $(this).parent().siblings('.cats-selected-row').click();
            });          
            
            $("#confirm-deletion-modal").on('shown.bs.modal', function() {
                $('.cancel-button').first().focus();
            });       
            
            $("#form-modal").on('shown.bs.modal', function() {
                $('#form-modal').find('.subject-name-input').first().focus();
            });
            
            $(document).keyup(function(e) {
                var ESC = 27;
                var activatedEditButton = $('.parent-edit').first();
                
                if (e.keyCode === ESC && activatedEditButton.length > 0) {
                    toggleAvailableParentsIcons(activatedEditButton);
                } 
            });
        };        
          
        toggleAvailableParentsIcons = function(clickedButton) { 
            var subjectBlock = getSubjectBlock(clickedButton);
            
            if (clickedButton.hasClass('parent-edit')) {
                clickedButton.removeClass('bg-yellow').removeClass('parent-edit');
                var otherEditParentIcons = $('.fa-caret-square-o-down:not(.null-parent)');

                otherEditParentIcons.each(function() {
                    $(this).removeClass('fa-caret-square-o-down').addClass('fa-exchange');
                });
                
                if (!subjectBlock.hasClass('base-subjects')) {
                    $('.null-parent-button').slideUp();
                }
            } else {
                clickedButton.addClass('bg-yellow').addClass('parent-edit');

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
        } 
         
        clearForm = function(form) {
            form.find('.subject-name-error').first().text('');
            form.find('.subject-description-error').first().text('');

            form.find('.subject-name-input').first().val('');
            form.find('.subject-description-input').first().val('');
        };
         
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
        
        createFormInputTemplates = function(parentSubject) {
            if (parentSubject === 'undefined') {
                parentSubject = 0;
            }

            var nameErrorBlock = $('#subject-error-block-template > p')
                    .clone()
                    .addClass('subject-name-error');
            var descriptionErrorBlock = $('#subject-error-block-template > p')
                    .clone()
                    .addClass('subject-description-error');
    
            var formTemplate = $('#subject-form')
                    .clone()
                    .attr('class', '')
                    .removeAttr('id');

            // campo de entrada para o nome e local exibir mensagens de erro
            var nameInputTemplate = formTemplate
                    .find('.subject-name-input')
                    .first()
                    .after(nameErrorBlock)
                    .parent();

            // campo de entrada para a descrição e local exibir mensagens de erro
            var descriptionInputTemplate = formTemplate
                    .find('.subject-description-input')
                    .first()
                    .after(descriptionErrorBlock)
                    .parent();

            // campo de entrada para a disciplina base
            var parentSubjectInputTemplate = formTemplate
                    .find('.subject-parent-input')
                    .first()
                    .val(parentSubject)
                    .parent()
                    .addClass('hide');

            return {
                name: nameInputTemplate,
                description: descriptionInputTemplate,
                parentSubject: parentSubjectInputTemplate
            };
        };

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
         * 
         * @param {type} name
         * @param {type} description
         * @param {type} id
         * @param {type} bgColorClass
         * @returns {undefined}
         */
        createBaseSubjectBlock = function(name, description, id, bgColorClass) {
            // template
            var baseSubjectBlockTemplate = $('#base-subject-block-template')
                    .clone()
                    .removeClass('hide');

            // cor de fundo aleatória
            if (bgColorClass === 'undefined' || bgColorClass === 'random') {
                // cores definidas
                bgColorClass = getRandomColorClass()
            }


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
         * 
         * @param {type} name
         * @param {type} description
         * @param {type} id
         * @returns {undefined}
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
        
        appendChildToSubject = function(parentSubjectBlock, id, name, description) {
            var parentSubjectId = parentSubjectBlock.data('id');
            var childSubjectBlock = createSubjectBlock(
                    name,
                    description,
                    id,
                    parentSubjectId);
            if (parentSubjectBlock.hasClass('base-subjects')) {
                console.log('not ary');
                var baseSubjectChildren = parentSubjectBlock.find('.base-subject-children').first();
                baseSubjectChildren.append(childSubjectBlock);    
            } else if (parentSubjectBlock.hasClass('subjects')) {  
                console.log('ary');
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
                initCreateFunctionality();
                initDeleteFunctionality();
                initEditFunctionality();
            }
        };

    }());    
    
    return index;
});