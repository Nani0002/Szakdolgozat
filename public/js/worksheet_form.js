window.addEventListener("load", init, false);

let printWorksheet = false;
let latestPrintDate = "";
let latestPrintTime = "";

function init() {
    const timeBtns = document.querySelectorAll(".time-btn");
    timeBtns.forEach((e) => {
        if (e.id == "liable-btn" || e.id == "coworker-btn") {
            e.addEventListener("click", setMe, false);
        } else {
            e.addEventListener("click", setNow, false);
        }
    });
    const outsourcingSW = document.querySelector("#outsourcing-switch");
    outsourcingSW.addEventListener("click", showHideOutsourcing, false);
    if (!outsourcingSW.checked) {
        const row = document.querySelector("#outsourcing-row");
        row.style.display = "none";
    }

    const printSW = document.querySelector("#print_check");
    printSW.addEventListener("click", printOnSend, false);

    document
        .querySelector("#company_id")
        .addEventListener("change", setCustomers, false);

    document.querySelector("#submit-btn").addEventListener("click", () => {
        if(printWorksheet)
            print()
    }, false)
}

function setNow(e) {
    const type = e.target.id.split("-")[0];
    const datePicker = document.querySelector(`#${type}`);
    const timePicker = document.querySelector(`#${type}_hour`);

    const date = new Date();
    datePicker.valueAsDate = date;
    timePicker.value = `${date.getHours().toString().padStart(2, "0")}:${date
        .getMinutes()
        .toString()
        .padStart(2, "0")}`;
}

function setMe(e) {
    const userId = document.querySelector("#form").dataset.userId;
    const type = e.target.id.split("-")[0];
    const select = document.querySelector(`#${type}_id`);

    if (select) {
        [...select.children].forEach((e) => {
            if (e.value == userId) {
                e.selected = true;
            }
        });
    }
}

function showHideOutsourcing(e) {
    const row = document.querySelector("#outsourcing-row");
    row.style.display = e.target.checked ? "flex" : "none";
}

function printOnSend(e) {
    const dateInput = document.querySelector("#print_date");
    const timeInput = document.querySelector("#print_date_hour");
    if (e.target.checked) {
        latestPrintDate = dateInput.value;
        latestPrintTime = timeInput.value;

        const date = new Date();
        dateInput.valueAsDate = date;
        timeInput.value = `${date.getHours().toString().padStart(2, "0")}:${date
            .getMinutes()
            .toString()
            .padStart(2, "0")}`;
        printWorksheet = true;
    } else {
        dateInput.value = latestPrintDate
        timeInput.value = latestPrintTime
        printWorksheet = false
    }
}

function setCustomers(e) {
    const val = e.target.value;
    const updateUrl = e.target.dataset.updateUrl + `?id=${val}`;

    $.ajax({
        url: updateUrl,
        type: "GET",
        success: function (response) {
            if (response.success) {
                const customersSelect = document.querySelector("#customer_id");
                customersSelect.innerHTML = "";
                response.customers.forEach((e) => {
                    let child = document.createElement("option");
                    child.value = e.id;
                    child.innerHTML = e.name;
                    child.id = `customer-id-${e.id}`;
                    customersSelect.appendChild(child);
                });
            } else {
                alert("No customers found.");
            }
        },
        error: function (xhr) {
            let response = JSON.parse(xhr.responseText || "{}");
            if (xhr.status === 401 && response.redirect) {
                window.location.href = response.redirect;
            } else {
                alert(
                    "An error occurred: " + (response.error || xhr.statusText)
                );
            }
        },
    });
}
