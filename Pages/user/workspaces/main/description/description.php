<!--
Author: Phang Shea Wen
Date: 2025/01/30
Filename: descriptionUpdate.php
Description: Page showing available workspace details and description
            - contains in-line script handling workspace name update, copy workspace ID, and workspace description update
            - workspace name and description includes sending AJAX request via XML Http request
-->
<?php
require '../../../../includes/db.php';
require '../../../../includes/userFunction.php';
require '../../workspace.php';
require_once '../../../../includes/session.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');
if (isset($_SESSION['workspace_id'])) {
    $workspaceId = $_SESSION['workspace_id'];
    $userId = $_SESSION['user_id'];
    $members = null;
    $descriptionFromDb = null;
    function getDescription($workspaceId) {
        global $pdo, $workspaceId, $descriptionFromDb;
        $stmt = $pdo -> prepare('SELECT `workspace`.`workspaceDescription`, `workspace`.`descriptionUpdate`
                                FROM `workspace`
                                WHERE `workspace`.`workspaceId` = :workspaceId;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        $descriptionFromDb = $stmt -> fetchAll();
    }
    $ownerId = getOwner($pdo, $workspaceId)[0]['userId'];
    $owner = ($ownerId == $userId);
}
// getDescription($workspaceId);
$workspaceDescription = getWorkspaceDetails($pdo, $workspaceId, 'workspaceDescription');
$workspaceDescriptionUpdate = getWorkspaceDetails($pdo, $workspaceId, 'descriptionUpdate');
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isOwner = <?php echo $owner ? 'true' : 'false'; ?>;
        const editWorkspaceNameButton = document.querySelector('a#edit-workspace-name');
        const editDescriptionButton = document.querySelector('button#edit-description-button');
        if (!isOwner) {
            editDescriptionButton.style.display = 'none';
            editWorkspaceNameButton.style.display = 'none';
        } else {
            editDescriptionButton.style.display = 'flex';
            editWorkspaceNameButton.style.display = 'inline';
        }
    })
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../../global/global.css">
    <link rel="stylesheet" href="../../workspaces.css">
    <link rel="stylesheet" href="description.css">
    <title>Workspaces</title>
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
    <?php include '../../../../includes/navigation.php' ?>
    <?php include '../../../notification/notification.php' ?>
    <nav class="side-nav">
        <span class="material-symbols-rounded expand">chevron_right</span>
        <ul>
            <div id="toggle-side-nav"><h2 id="side-nav-workspace-name"><?php echo getWorkspaceDetails($pdo, $workspaceId, 'workspaceName') ?></h2><span class="material-symbols-rounded close">chevron_left</span></div>
            <hr>
            <a href="../projects/projects.php"><li id="workspace-project">Projects</li></a>
            <hr>
            <a href="../members/members.php"><li id="workspace-member">Members</li></a>
            <hr>
            <a href="../description/"><li id="workspace-description">Description</li></a>
        </ul>
    </nav>
    <div class="content">
        <h1>Workspace's Details</h1>
        <div class="workspace-details">
            <label>Name</label>
            <form id="workspace-name-form">
                <input name="workspace-name" id="workspace-name" type='text' readonly autocomplete="off">
                <a id="edit-workspace-name">Edit</a>
                <button type="submit" id="submit-workspace-name-button" style="display: none;">Save</button>
            </form>

            <label>Workspace ID</label>
            <div id="workspace-id">
                <input id="workspace-id-text" readonly value="<?php echo $workspaceId ?>">
                <span class="material-symbols-rounded expand" id="workspace-id-copy" onclick="copyWorkspaceId()">content_copy</span>
            </div>

            <label>Owner</label>
            <?php
            $ownerName = getUserName($pdo, getWorkspaceDetails($pdo, $workspaceId, 'ownerId'));
            ?>
            <div><?php echo $ownerName?></div>

            <label>Created at</label>
            <?php
            $datetime = new DateTime(getWorkspaceDetails($pdo, $workspaceId, 'createdDateTime'));
            $workspaceCreatedDate = $datetime->format('d F Y');
            ?>
            <div><?php echo $workspaceCreatedDate?></div>
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
        /*
        Author: Phang Shea Wen
        Date: 2025/02/01
        Filename: inline JS script in 'description.php'
        Description: To handle workspace name update
        */
        document.addEventListener('DOMContentLoaded', () => {
            const workspaceName = document.querySelector('form#workspace-name-form input#workspace-name');

            workspaceName.value = '<?php echo getWorkspaceDetails($pdo, $workspaceId, 'workspaceName'); ?>';

            const editNameButton = document.querySelector('a#edit-workspace-name');
            const saveNameButton = document.querySelector('button#submit-workspace-name-button');
            editNameButton.addEventListener('click', () => {
                if (workspaceName.hasAttribute('readonly')) {
                    workspaceName.removeAttribute('readonly');
                    workspaceName.focus();
                    editNameButton.style.display = 'none';
                    saveNameButton.style.display = 'inline';
                }
            })

            saveNameButton.addEventListener('click', () => {
                if (!workspaceName.hasAttribute('readonly')) {
                    workspaceName.setAttribute('readonly', true);
                    editNameButton.style.display = 'inline';
                    saveNameButton.style.display = 'none';
                    }
            })

            const workspaceNameForm = document.querySelector('.content .workspace-details form#workspace-name-form');
            workspaceNameForm.addEventListener('submit', (event) => {
                event.preventDefault(); //prevent default form submission

                // retrieve form data
                const workspaceNameData = new FormData(workspaceNameForm);
                workspaceNameData.append("workspaceId", "<?php echo $workspaceId; ?>");

                // create new xml http request
                const xhr = new XMLHttpRequest();

                // set up request
                xhr.open('POST', 'descriptionUpdate.php', true)

                // define the callback function
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        workspaceName.value = response.workspaceName;
                        console.log(response.workspaceName);

                        const sideNavWorkspaceName = document.querySelector('.side-nav ul div#toggle-side-nav h2#side-nav-workspace-name');
                        sideNavWorkspaceName.textContent = response.workspaceName;
                    } else {
                        // display an error message
                        console.log('Form submission failed:', xhr.statusText);
                    }
                };
                
                // send the form data
                xhr.send(workspaceNameData);
            })
        })
    </script>

    <script>
        // Description: function to copy Workspace ID
        function copyWorkspaceId() {
            var text = document.getElementById('workspace-id-text');
            text.select(); // select all text in input field
            text.setSelectionRange(0, 99999) // for mobile devices, ensure text is fully texted on mobile devices, on some mobile browser, select() doesn't work

            navigator.clipboard.writeText(text.value); // copy selected text to clipboard

            alert("Workspace ID copied");
        }
    </script>

    <script>
        /*
        Author: Phang Shea Wen
        Date: 2025/01/31
        Filename: inline JS script in 'description.php'
        Description: To handle workspace description updates
        */
        document.addEventListener('DOMContentLoaded', () => {
            const description = document.querySelector('textarea#description');

            // display description last update datetime
            const storedDescription = <?php echo $workspaceDescription !== null ? json_encode($workspaceDescription) : 'null'; ?>;
            <?php
                $descriptionUpdateDate = null;
                $descriptionUpdateTime = null;
                if ($workspaceDescriptionUpdate) {
                    $datetime = new DateTime($workspaceDescriptionUpdate);
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
                descriptionData.append("workspaceId", "<?php echo $workspaceId; ?>");

                // create new xml http request
                const xhr = new XMLHttpRequest();

                // set up request
                xhr.open('POST', 'descriptionUpdate.php', true)

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
                                                hour12: true  // Use 24-hour time
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
    </script>
</body>
</html>