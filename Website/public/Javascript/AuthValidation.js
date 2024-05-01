// Initialize Foundation
$(document).foundation();

$(document).ready(function() {
$(".toggleFormBtn").click(function() {
$("#loginForm").parent().toggle();
$("#signup").toggle(); 
});

// Function to toggle password visibility
function togglePassword(toggleIconId, passwordInputId) {
$(toggleIconId).click(function() {
    const type = $(passwordInputId).attr("type") === "password" ? "text" : "password";
    $(passwordInputId).attr("type", type);
    $(this).toggleClass("bi-eye bi-eye-slash");
});
}

// Initialize password toggles for each password field
togglePassword("#toggleLoginPassword", "#loginPassword");
togglePassword("#toggleSignupPassword", "#signupPassword");
togglePassword("#toggleConfirmPassword", "#confirmPassword");


// Email validation function
function isValidEmail(email) {
var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
return pattern.test(email);
}

// Password length validation function
function isValidPasswordLength(password) {
return password.length >= 8;
}

// Clear error message
function clearErrorMessage(element) {
$(element).text('');
}

// Set error message
function setErrorMessage(element, message) {
$(element).text(message);
}

// Validate Login Form
$("#loginForm").submit(function(event) {
var isValid = true;
clearErrorMessage("#emailError");
clearErrorMessage("#passwordError");

var email = $("#email").val();
if (!isValidEmail(email)) {
setErrorMessage("#emailError", "Please enter a valid email.");
isValid = false;
}

var password = $("#loginPassword").val();
if (!isValidPasswordLength(password)) {
setErrorMessage("#passwordError", "Password must be at least 8 characters.");
isValid = false;
}

if (!isValid) {
event.preventDefault(); 
}
});

// Validate Signup Form
$("#signupForm").submit(function(event) {
var isValid = true;
clearErrorMessage("#signupEmailError"); 
clearErrorMessage("#signupPasswordError"); 
clearErrorMessage("#confirmPasswordError"); 

var email = $("#signupEmail").val();
if (!isValidEmail(email)) {
setErrorMessage("#signupEmailError", "Please enter a valid email."); 
isValid = false;
}

var password = $("#signupPassword").val();
if (!isValidPasswordLength(password)) {
setErrorMessage("#signupPasswordError", "Password must be at least 8 characters."); 
isValid = false;
}
});

$("#confirmPassword").on('keyup', function() {
var password = $("#signupPassword").val();
var confirmPassword = $(this).val();

// Clear previous error message
clearErrorMessage("#confirmPasswordError");

// Check if passwords match
if (password !== confirmPassword) {
// If they don't match, display an error message
setErrorMessage("#confirmPasswordError", "Passwords do not match.");
isValid = false;
}
});

});

$(document).ready(function() {
    // Handle the submission of the login form
    $('#loginForm').submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Serialize the form data
        var formData = $(this).serialize();

        // Perform the AJAX request
        $.ajax({
            type: "POST",
            url: "/login", // Specify your own URL for the login form
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response && response.message) {
                    console.log("Login Successful: ", response.message);
                    location.reload();
                } else {
                    console.error("Error: Invalid response format");
                }
            },
            error: function(xhr, status, error) {
                try {
                    response = JSON.parse(xhr.responseText);
                    console.error("Error: ", response.message);
                } catch (e) {
                    console.error("Error submitting login form: ", error);
                }
            }
        });
    });

    // Handle the submission of the signup form
    $('#signupForm').submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Serialize the form data
        var formData = $(this).serialize();

        // Perform the AJAX request
        $.ajax({
            type: "POST",
            url: "/signup", // Specify your own URL for the signup form
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response && response.message) {
                                        location.reload();                    
                } else {
                    console.error("Error: Invalid response format");
                }
            },
            error: function(xhr, status, error) {
                var errorMsg;
                try {
                    var err = JSON.parse(xhr.responseText);
                    errorMsg = err.error.description; // Accessing the error description sent from the server
                } catch (e) {
                    errorMsg = 'Failed to parse error message.';
                }
                // Display error message to the user
                // Optionally, update the frontend to show the error
                $('#error-message').text(errorMsg).show(); // Ensure you have a container with id `error-message` in your HTML
            }
        });
    });
});
