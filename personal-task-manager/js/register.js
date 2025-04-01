document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    if (!form) {
        console.error("No form element found on the page.");
        return;
    }

    form.addEventListener("submit", function (event) {
        // Retrieve input values
        const username = form.username.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value;
        const confirmPassword = form.confirm_password.value;

        // Check if all fields are filled
        if (!username || !email || !password || !confirmPassword) {
            event.preventDefault();
            alert("All fields are required.");
            return;
        }

        // Validate email format using a regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            event.preventDefault();
            alert("Invalid email format.");
            return;
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            event.preventDefault();
            alert("Passwords do not match.");
            return;
        }
    });
});
