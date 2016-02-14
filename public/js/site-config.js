/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


requirejs.config({
    baseUrl: '/vendor',
    paths: {
        jquery: 'AdminLTE/plugins/jQuery/jQuery-2.1.4.min',
        bootstrap: 'bootstrap/dist/js/bootstrap.min',
        smoothscroll: 'smooth-scroll/dist/js/smooth-scroll.min',
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
        // needed to use bootstrap data-spy
        $(window).trigger('load');
        $('.wrapper').fadeIn('slow');
        smoothScroll.init();

        require(['app/pages/site/site'], function (SiteModule) {
            SiteModule.init();
        });
        
    });
});