(function( $ ) {
    'use strict';

    $(".pegasus-trigger").on("click", function(event){

        //console.log( this );

        //first open the container with the social stuff
        $( this ).parents('.pegasus-toggle').find('.toggle-content').slideToggle();
        // Then add a class to the button to give it a hover
        $( this ).parents('.pegasus-toggle').find('.toggle-content').toggleClass("open");

        event.preventDefault();

    });


})( jQuery );

