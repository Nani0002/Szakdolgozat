window.addEventListener("load", init, false);

function init() {
    const timeBTNs = document.querySelectorAll(".time-btn");
    timeBTNs.forEach((e) => {
        if (e.id == "liable-btn" || e.id == "coworker-btn") {
            e.addEventListener("click", setMe, false);
        } else {
            e.addEventListener("click", setNow, false);
        }
    });
    document
        .querySelector("#outsourcing-switch")
        .addEventListener("click", showHideOutsourcing, false);
    document
        .querySelector("#print_check")
        .addEventListener("click", printOnSend, false);
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
    row.style.display = e.target.checked ? "inherit" : "none";
}

function printOnSend(e) {
    if (e.target.checked) {
        const date = new Date();
        document.querySelector("#print_date").valueAsDate = date;
        document.querySelector("#print_date_hour").value = `${date
            .getHours()
            .toString()
            .padStart(2, "0")}:${date
            .getMinutes()
            .toString()
            .padStart(2, "0")}`;
    }
}
