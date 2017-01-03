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

define(['jquery', 'datatable'], function () {
    var createApplication = (function () {

        var examsTable = $('#exams-table');

        createApplicationListeners = function () {
            /**
             * Antes de enviar o formulário, anexa os ids das linhas 
             * selecionadas da tabela de provas
             * 
             */
            $('#exam-application-form').submit(function () {
                $('.cats-selected-row').each(function () {
                    var currentCount = $('form fieldset > input').length;
                    
                    var template = $('form fieldset > span').data('template');
                    template = template.replace(/__index__/g, currentCount);
                    
                    var htmlTemplate = $(template);
                    htmlTemplate.val(+$(this).data('id'));
                    
                    $('form fieldset').append(htmlTemplate);
                });
                
                return true;
            });
        };

        initDataTable = function () {
            examsTable.DataTable({
                dom: 'ftp',
                autoWidth: false,
                order: [0, 'desc'], 
                pageLength: 6
            });
        };

        return {
            init: function () {
                initDataTable();
                createApplicationListeners();
            }
        };

    }());

    return createApplication;
});