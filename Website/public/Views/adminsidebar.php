<?php

?>
<div class="off-canvas-wrapper">
      <div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
        <div class="off-canvas position-left reveal-for-large" id="sidenav" data-off-canvas data-position="left">
            <div class="grid-x grid-padding-x">
                <div class="cell small-12 text-center">
                    <a id="logo">CleanSync</a>
                </div>
                <div class="cell">
                <ul class="vertical menu accordion-menu" data-accordion-menu>
                      <li><a class="navlink <?= (!isset($page) || $page == 'home') ? 'active' : '' ?>" href="/">Home</a></li>
                      <li><a class="navlink <?= (!isset($page) && $page == 'userdashboard.php') ? 'active' : '' ?>" href="/userdashboard">User Dashboard</a></li>
                      <li><a class="navlink  <?= (isset($page) && $page == 'notifications.php') ? 'active' : '' ?>" href="/notifications">Notifications</a></li>
                      <li>
                        <a class="navlink <?= (isset($page) && $page == 'rules.php') ? 'active' : '' ?>">Rules</a>
                        <ul class="menu vertical nested">
                                <li><a class="navlink <?= (isset($page) && $page == 'rules.php') ? 'active' : '' ?>" href="/rule">View All Rules</a></li>
                                <li><a class="navlink  <?= (isset($page) && $page == 'addrule.php') ? 'active' : '' ?>" href="/rule/create">Add Rule</a></li>
                        </ul>
                        </li>
                      <li><a class="navlink  <?= (isset($page) && $page == 'adminTasks.php') ? 'active' : '' ?>" href="/task">Tasks</a></li>
                      <li><a class="navlink <?= (isset($page) && $page == 'adminroom.php') ? 'active' : '' ?>" href="/room">Rooms</a></li>
                      <li><a class="navlink <?= (isset($page) && $page == 'household.php') ? 'active' : '' ?>" href="/household">HouseHold</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'schedule.php') ? 'active' : '' ?>" href="/schedule">Schedule</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'profile.php') ? 'active' : '' ?>" href="/profile">Profile</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'settings.php') ? 'active' : '' ?>" href="/settings">Settings</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'logout') ? 'active' : '' ?>" href="/logout">Logout</a></li>
                      <li><a id="inviteButton" class="navlink">Invite Link</a></li>
                     
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
<style>
.active {
text-decoration:underline !important;
}
.navlink:hover {
color:black;
}
.accordion-menu .is-accordion-submenu-parent:not(.has-submenu-toggle) > a::after {
border-color: black transparent transparent;

}
</style>