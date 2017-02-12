/* 
 * Copyright (C) 2017 Márcio Dias <marciojr91@gmail.com>
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


define([], function () {

    var CriteriaGrade = (function () {
        
        var socTarget, vulTarget, stTarget;
        var SOCIOECONOMIC = 0,
            VULNERABILITY = 1,
            STUDENT = 2;

        calcCriteriaGrade = function (grade, type) {
            var target;

            switch (type) {
                case SOCIOECONOMIC:
                    target = socTarget;
                    break;
                case VULNERABILITY:
                    target = vulTarget;
                    break;
                case STUDENT:
                    target = stTarget;
                    break;
                default:
                    target = 0;
            }

            var k = (grade <= target) ? 0 : 1;
            var result = 10 * (1 + Math.pow(-1, k) * (grade - target) / Math.max(target, 10 - target));
            return +result;
        };

        return {
            init: function (soc, vul, st) {
                socTarget = soc;
                vulTarget = vul;
                stTarget = st;
            },
            calcCriteriaGrade: calcCriteriaGrade,
            SOCIOECONOMIC: SOCIOECONOMIC,
            VULNERABILITY: VULNERABILITY,
            STUDENT: STUDENT
        };
    }());

    return CriteriaGrade;

});


