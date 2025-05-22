<?php
// Author: Phang Shea Wen
// Date: 2025/01/31
// Filename: workspace.php
// Description: Global functions for Workspace section

function generateId($type) {
    try {
        // in_array($type, ['workspace', 'project','project-task', 'project-sub-task']);
        if ($type == 'workspace') {
            $prefix = '#WSPC';
        } elseif ($type == 'project') {
            $prefix = '#PRJ';
        } elseif ($type == 'project-task') {
            $prefix = '#P-T';
        } elseif ($type == 'project-sub-task') {
            $prefix = '#P-ST';
        } else {
            throw new Exception('Invalid ID type :(');
        }
    } catch (Exception $e) {
        echo $e -> getMessage();
    }
    return $prefix.strtolower(substr(md5(uniqid()), 0, 5));
}

// Select Workspace Details
function getWorkspaceDetails($pdo, $workspaceId, $detail) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `workspace` WHERE workspaceId = :workspaceId;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        $result = $stmt -> fetch();

        if ($result) {
            if ($detail === 'workspaceName') {
                return $result['workspaceName'];
            } elseif ($detail === 'createdDateTime') {
                return $result['createdDateTime'];
            } elseif ($detail === 'ownerId') {
                return $result['ownerId'];
            } elseif ($detail === 'workspaceDescription') {
                return $result['workspaceDescription'];
            } elseif ($detail === 'descriptionUpdate') {
                return $result['descriptionUpdate'];
            } else {
                throw new Exception("Invalid workspace detail");
            }
        } else {
            throw new Exception("Result not found: ".$detail);
        }
    } catch (Exception $e) {
        error_log("Error getting workspace details: ".$e -> getMessage());
        return null;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting workspace details: ".$pdoE -> getMessage());
        return null;
    }
}

function getOwner($pdo, $workspaceId) {
    try {
        $stmt = $pdo -> prepare('SELECT `workspace`.`workspaceId`, `user`.`userId`, `user`.`name`
                            FROM `workspace`
                            LEFT JOIN `user`
                            ON `user`.`userId` = `workspace`.`ownerId`
                            WHERE `workspace`.`workspaceId` = :workspaceId;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        $owner = $stmt -> fetchAll();
        return $owner;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting workspace owner: ".$pdoE -> getMessage());
        return null;
    }
}

function getMembers($pdo, $workspaceId) {
    try {
        $stmt = $pdo -> prepare('SELECT `workspacemember`.`workspaceId`, `user`.`userId`, `user`.`name`
                                FROM `workspacemember`
                                LEFT JOIN `user`
                                ON `user`.`userId` = `workspacemember`.`memberId`
                                WHERE `workspacemember`.`workspaceId` = :workspaceId;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        $members = $stmt -> fetchAll();
        return $members;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all workspace members: ".$pdoE -> getMessage());
        return null;
    }
}

// Select all workspaces of a user
function getWorkspaces($pdo, $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT DISTINCT `workspace`.`workspaceId`, `workspace`.`workspaceName`
                                    FROM `workspace`
                                    LEFT JOIN `workspacemember`
                                    ON `workspacemember`.`workspaceId` = `workspace`.`workspaceId`
                                    WHERE `workspacemember`.`memberId` = :userId
                                    OR `workspace`.`ownerId` = :userId;');
        $stmt -> execute([':userId' => $userId]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all projects: ".$pdoE -> getMessage());
        return null;
    }
}

// Select all projects of a workspace
function getProjects($pdo, $workspaceId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `project` WHERE workspaceId = :workspaceId;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all projects: ".$pdoE -> getMessage());
        return null;
    }
}

function getPendingProjects($pdo, $workspaceId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `project` WHERE workspaceId = :workspaceId AND `projectStart` IS NULL AND `projectEnd` IS NULL;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting pending projects: ".$pdoE -> getMessage());
        return null;
    }
}

function getOngoingProjects($pdo, $workspaceId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `project` WHERE workspaceId = :workspaceId AND `projectStart` IS NOT NULL AND `projectEnd` IS NULL;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting ongoing projects: ".$pdoE -> getMessage());
        return null;
    }
}

