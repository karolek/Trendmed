/* 
 * General JS functions
 */
$(document).ready(function(){
    $(".confirm").click(function(){
       return confirm('Jesteś pewny?');
    });
})
/**
 * Behaviour for tree menu
 */
$(document).ready(function() {
    $(".nav-header a").click(function() {
        $(this).parent().next("ul").toggleClass("hidden");
        return false;
    });
    console.log();
    // if there is active class...
    $("#sidebar").find("a.active").parents("ul.hidden").removeClass("hidden");

});
