function kickUser(userId)
{
    $.ajax({
        url: 'household/remove', // Path to your household.php file
        type: 'POST', // GET method to fetch data
        data: 'userId='+userId,
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
            console.log(xhr.responseText);
        }
    });
}

function promoteUser(userId)
{
    $.ajax({
        url: 'household/promote', // Path to your household.php file
        type: 'POST', // GET method to fetch data
        data: 'userId='+userId,
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
        }
    });
}

function transferHousehold(userId)
{
    $.ajax({
        url: 'household/transfer', // Path to your household.php file
        type: 'POST', // GET method to fetch data
        data: 'userId='+userId,
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
            console.log(xhr.responseText);
        }
    });
}

function demoteUser(userId)
{
    $.ajax({
        url: 'household/demote', // Path to your household.php file
        type: 'POST', // GET method to fetch data
        data: 'userId='+userId,
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
        }
    });
}

function createHouse() {
    $.ajax({
        url: 'household/create', // Path to your household.php file
        type: 'GET', // GET method to fetch data
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
        }
    });
}

function leaveHouse() {
    $.ajax({
        url: 'household/leave', // Path to your household.php file
        type: 'GET', // GET method to fetch data
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
        }
    });
}

function deleteHouse() {
    $.ajax({
        url: 'household/delete', // Path to your household.php file
        type: 'GET', // GET method to fetch data
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle any errors
            console.error("Error: " + status + " " + error);
        }
    });
}