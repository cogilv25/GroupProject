<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CleanSync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CSS/dashboard.css"> 
    <link rel="stylesheet" href="/CSS/householdModal.css">



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

.small-margin {
    margin-left: 2rem; /* Adds margin to the left */
    margin-right: 2rem; /* Adds margin to the right */
}

h2.text-center {
    font-size: 2rem; /* Adjust the size as needed */
    margin-top: 1rem;
}
.customh5{
    margin-left:150px;
}
.custominputs {
height:30px;
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
                    <ul class="vertical menu" data-accordion-menu>
                        <li><a class="navlink" href="/">Home</a></li>
                        <li><a class="navlink" href="admindashboard.php">Admin Dashboard</a></li>
                        <li><a class="navlink" href="notifications">Notifications</a></li>
                        <li>
                        <a class="navlink">Rules</a>
                        <ul class="menu vertical nested">
                        <li><a class="navlink" href="rules.php">View All Rules</a></li>
                        <li><a class="navlink" href="rule/create">Add Rule</a></li>
                        </ul>
                        </li>
                        <li><a class="navlink" href="#">Tasks</a></li>
                        <a class="navlink" data-open="exampleModal">HouseHold</a>
                        <li><a class="navlink" href="schedule">Schedule</a></li>
                        <li><a class="navlink" href="#">Rooms</a></li>
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
            <div id="main-content" class="grid-x grid-margin-x">
                <div class="cell small-12" style="width: 100%;">
                    <div class="add-task-button-container">
                        <a href="rule/create" class="button" id="addTaskButton">Add Rule</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="unstriped custom-table" >
                            <thead>
                                <tr>
                                <th width="200">Members</th>
                                <th width="200">Rule Name</th>
                                <th width="200">Type of Rule</th>
                                <th width="150">Enable</th>
                                <th width="150">Disable</th>
                                <th width="150">Modify</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <td>Test 1</td>
                                <td>No hoovering at 15:00</td>
                                <td>User + Task</td>
                                <td><input type="checkbox" name="check1"></td>
                                <td><input type="checkbox" name="check2"></td>
                                <td><input type="checkbox" name="check3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js" crossorigin="anonymous"></script>   
<script>
    $(document).ready(function() {
        $(document).foundation();
    });
</script>

</body>
</html> 