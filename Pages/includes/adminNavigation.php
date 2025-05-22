<nav id="top-nav">
    <div class="left">
        <span class="material-symbols-rounded" id="menu">menu</span>
        <div class="history-tab">
            <p>Dashboard</p>
        </div>
    </div>
    <div class="right">
        <!-- <form action="" method="post" id="search-bar">
            <span class="material-symbols-rounded expand">search</span><input type="search" name="search-input" id="search-input" placeholder="Search ...">
        </form> -->
        <ul>
            <!-- <li><a class="material-symbols-rounded notifications">notifications</a></li> -->
            <li id="logout-mobile"><a href="/Goalify/Pages/includes/adminLogout.php" class="material-symbols-rounded">logout</a></li>
        </ul>
        <div class="profile-pic"><img src="/Goalify/Img/admin_profile.png" alt="admin-profile"></div>
    </div>
</nav>
<nav class="side-nav active">
    <img class="goalify-logo" src="/Goalify/Img/goalify_logo.png" alt="goalify_logo" width="130px" height="35px">
    <div class="profile">
        <div class="admin-profile-pic"><img src="/Goalify/Img/admin_profile.png" alt="admin-profile"></div>
        <p>Welcome, Admin</p>
    </div>
    <ul>
        <a href="/Goalify/Pages/admin/dashboard/dashboard.php"><li><span class="material-symbols-rounded">grid_view</span><p>Dashboard</p></li></a>
        <a href="/Goalify/Pages/admin/userManagement/userManagement.php"><li><span class="material-symbols-rounded">manage_accounts</span><p>User Management</p></li></a>
        <a href="/Goalify/Pages/admin/userMetrics/userMetrics.php"><li><span class="material-symbols-rounded">person</span><p>User Metrics</p></li></a>
        <a href="/Goalify/Pages/admin/productivity/productivity.php"><li><span class="material-symbols-rounded">workspaces</span><p>Productivity Metrics</p></li></a>
    </ul>
    <a href="/Goalify/Pages/includes/adminLogout.php" id="logout">
        <span class="material-symbols-rounded">logout</span>
        <p>Log Out</p>
    </a>
</nav>
<nav id="bottom-nav-mobile">
    <ul>
        <li><a href="/Goalify/Pages/admin/dashboard/dashboard.php" id="dashboard-mobile"><span class="material-symbols-rounded">grid_view</span>Dashboard</a></li>
        <li><a href="/Goalify/Pages/admin/userManagement/userManagement.php" id="user-management-mobile"><span class="material-symbols-rounded">manage_accounts</span>User Management</a></li>
        <li><a href="/Goalify/Pages/admin/userMetrics/userMetrics.php" id="user-metrics-mobile" href="/Goalify/Pages/admin/userMetrics/userMetrics.php"><span class="material-symbols-rounded">person</span>User Metrics</a></li>
        <li><a href="/Goalify/Pages/admin/productivity/productivity.php" id="productivity-mobile"><span class="material-symbols-rounded">workspaces</span>Productivity</a></li>
    </ul>
</nav>
