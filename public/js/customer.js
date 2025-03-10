window.addEventListener("load", init, false);

function init() {
    document
        .querySelector("#modal-send")
        .addEventListener("click", formCheck, false);
    const newCustomerBTNs = document.querySelectorAll(".new-customer-btn");
    for (let i = 0; i < newCustomerBTNs.length; i++) {
        newCustomerBTNs[i].addEventListener("click", updateModal, false);
    }
}

function updateModal(e) {
    const companyId = e.target.id.split("-")[2];
    document.querySelector("#form-company").value = companyId;
}

function formCheck(e) {
    const name = document.querySelector("#name").value;
    const email = document.querySelector("#email").value;
    const phone = document.querySelector("#phone").value;
    const company = document.querySelector("#form-company").value;
    const modal = $("#modal-body")
    const url = modal.data("request-url");
    const csrfToken = modal.data("csrf-token");

    let formData = new FormData();
    formData.append("_token", csrfToken);
    formData.append("company", company);
    formData.append("name", name);
    formData.append("email", email);
    formData.append("phone", phone);

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                console.log(response);

                $("#customer-modal").modal("hide");
            } else {
                alert(response);
            }
        },
        error: function (xhr) {
            let response = JSON.parse(xhr.responseText);
            if (xhr.status === 401 && response.redirect) {
                window.location.href = response.redirect;
            } else {
                alert("An error occurred: " + response.error);
            }
        },
    });


}
