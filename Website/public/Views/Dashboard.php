<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanSync</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/CSS/dashboard.css">
    <link rel="stylesheet" href="/CSS/scheduleTimeRangeControl.css">
    <link rel="stylesheet" href="/CSS/householdModal.css">
    
    <?php // TODO: only used on the schedule page so we should insert this line with php ideally ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/roundSlider/1.6.1/roundslider.css" integrity="sha512-XO53CaiPx+m4HUiZ02P4OEGLyyT46mJQzWhwqYsdqRR7IOjPuujK0UPAK9ckSfcJE4ED7dT9pF9r78yXoOKeYw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                      <li><a class="navlink" href="/notifications">Notifications</a></li>
                      <li>
                            <a class="navlink">Rules</a>
                            <ul class="menu vertical nested">
                                <li><a class="navlink" href="/rule">View All Rules</a></li>
                                <li><a class="navlink" href="/rule/create">Add Rule</a></li>
                            </ul>
                        </li>
                        <li>
                      <li><a class="navlink" href="/task">Tasks</a></li>
                      <li><a class="navlink" href="/household">HouseHold</a></li>
                      <li><a class="navlink" href="/schedule">Schedule</a></li>
                      <li><a class="navlink" href="/room">Rooms</a></li>
                      <li><a class="navlink" href="/profile">Profile</a></li>
                      <li><a class="navlink" href="/settings">Settings</a></li>
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
            <?php 
                if(isset($page))
                {
                    if($page !== false)
                        include($page);
                }
                else
                {
                    // Default view goes here.
            ?>
                


            <?php } ?>
            </div>
        </div>
    </div>
</div>




<?php 
    if(isset($dynamicScripts))
    {
        foreach($dynamicScripts as $path)
        {
            echo("<script>\n");
            include($path);
            echo("</script>\n");
        }
    }

    if(isset($staticScripts))
    {
        foreach($staticScripts as $path)
        {
            echo("<script src=\"" . $path . "\"></script>\n");
        }
    }
?>
</body>
</html> 