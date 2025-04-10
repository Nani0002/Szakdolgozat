$(document).ready(function () {
    const profileImage = $("#profile-img");
    const imageUpdateUrl = profileImage.data("image-update-url");
    const csrfToken = profileImage.data("csrf-token");

    $("#changeImageBtn").click(function () {
        $("#imageUpload").click();
    });

    $("#imageUpload").change(function () {
        var formData = new FormData();
        var file = $(this)[0].files[0];

        if (file) {
            formData.append("image", file);
            formData.append("_token", csrfToken);

            $.ajax({
                url: imageUpdateUrl,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        profileImage.attr("src", response.new_image_url);
                        alert("Image updated successfully!");
                    } else {
                        alert("Image update failed!");
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
    });
});
