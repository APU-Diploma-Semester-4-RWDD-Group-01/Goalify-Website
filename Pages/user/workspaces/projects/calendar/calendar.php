<!--
Author: Phang Shea Wen
Date: 2025/02/01
Filename: table.php
Description: Page showing table of project management
            (1) project task, sub-task, assigned members, priority, estimate, assigned date, due date, status
-->
<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';
require_once '../../../../includes/session.php';
require_once '../../../../includes/userFunction.php';

if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}

sessionTimeOut('/Goalify/Pages/user/login & register/login.php');
if (isset($_SESSION['project_id'])) {
    $userId = $_SESSION['user_id'];
    $workspaceId = $_SESSION['workspace_id'];
    // $workspaceId = 'W001';
    $selectedProjectId = $_SESSION['project_id'];
}

$workspaceName = getWorkspaceDetails($pdo, $workspaceId, 'workspaceName');
$projectName = getProjectDetails($pdo, $selectedProjectId, 'projectName');
$owner = getOwner($pdo, $workspaceId);
$allMembers = getMembers($pdo, $workspaceId);
?>

<script>
    var deviceTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace</title>
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../../global/global.css">
    <link rel="stylesheet" href="../../workspaces.css">
    <link rel="stylesheet" href="calendar.css">

    <script src="../../../global/JS/theme.js" defer></script>
    <script src="../../../global/JS/sideNav.js" defer></script>
    <script src="../../../global/JS/workspace.js" defer></script>
    <script src="../../../global/JS/global.js" defer></script>
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>

    <!-- Notification -->
    <link rel="stylesheet" href="../../../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script> -->

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">

    <!-- jQuery (Required) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>


</head>
<body>
    <?php include '../../../../includes/navigation.php'; ?>
    <?php include '../../../notification/notification.php' ?>
    <nav class="side-nav">
        <span class="material-symbols-rounded expand">chevron_right</span>
        <ul>
            <div id="toggle-side-nav"><h2 id="side-nav-workspace-name"><?php echo $workspaceName ?></h2><span class="material-symbols-rounded close">chevron_left</span></div>
            <hr>
            <a href="../../main/projects/projects.php"><li id="workspace-project">Projects</li></a>
            <hr>
            <a href="../../main/members/members.php"><li id="workspace-member">Members</li></a>
            <hr>
            <a href="../../main/description/description.php"><li id="workspace-description">Description</li></a>
            <br>
            <div class="projects">
                <h2 id="side-nav-project-name"><?php echo $projectName; ?></h2>
                <hr>
                <a href="../description/projectDescription.php"><li id="project-description">Description</li></a>
                <hr>
                <a href="../table/table.php"><li id="project-table">Table</li></a>
                <hr>
                <a href="../sharedFiles/sharedFiles.php"><li id="project-file">Shared Files</li></a>
                <hr>
                <a href="calendar.php"><li id="project-calendar">Calendar</li></a>
            </div>
        </ul>
    </nav>
    <div class="content">
        <div id="calendar"></div>
    </div>
</body>
</html>

