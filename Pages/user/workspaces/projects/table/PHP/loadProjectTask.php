<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';

if (isset($_SESSION['project_id'])) {
    $userId = $_SESSION['user_id'];
    $workspaceId = $_SESSION['workspace_id'];
    $selectedProjectId = $_SESSION['project_id'];
}

$workspaceName = getWorkspaceDetails($pdo, $workspaceId, 'workspaceName');
$projectName = getProjectDetails($pdo, $selectedProjectId, 'projectName');
$projectDeadline = getProjectDetails($pdo, $selectedProjectId, 'projectDeadline');
$owner = getOwner($pdo, $workspaceId);
$allMembers = getMembers($pdo, $workspaceId);

// array_udiff -> does not reset indexing (array keys), array_values -> help resets the indexing after removing certain elements from the array
$members = array_values(array_udiff($allMembers, $owner, function ($a, $b) {
    return $a['userId'] <=> $b['userId']; // compare by 'userId'
}));

$ownerMember = [...$owner, ...$members];

// select all project tasks
$allProjectTasks = getProjectTasks($pdo, $selectedProjectId);
$i = 0;
foreach ($allProjectTasks as $projectTask) {
    // select all project sub tasks
    // append new key 'sub-tasks', with array of sub-tasks as its value
    $allProjectSubTasks = getProjectSubTasks($pdo, $projectTask['projectTaskId']);
    $j = 0;
    foreach ($allProjectSubTasks as $projectSubTask) {
        if ($projectSubTask['assignedMemberId'] !== null) {
            $assignedMemberName = getUserName($pdo, $projectSubTask['assignedMemberId']);
            $allProjectSubTasks[$j]['assignedMemberName'] = $assignedMemberName;
        } else {
            $allProjectSubTasks[$j]['assignedMemberName'] = null;
        }
        $j++;
    }
    $allProjectTasks[$i]['sub-tasks']= $allProjectSubTasks;
    $i++;
}

echo json_encode(['selectedProjectId' => $selectedProjectId, 'projectName' => $projectName, 'allProjectTasks' => $allProjectTasks, 'ownerMembers' => $ownerMember, 'projectDeadline' => $projectDeadline])
?>