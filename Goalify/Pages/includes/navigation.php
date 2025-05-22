<!-- Top & Bottom Navigation Bar -->

<nav id="top-nav-desktop">
    <div class="left">
        <span class="material-symbols-rounded" id="menu">menu</span>
        <img class="goalify-logo" src="/Goalify/Img/goalify_logo.png" alt="goalify_logo" width="130px" height="35px">
        <ul>
            <li><a href="/Goalify/Pages/user/todo/task-overview/task-overview.php" id="todo-nav-desktop">To-do</a></li>
            <li id="workspace-li">
                <a href="#" id="workspace-nav-desktop">Workspaces <span class="material-symbols-rounded">keyboard_arrow_down</span></a>
                <ul class="drop-down">
                    <li id="create-workspace-button"><span class="material-symbols-rounded">add_box</span>Create Workspace</li>
                    <hr>
                    <li id="select-workspace-button"><span class="material-symbols-rounded">arrow_selector_tool</span>Select Workspace</li>
                    <hr>
                    <li id="join-workspace-button"><span class="material-symbols-rounded">tag</span>Join Workspace</li>
                </ul>
            </li>
            <li><a href="/Goalify/Pages/user/achievements/achievements.php" id="achievement-nav-desktop">Achievements</a></li>
            <li><a href="/Goalify/Pages/user/time-tracking/time_tracking.php" id="timer-nav-desktop">Time-Tracking</a></li>
            <li><a href="/Goalify/Pages/user/goal/goal.php" id="goal-nav-desktop">Goal</a></li>
        </ul>
    </div>
    <div class="right">
        <ul>
            <li><a class="material-symbols-rounded theme_mode">dark_mode</a></li>
            <li><span class="noti-and-pointer">
                    <a href="#" class="material-symbols-rounded notifications">notifications</a>
                    <span id="noti-pointer"></span>
                </span>
            </li>
            <li><a href="/Goalify/Pages/user/contactSupport/contactSupport.php" class="material-symbols-rounded contact_support">contact_support</a></li>
        </ul>
        <div class="profile-pic"><a href="/Goalify/Pages/user/profile/profile.php"><img src="/Goalify/Img/default_profile.png" alt="user-profile"></a></div>
    </div>
</nav>

<nav id="bottom-nav-mobile">
    <ul>
        <li><a href="/Goalify/Pages/user/todo/task-overview/task-overview.php" id="todo-nav-mobile"><span class="material-symbols-rounded">format_list_bulleted</span>To-do</a></li>
        <li><a href="#" id="workspace-nav-mobile"><span class="material-symbols-rounded">workspaces</span>Workspaces</a></li>
        <li><a href="/Goalify/Pages/user/achievements/achievements.php" id="achievement-nav-mobile"><span class="material-symbols-rounded">trophy</span>Achievements</a></li>
        <li><a href="/Goalify/Pages/user/time-tracking/time_tracking.php" id="timer-nav-mobile"><span class="material-symbols-rounded">timer</span>Time-Tracking</a></li>
        <li><a href="/Goalify/Pages/user/goal/goal.php" id="goal-nav-mobile"><span class="material-symbols-rounded">flag_2</span>Goal</a></li>
    </ul>
</nav>
<script src="/Goalify/Pages/user/global/JS/global.js"></script>