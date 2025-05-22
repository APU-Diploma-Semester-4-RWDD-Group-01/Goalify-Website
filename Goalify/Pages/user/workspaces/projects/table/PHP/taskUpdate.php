<?php
// session_start();
require '../../../../../includes/db.php';
require '../../../workspace.php';
require '../../../../../includes/logging.php';

ob_clean(); // Clean the output buffer
header('Content-Type: application/json'); // response in json format
date_default_timezone_set('Asia/Kuala_Lumpur'); // Set your desired time zone
// date_default_timezone_set($SESSION['time-zone']); // Set PHP time zone
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set Time Zone
    if (isset($_POST['time-zone'])) {
        $timezone = $_POST['time-zone'];
        // $SESSION['time-zone'] = $timezone;
        date_default_timezone_set($timezone);
    } else {
        error_log("No time zone is received");
    }

    // Add New Project Task
    if (isset($_POST['new-project-task-name']) && isset($_POST['project-id'])) {
        addProjectTask();
    }
    // Modify Project Task Name
    if (isset($_POST['project-task-id']) && isset($_POST['project-task-name'])) {
        updateProjectTaskName();
    }
    // Delete Project Task
    if (isset($_POST['delete-project-task-id'])) {
        removeProjectTask();
    }

    // Add New Project Sub-Task
    if (isset($_POST['project-task-id']) && isset($_POST['sub-task-name'])) {
        addProjectSubTask();
    }
    // Modify Project Sub-Task Name
    if (isset($_POST['project-sub-task-name']) && isset($_POST['project-sub-task-id'])) {
        error_log($_POST['project-sub-task-name']);
        updateProjectSubTaskName();
    }
    // Delete Project Sub-Task
    if (isset($_POST['delete-project-sub-task-id'])) {
        removeProjectSubTask();
    }

    // Update Project Sub Task Assigned Member
    if (isset($_POST['project-sub-task-id']) && isset($_POST['assigned-member'])) {
        updateProjectSubTaskMember();
    }

    // Update Project Sub Task Priority
    if (isset($_POST['project-sub-task-id']) && isset($_POST['priority'])) {
        updateProjectSubTaskPriority();
    }

    // Update Project Sub Task Estimation
    if (isset($_POST['project-sub-task-id']) && isset($_POST['sub-task-estimation'])) {
        updateProjectSubTaskEstimate();
    }

    // Update Project Sub Task Assigned Date
    if (isset($_POST['project-sub-task-id']) && isset($_POST['assigned-date'])) {
        updateProjectSubTaskAssignedDate();
    }

    // Update Project Sub Task Due Date
    if (isset($_POST['project-sub-task-id']) && isset($_POST['due-date'])) {
        updateProjectSubTaskDueDate();
    }

    // Update Project Sub Task Status
    if (isset($_POST['project-sub-task-id']) && isset($_POST['status'])) {
        updateProjectSubTaskStatus();
    }

    // Get Project Deadline
    if (isset($_POST['projectDetails'])) {
        if ($_POST['projectDetails'] == 'projectDeadline') {
            $projectId = $_SESSION['project_id'];
            $deadline = getProjectDetails($pdo, $projectId, 'projectDeadline');
            if ($deadline) {
                echo json_encode(['success' => 'Project deadline is fetched', 'projectDeadline' => $deadline]);
            } else {
                echo json_encode(['success' => 'Failed to fetch project deadline']);
            }
        }
    }
}

function addProjectTask() {
    global $pdo;
    // $projectId = $_POST['project-id'];
    $projectId = $_SESSION['project_id'];
    $projectTaskId = generateId('project-task');
    $projectTaskName = $_POST['new-project-task-name'];
    if (insertNewProjectTask($pdo, $projectId, $projectTaskId, $projectTaskName)) {
        $projectTaskAddedDateTime = getProjectTaskDetails($pdo, $projectTaskId, 'createdDateTime');
        insertActivityLog($pdo, $_SESSION['user_id'], 'A020', "New Project Task '{$projectTaskName}' is added");
        echo json_encode(['projectTaskName' => $projectTaskName, 'projectTaskAddedDateTime' => $projectTaskAddedDateTime, 'success' => "New Project Task '{$projectTaskName}' is added"]);
    } else {
        echo json_encode(['fail' => "New Project Task '{$projectTaskName}' is not added"]);
    }
}

function removeProjectTask() {
    global $pdo;
    $deletedProjectTaskId =  $_POST['delete-project-task-id'];
    // error_log($deletedProjectTaskId); // error testing
    deleteProjectTask($pdo, $deletedProjectTaskId);
    insertActivityLog($pdo, $_SESSION['user_id'], 'A027', "Project Task '$deletedProjectTaskId' is deleted");
    echo json_encode(['success' => "Project Task '$deletedProjectTaskId' is deleted"]);
}