<script>
function hexToRGBA(hex, a) {
    // '/.../ -> regular expression, ^ -> matches beginning of the string, # -> look for # char at the beginning of the string
    var hex = hex.replace(/^#/, '');
    if (hex.length === 3) {
        let char = hex.split('');
        hex = char[0].repeat(2) + char[1].repeat(2) + char[2].repeat(2);
    }
    // convert hex to decimal
    let r = parseInt(hex.substring(0, 2), 16);
    let g = parseInt(hex.substring(2, 4), 16);
    let b = parseInt(hex.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${a})`;
}
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
function toDateTime(isoDate) {
    const date = new Date(isoDate);
    const dateStr = date.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
    const timeStr = date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true}).toUpperCase();
    return dateStr + ' ' + timeStr;
}

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            console.log("Fetching events...");

            fetch('/Goalify/Pages/user/workspaces/projects/calendar/PHP/fetchEvents.php')
                .then(response => response.json())
                .then(data => {
                    console.log("Server Response:", data);

                    if (data.success && Array.isArray(data.allSubTasks)) {
                        let events = data.allSubTasks.map(subTask => {
                            let startDate = subTask.projectSubTaskAssignedDate || subTask.createdDateTime;
                            let dueDate = subTask.projectSubTaskDueDate || 'undefined';
                            let getStatus = subTask.projectSubTaskStatus || 'pending';
                            let getPriority = subTask.projectSubTaskPriority || 'none';

                            let endDate = null;
                            if (dueDate && dueDate !== 'undefined') {
                                endDate = new Date(dueDate);
                                endDate.setDate(endDate.getDate() + 1);
                            }

                            if (!startDate) {
                                console.warn("Missing start date for:", subTask.projectSubTaskId);
                                return null;
                            }

                            return {
                                title: subTask.projectSubTaskName,
                                start: startDate,
                                end: endDate ? endDate.toISOString().split('T')[0] : null, // Format to YYYY-MM-DD
                                due: dueDate,
                                status: capitalize(getStatus),
                                priority: capitalize(getPriority),
                                backgroundColor: subTask.color || "#007bff",
                                borderColor: subTask.color || "#007bff"
                            };
                        }).filter(event => event !== null);

                        console.log("Events to Render:", events);
                        successCallback(events);
                    } else {
                        console.error("Invalid response format", data);
                        failureCallback("Invalid response format");
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    failureCallback("Error fetching events");
                });
        },
        eventDidMount: function(info) {
            let container = document.createElement('div');
            container.style.backgroundColor = hexToRGBA(info.event.backgroundColor, 0.6);
            container.style.border = `1px solid ${info.event.borderColor}`;

            let circle = document.createElement('span');
            circle.style.width = '10px';
            circle.style.height = '10px';
            circle.style.borderRadius = '50%';
            circle.style.backgroundColor = info.event.backgroundColor;
            circle.style.marginRight = '10px';

            let eventTitle = document.createElement('span');
            eventTitle.textContent = info.event.title;
            // eventTitle.style.color = '#fff'; // Ensure text is visible

            container.appendChild(circle);
            container.appendChild(eventTitle);

            // Clear and append the styled container
            info.el.innerHTML = ''; // Fix property name
            info.el.appendChild(container);
        },
        eventClick: function(info) {
            if (document.querySelector('div.sub-task-details')) {
                document.removeChild(document.querySelector('div.sub-task-details'));
            }
            const popUpEvent = document.createElement('div');
            popUpEvent.classList.add('sub-task-details');
            var formattedDueDate = '';
            if (info.event.extendedProps.due !== 'undefined') {
                formattedDueDate = toDateTime(info.event.extendedProps.due);
            } else {
                formattedDueDate = capitalize(info.event.extendedProps.due);
            }
            popUpEvent.innerHTML = `
            <div id="pop-up-close-div"><span id="pop-up-close" class="material-symbols-rounded">close</span></div>
            <h3>Sub Task Details</h3>
            <label>Name</label>
            <div>${info.event.title}</div>

            <label>Assigned Date</label>
            <div>${toDateTime(info.event.start)}</div>

            <label>Deadline</label>
            <div>${formattedDueDate}</div>

            <label>Status</label>
            <div>${info.event.extendedProps.status}</div>

            <label for="">Priority</label>
            <div>${info.event.extendedProps.priority}</div>
            `
            const popUpWrapper = document.createElement('div');
            popUpWrapper.classList.add('pop-up-wrapper');
            popUpWrapper.appendChild(popUpEvent);
            document.body.appendChild(popUpWrapper);

            const closePopUp = popUpEvent.querySelector('span#pop-up-close');
            closePopUp.addEventListener('click', () => {
                document.body.removeChild(popUpWrapper);
            })
        }
    });
    calendar.render();
});
</script>