window.addEventListener("load", init, false);

function init() {
    const form = document.querySelector("#form");

    try {
        form.addEventListener("submit", submitForm, false);
    } catch {}
    try {
        document.querySelector("#print-btn").addEventListener("click", print, false);
    } catch {}
}

function submitForm(e) {
    e.preventDefault();
    const form = e.target
    let formData = new FormData(form);
    const printing = document.querySelector("#print_check").checked;
    const url = form.action;
    if (form.dataset.method == "put") {
        formData.append("_method", "put");
    }
    formData.append("_token", form.dataset.csrfToken);

    const previewBaseUrl = form.dataset.previewBaseUrl;
    const showBaseUrl = form.dataset.showUrlBase;

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                let worksheetId = response.id;
                const previewUrl = previewBaseUrl.replace(
                    "PLACEHOLDER_ID",
                    worksheetId
                );
                const showUrl = showBaseUrl.replace(
                    "PLACEHOLDER_ID",
                    worksheetId
                );
                if (printing) {
                    window.open(previewUrl, "_blank");
                }

                window.location.href = showUrl;
            } else {
                alert(response);
            }
        },
        error: function (xhr) {
            let response = JSON.parse(xhr.responseText);
            if (xhr.status === 422) {
                handleAjaxErrors(xhr.responseJSON.errors);
            } else if (xhr.status === 401 && response.redirect) {
                window.location.href = response.redirect;
            } else {
                alert("An error occurred: " + response.error);
            }
        },
    });
}

function print(e){
    window.open(e.target.dataset.previewUrl, "_blank");
}
