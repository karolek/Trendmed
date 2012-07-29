$(function () {
    $("select#cityFilter").on("change", function () {
        $(this).parent("form").submit();
    });
})