function addProjectSubTask() {
    global $pdo;
    $projectTaskId = $_POST['project-task-id'];
    $projectSubTaskId = generateId('project-sub-task');
    $projectSubTaskName = $_POST['sub-task-name'];

    if (insertNewProjectSubTask($pdo, $projectTaskId, $projectSubTaskId, $projectSubTaskName)) {
        insertActivityLog($pdo, $_SESSION['user_id'], 'A044', "New Project Sub Task '{$projectSubTaskName}' is added!");
        if (isset($_POST['assigned-members']) && $_POST['assigned-members'] != '') {
            $assignedMember = $_POST['assigned-members'];
            updateProjectSubTask($pdo, 'assignedMemberId', $assignedMember, $projectSubTaskId);
            insertActivityLog($pdo, $_SESSION['user_id'], 'A041', "Project Sub-Task '$projectSubTaskName' is assigned to Member '$assignedMember'!");
        }
        if (isset($_POST['priority']) && $_POST['priority'] != '') {
            $priority = $_POST['priority'];
            updateProjectSubTask($pdo, 'projectSubTaskPriority', $priority, $projectSubTaskId);
            insertActivityLog($pdo, $_SESSION['user_id'], 'A030', "Project Sub-Task '$projectSubTaskName' is set to priority '$priority'!");
        }
        if (isset($_POST['add-estimation']) && $_POST['add-estimation'] != '') {
            $estimate = $_POST['add-estimation'];
            updateProjectSubTask($pdo, 'projectSubTaskEstimate', $estimate, $projectSubTaskId);
            insertActivityLog($pdo, $_SESSION['user_id'], 'A042', "Project Sub-Task '$projectSubTaskName' estimated completion time to $estimate hour!");
        }
        if (isset($_POST['assigned-date']) && $_POST['assigned-date'] != '') {
            $assignedDate = $_POST['assigned-date'];
            updateProjectSubTask($pdo, 'projectSubTaskAssignedDate', $assignedDate, $projectSubTaskId);
            insertActivityLog($pdo, $_SESSION['user_id'], 'A032', "Project Sub-Task '$projectSubTaskName' assigned date is set to $assignedDate!");
        }
        if (isset($_POST['due-date']) && $_POST['due-date'] != '') {
            $dueDate = $_POST['due-date'];
            updateProjectSubTask($pdo, 'projectSubTaskDueDate', $dueDate, $projectSubTaskId);
            insertActivityLog($pdo, $_SESSION['user_id'], 'A033', "Project Sub-Task '$projectSubTaskName' due date is set to $dueDate!");
        }
        if (isset($_POST['status']) && $_POST['status'] != '') {
            $status = $_POST['status'];
            updateProjectSubTask($pdo, 'projectSubTaskStatus', $status, $projectSubTaskId);
            insertActivityLog($pdo, $_SESSION['user_id'], 'A030', "Project Sub-Task '$projectSubTaskName' is set to status '$status'!");
            if ($status == 'in progress') {
                $startDateTime = date('Y-m-d H:i:s');
                updateProjectSubTask($pdo, 'projectSubTaskStart', $startDateTime, $projectSubTaskId);
            } elseif ($status == 'completed') {
                $startDateTime = date('Y-m-d H:i:s');
                $endDateTime = date('Y-m-d H:i:s');
                updateProjectSubTask($pdo, 'projectSubTaskStart', $startDateTime, $projectSubTaskId);
                updateProjectSubTask($pdo, 'projectSubTaskEnd', $endDateTime, $projectSubTaskId);
            }
        }
        echo json_encode(['success' => "New Project Sub Task '{$projectSubTaskName}' is added!"]);
    } else {
        echo json_encode(['fail' => "New Project Sub Task '{$projectSubTaskName}' is not added"]);
    }
    // $assignedMember = (isset($_POST['assigned-members'])) ? $_POST['assigned-members'] : null;
}

function updateProjectTaskName() {
    global $pdo;
    $projectTaskId = $_POST['project-task-id'];
    $updatedProjectTaskName = $_POST['project-task-name'];
    updateProjectTask($pdo, 'projectTaskName', $updatedProjectTaskName, $projectTaskId);

    $getProjectTaskName = getProjectTaskDetails($pdo, $projectTaskId, 'projectTaskName');

    if ($getProjectTaskName) {
        insertActivityLog($pdo, $_SESSION['user_id'], 'A043', "Project Task {$projectTaskId} is renamed");
        echo json_encode(['projectTaskName' => $getProjectTaskName]);
    } else {
        echo json_encode(['error' => 'Failed to retrieve project task name']);
    }
}

