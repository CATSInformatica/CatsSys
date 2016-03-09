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

define(['jquery', 'datatable'], function () {

    var roleModule = (function () {
        // your module code goes here
        // var config = null;

        var userxrolesTable = $("#userxrolesTable");
        var roleTable = $("#roleTable");

        initDataTable = function (table) {

            var dt = table.DataTable({
                dom: 'lftip',
                paging: false
            });
        };

        return {
            init: function () {
                if (userxrolesTable.length > 0) {
                    initDataTable(userxrolesTable);
                } else if (roleTable.length > 0) {
                    initDataTable(roleTable);
                }

            }
        };

    }());

    return roleModule;

});