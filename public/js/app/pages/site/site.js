/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['app/pages/administrative-structure/department/departments'], function (departments) {

    var Site = (function () {

        pastExamsAjax = function () {
            //  Carrega os vestibulinhos passados automaticamente por AJAX
            var pastExams = ''; // HTML que será inserido sob #past-exams
            var i = 0;
            $.ajax({
                type: "POST",
                url: "/school-management/study-resources/get-past-exams",
                success: function (data) {
                    for (i = 0; i < data.psa['source'].length; ++i) {
                        pastExams += '<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 catssys-test">'
                                + '<a href="' + data.psa_dir + '/' + data.psa['source'][i] + '" target="_blank">'
                                + '<i class="fa fa-file-text-o"></i><h4>' + data.psa['number'][i]
                                + 'º' + ' PSA ' + data.psa['year'][i];
                        if (data.psa['part'][i] !== "") {
                            pastExams += ' pt. ' + data.psa['part'][i];
                        }
                        pastExams += '</h4></a></div>';
                    }
                    $("#past-exams").html(pastExams);
                }
            });
        };

        return {
            init: function () {
                pastExamsAjax();
                departments.init();
            }
        };
    }());

    return Site;
});
