/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['bootbox', 'jquery'], function (bootbox) {

    zipService = function (config) {

        config.dataHolder.each(function () {

            var holder = $(this);

            holder.siblings('span').click(function () {
                var tholder = $(this);
                tholder.find('i').addClass('fa-spinner');

                $.ajax({
                    url: '/recruitment/address/searchByZipcode',
                    type: 'POST',
                    data: {zipcode: holder.val()},
                    success: function (data) {

                        if (data.response) {
                            content = data.data;
                            if (content !== null) {
                                var addrGroup = $(holder).closest('.address-group');
                                addrGroup.find('select[name*=addressState]').val(content.addressState);
                                addrGroup.find('input[name*=addressCity]').val(content.addressCity);
                                addrGroup.find('input[name*=addressNeighborhood]').val(content.addressNeighborhood);
                                addrGroup.find('input[name*=addressStreet]').val(content.addressStreet);
                            } else {
                                bootbox.alert('O endereço digitado não' +
                                        ' consta na base de dados. Por favor insira manualmente.');
                            }
                        } else {
                            bootbox.alert(data.msg);
                        }

                        tholder.find('i').removeClass('fa-spinner');
                    },
                    error: function () {
                        bootbox.alert('Erro inesperado. Por favor preencha manualmente.');
                        tholder.find('i').removeClass('fa-spinner');
                    }
                });
            });
        });
    };


    return {
        bindZipService: zipService
    };

});