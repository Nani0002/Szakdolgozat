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
    let moved = document.getElementById(data);

    if (
        (e.target.closest(".accordion-item") === null ||
            e.target.closest(".accordion-item").id !==
                e.dataTransfer.getData("text")) &&
        (!("final" in moved.dataset) || originalParent.id == container.id)
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

        if (container == e.target) {
            //Last place
            container.insertBefore(
                moved,
                container.children[container.children.length - 1]
            );
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
        const movedSlot = container.id.split("-")[2];

        let closeForm = document.querySelector(`#close-form-${movedId}`);
        let closeBTN = document.querySelector(`#close-btn-${movedId}`);

        if (movedSlot == "closed") {
            closeBTN.value = "Törlés";
            closeForm.action = moved.dataset.deleteUrl;
            closeForm.children[1].value = "delete";
        } else {
            closeBTN.value = "Lezárás";
            closeForm.action = moved.dataset.closeUrl;
            closeForm.children[1].value = "patch";
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
    formData.append("newSlot", newSlot == "" ? 0 : newSlot);
    for (const pair of formData.entries()) {
        console.log(pair[0], pair[1]);
      }
    $.ajax({
        url: updateUrl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
            } else {
                alert(response);
            }
        },
        error: function (xhr) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (xhr.status === 401 && response.redirect) {
                    window.location.href = response.redirect;
                } else if (xhr.status === 403) {
                    alert("Nincs jogosultságod.");
                } else {
                    alert(
                        "Hiba történt: " +
                            (response.error || JSON.stringify(response))
                    );
                }
            } catch (e) {
                alert("Belső szerverhiba (500). Ellenőrizd a Laravel logokat.");
                console.error(xhr.responseText);
            }
        },
    });
}
