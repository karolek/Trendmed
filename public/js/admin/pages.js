// initializing select for sponsored clinic
$(function () {
    check_fields_values();
    $("select#type").on("change", check_fields_values);
});
function check_fields_values() {
    if ($("select#type").val() == 'article_sponsored') {
        $("select#sponsoredByClinic").parents("div.control-group").fadeIn();
    } else {
        $("select#sponsoredByClinic").parents("div.control-group").hide();
    }
}