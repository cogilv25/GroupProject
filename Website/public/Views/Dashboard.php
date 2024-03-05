<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanSync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CSS/dashboard.css">
    <link rel="stylesheet" href="/CSS/householdModal.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js" crossorigin="anonymous"></script>    
</head>
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
                      <li><a class="navlink" href="rules">Rules</a></li>
                      <li><a class="navlink" href="#">Tasks</a></li>
                      <li><a id="loadHousehold" class="navlink" href="#">HouseHold</a></li>
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
                <div id="main-content" class="grid-x grid-margin-x small-up-1 medium-up-3 large-up-4">

                </div>
            </div>
        </div>
    </div>





    <?php include 'householdModal.html'; ?>
    <!--Invite members to household via this universal link -->
    <div class="reveal" id="inviteModal" data-reveal>
        <div class="grid-x grid-padding-x align-center">
        <div class="cell small-12 medium-12 large-12">
                <h2>Invite Household Members</h2>
                <p>Household Universal Link to allow members to join.</p>
                <form>
                    <div class="grid-x grid-padding-x align-middle">
                        <div class="cell auto urlstyle">
                            <!-- -->
                            <p><a id="invitationUrl"><?=$link?></a></p>
                        </div>
                        <div class="cell shrink">
                            <button type="button" class="button primary">Copy</button>
                        </div>
                    </div>
                </form>
                <button class="close-button" data-close aria-label="Close modal" type="button">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>


    <script src="/Javascript/dashboard.js"></script>
<script>

$(document).ready(function() {
    $(document).foundation();

    // Listen for click event on the link with ID 'loadHousehold'
    $('#loadHousehold').click(function(e) {
        e.preventDefault(); // Prevent the default link behavior
        $.ajax({
            url: 'household', // Path to your household.php file
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