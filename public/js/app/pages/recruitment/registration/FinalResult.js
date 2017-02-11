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

define(['app/models/CriteriaGrade', 'datatable'], function (CriteriaGrade) {

    var FinalResultModule = (function () {

        var targetTable = $("#results-table");

        processTable = function () {
            targetTable.find('tr.result-row').each(function () {

                var soc = $(this).children('td.socioeconomic');
                var vul = $(this).children('td.vulnerability');
                var std = $(this).children('td.student');

                var socVal = CriteriaGrade.calcCriteriaGrade(soc.data('value'), CriteriaGrade.SOCIOECONOMIC);
                var vulVal = CriteriaGrade.calcCriteriaGrade(vul.data('value'), CriteriaGrade.VULNERABILITY);
                var stdVal = CriteriaGrade.calcCriteriaGrade(std.data('value'), CriteriaGrade.STUDENT);
                var result = (socVal + vulVal + stdVal) / 3;

                soc.text(socVal.toFixed(3));
                vul.text(vulVal.toFixed(3));
                std.text(stdVal.toFixed(3));

                $(this).children('td.result').text(result.toFixed(3));
            });

            targetTable.DataTable({
                paging: false,
                columnDefs: [
                    {
                        targets: [0, 1, 2, 3, 4, 5, 6],
                        orderable: false
                    }
                ],
                order: [[6, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc']]
            });

            targetTable.find('tr.result-row').each(function (i) {
                $(this).children('td.position').text(i + 1);
            });
        };

        return {
            init: function () {
                var socTarget = targetTable.data('socioeconomic');
                var vulTarget = targetTable.data('vulnerability');
                var stTarget = targetTable.data('student');
                CriteriaGrade.init(socTarget, vulTarget, stTarget);
                processTable();
            }
        };
    }());

    return FinalResultModule;
});

