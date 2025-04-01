window.addEventListener("load", init, false);

let computers = [];

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
}

function getComputers(e) {
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
    const url = e.target.dataset.attachUrl;
    const csrfToken = e.target.dataset.csrfToken;

    const computer_id = document.querySelector("#computer_id").value;
    const password = document.querySelector("#password").value;
    const condition = document.querySelector("#condition").value;
    const imagename = document.querySelector("#imagefile").files[0];

    let formData = new FormData();
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

                $newCard.insertBefore($container.children().eq(-1));
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
