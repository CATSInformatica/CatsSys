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
    var index = (function () {

        var configTable = $('#config-table');

        initDataTable = function () {
            configTable.DataTable({
                dom: 'lftip',
                paging: false
            });
            
            $('#config-table').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = configTable.DataTable().row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(tr, row.data())).show();
                    tr.addClass('shown');
                }
            });
            
            function format (tr, rowData) {
                var cardPreview = $('#student-id-cards .student-id-card').first().clone();
                cardPreview.find('.card').css( {
                    'background-image': "url(/img/" + tr.data('img-url') + ")",
                    'background-size': '100%'
                });
                cardPreview.find('span.phrase').first().text(rowData[1]);
                cardPreview.find('span.author').first().text(rowData[2]);
                
                return cardPreview;
            }
        };

        return {
            init: function () {
                initDataTable();
            }
        };

    }());    
    
    return index;
});