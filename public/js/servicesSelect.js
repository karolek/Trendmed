$(document).ready(function(){
   $("#maincategory").change(function() {
       var parentId = $(this).val();
       $.ajax({
           url: "/index/get-categories",
           dataType: 'json',
           data: {
               parentId: parentId
           },
           beforeSend: function() {
               $("#maincategory #loader").removeClass("hidden");
           },
           complete: function() {
               $("#maincategory #loader").addClass("hidden");
           },
           success: function(data) {
               $("#subcategory").children().remove();
               $.each(data, function(i, item) {
                   $("#subcategory").append($('<option>', { value : item.id })
                       .text(item.name));
               });
           },
           type: 'POST'
       });
   });
});