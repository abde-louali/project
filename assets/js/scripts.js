function togglePasswordVisibility(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "🔒"; // Change to "lock" icon
    } else {
        input.type = "password";
        icon.textContent = "👁"; // Change back to "eye" icon
    }
}