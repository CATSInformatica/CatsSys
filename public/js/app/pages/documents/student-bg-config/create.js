/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery'], function() {
    var create = (function() {
                
        initPreview = function() {
            /*
             * Sempre que uma imagem for selecionada, atualiza o preview
             */
            $("#bg-img-input").change(function(){
                updateBgPreview(this);
            });
            
            /*
             * Mudando a frase, atualiza o preview
             */
            $("#phrase-input").keyup(function(){
                updatePhrasePreview($(this).val());
            });
            
            /*
             * Mudando o autor, atualiza o preview
             */
            $("#author-input").keyup(function(){
                updateAuthorPreview($(this).val());
            });
            
            if ($("#phrase-input").val() !== "") {
                setCardBgImg($('#bg-img-input').data('url'));
                $("#phrase-input").trigger('keyup');
                $("#author-input").trigger('keyup');
            }
            
            function updateBgPreview(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        setCardBgImg(e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            function setCardBgImg(url) {
                $('.card').css({
                    'background-image': 'url(' + url + ')',
                    'background-size': '100%'
                });
            }
            
            function updatePhrasePreview(phrase) {
                $('.phrase').text(phrase);
            }
            
            function updateAuthorPreview(author) {
                $('.author').text(author);
            }
        };
        
        return {
            init: function() {
                initPreview();
            }
        };
    }());
    
    return create;
});