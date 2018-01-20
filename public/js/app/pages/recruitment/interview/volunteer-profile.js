/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
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

define(['app/pages/recruitment/interview/profile', 
    'app/pages/recruitment/interview/volunteer-form', 
    'app/pages/recruitment/registration/registration'], function (profile, volunteerForm, registrationForm) {
    var volunteerProfile = (function () {
        
        return {
            init: function () {
                profile.init();
                volunteerForm.initSlider();
                volunteerForm.initDatepickers();
                registrationForm.initDesiredJobsInput();
            }
        };
    }());

    return volunteerProfile;

});