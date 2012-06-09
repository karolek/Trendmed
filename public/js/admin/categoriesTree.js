$(document).ready(function(){
   $("#tree").ajaxStart(function() {
       $(this).append('<p style="text-align: center"><img src="/img/ajax-loader.gif"></p>');
       $(this).find("a").bind("click", false);
   });
   $("#tree").on("click", "a.ajax", function() {
       var url = $(this).attr("href");
       $("#tree").load(url, function() {
       });
       return false;
   })
});