function updateProjectSubTaskName() {
    global $pdo;
    $updatedProjectSubTaskName = $_POST['project-sub-task-name'];
    $projectSubTaskId = $_POST['project-sub-task-id'];
    updateProjectSubTask($pdo, 'projectSubTaskName', $updatedProjectSubTaskName, $projectSubTaskId);

    $getProjectSubTaskName = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskName');

    if ($getProjectSubTaskName) {
        insertActivityLog($pdo, $_SESSION['user_id'], 'A029', "Project Sub Task {$projectSubTaskId} is renamed");
        echo json_encode(['projectSubTaskName' => $getProjectSubTaskName, 'success' => "Project Sub Task '{$projectSubTaskId}' is renamed"]);
    } else {
        echo json_encode(['error' => 'Failed to rename project sub task name']);
    }
}
function removeProjectSubTask() {
    global $pdo;
    $deletedProjectSubTaskId = $_POST['delete-project-sub-task-id'];
    deleteProjectSubTask($pdo, $deletedProjectSubTaskId);
    insertActivityLog($pdo, $_SESSION['user_id'], 'A028', "Project Task '$deletedProjectSubTaskId' is deleted");
    echo json_encode(['success' => "Project Task '$deletedProjectSubTaskId' is deleted"]);
}

function updateProjectSubTaskMember() {
    global $pdo;
    $projectSubTaskId = $_POST['project-sub-task-id'];
    $assignedMemberId = $_POST['assigned-member'];
    if (updateProjectSubTask($pdo, 'assignedMemberId', $assignedMemberId, $projectSubTaskId)) {
        $projectSubTaskName = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskName');
        insertActivityLog($pdo, $_SESSION['user_id'], 'A041', "Project Sub-Task '$projectSubTaskName' is assigned to Member '$assignedMemberId'!");
        echo json_encode(['success' => "Project Sub Task '$projectSubTaskName' is assigned with member '$assignedMemberId'!"]);
    } else {
        echo json_encode(['fail' => "Project Sub Task '$projectSubTaskId' failed to be assigned with member '$assignedMemberId'"]);
    }
}

function updateProjectSubTaskPriority() {
    global $pdo;
    $projectSubTaskId = $_POST['project-sub-task-id'];
    $priority = $_POST['priority'];
    if (updateProjectSubTask($pdo, 'projectSubTaskPriority', $priority, $projectSubTaskId)) {
        $projectSubTaskName = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskName');
        insertActivityLog($pdo, $_SESSION['user_id'], 'A030', "Project Sub-Task '$projectSubTaskName' is set to priority '$priority'!");
        echo json_encode(['success' => "Project Sub-Task '$projectSubTaskName' is set to priority '$priority'!"]);
    } else {
        echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." failed to be assigned with priority '{$priority}'"]);
    }
}

function updateProjectSubTaskEstimate() {
    global $pdo;
    // $estimate = number_format((float) $_POST['sub-task-estimation'], 2);
    $estimate = (int) $_POST['sub-task-estimation'];
    $projectSubTaskId = $_POST['project-sub-task-id'];
    if (updateProjectSubTask($pdo, 'projectSubTaskEstimate', $estimate, $projectSubTaskId)) {
        $projectSubTaskName = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskName');
        insertActivityLog($pdo, $_SESSION['user_id'], 'A042', "Project Sub-Task '$projectSubTaskName' estimated completion time to $estimate hour!");
        echo json_encode(['estimate' => $estimate, 'success' => 'Project Sub Task '.$projectSubTaskId." is estimated to complete in '{$estimate}' hours"]);
    } else {
        echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." failed to be set an estimated completion time"]);
    }
}

