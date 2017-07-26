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

/**
 * Encapsula a busca por avaliações e o cálculo da nota final de um candidato, 
 * dadas as notas finais de cada entrevistador.
 * 
 */
define([], function () {

    var volunteerRatings = (function () {
        
        /**
         * Busca todas as avaliações de um candidato e chama um callback com as 
         * avaliações como argumento
         * 
         * @param {integer} registrationId
         * @param {function} callback - argumento: ratings
         *      ratings = {
         *          <nome-do-entrevistador>: {
         *              volunteerProfileRating: <int>,
         *              volunteerProfile: <string>, 
         *              volunteerAvailabilityRating: <int>,
         *              volunteerAvailability: <string>,   
         *              volunteerResponsabilityAndCommitmentRating: <int>,   
         *              volunteerResponsabilityAndCommitment: <string>,
         *              volunteerOverallRating: <int>,   
         *              volunteerOverallRemarks: <string>,
         *              volunteerFinalRating: <float>    
         *          },
         *          .
         *          .
         *          .
         *      }
         *  
         */
        fetchInterviewersEvaluations = function (registrationId, callback) {
            $.ajax({
                url: '/recruitment/interview/get-interviewers-evaluations/' + registrationId,
                type: 'GET',
                /**
                 * 
                 * @param {array} response
                 *      response = {
                 *          ratings: <array> // descrito acima
                 *          error: <bool>,
                 *          message: <string>
                 *      }
                 */
                success: function (response) {
                    callback(response.ratings);
                },
                error: function (txtStatus) {
                    console.log(txtStatus);
                }
            });    
        };
        
        /**
         * Calcula a nota final do candidato, dadas as notas finais dos entrevistadores
         * 
         * @param {array} ratings - array das notas finais dos entrevistadores 
         * @returns {Number}
         */
        calculateFinalRating = function (ratings) {
            if (ratings.length === 0) {
                return 0;
            }
            
            var ratingsSum = 0;
            for (var i = 0; i < ratings.length; ++i) {
                ratingsSum += ratings[i];
            }
            
            return (ratingsSum / ratings.length).toFixed(3);
        };

        return {
            getInterviewersEvaluations: function (registrationId, callback) {
                fetchInterviewersEvaluations(registrationId, callback);
            },
            getFinalRating: function (ratings) {
                return calculateFinalRating(ratings);
            }
        };
    }());
    
    return volunteerRatings;
});