function searchProjectByKeyWord($pdo, $keyword, $workspaceId) {
    try {
        $stmt = $pdo -> prepare("SELECT * FROM `project` WHERE workspaceId = :workspaceId AND `projectName` LIKE :keyword;");
        $wildcard = "%{$keyword}%";
        $stmt -> execute([':workspaceId' => $workspaceId, ':keyword' => $wildcard]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting searched projects: ".$pdoE -> getMessage());
        return null;
    }
}

// Select all project files
function getAllProjectFiles($pdo, $projectId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `projectfiles` WHERE `projectId` = :projectId;');
        $stmt -> execute([':projectId' => $projectId]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all project files: ".$pdoE -> getMessage());
        return null;
    }
}

// Select all project tasks of a project
function getProjectTasks($pdo, $projectId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `projecttask` WHERE projectId = :projectId;');
        $stmt -> execute([':projectId' => $projectId]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all project tasks: ".$pdoE -> getMessage());
        return null;
    }
}

// Select all project sub tasks of a project task
function getProjectSubTasks($pdo, $projectTaskId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `projectsubtask` WHERE projectTaskId = :projectTaskId;');
        $stmt -> execute([':projectTaskId' => $projectTaskId]);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all project sub tasks: ".$pdoE -> getMessage());
        return null;
    }
}

// Select project details
function getProjectDetails($pdo, $projectId, $detail) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `project` WHERE projectId = :projectId;');
        $stmt -> execute([':projectId' => $projectId]);
        $result = $stmt -> fetch();

        if ($detail == 'projectName') {
            return $result['projectName'];
        } elseif ($detail == 'projectCreatedDateTime') {
            return $result['projectCreatedDateTime'];
        } elseif ($detail == 'projectDeadline') {
            return $result['projectDeadline'];
        } elseif ($detail == 'projectStart') {
            return $result['projectStart'];
        } elseif ($detail == 'projectEnd') {
            return $result['projectEnd'];
        } elseif ($detail == 'projectStatus') {
            return $result['projectStatus'];
        } elseif ($detail == 'workspaceId') {
            return $result['workspaceId'];
        } elseif ($detail == 'projectDescriptionUpdate') {
            return $result['projectDescriptionUpdate'];
        } elseif ($detail == 'projectDescription') {
            return $result['projectDescription'];
        } else {
            throw new Exception("Invalid Project table column");
        }
    } catch (Exception $e) {
        error_log("Error getting project details: ".$e -> getMessage());
        return null;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting project details: ".$pdoE -> getMessage());
        return null;
    }
}

// Select project task details
function getProjectTaskDetails($pdo, $projectTaskId, $detail) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `projecttask` WHERE projectTaskId = :projectTaskId;');
        $stmt -> execute([':projectTaskId' => $projectTaskId]);
        $result = $stmt -> fetch();

        if ($result) {
            if ($detail == 'projectTaskName') {
                return $result['projectTaskName'];
            } elseif ($detail == 'createdDateTime') {
                return $result['createdDateTime'];
            } elseif ($detail == 'projectId') {
                return $result['projectId'];
            } else {
                throw new Exception("Invalid Project Task table column");
            }
        } else {
            throw new Exception("Result not found: ".$detail);
        }
    } catch (Exception $e) {
        error_log("Error getting project sub-task details: ".$e -> getMessage());
        return null;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting project sub-task details: ".$pdoE -> getMessage());
        return null;
    }
}

// Select project sub task details
function getProjectSubTaskDetails($pdo, $projectSubTaskId, $detail) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `projectsubtask` WHERE projectSubTaskId = :projectSubTaskId;');
        $stmt -> execute([':projectSubTaskId' => $projectSubTaskId]);
        $result = $stmt -> fetch();

        if ($result) {
            if ($detail == 'projectSubTaskName') {
                return $result['projectSubTaskName'];
            } elseif ($detail == 'createdDateTime') {
                return $result['createdDateTime'];
            } elseif ($detail == 'assignedMemberId') {
                return $result['assignedMemberId'];
            } elseif ($detail == 'projectSubTaskPriority') {
                return $result['projectSubTaskPriority'];
            } elseif ($detail == 'projectSubTaskEstimate') {
                return $result['projectSubTaskEstimate'];
            } elseif ($detail == 'projectSubTaskAssignedDate') {
                return $result['projectSubTaskAssignedDate'];
            } elseif ($detail == 'projectSubTaskDueDate') {
                return $result['projectSubTaskDueDate'];
            } elseif ($detail == 'projectSubTaskStart') {
                return $result['projectSubTaskStart'];
            } elseif ($detail == 'projectSubTaskEnd') {
                return $result['projectSubTaskEnd'];
            } elseif ($detail == 'projectSubTaskStatus') {
                return $result['projectSubTaskStatus'];
            } elseif ($detail == 'projectTaskId') {
                return $result['projectTaskId'];
            } else {
                throw new Exception("Invalid Project Sub Task table column");
            }
        } else {
            throw new Exception("Result not found -> ".$detail);
        }
    } catch (Exception $e) {
        error_log("Error getting project sub-task details: ".$e -> getMessage());
        return null;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting project sub-task details: ".$pdoE -> getMessage());
        return null;
    }
}

