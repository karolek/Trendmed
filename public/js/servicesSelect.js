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
               $("#subcategory").attr("disabled", "disabled");
           },
           complete: function() {
               $("#subcategory").removeAttr("disabled");
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