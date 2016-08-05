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

define(['jquery', 'datetimepicker'], function () {
    var examConfig = (function () {

        /*
         * Eventos que envolvem o formulário
         * 
         */
        examConfigListeners = function() {
            /*
             * Habilita/Desabilita os datepickers de dia e hora, que são necessários
             * somente se o simulado possuir o cabeçalho com instruções
             * 
             */
            $('#exam-instructions').on('change', function () {
                if ($('#exam-instructions').is(":checked")) {
                    $('#exam-begin-time').prop('disabled', false);
                    $('#exam-end-time').prop('disabled', false);
                } else {
                    $('#exam-begin-time').prop('disabled', true);
                    $('#exam-end-time').prop('disabled', true);
                }
            });
            
            /*
             * Atualiza o total de questões
             * 
             */
            $('.amount-input').change(function () {
                var count = 0;
                $('.amount-input').each(function () {
                    if ($(this).val() !== '') {
                        count += parseInt($(this).val());
                    }
                });
                $('#question-count').html(count);
            });
            
            
            $('#save-button').click(function () {
                saveConfig();
            });
        };

        /*
         * Salva as configurações do simulado
         * 
         */
        saveConfig = function() {
            var baseSubjects = '';
            var index = 0;
            var areas = [];
            
            $('.base-subject').each(function () {
                baseSubjects += '<input type="hidden" name="baseSubjects[' + 
                        (index++) + '][sId]" value="' + (+$(this).data('id')) + '">';                
            });
            
            $("#exam-form").append(baseSubjects);
            $("#exam-form").append($('#examId'));
            $.post(
                '/school-management/school-exam/save-config', 
                $("#exam-form").serialize()
            ).done(function(data) {
                $('#save-exam-message').remove();
                $('#exam-form').prepend('<p id="save-exam-message"></p>');
                if (data.error) {
                    $('#save-exam-message').addClass('text-red');
                    $('#save-exam-message').text("Um erro ocorreu ao tentar salvar o formulário. Verifique se as informações do formulário estão corretas.");
                } else {
                    $('#save-exam-message').addClass('text-green');
                    $('#save-exam-message').text(data.message + ' Redirecionando para "Mostrar simulados".');
                    setTimeout(function () {
                        window.location.replace("/school-management/school-exam/index");
                    }, 3000);
                }
            });
        };

        /*
         * Inicializa os campos de quantidade de questões de cada disciplina
         * 
         */
        initQuantities = function() {
            var QUESTIONS_PER_BASE_SUBJECT = 45;
            var quantityIsDefined =  ($('.base-subject').find('.quantity-block').data('quantity') !== '') ? true : false;
            $('.base-subject').each(function() {
                var questionQuantity = parseInt(QUESTIONS_PER_BASE_SUBJECT / parseInt($(this).find('.amount-input').length));
                
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
         * Inicializa os  os datepickers de dia e hora, utilizados se o simulado 
         * possuir o cabeçalho com instruções
         * 
         */
        initDatepicker = function () {
            $('#exam-day').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                defaultDate: ($('#exam-day').closest('.input-group').val() == '' ? new Date() : false),
                locale: 'pt-br',
                viewMode: 'months'
            });
            $('#exam-begin-time').closest('.input-group').datetimepicker({
                format: 'HH:mm',
                defaultDate: ($('#exam-begin-time').closest('.input-group').val() == '' ? '2015-10-21 13:30:00' : false),
                locale: 'pt-br'
            });
            $('#exam-end-time').closest('.input-group').datetimepicker({
                format: 'HH:mm',
                defaultDate: ($('#exam-end-time').closest('.input-group').val() == '' ? '2015-10-21 17:30:00' : false),
                locale: 'pt-br'
            });
        };

        return {
            init: function () {
            } 
        };

    }());

    return examConfig;
});