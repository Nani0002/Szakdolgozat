window.addEventListener("load", init, false);

let computers = [];

let pivot = "";
let key = 0;

let editmode = false;

function init() {
    document
        .querySelector("#select-computer")
        .addEventListener("click", getComputers, false);

    document
        .querySelector("#computer_id")
        .addEventListener("change", update, false);

    document.querySelector("#changeImageBtn").addEventListener(
        "click",
        function () {
            document.querySelector("#imagefile").click();
        },
        false
    );

    document
        .querySelector("#imagefile")
        .addEventListener("change", updateImage, false);

    document
        .querySelector("#attach-btn")
        .addEventListener("click", attach, false);

    const refreshBtns = document.querySelectorAll(".get-btn");
    refreshBtns.forEach((e) => e.addEventListener("click", get, false));
}

function getComputers(e) {
    editmode = false;
    const updateUrl = e.target.dataset.getUrl;

    $.ajax({
        url: updateUrl,
        type: "GET",
        success: function (response) {
            if (response.success) {
                const select = document.querySelector("#computer_id");
                select.innerHTML = "";
                computers = response.computers;
                computers.forEach((e) => {
                    let child = document.createElement("option");
                    child.value = e.id;
                    child.innerHTML = e.serial_number;
                    child.id = `computer-id-${e.id}`;
                    select.appendChild(child);
                });
                document.querySelector("#static-manufacturer").innerHTML =
                    computers[0]["manufacturer"];
                document.querySelector("#static-type").innerHTML =
                    computers[0]["type"];

                document.querySelector("#condition").value =
                    computers[0]["latest_info_pivot"]["condition"];
                document.querySelector("#password").value =
                    computers[0]["latest_info_pivot"]["password"];
                document.querySelector("#prewiew").src =
                    "/storage/images/" +
                    computers[0]["latest_info_pivot"]["imagename_hash"];
                document.querySelector("#prewiew").alt =
                    computers[0]["latest_info_pivot"]["imagename"];
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

function update(e) {
    const val = e.target.value;

    document.querySelector("#static-manufacturer").innerHTML =
        computers[val]["manufacturer"];
    document.querySelector("#static-type").innerHTML = computers[val]["type"];
    document.querySelector("#condition").value =
        computers[val]["latest_info_pivot"]["condition"];
    document.querySelector("#password").value =
        computers[val]["latest_info_pivot"]["password"];
    document.querySelector("#prewiew").src =
        "/storage/images/" +
        computers[val]["latest_info_pivot"]["imagename_hash"];
    document.querySelector("#prewiew").alt =
        computers[val]["latest_info_pivot"]["imagename"];
}

function updateImage() {
    const file = this.files[0];

    if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.querySelector("#prewiew").src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function attach(e) {
    let url = "";
    let formData = new FormData();
    if (editmode) {
        url = e.target.dataset.refreshUrl;
        formData.append("_method", "put");
        formData.append("pivot_id", pivot.id);
        formData.append("key", key);
    } else url = e.target.dataset.attachUrl;
    const csrfToken = e.target.dataset.csrfToken;

    const computer_id = document.querySelector("#computer_id").value;
    const password = document.querySelector("#password").value;
    const condition = document.querySelector("#condition").value;
    const imagename = document.querySelector("#imagefile").files[0];

    formData.append("_token", csrfToken);
    formData.append("computer_id", computer_id);
    formData.append("password", password);
    formData.append("condition", condition);
    if (imagename != undefined) formData.append("imagefile", imagename);

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                const $container = $("#computer-container");
                const $newCard = $(response.html);
                if (!editmode)
                    $newCard.insertBefore($container.children().eq(-1));
                else{
                    $container.children().eq(key).replaceWith($newCard);
                }

                $("#attach-btn").blur();
                $("#select-modal").modal("hide");
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

function get(e) {
    editmode = true;
    key = e.target.id.split('-')[1];
    const url = e.target.dataset.getUrl;

    $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
            if (response.success) {
                const select = document.querySelector("#computer_id");
                pivot = response.pivot;
                let computer = response.computer;
                select.innerHTML = "";

                let child = document.createElement("option");
                child.value = computer.id;
                child.innerHTML = computer.serial_number;
                select.appendChild(child);

                document.querySelector("#static-manufacturer").innerHTML =
                    computer.manufacturer;
                document.querySelector("#static-type").innerHTML =
                    computer.type;

                document.querySelector("#condition").value = pivot.condition;
                document.querySelector("#password").value = pivot.password;
                document.querySelector("#prewiew").src =
                    "/storage/images/" + pivot.imagename_hash;
                document.querySelector("#prewiew").alt = pivot.imagename;
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
