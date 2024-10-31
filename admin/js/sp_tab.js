jQuery(document).ready(function($) {
    $('.custom-tab').click(function(e) {
        e.preventDefault();
        var $tab = $(this),
                $panel_id = $tab.attr('href');

        /*Remoe active class from all other items*/
        $('.custom-tab').each(function() {
            $(this).removeClass('nav-tab-active');
        });
        /*Add active class to curent*/
        $tab.addClass('nav-tab-active');

        /*Remoe active class from all other panels*/
        $('.panel').each(function() {
            $(this).removeClass('active');
        });
        /*Add active class to curent*/
        $('div' + $panel_id).addClass('active');
    });
});