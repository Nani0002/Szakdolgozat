window.addEventListener("load", init, false);

let editMode = false;

function init() {
    document
        .querySelector("#modal-send")
        .addEventListener("click", formCheck, false);

    const newCustomerBTNs = document.querySelectorAll(".new-customer-btn");
    for (let i = 0; i < newCustomerBTNs.length; i++) {
        newCustomerBTNs[i].addEventListener("click", storeModal, false);
    }

    const editCustomerBTNs = document.querySelectorAll(".edit-customer-btn");
    for (let i = 0; i < editCustomerBTNs.length; i++) {
        editCustomerBTNs[i].addEventListener("click", updateModal, false);
    }
}

function storeModal(e) {
    const companyId = e.target.id.split("-")[2];
    document.querySelector("#form-company").value = companyId;
    document.querySelector("#modal-send").innerHTML = "Hozzáadás";
    document.querySelector("#customer-modal-label").innerHTML = "Új ügyfél felvétele";
    document.querySelector("#name").value = "";
    document.querySelector("#email").value = "";
    document.querySelector("#phone").value = "";

    editMode = false;
}

function updateModal(e) {
    const customerId = e.target.id.split("-")[2];
    const companyId = e.target.id.split("-")[3];
    document.querySelector("#form-company").value = companyId;
    document.querySelector("#name").value = document
        .querySelector(`#customer-name-${customerId}`)
        .innerHTML.trimStart();
    document.querySelector("#email").value = document
        .querySelector(`#customer-email-${customerId}`)
        .innerHTML.trimStart();
    document.querySelector("#phone").value = document
        .querySelector(`#customer-phone-${customerId}`)
        .innerHTML.trimStart();
    document.querySelector("#modal-send").innerHTML = "Szerkesztés";
    document.querySelector("#customer-modal-label").innerHTML = "Ügyfél szerkesztése";
    document.querySelector("#form-customer").value = customerId;

    editMode = true;
}

function formCheck() {
    const name = document.querySelector("#name").value;
    const email = document.querySelector("#email").value;
    const phone = document.querySelector("#phone").value;
    const id = editMode
        ? document.querySelector("#form-customer").value
        : document.querySelector("#form-company").value;

    const modal = $("#modal-body");
    const url = editMode
        ? `${modal.data("request-url")}/${id}`
        : modal.data("request-url");
    const csrfToken = modal.data("csrf-token");

    let formData = new FormData();
    formData.append("_token", csrfToken);
    if (editMode) {
        formData.append("_method", "put");
    }

    formData.append("id", id);
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
                $("#customer-modal").modal("hide");
                if (editMode) {
                    document.querySelector(`#customer-${id}`).innerHTML =
                        response.html;
                } else {
                    const temp = document.createElement("div");
                    temp.innerHTML = response.html.trim();
                    const newElement = temp.firstElementChild;

                    const container = document.querySelector(
                        `#accordion-collapse-${id}`
                    );
                    const innerContainer = container.firstElementChild;

                    innerContainer.insertBefore(
                        newElement,
                        innerContainer.lastElementChild
                    );
                }
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
