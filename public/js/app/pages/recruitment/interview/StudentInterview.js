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
        
        logInterviewChange = function() {
            $("button[name='interviewSubmit']").click(function() {
                if (localStorage.getItem('regId') && parseInt(localStorage.getItem('regId')) === $('#candidate-info').data('regid')) {
                    localStorage.removeItem('regId');
                } 
                if ($('#candidate-info').data('regid') !== -1) {
                    localStorage['regId'] = $('#candidate-info').data('regid');
                }
            });
        };
        
        initDatetimepicker = function() {
            $('#interview-starttime').closest('.input-group').datetimepicker({
                format: 'LT',
                locale: 'pt-br',
                viewDate: moment()
            });
            
             masks.bind({
                timeNoSeconds: "input[name*=interviewStartTime]"
            });
        };

        return {
            init: function () {
                moment.locale("pt-br");
                initDatetimepicker();
                logInterviewChange();
            }
        };
    }());
    
    return StudentInterviewModule;
});