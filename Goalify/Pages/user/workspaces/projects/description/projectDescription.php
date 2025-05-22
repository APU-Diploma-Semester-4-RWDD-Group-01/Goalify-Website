<?php
require '../../../../includes/db.php';
require '../../../../includes/userFunction.php';
require '../../workspace.php';
require_once '../../../../includes/session.php';
include '../../../notification/notification.php';
session_start();
error_log("session_status: " . session_status());
if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');
if (isset($_SESSION['workspace_id']) && isset($_SESSION['project_id'])) {
    $userId = $_SESSION['user_id'];
    $workspaceId = $_SESSION['workspace_id'];
    $projectId = $_SESSION['project_id'];
    $owner = false;
    $descriptionFromDb = null;
    $stmt = $pdo -> prepare('SELECT 1
                            FROM `workspace`
                            WHERE `ownerId` = :userId AND `workspaceId` = :workspaceId;');
    $stmt -> execute([':userId' => $userId, ':workspaceId' => $workspaceId]);
    $result = $stmt -> fetchAll();
    if ($result) {
        $owner = true;
    }
    $projectName = getProjectDetails($pdo, $projectId, 'projectName');
    $projectCreatedDateTime = getProjectDetails($pdo, $projectId, 'projectCreatedDateTime');
    $projectDeadline = getProjectDetails($pdo, $projectId, 'projectDeadline');
    $projectStatus = getProjectDetails($pdo, $projectId, 'projectStatus');
    $projectDescription = getProjectDetails($pdo, $projectId, 'projectDescription');
    $projectDescriptionUpdate = getProjectDetails($pdo, $projectId, 'projectDescriptionUpdate');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../../../global/global.css">
    <link rel="stylesheet" href="../../workspaces.css">
    <link rel="stylesheet" href="projectDescription.css">
    <title>Workspaces</title>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../../../global/JS/theme.js" defer></script>
    <script src="../../../global/JS/sideNav.js" defer></script>
    <script src="../../../global/JS/workspace.js" defer></script>
    <!-- Notification -->
    <link rel="stylesheet" href="../../../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
</head>
<body>
    <?php include '../../../../includes/navigation.php'; ?>
    <?php include '../../../notification/notification.php' ?>
    <nav class="side-nav">
        <span class="material-symbols-rounded expand">chevron_right</span>
        <ul>
            <div id="toggle-side-nav"><h2 id="side-nav-workspace-name"><?php echo getWorkspaceDetails($pdo, $workspaceId, 'workspaceName') ?></h2><span class="material-symbols-rounded close">chevron_left</span></div>
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
                <a href="../calendar/calendar.php"><li id="project-calendar">Calendar</li></a>
            </div>
        </ul>
    </nav>
    <div class="content">
        <h1>Project's Details</h1>
        <div class="project-details">
            <label>Name</label>
            <form id="project-name-form">
                <input name="project-name" id="project-name" type='text' readonly autocomplete="off">
                <a id="edit-project-name">Edit</a>
                <button type="submit" id="submit-project-name-button" style="display: none;">Save</button>
            </form>

            <label>Created at</label>
            <?php
            $createdDatetime = new DateTime($projectCreatedDateTime);
            $projectCreatedDate = $createdDatetime->format('d F Y');
            ?>
            <div><?php echo $projectCreatedDate?></div>

            <label>Deadline</label>
            <div id="project-deadline"></div>

            <label>Status</label>
            <div><?php echo ucfirst($projectStatus)?></div>
        </div>

        <h1>Description</h1>
        <form action="#" method="POST" id="description-form">
            <textarea name="description" id="description" type='input' readonly placeholder="Add a Description..."></textarea>
            <p id="last-updated-time"></p>
            <div class="button">
                <button type="button" id="edit-description-button">Edit</button>
                <button type="submit" id="submit-description-button" style="display: none;">Done</button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const projectName = document.querySelector('form#project-name-form input#project-name');

            projectName.value = '<?php echo getProjectDetails($pdo, $projectId, 'projectName'); ?>';

            const editNameButton = document.querySelector('a#edit-project-name');
            const saveNameButton = document.querySelector('button#submit-project-name-button');
            editNameButton.addEventListener('click', () => {
                if (projectName.hasAttribute('readonly')) {
                    projectName.removeAttribute('readonly');
                    projectName.focus();
                    editNameButton.style.display = 'none';
                    saveNameButton.style.display = 'inline';
                }
            })

            saveNameButton.addEventListener('click', () => {
                if (!projectName.hasAttribute('readonly')) {
                    projectName.setAttribute('readonly', true);
                    editNameButton.style.display = 'inline';
                    saveNameButton.style.display = 'none';
                    }
            })

            const projectNameForm = document.querySelector('.content .project-details form#project-name-form');
            projectNameForm.addEventListener('submit', (event) => {
                event.preventDefault(); //prevent default form submission

                // retrieve form data
                const projectNameData = new FormData(projectNameForm);
                projectNameData.append("projectId", "<?php echo $projectId; ?>");

                // create new xml http request
                const xhr = new XMLHttpRequest();

                // set up request
                xhr.open('POST', 'projectDescriptionUpdate.php', true)

                // define the callback function
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        projectName.value = response.projectName;
                        console.log(response.projectName);
                        alert(`Project name is updated to '${response.projectName}'!`);

                        const sideNavProjectName = document.querySelector('.side-nav ul div.projects h2#side-nav-project-name'); // not sure
                        sideNavProjectName.textContent = response.projectName;
                    } else {
                        // display an error message
                        console.log('Form submission failed:', xhr.statusText);
                    }
                };
                
                // send the form data
                xhr.send(projectNameData);
            })
        })
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const description = document.querySelector('textarea#description');

            // display description last update datetime
            const storedDescription = <?php echo $projectDescription !== null ? json_encode($projectDescription) : 'null'; ?>;
            <?php
                $descriptionUpdateDate = null;
                $descriptionUpdateTime = null;
                if ($projectDescriptionUpdate) {
                    $datetime = new DateTime($projectDescriptionUpdate);
                    $descriptionUpdateDate = $datetime->format('d F Y');
                    $descriptionUpdateTime = $datetime->format('h:i:s A'); // can use strtoupper() to ensure PM is in capital
                } // continue here
            ?>
            let descriptionUpdateDate = "<?php echo $descriptionUpdateDate ?>";
            let descriptionUpdateTime = "<?php echo $descriptionUpdateTime ?>";
            
            const lastUpdated = document.querySelector('p#last-updated-time');

            if (storedDescription) {
                description.textContent = storedDescription;
                lastUpdated.textContent = `Last updated on ${descriptionUpdateDate}, at ${descriptionUpdateTime}`;
            }

            // dynamically resize the description textarea
            description.addEventListener('input', () => {
                description.style.height = "auto";
                description.style.height = description.scrollHeight + "px";
            })

            // alternatively display edit button and done button
            const editButton = document.querySelector('button#edit-description-button');
            const submitButton = document.querySelector('button#submit-description-button');
            editButton.addEventListener('click', () => {
                if (description.hasAttribute('readonly')) {
                    description.removeAttribute('readonly');
                    description.focus();
                    editButton.style.display = 'none';
                    submitButton.style.display = 'inline';
                }
            })

            submitButton.addEventListener('click', () => {
                if (!description.hasAttribute('readonly')) {
                    description.setAttribute('readonly', true);
                    editButton.style.display = 'inline';
                    submitButton.style.display = 'none';
                    }
            })

            // AJAX request to handle description update in db
            const descriptionForm = document.querySelector('form#description-form');
            descriptionForm.addEventListener('submit', (event) => {
                event.preventDefault(); //prevent form submission

                // retrieve form data
                const descriptionData = new FormData(descriptionForm);
                descriptionData.append("projectId", "<?php echo $projectId; ?>");

                // create new xml http request
                const xhr = new XMLHttpRequest();

                // set up request
                xhr.open('POST', 'projectDescriptionUpdate.php', true)

                // define the callback function
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        const updateDateTime = new Date(response.descriptionUpdate); // Convert timestamp to Date object
                        const formattedDate = updateDateTime.toLocaleDateString('en-GB', { // en-GB, is english for Great Britain (UK) (e.g. 01 January 2000)
                                                day: '2-digit',
                                                month: 'long',
                                                year: 'numeric'
                                                });
                        const formattedTime = updateDateTime.toLocaleTimeString('en-GB', {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                second: '2-digit',
                                                hour12: true  // Use 12-hour time
                                                }).toUpperCase();
                        lastUpdated.textContent = `Last updated on ${formattedDate}, at ${formattedTime}`;
                    } else {
                        // handle error (e.g., display an error message)
                        console.log('Form submission failed:', xhr.statusText);
                    }
                };
                
                // send the form data
                xhr.send(descriptionData);
            })
        })

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const projectDdlDiv = document.querySelector('div#project-deadline');
            const projectDeadline = "<?php echo $projectDeadline; ?>";
            if (projectDeadline != '' && projectDeadline != null) {
                const ddlDate = new Date(projectDeadline);
                const setDdlDate = ddlDate.toLocaleDateString('en-GB', {
                                                                day: '2-digit',
                                                                month: 'long',
                                                                year: 'numeric'
                                                                });
                const setDdlTime = ddlDate.toLocaleTimeString('en-GB', {
                                                                hour: '2-digit',
                                                                minute: '2-digit',
                                                                second: '2-digit',
                                                                hour12: true  // Use 12-hour time
                                                                }).toUpperCase();
                const setDdl = setDdlDate + ' ' + setDdlTime;
                projectDdlDiv.textContent = setDdl;
            } else {
                projectDdlDiv.textContent = 'Undefined';
            }
            const pickDate = flatpickr(projectDdlDiv, {
                enableTime: true,
                time_24hr: true,
                dateFormat: 'Y-m-d H:i', // YYYY-MM-DD HH:MM
                defaultDate: projectDeadline && projectDeadline !== "null"? new Date(projectDeadline): null,
                clickOpens: true,
                minDate: 'today',
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length === 1) {
                        // isoDate = "2025-03-12T15:30:45.123Z"
                        const formattedDate = selectedDates[0].toISOString().split('T')[0]; // YYYY-MM-DD
                        fetch('/Goalify/Pages/user/workspaces/projects/description/projectDescriptionUpdate.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: "project-deadline=" + encodeURIComponent(dateStr)
                        })
                        .then(response => response.json())
                        .then(response => {
                            const successMsg = response.success;
                            if (successMsg) {
                                console.log(successMsg);
                                const newDeadline = new Date(response.projectDdl);
                                const newDeadlineDate = newDeadline.toLocaleDateString('en-GB', {
                                                                                    day: '2-digit',
                                                                                    month: 'long',
                                                                                    year: 'numeric'
                                                                                    })
                                const newDeadlineTime = newDeadline.toLocaleTimeString('en-GB', {
                                                                hour: '2-digit',
                                                                minute: '2-digit',
                                                                second: '2-digit',
                                                                hour12: true  // Use 12-hour time
                                                                }).toUpperCase();
                                projectDdlDiv.textContent = newDeadlineDate + ' ' + newDeadlineTime;
                                alert(successMsg);
                            } else {
                                const failMsg = response.fail;
                                console.log(failMsg);
                                alert(failMsg);
                            }
                        })
                        .catch(error => console.error("Error: ", error));
                    }
                }
            });
            projectDdlDiv.addEventListener("click", () => {
                pickDate.open();
            });
        })
        
    </script>
</body>
</html>