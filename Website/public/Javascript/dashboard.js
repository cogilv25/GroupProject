$(document).foundation();
// Function to check if the modal has been shown before
function hasModalBeenShown() {
    return localStorage.getItem('modalShown') === 'true';
}
function openModal() {
    $('#inviteModal').foundation('open');
}
// Function to mark the modal as shown
function markModalAsShown() {
    localStorage.setItem('modalShown', 'true');
}

// Open the modal only if it hasn't been shown before
$(document).ready(function() {
if (!hasModalBeenShown()) {
    $('#inviteModal').foundation('open');
    markModalAsShown();
}

    $('#inviteButton').on('click', function(e) {
    e.preventDefault(); // Prevent the default behavior of the link
    openModal(); // Call the function to show the modal
});

$('.button.primary').click(function() {
    // Select the text inside the <a> tag
    var textToCopy = $('#invitationUrl').text();
    // Create a temporary input element
    var tempInput = $("<input>");
    // Append it to the body
    $("body").append(tempInput);
    // Set the value of the input to the text we want to copy
    tempInput.val(textToCopy).select();
    // Copy the selected text to the clipboard
    document.execCommand("copy");
    // Remove the temporary input element
    tempInput.remove();
    // Change button text to "Copied"
    $(this).text("Copied");
});
});