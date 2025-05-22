<!--
Author: Phang Shea Wen
Date: 2025/01/28
Filename: members.php
Description: Page showing workspace members
-->
<?php
require '../../../../includes/db.php';
require '../../workspace.php';
require('../../../../includes/session.php');
require_once '../../../../includes/userFunction.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');
if (isset($_SESSION['workspace_id'])) {
    $workspaceId = $_SESSION['workspace_id'];
    $owner = getOwner($pdo, $workspaceId);
    $allMembers = getMembers($pdo, $workspaceId);

    $owner[0]['profile_img'] = getUserProfileImg($pdo, $owner[0]['userId']);

    $i = 0;
    foreach($allMembers as $member) {
        $allMembers[$i]['profile_img'] = getUserProfileImg($pdo, $member['userId']);
        $i++;
    }

    // array_udiff -> does not reset indexing (array keys), array_values -> help resets the indexing after removing certain elements from the array
    $members = array_values(array_udiff($allMembers, $owner, function ($a, $b) {
        return $a['userId'] <=> $b['userId']; // compare by 'userId'
    }));
}
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const owner = <?php echo json_encode($owner); ?>;
        const members = <?php echo json_encode($members); ?>;

        const numMembers = document.querySelector('#num-members');
        const listMembers = document.querySelector('.content .members-list')
        if (Array.isArray(owner)) {
            ownerDiv = document.createElement('div');
            let ownerId = owner[0]['userId'];
            ownerDiv.class = 'owner';
            const img = owner[0]['profile_img'];
            if (img == null) {
                imgPath = '/Goalify/Img/default_profile.png'
            } else {
                imgPath = `/Goalify/Pages/user/profile/${owner[0]['profile_img']}`;
            }
            ownerDivContent = `
            <div class="profile-pic">
                <img src="${imgPath}" alt="user-profile">
                <p>${owner[0]['name']}</p>
            </div>
            <p id="owner-role">Owner</p>
            `;
            ownerDiv.innerHTML = ownerDivContent;
            listMembers.appendChild(ownerDiv);
            listMembers.appendChild(document.createElement('hr'));
        }

        // need to check online or not
        if (Array.isArray(members)) {
            for (const member of members) {
                memberDiv = document.createElement('div');
                let memberId = member['userId'];
                memberDiv.class = 'member';
                const img = member['profile_img'];
                console.log(img);
                if (img == null) {
                    imgPath = '/Goalify/Img/default_profile.png'
                    console.log(imgPath);
                } else {
                    imgPath = `/Goalify/Pages/user/profile/${member['profile_img']}`;
                    console.log(imgPath)
                }
                memberDivContent = `
                <div class="profile-pic">
                    <img src="${imgPath}" alt="user-profile">
                    <p>${member['name']}</p>
                </div>
                `;
                memberDiv.innerHTML = memberDivContent;
                listMembers.appendChild(memberDiv);
                listMembers.appendChild(document.createElement('hr'));
            }
            numMembers.textContent = `( ${members.length + owner.length} )`;
        } else {
            console.log('no members?');
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
    <link rel="stylesheet" href="members.css">
    <title>Workspaces</title>
    <script src="../../../global/JS/theme.js" defer></script>
    <script src="../../../global/JS/sideNav.js" defer></script>
    <script src="../../../global/JS/global.js" defer></script>
    <script src="../../../global/JS/workspace.js" defer></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
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
            <a href="../description/description.php"><li id="workspace-description">Description</li></a>
        </ul>
    </nav>
    <div class="content">
        <div class="members-section">
            <div class="members"><h1>Members</h1><p id="num-members"></p></div>
            <div id="add-members-button"><span class="material-symbols-rounded expand">add</span><p>New Member</p></div>
        </div>
        <div class="members-list">
            <hr>
        </div>
    </div>

    <script>
        /*
        Author: Phang Shea Wen
        Date: 2025/01/29
        Filename: members.php in-line script
        Description: To handle invite new member pop-up window
        */
        document.addEventListener('DOMContentLoaded', () => {
            const addMemberButton = document.querySelector('.content .members-section #add-members-button');
            const body = document.body;

            addMemberButton.addEventListener('click', () => {
                window.createOverlay(
                    'add-member-form',
                    `
                    <label for="add-member-id">Invite Member</label>
                    <input type="text" name="add-member-id" id="add-member-id" autofocus placeholder="#USR12345" autocomplete="off">
                    <div class="button">
                        <button type="button" id="close-add-member-form">Cancel</button>
                        <button type="submit" id="confirm-add-member-form">Invite</button>
                    </div>
                    `,
                    ''
                );

                const addMemberForm = document.getElementById('add-member-form');
                addMemberForm.addEventListener('submit', (event) => {
                    event.preventDefault();
                    const memberInviteForm = new FormData(addMemberForm);
                    fetch('PHP/workspaceInvite.php', {
                        method: 'POST',
                        body: memberInviteForm
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.success) {
                            const successMsg = response.success;
                            console.log(successMsg);
                            alert(successMsg);
                            window.removeOverlay('add-member-form');
                        } else {
                            const failMsg = response.fail;
                            console.log(failMsg);
                            alert(failMsg);
                            window.removeOverlay('add-member-form');
                        }
                    })
                })

                const closeMemberForm = document.querySelector('#close-add-member-form');
                closeMemberForm.addEventListener('click', () => {
                    window.removeOverlay('add-member-form');
                })
            })
        })
    </script>
</body>
</html>