/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


requirejs.config({
    baseUrl: '/vendor',
    paths: {
        jquery: 'jquery/dist/jquery.min',
        bootstrap: 'bootstrap/dist/js/bootstrap.min',
        smoothscroll: 'smooth-scroll/dist/js/smooth-scroll.min',
        moment: 'moment/min/moment-with-locales.min',
        app: '/js/app'
    },
    shim: {
        bootstrap: {
            deps: ['jquery']
        }
    }
});

define(['smoothscroll', 'jquery', 'bootstrap'], function (smoothScroll) {
    $(function () {

        $('.wrapper').fadeIn('slow');
        $("body").scrollspy();

        /**
         * Salva a sessão que o usuário passou,
         * caso ele recarregue o site a última sessão visitada será exibida
         */
        $("body").on('activate.bs.scrollspy', function (e) {
            history.replaceState({}, "", $("a[href^='#']", e.target).attr("href"));
        });

        require(['app/pages/site/site'], function (SiteModule) {

            /**
             * Arranjar uma solução decente algum dia
             * Hack para o smoothscroll
             */
            var numberOfAjaxCallsAtLoad = 1;
            $(document).ajaxStop(function () {
                numberOfAjaxCallsAtLoad--;
                if (numberOfAjaxCallsAtLoad === 0) {
                    smoothScroll.init();
                }
            });

            // inicializar módulo do site
            SiteModule.init();
        });
    });
});
