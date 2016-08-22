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
    var applications = (function () {

        var applicationsTable = $('#applications-table');

        initDataTable = function () {
            applicationsTable.DataTable({
                dom: 'lftip',
                autoWidth: false,
                order: [0, 'desc'] 
            });
        };

        return {
            init: function () {
                initDataTable();
            },
            getCallbackOf: function (element) {
                
                return {
                    exec: function (data) {
                        applicationsTable
                                .DataTable()
                                .row('#application-' + data.applicationId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());

    return applications;
});