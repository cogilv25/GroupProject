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
                <div class="off-canvas-wrapper">
      <div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
        <div class="off-canvas position-left reveal-for-large" id="sidenav" data-off-canvas data-position="left">
            <div class="grid-x grid-padding-x">
                <div class="cell small-12 text-center">
                    <a id="logo">CleanSync</a>
                </div>
                <div class="cell">
                <ul class="vertical menu accordion-menu" data-accordion-menu>
                       
                <?php if($currentUser['homeless'] == true) { ?>
                    <li><a class="navlink <?= (!isset($page) || $page == 'home') ? 'active' : '' ?>" href="/">Home</a></li>
                    <li><a class="navlink <?= (isset($page) && $page == 'household.php') ? 'active' : '' ?>" href="/household">HouseHold</a></li>
                    <li><a class="navlink <?= (isset($page) && $page == 'logout') ? 'active' : '' ?>" href="/logout">Logout</a></li>

                    <?php } else { ?>
                        <li><a class="navlink <?= (!isset($page) || $page == 'home') ? 'active' : '' ?>" href="/">Home</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'notifications') ? 'active' : '' ?>" href="/notifications">Notifications</a></li>
                        <li>
                            <a class="navlink <?= (isset($page) && $page == 'rules.php') ? 'active' : '' ?>">Rules</a>
                            <ul class="menu vertical nested">
                                <li><a class="navlink <?= (isset($page) && $page == 'view_rules.php') ? 'active' : '' ?>" href="/rule">View Rules</a></li>
                                <li><a class="navlink  <?= (isset($page) && $page == 'add_rule.php') ? 'active' : '' ?>" href="rule/create">Add Rule</a></li>
                            </ul>
                        </li>
                        <li><a class="navlink <?= (isset($page) && $page == 'household.php') ? 'active' : '' ?>" href="/household">HouseHold</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'schedule.php') ? 'active' : '' ?>" href="/schedule">Schedule</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'profile.php') ? 'active' : '' ?>" href="/profile">Profile</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'settings.php') ? 'active' : '' ?>" href="/settings">Settings</a></li>
                        <li><a class="navlink <?= (isset($page) && $page == 'logout') ? 'active' : '' ?>" href="/logout">Logout</a></li>
                       <?php } ?>
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
        