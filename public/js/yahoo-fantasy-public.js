(function ($) {
    'use strict';

    /**
     * Provides document.ready() functionality for the public facing Yahoo
     * Fantasy Sports plugin.
     */
    $(document).ready(function(){
        
        // For each .yahoo-logo class, look for the data-yahoo-logo element
        // and perform a src addition to the <img>.  Allows for quicker 
        // initial loading.
        $(".yahoo-logo").each(function(idx, ele){
            var $ele = $(ele);
            var src = $ele.data('yahoo-logo');
            $ele.attr('src', src);
        });
    });

})(jQuery);
