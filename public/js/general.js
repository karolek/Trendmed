/* 
 * General JS functions
 */
$(document).ready(function(){
    $(".confirm").click(function() {
        if ($(this).attr('confirm-message') != undefined) {
            return confirm($(this).attr('confirm-message'));
        } else {
            return confirm('Jesteś pewny?');
        }
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

var addClinicToFav = "Dodaj klinikę do ulubionych";
var removeClinicFromFav = "Usuń klinikę z ulubionych";
function prepareTooltips() {
	$(".rating .fav").tooltip({
		placement: "top",
		title: addClinicToFav
	});
	$(".rating .unfav").tooltip({
		placement: "top",
		title: removeClinicFromFav
	});
}

// adding clinic as fav.
$(document).ready(function() {
	
	prepareTooltips();
	
    $("a.add-to-fav").click(function(e){
        var url = $(this).attr("href");
        var clickedLink = this;

        $.post(url, {
            entity: $(clickedLink).attr("entity"),
            format: "html"
        }, function(response) {
            if($(clickedLink).hasClass('fav')) {
                $(clickedLink).addClass('unfav');
                $(clickedLink).removeClass('fav');
            } else {
                $(clickedLink).addClass('fav');
                $(clickedLink).removeClass('unfav');
            }
			$(clickedLink).removeData('tooltip');
			$('.tooltip').fadeOut();
			prepareTooltips();
        })
		
		
        e.preventDefault();
    })
    .ajaxStart(function(){
        $(this).addClass("loading");
    })
    .ajaxComplete(function() {
        $(this).removeClass("loading");
    });
});

