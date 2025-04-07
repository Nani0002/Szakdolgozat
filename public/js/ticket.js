$(document).ready(function () {
    $("#users").select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: $("#users").data("placeholder"),
        closeOnSelect: false,
    });
});

$("#form-submit-btn").click(function () {
    $("#ticket-form").submit();
});
