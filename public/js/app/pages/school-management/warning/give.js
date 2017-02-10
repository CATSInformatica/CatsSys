/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['moment', 'jquery', 'datetimepicker'], function (moment) {

    var create = (function () {

        initDatepickers = function () {
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                minDate: moment().subtract(1, 'year'),
                useCurrent: true,
                maxDate: moment().add(2, 'months'),
                locale: 'pt-br',
                viewMode: 'months'
            });
        };
        
        initStudentSelection = function() {
            $('#class-input').on('change', function() {
                removeStudentsOptions();
                loadClassStudents($(this).val());
            });
            $('#class-input').trigger('change');
            
            function removeStudentsOptions() {
                $('#students-input').html('');                
            }
            
            function loadClassStudents(classId) {
                var request = $.ajax({
                    type: 'POST',
                    url: '/school-management/student-class/get-students',
                    data: {
                        id: classId
                    }
                });

                request.done(function(response) {
                    var students = response.students;
                    for (var i = 0; i < students.length; ++i) {
                        var fullName = students[i].personFirstName + ' ' + students[i] .personLastName;
                        
                        var option = $('<option>' + fullName + '</option>');
                        option.val(students[i].enrollmentId);
                        
                        $('#students-input').append(option);
                    }
                });
            }
        };

        return {
            init: function () {
                initDatepickers();
                initStudentSelection();
            }
        };

    }());

    return create;

});