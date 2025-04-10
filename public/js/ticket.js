$(document).ready(function () {
    $("#users").select2({
        theme: "bootstrap-5",
        width: "100%",
        placeholder: $("#users").data("placeholder"),
        closeOnSelect: false,
    });

    const btns = document.querySelectorAll('.edit-comment-btn')
    btns.forEach((e) => {
        e.addEventListener('click', (btn) => {
            $(`#edit-comment-${btn.target.id.split('-')[2]}-form`).submit();
        }, false);
    })
});

$("#form-submit-btn").click(function () {
    $("#ticket-form").submit();
});
