let originalParent;

function allowDrop(e) {
    e.preventDefault();
}

function drag(e) {
    originalParent = e.target.closest(".dragdrop-container");
    e.dataTransfer.setData("text", e.target.id);
}

function drop(e) {
    e.preventDefault();
    const data = e.dataTransfer.getData("text");
    const container = e.target.closest(".dragdrop-container");
    if (
        e.target.closest(".accordion-item") === null ||
        e.target.closest(".accordion-item").id !==
            e.dataTransfer.getData("text")
    ) {
        let i = 0;
        [...container.children].forEach((element) => {
            if (element.dataset.slot != i) {
                element.dataset.slot = i;
            }
            i++;
        });
        i = 0;

        [...originalParent.children].forEach((element) => {
            if (element.dataset.slot != i) {
                element.dataset.slot = i;
            }
            i++;
        });
        let slot = 0;
        let moved = document.getElementById(data);

        if (container == e.target) {
            //Last place
            container.appendChild(moved);
        } else {
            //Middle
            slot = e.target.closest(".accordion-item").dataset.slot;
            [...container.children].forEach((element) => {
                if (element.dataset.slot == slot) {
                    container.insertBefore(moved, element);
                }
            });
        }

        i = 0;
        [...container.children].forEach((element) => {
            if (element.dataset.slot != i) {
                element.dataset.slot = i;
            }
            i++;
        });

        i = 0;
        [...originalParent.children].forEach((element) => {
            if (element.dataset.slot != i) {
                element.dataset.slot = i;
            }
            i++;
        });

        const movedId = moved.id.split("-")[1];
        const movedSlot = container.id.split("-")[2]

        let closeForm = document.querySelector(`#close-form-${movedId}`);
        let closeBTN = document.querySelector(`#close-btn-${movedId}`);

        if (movedSlot == "closed") {
            closeBTN.value = "Törlés"
            closeForm.action = moved.dataset.deleteUrl
            closeForm.children[1].value = "delete"
        }
        else{
            closeBTN.value = "Lezárás"
            closeForm.action = moved.dataset.closeUrl
            closeForm.children[1].value = "patch"
        }

        move(movedId, movedSlot, moved.dataset.slot);
    } else {
        //Same place, do nothing
        return;
    }
}

function move(id, newStatus, newSlot) {
    const frame = $("#dragdrop-frame");
    const updateUrl = frame.data("update-url");
    const csrfToken = frame.data("csrf-token");

    let formData = new FormData();
    formData.append("_token", csrfToken);
    formData.append("id", id);
    formData.append("newStatus", newStatus);
    formData.append("newSlot", newSlot);

    $.ajax({
        url: updateUrl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                console.log(response);
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
