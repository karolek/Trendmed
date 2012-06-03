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
    })
});
