/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['moment', 'masks', 'app/models/Service', 'jquery', 'datetimepicker'], function (moment, masks, service) {
    var form = (function () {
        // your module code goes here
        // var config = null;
        /**
         * 
         * private functions
         */
        initDatepickers = function () {
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                minDate: moment().subtract(100, 'years'),
                useCurrent: false,
                maxDate: moment().subtract(15, 'years'),
                locale: 'pt-br',
                viewMode: 'years',
                viewDate: moment().subtract(21, 'years')
            });
        };

        initMasks = function () {
            masks.bind({
                phone: "input[name='registration[person][personPhone]']",
                cpf: "input[name='registration[person][personCpf]']",
                date: "input[name='registration[person][personBirthday]']",
                zip: "input[name*=addressPostalCode]"
            });
        };

        initServices = function () {
            service.bindZipService({
                dataHolder: $('input[name*=addressPostalCode]')
            });
        };
       
        /**
         * Permite que o usuário faça múltiplos cliques para selecionar os cargos 
         * desejados, ao invés de precisar utilizar a tecla Ctrl para manter
         * as seleções já feitas.
         * A funcionalidade é aplicada a qualquer campo do tipo 'select' que possua
         * a classe 'allow-multiple-clicks'
         */
        initDesiredJobsInput = function () {  
            $('.allow-multiple-clicks option').mousedown(function(e) {
                e.preventDefault();
                $(this).prop('selected', !$(this).prop('selected'));
                return false;
            });
        };
        
        /**
         * Rege o funcionamento do campo "Nome Completo"
         * O candidato preenche seu nome completo e então seleciona, dentre as
         * partes do seu nome, quais são referentes ao sobrenome
         * 
         */
        initFullNameField = function () {
            $('#full-name-field-wrapper').show();
            $('.name-field').hide();
            
            var lastNameSelect = $('#last-name-select');
            
            var nameInputTimer;
            var doneTypingInterval = 3000;
            
            /**
             * As partes mudam sempre que há uma alteração no campo
             */
            $('#full-name-field').on('input', function() {
                clearTimeout(nameInputTimer);
                nameInputTimer = setTimeout(handleNameInput, doneTypingInterval);
            });
            
            function handleNameInput() {
                lastNameSelect.children('.partial-name').remove();
                var partialNames = $('#full-name-field').val().split(' ');
                
                if (partialNames.length > 0) {
                    $('#last-name-select').show();
                } else {
                    $('#last-name-select').hide();
                }
                    
                
                for (var i = 0; i < partialNames.length; ++i) {
                    if (partialNames[i] === "") {
                        continue;
                    }
                    
                    var partialNameTemplate = $('#partial-name-template > div').clone();
                    partialNameTemplate.find('.partial-name-text').first().attr('value', partialNames[i]);
                    partialNameTemplate.find('.partial-name-text').first().html(partialNames[i]);
                    lastNameSelect.append(partialNameTemplate[0]);
                }
                
                $('.partial-name-text').on('click', function() {
                    var selectedName = $(this).val();
                    if ($(this).hasClass('last-name-bg')) {
                        setLastName(false, $(this));
                    } else {
                        setLastName(true, $(this));
                    }
                });
            }
            
            updateFullNameField();
            handleNameInput();
            updateLastNameField();
            
            $('form').submit(function() {
                var firstName = "";
                var lastName = "";
                
                lastNameSelect.find('.partial-name-text').each(function() {
                    if ($(this).hasClass('last-name-bg')) {
                        if (lastName !== "") {
                            lastName += " ";
                        }
                        lastName += $(this).val();
                    } else {
                        if (firstName !== "") {
                            firstName += " ";                            
                        }
                        firstName += $(this).val();
                    }
                });

                $('input[name="registration[person][personFirstName]"').val(firstName);
                $('input[name="registration[person][personLastName]"').val(lastName);
            });
            
            /**
             * Identifica, através da classe .last-name-bg, quais partes do nome
             * completo são sobrenomes.
             * 
             * @param bool ln - true, se sobrenome
             * @param Object wrapper - objeto jQuery que representa o wrapper da
             *  parte do nome
             */
            function setLastName(ln, wrapper) {
                if (ln) {
                    wrapper.addClass('last-name-bg');
                    wrapper.removeClass('btn-default');
                    wrapper.addClass('btn-primary');                    
                } else {
                    wrapper.removeClass('last-name-bg');
                    wrapper.addClass('btn-default');
                    wrapper.removeClass('btn-primary');                    
                }                
            }
            
            /**
             * Em caso de edição, concatena o texto dos campos de primeiro nome e
             * sobrenome para formar o nome completo.
             */
            function updateFullNameField() {
                var firstName = $('input[name="registration[person][personFirstName]"').val();
                var lastName = $('input[name="registration[person][personLastName]"').val();  
                
                $('#full-name-field').val(firstName + " " + lastName);
            }
            
            /**
             * Em caso de edição, identifica os sobrenomes na lista de partes a
             * partir do campo de sobrenome.
             */
            function updateLastNameField() {
                var lastName = $('input[name="registration[person][personLastName]"').val();  
                var partialLastName = lastName.split(' ');                
                lastNameSelect.find('.partial-name-text').each(function() {
                    if (partialLastName.includes($(this).val())) {
                        setLastName(true, $(this));
                    }
                });                
            }
        };

        return {
            init: function () {
                initDatepickers();
                initMasks();
                initServices();
                initDesiredJobsInput();
                //initFullNameField();
            },
            initDesiredJobsInput: function() {
                initDesiredJobsInput();
            }
        };
    }());

    return form;
});