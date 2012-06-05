$(document).ready(function(){
   $("#mainCategory").change(function() {
       var parentId = $(this).val();
       $.ajax({
           url: "/index/get-categories",
           dataType: 'json',
           data: {
               parentId: parentId
           },
           beforeSend: function() {
               $("#subCategory").attr("disabled", "disabled");
           },
           complete: function() {
               $("#subCategory").removeAttr("disabled");
           },
           success: function(data) {
               $("#subCategory").children().remove();
               // if response is empty we will copy main cat to sub cat
               if($.isEmptyObject(data)) {
                   alert('Błąd. Wybrana kategoria główna nie ma podkategorii. ' +
                       'Prosimy o wybranie innej kategorii glównej.');
               } else {
                   $.each(data, function(i, item) {
                       $("#subCategory").append($('<option>', { value : item.id })
                           .text(item.name));
                   });
               }
           },
           type: 'POST'
       });
   });
});