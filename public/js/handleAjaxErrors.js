function handleAjaxErrors(errors) {

    document.querySelectorAll(".is-invalid").forEach((el) => {
        el.classList.remove("is-invalid");
    });

    document.querySelectorAll(".invalid-feedback").forEach((el) => {
        el.classList.remove("d-block");
        el.classList.add("d-none");
        el.textContent = "";
    });

    for (let field in errors) {
        let input = document.querySelector(`[name="${field}"]`);
        let feedback = input?.parentElement.querySelector(".invalid-feedback");

        if (input) {
            input.classList.add("is-invalid");
        }

        if (feedback) {
            feedback.classList.add("d-block");
            feedback.classList.remove("d-none");
            feedback.textContent = errors[field][0];
        }
    }
}
