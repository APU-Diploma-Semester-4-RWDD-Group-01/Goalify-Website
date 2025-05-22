<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';


header('Content-Type: application/json');

if (isset($_SESSION['project_id'])) {
    $projectId = $_SESSION['project_id'];
    $allProjectTasks = getProjectTasks($pdo, $projectId);
    
    $allSubTasks = []; // Store all sub-tasks
    $taskColors = []; // Store colors for each task

    foreach ($allProjectTasks as $projectTask) {
        $taskId = $projectTask['projectTaskId'];

        // Generate or get a color for this task
        if (!isset($taskColors[$taskId])) {
            $taskColors[$taskId] = sprintf("#%06X", mt_rand(0, 0xFFFFFF)); // Random Hex Color
        }

        // Fetch sub-tasks for this task
        $subTasks = getProjectSubTasks($pdo, $taskId);
        foreach ($subTasks as $subTask) {
            if ($subTask['assignedMemberId'] !== null) {
                $subTask['assignedMemberName'] = getUserName($pdo, $subTask['assignedMemberId']);
            } else {
                $subTask['assignedMemberName'] = null;
            }

            // Assign the same color as the parent task
            $subTask['color'] = $taskColors[$taskId];

            $allSubTasks[] = $subTask; // Add sub-task to the list
        }
    }
    
    echo json_encode(['success' => 'All sub-tasks fetched', 'allSubTasks' => $allSubTasks]);
} else {
    echo json_encode(['fail' => 'Sub-tasks not fetched, project ID undefined']);
}
exit();
?>

