document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll("form").forEach(form => {

        form.addEventListener("submit", e => {

            const button = form.querySelector(
                'button[type="submit"], button:not([type])'
            );

            if (button && !form.checkValidity()) return;

            if (button) {
                button.disabled = true;
            }

        });

    });

});