function updateProjectSubTaskStatus() {
    global $pdo;
    $projectSubTaskId = $_POST['project-sub-task-id'];
    $status = $_POST['status'];
    $projectSubTaskName = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskName');
    $storedStatus = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskStatus');
    if ($status == 'in progress') {
        switch ($storedStatus) {
            case 'in progress':
                insertActivityLog($pdo, $_SESSION['user_id'], 'A030', "Project Sub-Task '$projectSubTaskName' was already previously set to be '$status'!");
                echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." was already previously set to be '{$status}'"]);
                break;
            case 'completed':
                $datetime = new DateTime();
                $currentDateTime = $datetime->format('Y-m-d h:i:s'); // 2025-01-1 00:00:00 (no 'A' -> 24 hours)
                $updateStatus = updateProjectSubTask($pdo, 'projectSubTaskStatus', $status, $projectSubTaskId);
                $updateStartTime = updateProjectSubTask($pdo, 'projectSubTaskStart', $currentDateTime, $projectSubTaskId);
                $updateEndTime = updateProjectSubTask($pdo, 'projectSubTaskEnd', null, $projectSubTaskId);
                if ($updateStatus && $updateStartTime && $updateEndTime) {
                    insertActivityLog($pdo, $_SESSION['user_id'], 'A030', 'Project Sub Task '.$projectSubTaskId." that was previously 'completed' is now set to be '{$status}'");
                    echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." that was previously 'completed' is now set to be '{$status}'"]);
                } else {
                    echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." that was previously 'completed' failed to be set to '{$status}'"]);
                }
                break;
            case null:
                $datetime = new DateTime();
                $currentDateTime = $datetime->format('Y-m-d h:i:s'); // 2025-01-1 00:00:00 (no 'A' -> 24 hours)
                $updateStatus = updateProjectSubTask($pdo, 'projectSubTaskStatus', $status, $projectSubTaskId);
                $updateStartTime = updateProjectSubTask($pdo, 'projectSubTaskStart', $currentDateTime, $projectSubTaskId);
                if ($updateStatus && $updateStartTime) {
                    insertActivityLog($pdo, $_SESSION['user_id'], 'A030', "Project Sub-Task '$projectSubTaskName' is set to status '$status'!");
                    echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." is set to be '{$status}'"]);
                } else {
                    echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." failed to be set to '{$status}'"]);
                }
                break;
        }
    } elseif ($status == 'completed') {
        switch ($storedStatus) {
            case 'in progress':
                $datetime = new DateTime();
                $currentDateTime = $datetime->format('Y-m-d h:i:s');
                $updateStatus = updateProjectSubTask($pdo, 'projectSubTaskStatus', $status, $projectSubTaskId);
                $updateEndTime = updateProjectSubTask($pdo, 'projectSubTaskEnd', $currentDateTime, $projectSubTaskId);
                if ($updateStatus && $updateEndTime) {
                    insertActivityLog($pdo, $_SESSION['user_id'], 'A030', "Project Sub-Task '$projectSubTaskName' is set to status '$status'!");
                    echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." is set to be '{$status}'"]);
                } else {
                    echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." failed to be set to '{$status}'"]);
                }
                break;
            case 'completed':
                insertActivityLog($pdo, $_SESSION['user_id'], 'A030', "Project Sub-Task '$projectSubTaskName' was already previously set to be '$status'!");
                echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." was already previously set to be '{$status}'"]);
                break;
            case null:
                $datetime = new DateTime();
                $currentDateTime = $datetime->format('Y-m-d h:i:s');
                $updateStatus = updateProjectSubTask($pdo, 'projectSubTaskStatus', $status, $projectSubTaskId);
                $updateStartTime = updateProjectSubTask($pdo, 'projectSubTaskStart', $currentDateTime, $projectSubTaskId);
                $updateEndTime = updateProjectSubTask($pdo, 'projectSubTaskEnd', $currentDateTime, $projectSubTaskId);
                if ($updateStatus && $updateEndTime) {
                    insertActivityLog($pdo, $_SESSION['user_id'], 'A030', 'Project Sub Task '.$projectSubTaskId." that has not started is now set to be '{$status}'");
                    echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." that has not started is now set to be '{$status}'"]);
                } else {
                    echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." that has not started failed to be set to '{$status}'"]);
                }
                break;
        }
    }
}

function updateProjectSubTaskAssignedDate() {
    global $pdo;
    $projectSubTaskId = $_POST['project-sub-task-id'];
    $assignedDate = $_POST['assigned-date'];
    if (updateProjectSubTask($pdo, 'projectSubTaskAssignedDate', $assignedDate, $projectSubTaskId)) {
        $projectSubTaskName = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskName');
        insertActivityLog($pdo, $_SESSION['user_id'], 'A032', "Project Sub-Task '$projectSubTaskName' assigned date is set to $assignedDate!");
        echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." is assigned at '{$assignedDate}'"]);
    } else {
        echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." failed to be assigned at '{$assignedDate}'"]);
    }
}

function updateProjectSubTaskDueDate() {
    global $pdo;
    $projectSubTaskId = $_POST['project-sub-task-id'];
    $dueDate = $_POST['due-date'];
    if (updateProjectSubTask($pdo, 'projectSubTaskDueDate', $dueDate, $projectSubTaskId)) {
        $projectSubTaskName = getProjectSubTaskDetails($pdo, $projectSubTaskId, 'projectSubTaskName');
        insertActivityLog($pdo, $_SESSION['user_id'], 'A033', "Project Sub-Task '$projectSubTaskName' due date is set to $dueDate!");
        echo json_encode(['success' => 'Project Sub Task '.$projectSubTaskId." is due at '{$dueDate}'"]);
    } else {
        echo json_encode(['fail' => 'Project Sub Task '.$projectSubTaskId." failed to be due at '{$dueDate}'"]);
    }
}
exit();
?>