function getProjectFileDetails($pdo, $fileId, $detail) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `projectfiles` WHERE `fileId` = :fileId;');
        $stmt -> execute([':fileId' => $fileId]);
        $result = $stmt -> fetch();

        if ($result) {
            if ($detail == 'fileName') {
                return $result['fileName'];
            } elseif ($detail == 'fileType') {
                return $result['fileType'];
            } elseif ($detail == 'fileSize') {
                return $result['fileSize'];
            } elseif ($detail == 'fileData') {
                return $result['fileData'];
            } elseif ($detail == 'projectId') {
                return $result['projectId'];
            } elseif ($detail == 'uploadDateTime') {
                return $result['uploadDateTime'];
            } elseif ($detail == 'userId') {
                return $result['userId'];
            } else {
                throw new Exception("Invalid Project File table column");
            }
        } else {
            throw new Exception("Result not found: ".$detail);
        }
    } catch (Exception $e) {
        error_log("Error getting project file details: ".$e -> getMessage());
        return null;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting project file details: ".$pdoE -> getMessage());
        return null;
    }
}

function getWorkspaceInvitationDetails($pdo, $invitationId, $detail) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `workspaceinvitation` WHERE `invitationId` = :invitationId;');
        $stmt -> execute([':invitationId' => $invitationId]);
        $result = $stmt -> fetch();

        if ($result) {
            if ($detail == 'workspaceId') {
                return $result['workspaceId'];
            } elseif ($detail == 'senderId') {
                return $result['senderId'];
            } elseif ($detail == 'receiverId') {
                return $result['receiverId'];
            } elseif ($detail == 'sendDateTime') {
                return $result['sendDateTime'];
            } elseif ($detail == 'invitationStatus') {
                return $result['invitationStatus'];
            } else {
                throw new Exception("Invalid Workspace Invitation table column");
            }
        } else {
            throw new Exception("Result not found: ".$detail);
        }
    } catch (Exception $e) {
        error_log("Error getting workspace invitation details: ".$e -> getMessage());
        return null;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting workspace invitation details: ".$pdoE -> getMessage());
        return null;
    }
}

