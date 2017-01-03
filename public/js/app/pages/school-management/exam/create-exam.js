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
    var createExam = (function () {
        
        /**
         * Inicializa os DateTimePickers 
         * 
         */
        initDatepickers = function () {            
            $('#exam-day').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                defaultDate: ($('#exam-day').closest('.input-group').val() === '' ? new Date() : false),
                locale: 'pt-br',
                viewMode: 'days'
            });
            $('#exam-start-time').closest('.input-group').datetimepicker({
                format: 'HH:mm',
                defaultDate: ($('#exam-start-time').closest('.input-group').val() === '' ? '2015-10-21 13:30:00' : false),
                locale: 'pt-br'
            });
            $('#exam-end-time').closest('.input-group').datetimepicker({
                format: 'HH:mm',
                defaultDate: ($('#exam-end-time').closest('.input-group').val() === '' ? '2015-10-21 17:30:00' : false),
                locale: 'pt-br'
            });
        };
        
        return {
            init: function () {
                initDatepickers();
            }
        };

    }());

    return createExam;
});