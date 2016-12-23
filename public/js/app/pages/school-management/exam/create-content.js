/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
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

define(['jquery', 'datetimepicker', 'jqueryui'], function () {
    var createContent = (function () {  

        /**
         * Cópia do JSON do conteúdo
         * 
         * {
         *       questionsStartAtNumber: <number>,
         *       numberOfQuestions: <number>,
         *       groups: [
         *           {
         *               id: <number>,
         *               groupName: <string>,
         *               subgroups: [
         *                   {
         *                       id: <number>,
         *                       subgroupName: <string>,
         *                       singleColumn: <boolean>,
         *                       numberOfProposedQuestions: <number>,
         *                       questions: [
         *                           {
         *                               id: <number>,
         *                               subjectId: <number>,
         *                               subjectName: <string>
         *                           },
         *                           ...
         *                       ],
         *                   },
         *                   // disciplinas paralelas
         *                   [
         *                       <subject>, 
         *                       ...
         *                   ],
         *                   ...
         *               ]
         *           },
         *           ...
         *       ]
         *   }
         * 
         */
        var contentConfig;         
        
        // Variável para evitar a repetição de identificadores de 
        // disciplinas paralelas e disciplinas de coluna única
        var totalParallelGroups = 0;
        var totalSingleColumnSubjects = 0;
        
        /*
         * Eventos que envolvem o formulário
         * 
         */
        createContentListeners = function() {
            /*
             * Atualiza o total de questões
             * 
             */
            $('.amount-input').change(function () {
                var count = 0;
                $('.amount-input').each(function () {
                    if ($(this).val() !== '') {
                        count += +$(this).val();
                    }
                });
                $('#question-count').html(count);
            });
            
            /*
             * Antes de submeter o formulário, anexa o JSON do conteúdo
             *  
             */
            $("#exam-content-form").submit( function() {
                var numberOfQuestions = 0;
                var groups = [];

                $('.base-subjects').each(function() {
                    var baseSubjectId = +$(this).data('id');
                    var subgroups = [];

                    $(this).find('.subjects').each(function() {        
                        if ($(this).hasClass('subject-done'))
                            return;

                        var subjectId = +$(this).data('id');
                        var quantity = +$(this).find('.quantity-block > .amount-input').val();
                        
                        numberOfQuestions += quantity;
                        
                        var questions = [];
                        if (contentConfig !== null) {
                            questions = getSubjectQuestions(contentConfig, baseSubjectId, subjectId);
                        }

                        var subject = {
                            id: subjectId,
                            subgroupName: $(this).data('name'),
                            singleColumn: $(this).hasClass('single-column-subject') ? true : false,                         
                            numberOfProposedQuestions: quantity,
                            questions: questions
                        };

                        if ($(this).hasClass('parallel-subject')) {
                            var parallelGroup = [];

                            parallelGroup.push(subject);
                            $(this).addClass('subject-done');

                            var group = +$(this).data('parallel-group');
                            $(this).siblings('.parallel-group-' + group).each(function() {
                                var subjectId = +$(this).data('id');
                                var quantity = +$(this).find('.quantity-block > .amount-input').val();
                                var questions = [];

                                if (contentConfig !== null) {
                                    questions = getSubjectQuestions(contentConfig, baseSubjectId, subjectId);
                                }

                                subject = {
                                    id: +$(this).data('id'),
                                    subgroupName: $(this).data('name'),
                                    singleColumn: $(this).hasClass('single-column-subject') ? true : false,
                                    numberOfProposedQuestions: quantity,
                                    questions: questions
                                };
                                parallelGroup.push(subject);
                                numberOfQuestions += quantity;

                                $(this).addClass('subject-done');
                            });

                            subgroups.push(parallelGroup);
                        } else {
                            subgroups.push(subject);
                        }
                    });
                    groups.push({
                        id: $(this).data('id'),
                        groupName: $(this).data('name'),
                        subgroups: subgroups
                    });
                });  
                
                $('<input />').attr('type', 'hidden')
                    .attr('name', "contentJson")
                    .attr('value', 
                            JSON.stringify({
                                questionsStartAtNumber: +$('#start-number').val(),
                                numberOfQuestions: numberOfQuestions,
                                groups: groups
                            }))
                    .appendTo('form');                
                
                return true;
                
                
                function getSubjectQuestions(contentConfig, baseSubjectId, subjectId) {
                    for (var i = 0; i < contentConfig.groups.length; ++i) {
                        if (contentConfig.groups[i].id === baseSubjectId) {

                            for (var j = 0; j < contentConfig.groups[i].subgroups.length; ++j) {
                                // disciplina paralela
                                if (Array.isArray(contentConfig.groups[i].subgroups[j])) {
                                    for (var k = 0; k < contentConfig.groups[i].subgroups[j].length; ++k) {
                                        if (contentConfig.groups[i].subgroups[j][k].id === subjectId) {
                                            return contentConfig.groups[i].subgroups[j][k].questions;
                                        }
                                    }
                                } else if (contentConfig.groups[i].subgroups[j].id === subjectId) {
                                    return contentConfig.groups[i].subgroups[j].questions;
                                }
                            }
                        }
                    }

                    return [];
                };
            });
            
            /*
             *  Ao selecionar duas ou mais disciplinas e clicar no botão com a 
             *  classe 'select-parallel-subjects', cria um conjunto de disciplinas
             *  paralelas.
             * 
             */
            $('.select-parallel-subjects').click(function() {
                var baseSubject = $(this).closest('.base-subjects');
                var subjects = baseSubject.find('.cats-selected-row');
                var errorMessage = baseSubject.find('.parallel-group-error');
                
                var errorCheck = parallelSubjectsErrorCheck(subjects);
                
                errorMessage.text(errorCheck.message);
                if (errorCheck.error) {
                    return;
                }
                
                var totalSubjects = subjects.length;
                var subjectNames = '';
                var parallelGroups = baseSubject.find('.parallel-groups');

                subjects.each(function(i) {         
                    subjectNames += $(this).data('name') + (i === totalSubjects - 1 ? '' : ', ');
                });
                                
                subjects.each(function() {
                    $(this).data('parallel-group', totalParallelGroups);
                    $(this).addClass('parallel-subject');
                    $(this).addClass('parallel-group-' + totalParallelGroups);
                    $(this).removeClass('cats-selected-row');
                    $(this).find('.cats-selected-bg').each(function() {
                        $(this).removeClass('cats-selected-bg');
                    });
                });
                parallelGroups.append('<span data-group="' + totalParallelGroups + '">'
                        + subjectNames
                        + '<i class="fa fa-close remove-parallel-group text-red" aria-hidden="true" style="cursor: pointer; margin-left: 5px;"></i>'
                        + '<br></span>');
                
                ++totalParallelGroups;
            });
            
            /*
             *  Ao selecionar uma disciplina e clicar no botão com a classe 
             *  'select-single-column-subjects', ela se torna de coluna única.
             * 
             */
            $('.select-single-column-subjects').click(function() {
                var baseSubject = $(this).closest('.base-subjects');
                var subjects = baseSubject.find('.cats-selected-row');
                var singleColumnSubjects = baseSubject.find('.single-column-subjects');
                                
                subjects.each(function() {
                    var conflictCheck = subjectFormattingConflict($(this), 'single-column');
                    if (conflictCheck.conflict) {
                        baseSubject.find('.single-column-subject-error')
                                .text(conflictCheck.message);
                        return;
                    }
                    if ($(this).hasClass('single-column-subject')) {
                        return;
                    }
                    
                    $(this).data('single-column-subject', totalSingleColumnSubjects);
                    $(this).addClass('single-column-subject');
                    $(this).addClass('single-column-subject-' + totalSingleColumnSubjects);
                    
                    $(this).removeClass('cats-selected-row');
                    $(this).find('.cats-selected-bg').each(function() {
                        $(this).removeClass('cats-selected-bg');
                    });             
                    
                    singleColumnSubjects.append('<span data-single-column-subject="' + totalSingleColumnSubjects + '">'
                            + $(this).data('name')
                            + '<i class="fa fa-close remove-single-column-subject text-red" aria-hidden="true" style="cursor: pointer; margin-left: 5px;"></i>'
                            + '<br></span>');
                    
                    ++totalSingleColumnSubjects;
                });
            });
            
            /*
             * Ao clicar no ícone de remoção, ao lado de um grupo de disciplinas
             * paralelas, remove a paralelidade entre as disciplinas do grupo. 
             * 
             */
            $('body').on('click', '.remove-parallel-group', function() {
                var group = +$(this).parent().data('group');
                $('.parallel-group-' + group).each(function() {
                    $(this).removeClass('parallel-subject');
                    $(this).removeClass('parallel-group-' + group);
                });
                $(this).closest('.parallel-block').find('.parallel-group-error').text('');
                $(this).parent().remove();
            });
            
            /*
             * Ao clicar no ícone de remoção, ao lado de uma disciplina de 
             * coluna única, remove esta característica da disciplina.
             * 
             */
            $('body').on('click', '.remove-single-column-subject', function() {
                var singleColumnSubject = +$(this).parent().data('single-column-subject');
                $('.single-column-subject-' + singleColumnSubject)
                        .removeClass('single-column-subject')
                        .removeClass('single-column-subject-' + singleColumnSubject);
                $(this).closest('.single-column-block').find('.single-column-subject-error').text('');
                $(this).parent().remove();
            });
        };

        /**
         * Verifica se o usuário selecionou as disciplinas corretamente
         * 
         * @param {JQuery Object} subjects - disciplinas selecionadas 
         * @returns {object}
         * Objeto de retorno: 
         * returnObj = {
         *      error: <boolean> // se há um erro, true
         *      message: <string> // se há um erro, mensagem do erro
         * }
         */
        parallelSubjectsErrorCheck = function(subjects) {
            var totalSubjects = subjects.length;
            var returnObj = {
                error: false,
                message: ''                        
            };            

            if (totalSubjects < 2) {
                returnObj = {
                    error: true,
                    message: 'Um grupo de disciplinas paralelas deve possuir, ao menos, duas disciplinas.'
                };
            } else {
                subjects.each(function() {
                    var conflictCheck = subjectFormattingConflict($(this), 'parallel');
                    if (conflictCheck.conflict) {
                        returnObj = {
                            error: true,
                            message: conflictCheck.message
                        };
                        return false;
                    }
                    
                    if ($(this).hasClass('parallel-subject')) {
                        returnObj = {
                            error: true,
                            message: 'Não é possível que uma disciplina faça parte de mais de um grupo de disciplinas paralelas.'                        
                        };
                        return false;
                    }
                });
            }

            return returnObj;
        };
        
        /**
         * Verifica se o usuário está tentando tornar uma disciplina de coluna 
         * única e paralela, ao mesmo tempo.
         * 
         * @param {JQuery Object} subject - disciplina em questão
         * @returns {object}
         * Objeto de retorno: 
         * returnObj = {
         *      conflict: <boolean> // se há um conflito, true
         *      message: <string> // se há um conflito, mensagem de alerta
         * }
         */
        subjectFormattingConflict = function(subject, intendedFormatting) {
            if (intendedFormatting === 'parallel' && subject.hasClass('single-column-subject')
                    || intendedFormatting === 'single-column' && subject.hasClass('parallel-subject')) {
                return {
                    conflict: true,
                    message: 'Não é possível que uma disciplina seja de coluna única e paralela.'
                };
            } else {
                return false;
            }
        };

        /*
         * Inicializa os campos de quantidade de questões de cada disciplina
         * 
         */
        initQuantities = function() {
            var QUESTIONS_PER_BASE_SUBJECT = 45;
            var quantityIsDefined = ($('.base-subjects').find('.quantity-block').data('quantity') !== '') ? true : false;
            $('.base-subjects').each(function() {
                var questionQuantity = parseInt(QUESTIONS_PER_BASE_SUBJECT / +$(this).find('.amount-input').length);
                $(this).find('.amount-input').each(function() {
                    if (quantityIsDefined) {
                        $(this).val($(this).parents('.quantity-block').data('quantity'));
                    } else {
                        $(this).val(questionQuantity);
                    }
                });
            });
        };

        /*
         * Provoca a contagem do número total de questões, definidas por padrão, e atualiza a interface
         * 
         */
        initQuestionAmount = function () {
            $(".amount-input").each(function () {
                $(this).trigger("change");
            });
        };
        
        /*
         * Inicializa o plugin que permite arrastar e soltar as disciplinas 
         * para ordená-las
         */
        initSortable = function() {
            $('tbody').sortable();
            $('#all-base-subjects').sortable();                            
        };
        
        /*
         * Em caso de edição do conteúdo, certifica-se que esses grupos 
         * são exibidos abaixo do respectivo campo de "DISCIPLINAS PARALELAS".
         * 
         */
        initParallelSubjects = function(initSingleColumnSubjects) {
            $('.cats-selected-row.parallel-flag').each(function() {
                $(this).removeClass('parallel-flag');
                // Essa condicional é necessária pois a classe pode ter sido
                // removida, se a disciplina for paralela a anterior.
                if (!$(this).hasClass('cats-selected-row')) 
                    return;
                
                $(this).closest('.base-subjects')
                        .find('.select-parallel-subjects')
                        .click();
            });
        };
        
        /*
         * Em caso de edição do conteúdo, certifica-se que as disciplinas 
         * de coluna única são exibidas abaixo do respectivo campo 
         * de "DISCIPLINAS DE COLUNA ÚNICA".
         * 
         */
        initSingleColumnSubjects = function() {
            $('.cats-selected-row.single-column-flag').each(function() {
                $(this).removeClass('single-column-flag');
                
                $(this).closest('.base-subjects')
                        .find('.select-single-column-subjects')
                        .click();
            });
        };
        
        /*
         * 
         * 
         */
        initContentConfig = function () {
            contentConfig = null;
            
            if ($('#content-info').length !== 0) {
                $.ajax({
                    method: "POST",
                    url: '/school-management/school-exam/get-content',
                    data: {
                        contentId: +$('#content-info').data('id')
                    },
                    success: function (json){
                        contentConfig = JSON.parse(json.config);
                    }                        
                });  
            }
        }
        

        return {
            init: function () {
                createContentListeners();
                initQuantities();
                initQuestionAmount();
                initSortable();
                initParallelSubjects();
                initSingleColumnSubjects();
                initContentConfig();
            } 
        };

    }());

    return createContent;
});