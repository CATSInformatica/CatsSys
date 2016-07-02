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

define(['datatable'], function () {

    var registrationsTable = null;
    var detailContent = null;
    var StudentListModule = (function () {

        initDataTable = function () {
            registrationsTable = $('#student-list-table').DataTable({
                iDisplayLength: 50
            });
        };
        /**
         * Exibir mais informações ao clicar na linha de algum aluno.
         * @returns {undefined}
         */
        initTableListeners = function () {

            console.log("test");
            $('#student-list-table').on("click", "td.details-control", function () {
                var tr = $(this).closest("tr");
                var registrationId = tr.data("id");
                var row = registrationsTable.row(tr);
                if (row.child.isShown()) {
                    tr.removeClass("details");
                    row.child.hide();
                } else {
                    tr.addClass("details");
                    detailContent = getSpinner();
                    row.child(detailContent).show();
                    getDetailsOf(registrationId).then(function () {
                        row.child(detailContent).show();
                    });
                }
            });
        };
        /**
         * Mostra um icone de carregamento.
         * 
         * @returns {String}
         */
        getSpinner = function () {
            return '<p class="text-center">' +
                    '<i class="fa fa-refresh fa-spin fa-4x"></i></p>';
        };
        /**
         * @Todo 
         *  - Fazer requisição ajax para /recruitment/interview/get-candidate-info
         *  - Exibir de forma bacana as informações do candidato
         *     - Foto?
         *     - Nome, data de nascimento, etc..
         *     - Informações da pré-entrevista organizada em abas
         *          - socioeconômico
         *          - vulnerabilidade
         *          - perfil do estudante
         *     (utilizar como base o CsvViewer)
         *     - Informações da entrevista e pontuação.
         * 
         * @param int registrationId
         * @returns promise
         */
        getDetailsOf = function (registrationId) {

            return $.ajax({
                url: '/recruitment/interview/get-student-info/' + registrationId,
                type: 'GET',
                success: function (response) {
                    console.log(response);
                    detailContent = createContent(response.info);
                },
                error: function (txtStatus) {
                    console.log(txtStatus);
                }
            });
        };

        createContent = function (info) {

            return '<div class="row">' +
                    '<div class="col-md-3">' +
                    '<div class="box box-primary">' +
                    '<div class="box-body box-profile">' +
                    '<img class="profile-user-img img-responsive img-circle" src="/img/user.png" alt="__NAME__">' +
                    '<h3 class="profile-username text-center" > __NAME__ </h3>' +
                    '<p class="text-muted text-center" > __AGE__ </p>' +
                    '</div>' +
                    '</div>' +
                    '<div class="box box-primary" >' +
                    '<div class="box-header with-border">' +
                    '<h3 class="box-title"> Sobre Mim </h3>' +
                    '</div>' +
                    '<div class="box-body" >' +
                    '<strong> <i class="fa fa-book margin-r-5"></i> Education</strong>' +
                    '<p class = "text-muted" >' +
                    'B.S.in Computer Science from the University of Tennessee at Knoxville' +
                    '</p>' +
                    '<hr>' +
                    '<strong><i class="fa fa-map-marker margin-r-5"></i>Location</strong>' +
                    '<p class = "text-muted" > Malibu, California < /p>' +
                    '<hr >' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-9">' +
                    '<div class="nav-tabs-custom">' +
                    '<ul class="nav nav-tabs">' +
                    '<li class="active"><a href="#activity" data-toggle="tab" aria-expanded="true">Socioeconômico</a></li>' +
                    '<li class=""><a href="#timeline" data-toggle="tab" aria-expanded="false">Vulnerabilidade</a></li>' +
                    '<li class=""><a href="#settings" data-toggle="tab" aria-expanded="false">Perfil de Estudante</a></li>' +
                    '</ul>' +
                    '<div class="tab-content">' +
                    '<div class="tab-pane active" id="activity">' +
                    '</div>' +
                    '<div class="tab-pane active" id="timeline">' +
                    '</div>' +
                    '<div class="tab-pane" id="settings">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
        };

        return {
            init: function () {
                initDataTable();
                initTableListeners();
            }
        };
    }());
    return StudentListModule;
});

