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
    // if there is active class...
    $("#sidebar").find("a.active").parents("ul.hidden").removeClass("hidden");

});
// adding clinic as fav.
$(document).ready(function() {
    $("a.add-to-fav").click(function(e){
        var url = $(this).attr("href");
        var clickedLink = this;
        $.post(url, {
            entity: $(clickedLink).attr("entity")
        }, function(response) {
            alert(response);
            if($(clickedLink).hasClass('fav')) {
                $(clickedLink).addClass('unfav');
                $(clickedLink).removeClass('fav');
            } else {
                $(clickedLink).addClass('fav');
                $(clickedLink).removeClass('unfav');
            }
        })
        e.preventDefault();
    });
})
