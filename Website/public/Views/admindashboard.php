<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CleanSync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CSS/dashboard.css"> 
    <link rel="stylesheet" href="/CSS/householdModal.css">
    <link rel="stylesheet" href="/CSS/scheduleTimeRangeControl.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js" crossorigin="anonymous"></script>    

</head>
<body>
<style>
  body,  html {
    height:100vh;
  }
#sidenav, .navlink {
  background-color: #468F8B;
  color: black;
  font-weight: bold;
}
#logo {
    font-weight: bold;
    text-align: center;
    align-items: center;
    color: black;
    font-size: larger;
}
.card{
  border:solid 2px black;
  margin: 5px;
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  border-radius: 5px;
}
.custombtn {
  margin-bottom: 1rem !important;
  color:black !important;
  font-weight:bolder;
  font-size:13px;
}

.table-wrapper {
    width: 90%; /* Adjust this value to control the width of the table */
    margin: auto; /* Centers the table wrapper within its parent */
    overflow-x: auto; /* Adds a scrollbar if the table overflows */
}

.custom-table {
    border: 2px solid black;
    width: 100%; /* Makes the table take up the full width of its wrapper */
}

.custom-table th, .custom-table td {
    border-right: 1px solid black;
    border-collapse: collapse;
}

.custom-table th:last-child, .custom-table td:last-child {
    border-right: none;
}

#addTaskButton {
    /* Button styles */
    padding-right: 20px;

}

@media screen and (max-width: 640px) {
    .custom-table th, .custom-table td {
        border-right: none;
    }
}

.add-task-button-container {
    text-align: right;
    padding-top: 15px;
    margin-right:150px;
     /* Adjust the space between the button and the table */
}
  </style>
  <body>

    <div class="off-canvas-wrapper">
      <div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
        <div class="off-canvas position-left reveal-for-large" id="sidenav" data-off-canvas data-position="left">
            <div class="grid-x grid-padding-x">
                <div class="cell small-12 text-center">
                    <a id="logo">CleanSync</a>
                </div>
                <div class="cell">
                    <ul class="vertical menu ">
                      <li><a class="navlink" href="/">Home</a></li>
                      <li><a class="navlink" href="notifications">Notifications</a></li>
                      <li><a id="loadRules"  class="navlink" >Rules</a></li>
                      <li><a id="loadTasks" class="navlink" >Tasks</a></li>
                      <li><a id="loadHousehold" class="navlink" >HouseHold</a></li>
                      <li><a id="loadSchedule" class="navlink">Schedule</a></li>
                      <li><a id="loadRooms" class="navlink">Rooms</a></li>
                      <li><a class="navlink" href="profile">Profile</a></li>
                      <li><a class="navlink" href="settings">Settings</a></li>
                      <li><a class="navlink" href="/logout">Logout</a></li>
                  </ul>
                </div>
            </div>
        </div>
        <div class="off-canvas-content" data-off-canvas-content>
            <div class="title-bar hide-for-large">
                <div class="title-bar-left">
                    <button class="menu-icon" type="button" data-open="sidenav"></button>
                    <span class="title-bar-title">CleanSync</span>
                </div>
            </div>
          <div id="main-content" class="grid-x grid-margin-x small-up-1 medium-up-3 large-up-4">
                <div class="cell">
                  <div class="card" >
                    <div class="card-section">
                      <h4>Dashboard Card.</h4>
                      <p>Responsive card width n height</p>
                    </div>
                  </div>
                </div>
                <div class="cell">
                  <div class="card" >
                    <div class="card-section">
                      <h4>Dashboard Card.</h4>
                      <p>Responsive card width n height</p>
                    </div>
                  </div>
                </div>
                <div class="cell">
                  <div class="card" >
                    <div class="card-section">
                      <h4>Dashboard Card.</h4>
                      <p>Responsive card width n height</p>
                    </div>
                  </div>
                </div>
                <div class="cell">
                  <div class="card" >
                    <div class="card-section">
                      <h4>Dashboard Card.</h4>
                      <p>Responsive card width n height</p>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $(document).foundation();

    // Listen for click event on the link with ID 'loadHousehold'
    $('#loadHousehold').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'household/page', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Insert the fetched content into the 'household-content' div
                $('#main-content').html(response);
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });

    
     // Listen for click event on the link with ID 'loadHousehold'
    $('#loadSchedule').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'schedule/page', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Insert the fetched content into the 'household-content' div
                $('#main-content').html(response);
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });

    $('#loadRooms').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'adminroom.php', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Insert the fetched content into the 'household-content' div
                $('#main-content').html(response);
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });

    $('#loadTasks').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'adminTasks.php', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Insert the fetched content into the 'household-content' div
                $('#main-content').html(response);
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });

    $('#loadRules').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'adminRules.php', // Path to your household.php file
            type: 'GET', // GET method to fetch data
            success: function(response) {
                // Insert the fetched content into the 'household-content' div
                $('#main-content').html(response);
            },
            error: function(xhr, status, error) {
                // Handle any errors
                console.error("Error: " + status + " " + error);
            }
        });
    });


});
</script>

</body>
</html> 