// Insert new table row in database
function insertNewWorkspace($pdo, $ownerId, $workspaceId, $workspaceName) {
    try {
        // $workspaceId = generateId('workspace');
        $stmt = $pdo -> prepare('INSERT INTO `workspace`(`workspaceId`, `workspaceName`, `ownerId`) VALUES
                                (:workspaceId, :workspaceName, :ownerId);');
        $stmt -> execute([':workspaceId' => $workspaceId, ':workspaceName' => $workspaceName, ':ownerId' => $ownerId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new workspace: ".$pdoE -> getMessage());
        return false;
    }
}

function insertNewWorkspaceMember($pdo, $userId, $workspaceId) {
    try {
        $stmt1 = $pdo -> prepare('SELECT * FROM `workspacemember` WHERE
                                `workspaceId` = :workspaceId and `memberId` = :memberId;');
        $stmt1 -> execute([':workspaceId' => $workspaceId, ':memberId' => $userId]);
        $result = $stmt1 -> fetch();
        error_log("SQL Result: " . print_r($result, true)); // <-- Debugging log
        if ($result) {
            return 'joined';
        }
        // $workspaceId = generateId('workspace');
        $stmt2 = $pdo -> prepare('INSERT INTO `workspacemember`(`workspaceId`, `memberId`) VALUES
                                (:workspaceId, :memberId)');
        $stmt2 -> execute([':workspaceId' => $workspaceId, ':memberId' => $userId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new workspace: ".$pdoE -> getMessage());
        return false;
    }
}

function insertNewProject($pdo, $workspaceId, $projectId, $projectName) {
    try {
        $stmt = $pdo -> prepare("INSERT INTO `project`(`projectId`, `projectName`, `workspaceId`, `projectStatus`) VALUE
                                (:projectId, :projectName, :workspaceId, 'pending');");
        $stmt -> execute([':projectId' => $projectId, ':projectName' => $projectName, ':workspaceId' => $workspaceId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new project: ".$pdoE -> getMessage());
        return false;
    }
}

function insertNewProjectTask($pdo, $projectId, $projectTaskId, $projectTaskName) {
    try {
        $stmt = $pdo -> prepare('INSERT INTO `projecttask`(`projectTaskId`, `projectTaskName`, `projectId`) VALUE
                                (:projectTaskId, :projectTaskName, :projectId);');
        $stmt -> execute([':projectTaskId' => $projectTaskId, ':projectTaskName' => $projectTaskName, ':projectId' => $projectId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new project task: ".$pdoE -> getMessage());
        return false;
    }
    }

function insertNewProjectSubTask($pdo, $projectTaskId, $projectSubTaskId, $projectSubTaskName) {
    try {
        $stmt = $pdo -> prepare('INSERT INTO `projectsubtask`(`projectSubTaskId`, `projectSubTaskName`, `projectTaskId`) VALUE
                                (:projectSubTaskId, :projectSubTaskName, :projectTaskId);');
        $stmt -> execute([':projectSubTaskId' => $projectSubTaskId, ':projectSubTaskName' => $projectSubTaskName, ':projectTaskId' => $projectTaskId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new project sub task: ".$pdoE -> getMessage());
        return false;
    }
}

function insertNewProjectFile($pdo, $userId, $projectId, $fileName, $fileType, $fileSize, $fileData) {
    try {
        $stmt = $pdo -> prepare('INSERT INTO `projectfiles`(`fileName`, `fileType`, `fileSize`, `fileData`, `projectId`, `userId`) VALUES
                                (:fileName, :fileType, :fileSize, :fileData, :projectId, :userId);');
        $stmt -> execute([':fileName' => $fileName, ':fileType' => $fileType, ':fileSize' => $fileSize, ':fileData' => $fileData, ':projectId' => $projectId, ':userId' => $userId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new project file: ".$pdoE -> getMessage());
        return false;
    }
}

// Insert Workspace Invitation
function insertWorkspaceInvitation($pdo, $workspaceId, $senderId, $receivedId) {
    try {
        $stmt = $pdo -> prepare('INSERT INTO `workspaceinvitation`(`workspaceId`, `senderId`, `receiverId`) VALUES
                                (:workspaceId, :senderId,:receiverId);');
        $stmt -> execute([':workspaceId' => $workspaceId, ':senderId' => $senderId, ':receiverId' => $receivedId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new workspace invitation: ".$pdoE -> getMessage());
        return false;
    }
}

// Update table row data in Database
function updateWorkspace($pdo, $column, $columnData, $workspaceId) {
    try {
        $columns = ['workspaceName', 'workspaceDescription'];
        if (!in_array($column, $columns)) {
            throw new Exception("Invalid Workspace table column");
        }
        if ($column == 'workspaceName') {
            $stmt = $pdo -> prepare('UPDATE `workspace`
                                    SET `workspaceName` = :workspaceName
                                    WHERE `workspace`.`workspaceId` = :workspaceId');
            $stmt -> execute([':workspaceName' => $columnData, ':workspaceId' => $workspaceId]);
        } elseif ($column == 'workspaceDescription') {
            $stmt = $pdo -> prepare('UPDATE `workspace`
                                    SET `workspaceDescription` = :description
                                    WHERE `workspace`.`workspaceId` = :workspaceId');
            $stmt -> execute([':description' => $columnData, ':workspaceId' => $workspaceId]);
        }
        return true;
    } catch (Exception $e) {
        error_log("Error updating workspace: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error updating workspace: ".$pdoE -> getMessage());
        return false;
    }
}

function updateProject($pdo, $column, $columnData, $projectId) {
    try {
        $columns = ['projectName', 'projectDeadline', 'projectStart', 'projectEnd', 'projectStatus', 'projectDescription'];
        if (!in_array($column, $columns)) {
            throw new Exception("Invalid Project table column");
        }
        if ($column == 'projectName') {
            $stmt = $pdo -> prepare('UPDATE `project`
                                    SET `projectName` = :projectName
                                    WHERE `project`.`projectId` = :projectId');
            $stmt -> execute([':projectName' => $columnData, ':projectId' => $projectId]);
        } elseif ($column == 'projectDeadline') {
            $stmt = $pdo -> prepare('UPDATE `project`
                                    SET `projectDeadline` = :projectDeadline
                                    WHERE `project`.`projectId` = :projectId');
            $stmt -> execute([':projectDeadline' => $columnData, ':projectId' => $projectId]);
        } elseif ($column == 'projectStart') {
            $stmt = $pdo -> prepare('UPDATE `project`
                                    SET `projectStart` = :projectStart
                                    WHERE `project`.`projectId` = :projectId');
            $stmt -> execute([':projectStart' => $columnData, ':projectId' => $projectId]);
        } elseif ($column == 'projectEnd') {
            $stmt = $pdo -> prepare('UPDATE `project`
                                    SET `projectEnd` = :projectEnd
                                    WHERE `project`.`projectId` = :projectId');
            $stmt -> execute([':projectEnd' => $columnData, ':projectId' => $projectId]);
        } elseif ($column == 'projectStatus') {
            $stmt = $pdo -> prepare('UPDATE `project`
                                    SET `projectStatus` = :projectStatus
                                    WHERE `project`.`projectId` = :projectId');
            $stmt -> execute([':projectStatus' => $columnData, ':projectId' => $projectId]);
        } elseif ($column == 'projectDescription') {
            $stmt = $pdo -> prepare('UPDATE `project`
                                    SET `projectDescription` = :projectDescription
                                    WHERE `project`.`projectId` = :projectId');
            $stmt -> execute([':projectDescription' => $columnData, ':projectId' => $projectId]);
        }
        return true;
    } catch (Exception $e) {
        error_log("Error updating project: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error updating project: ".$pdoE -> getMessage());
        return false;
    }
}

function updateProjectTask($pdo, $column, $columnData, $projectTaskId) {
    try {
        $columns = ['projectTaskName'];
        if (!in_array($column, $columns)) {
            throw new Exception("Invalid Workspace table column");
        }
        if ($column == 'projectTaskName') {
            $stmt = $pdo -> prepare('UPDATE `projecttask`
                                    SET `projectTaskName` = :projectTaskName
                                    WHERE `projecttask`.`projectTaskId` = :projectTaskId');
            $stmt -> execute([':projectTaskName' => $columnData, ':projectTaskId' => $projectTaskId]);
        }
        return true;
    } catch (Exception $e) {
        error_log("Error updating project task: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error updating project task: ".$pdoE -> getMessage());
        return false;
    }
}

function updateProjectSubTask($pdo, $column, $columnData, $projectSubTaskId) {
    try {
        $columns = ['projectSubTaskName', 'assignedMemberId', 'projectSubTaskAssignedDate', 'projectSubTaskPriority', 'projectSubTaskEstimate', 'projectSubTaskDueDate', 'projectSubTaskStart', 'projectSubTaskEnd', 'projectSubTaskStatus'];
        if (!in_array($column, $columns)) {
            throw new Exception("Invalid Project Sub Task table column");
        }
        if ($column == 'projectSubTaskName') {
            $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                    SET `projectSubTaskName` = :projectSubTaskName
                                    WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
            $stmt -> execute([':projectSubTaskName' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
        } elseif ($column == 'assignedMemberId') {
            $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                    SET `assignedMemberId` = :assignedMemberId
                                    WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
            $stmt -> execute([':assignedMemberId' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
        } elseif ($column == 'projectSubTaskAssignedDate') {
            if ($column !== '') {
                $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                        SET `projectSubTaskAssignedDate` = :projectSubTaskAssignedDate
                                        WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
                $stmt -> execute([':projectSubTaskAssignedDate' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
            } else {
                $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                        SET `projectSubTaskAssignedDate` = NULL
                                        WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
                $stmt -> execute([':projectSubTaskId' => $projectSubTaskId]);
            }
        } elseif ($column == 'projectSubTaskPriority') {
            $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                    SET `projectSubTaskPriority` = :projectSubTaskPriority
                                    WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
            $stmt -> execute([':projectSubTaskPriority' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
        } elseif ($column == 'projectSubTaskEstimate') {
            $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                    SET `projectSubTaskEstimate` = :projectSubTaskEstimate
                                    WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
            $stmt -> execute([':projectSubTaskEstimate' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
        } elseif ($column == 'projectSubTaskDueDate') {
            if ($columnData !== '') {
                $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                        SET `projectSubTaskDueDate` = :projectSubTaskDueDate
                                        WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
                $stmt -> execute([':projectSubTaskDueDate' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
            } else {
                $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                        SET `projectSubTaskDueDate` = NULL
                                        WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
                $stmt -> execute([':projectSubTaskId' => $projectSubTaskId]);
            }
        } elseif ($column == 'projectSubTaskStart') {
            $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                    SET `projectSubTaskStart` = :projectSubTaskStart
                                    WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
            $stmt -> execute([':projectSubTaskStart' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
        } elseif ($column == 'projectSubTaskEnd') {
            if ($columnData == null) {
                $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                    SET `projectSubTaskEnd` = NULL
                                    WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
                $stmt -> execute([':projectSubTaskId' => $projectSubTaskId]);
            } else {
                $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                        SET `projectSubTaskEnd` = :projectSubTaskEnd
                                        WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
                $stmt -> execute([':projectSubTaskEnd' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
            }
        } elseif ($column == 'projectSubTaskStatus') {
            $stmt = $pdo -> prepare('UPDATE `projectsubtask`
                                    SET `projectSubTaskStatus` = :projectSubTaskStatus
                                    WHERE `projectsubtask`.`projectSubTaskId` = :projectSubTaskId');
            $stmt -> execute([':projectSubTaskStatus' => $columnData, ':projectSubTaskId' => $projectSubTaskId]);
        }
        return true;
    } catch (Exception $e) {
        error_log("Error updating project sub task: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error updating project sub task: ".$pdoE -> getMessage());
        return false;
    }
}

// Delete table row in database
function deleteWorkspace($pdo, $workspaceId) {
    try {
        $stmt = $pdo -> prepare('DELETE FROM `workspace` WHERE `workspaceId` = :workspaceId;');
        $stmt -> execute([':workspaceId' => $workspaceId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting workspace: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error deleting workspace: ".$pdoE -> getMessage());
        return false;
    }
}

function deleteProject($pdo, $projectId) {
    try {
        $stmt = $pdo -> prepare('DELETE FROM `project` WHERE `projectId` = :projectId;');
        $stmt -> execute([':projectId' => $projectId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting project: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error deleting project: ".$pdoE -> getMessage());
        return false;
    }
}

function deleteProjectTask($pdo, $projectTaskId) {
    try {
        $stmt = $pdo -> prepare('DELETE FROM `projecttask` WHERE `projectTaskId` = :projectTaskId;');
        $stmt -> execute([':projectTaskId' => $projectTaskId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting project task: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error deleting project task: ".$pdoE -> getMessage());
        return false;
    }
}

function deleteProjectSubTask($pdo, $projectSubTaskId) {
    try {
        $stmt = $pdo -> prepare('DELETE FROM `projectsubtask` WHERE `projectSubTaskId` = :projectSubTaskId;');
        $stmt -> execute([':projectSubTaskId' => $projectSubTaskId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting project sub task: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error deleting project sub task: ".$pdoE -> getMessage());
        return false;
    }
}

function deleteProjectFile($pdo, $fileId, $projectId) {
    try {
        $stmt = $pdo -> prepare('DELETE FROM `projectfiles` WHERE `fileId` = :fileId AND `projectId` = :projectId;');
        $stmt -> execute([':fileId' => $fileId, ':projectId' => $projectId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting project file: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error deleting project file: ".$pdoE -> getMessage());
        return false;
    }
}
?>