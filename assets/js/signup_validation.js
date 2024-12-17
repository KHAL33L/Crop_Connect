// Validate the form before submission
document.getElementById('signup-form').addEventListener('submit', function(event) {
    var password = document.getElementById('password').value;
    var repeatPassword = document.getElementById('repeat-password').value;

    // Updated password regex to require at least one uppercase letter, one number, and one special character
    var passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>])[A-Za-z\d!@#$%^&*(),.?":{}|<>]{8,}$/;

    // Check if password meets the criteria
    if (!passwordRegex.test(password)) {
        alert("Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character.");
        event.preventDefault();
        return false;
    }

    // Check if passwords match
    if (password !== repeatPassword) {
        alert("Passwords do not match.");
        event.preventDefault();
        return false;
    }

    return true; // Allow form submission if validation passes
});
