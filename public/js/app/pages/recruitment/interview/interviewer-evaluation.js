/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
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

define(['app/pages/recruitment/interview/keep-alive', 
    'app/pages/recruitment/interview/interviewers-evaluations',
    'datetimepicker', 'bootstrapslider'], function (keepAlive, interviewersEvaluations) {

    var interviewerEvaluation = (function () {
        
        var evaluations = [];
        var sliders = [];
        
        /**
         * Inicializa os sliders para seleção das notas
         * 
         */
        initSliders = function () {            
            $('.input-slider').each(function() {
                var value = $(this).val();
                if (value === '') {
                    value = $(this).data('default');
                }
                
                sliders.push($(this).slider({
                    tooltip: 'always',
                    min: +$(this).data('min'),
                    max: +$(this).data('max'),
                    step: +$(this).data('step'),
                    value: +value,
                    focus: true
                }));
            });
        };
        
        /**
         * Busca todas as avaliações disponíveis para o candidato, de forma que 
         * se um entrevistador quiser editar sua avaliação, ao selecionar o seu nome,
         * todos os campos são preenchidos conforme a avaliação anterior.
         * 
         */
        getInterviewersEvaluations = function () {
            var regId = +$('#interviewer-select').data('reg-id');
            interviewersEvaluations.getInterviewersEvaluations(regId, function(response) {
                evaluations = response;
            });
            
            $('#interviewer-select').change(function() {
                var evaluation = evaluations[$('#interviewer-select').val()];
                if (evaluation) {
                    for (var field in evaluation) {
                        var input = $('[name="interviewerEvaluationFieldset[' + field +']"]');
                        if (input.prop("tagName") === "TEXTAREA") {
                            input.val(evaluation[field]);
                        } else {
                            input.attr('value', evaluation[field]);
                        }
                    }
                }
                updateSliders();
            });
            
            
            function updateSliders() {
                for (var i = 0; i < sliders.length; ++i) {
                    var value = +sliders[i].parent().find('.input-slider').first().attr('value');
                    sliders[i].slider('setValue', value);
                }
            }
        };
        
        /**
         * Ao submeter um formulário, o registration id é salvo no localStorage
         * do navegador para que outras páginas possam atualizar os dados.
         * Útil para atualizar a ordem da lista de candidatos conforme as avaliações
         * são submetidas.
         * 
         */
        updateLocalStorage = function () {
            $('form').on('submit', function () {
                if (localStorage.getItem('regId') 
                        && parseInt(localStorage.getItem('regId')) === $('#interviewer-select').data('reg-id')) {
                    localStorage.removeItem('regId');
                }
                if ($('#interviewer-select').data('reg-id') !== -1) {
                    localStorage['regId'] = $('#interviewer-select').data('reg-id');
                }
                
                return true;
            });            
        };

        return {
            init: function () {
                initSliders();
                getInterviewersEvaluations();
                updateLocalStorage();
                keepAlive.init(); 
            }
        };
    }());
    
    return interviewerEvaluation;
});

