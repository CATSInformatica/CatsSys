/* 
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
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

define(['moment', 'masks', 'datetimepicker'], function (moment, masks) {
    var StudentInterviewModule = (function () {

        logInterviewChange = function () {
            $("button[name='interviewSubmit']").submit(function () {
                if (localStorage.getItem('regId') && parseInt(localStorage.getItem('regId')) === $('#candidate-info').data('regid')) {
                    localStorage.removeItem('regId');
                }
                if ($('#candidate-info').data('regid') !== -1) {
                    localStorage['regId'] = $('#candidate-info').data('regid');
                }
            });
        };

        initDatetimepicker = function () {
            $('.interview-time').closest('.input-group').datetimepicker({
                format: 'LT',
                locale: 'pt-br',
                viewDate: moment()
            });

            masks.bind({
                timeNoSeconds: ".interview-time"
            });
        };

        /**
         * Mantém a sessão para o formulário de entrevita ativa.
         * 
         * A cada 5 mins uma requisição é enviada ao servidor para manter a
         * sessão ativa.
         * 
         * @returns {undefined}
         */
        keepMeAlive = function () {

            setTimeout(function () {
                $.ajax({
                    url: '/recruitment/interview/keepAlive',
                    type: 'GET',
                    success: function (data) {
                        keepMeAlive();
                    },
                    error: function (texErr) {
                        console.log('Error', texErr);
                    }
                });
            }, 300000);

        };

        return {
            init: function () {
                moment.locale("pt-br");
                // desabilitar rolagem em campos tipo número
                $(':input[type=number]').on('mousewheel', function () {
                    $(this).blur();
                });
                initDatetimepicker();
                logInterviewChange();
                keepMeAlive();
            }
        };
    }());

    return StudentInterviewModule;
});