<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanSync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CSS/dashboard.css">
</head>
<body>

    <div class="sidenav">
        <a id="logo">CleanSync</a>
        <a class="navlink" href="#">Home</a>
        <hr id="sidehr">
        <a class="navlink" href="/notifications">Notifications</a>
        <a class="navlink" href="/rules">Rules</a>
        <a class="navlink" href="/tasks">Tasks</a>
        <a class="navlink" href="/household">HouseHold</a>
        <!--Maybe household Admins can only see rooms not too sure yet -->
        <a class="navlink" href="/rooms">Rooms</a>
        <a class="navlink" href="">Profile</a>
        <a class="navlink" href="">Settings</a>
        <a class="navlink" href="/logout">Logout</a>
        <a class="navlink button" id="inviteButton">Invite</a>
    </div>

    <div class="main">

    </div>

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
                            <p><a id="invitationUrl">Output backend stuff here</a></p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js" crossorigin="anonymous"></script>    
    <script src="/Javascript/dashboard.js"></script>

</body>
</html> 