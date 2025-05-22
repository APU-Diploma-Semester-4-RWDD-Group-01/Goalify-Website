-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 23, 2025 at 08:44 AM
-- Server version: 8.0.40
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `goalify`
--

-- --------------------------------------------------------

--
-- Table structure for table `action`
--

DROP TABLE IF EXISTS `action`;
CREATE TABLE IF NOT EXISTS `action` (
  `actionId` varchar(10) NOT NULL,
  `actionType` varchar(50) NOT NULL,
  PRIMARY KEY (`actionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `action`
--

INSERT INTO `action` (`actionId`, `actionType`) VALUES
('A001', 'User Logged In'),
('A002', 'User Logged Out'),
('A003', 'Uploaded Profile Picture'),
('A004', 'Edited Profile Name'),
('A005', 'Edited Profile Email'),
('A006', 'Changed Password'),
('A007', 'Added a Short-Term Goal'),
('A008', 'Added a Long-Term Goal'),
('A009', 'Completed a Goal'),
('A010', 'Started Focus Session'),
('A011', 'Completed Focus Session'),
('A012', 'Added Task to Timeline'),
('A013', 'Completed a Task'),
('A014', 'Created a Workspace'),
('A015', 'Edited Workspace Description'),
('A016', 'Invited Member to Workspace'),
('A017', 'Accepted Workspace Invitation'),
('A018', 'Rejected Workspace Invitation'),
('A019', 'User Joined a Workspace'),
('A020', 'Created a Project'),
('A021', 'Started a Project'),
('A022', 'Deleted a Project'),
('A023', 'Edited Project Description'),
('A024', 'Set Project Deadline'),
('A025', 'Ended a Project'),
('A026', 'Added Project Task'),
('A027', 'Deleted Project Task'),
('A028', 'Deleted Project Sub-Task'),
('A029', 'Edited Sub-Task Name'),
('A030', 'Updated Sub-Task Priority'),
('A031', 'Updated Sub-Task Progress'),
('A032', 'Set Sub-Task Assigned Date'),
('A033', 'Set Sub-Task Due Date'),
('A034', 'Updated Sub-Task Status'),
('A035', 'Uploaded a File to Project'),
('A036', 'Downloaded a File from Project'),
('A037', 'Deleted a File from Project'),
('A038', 'Set Task Deadline'),
('A039', 'Add To-Do Task'),
('A040', 'Edited Workspace Name'),
('A041', 'Assign Sub Task to Member'),
('A042', 'Set Estimated Sub-Task Completion Time'),
('A043', 'Edited Project Task Name'),
('A044', 'Added Project Sub-Task'),
('A045', 'Edited Project Name'),
('A046', 'Deleted a Goal'),
('A047', 'Set Task Schedule Time');

-- --------------------------------------------------------

--
-- Table structure for table `activitylog`
--

DROP TABLE IF EXISTS `activitylog`;
CREATE TABLE IF NOT EXISTS `activitylog` (
  `activityLogId` int NOT NULL AUTO_INCREMENT,
  `userId` varchar(10) NOT NULL,
  `actionId` varchar(10) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` varchar(800) NOT NULL,
  `ipAddress` varchar(20) NOT NULL,
  `deviceBrowser` varchar(50) NOT NULL,
  PRIMARY KEY (`activityLogId`),
  KEY `actionId` (`actionId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activitylog`
--

INSERT INTO `activitylog` (`activityLogId`, `userId`, `actionId`, `timestamp`, `details`, `ipAddress`, `deviceBrowser`) VALUES
(2, '#USR47310', 'A001', '2025-03-23 08:35:26', 'Successfully logged in !', '192.168.237.1', 'Chrome on Windows 10'),
(3, '#USR47310', 'A003', '2025-03-23 08:36:57', 'Updated profile image', '192.168.237.1', 'Chrome on Windows 10'),
(4, '#USR47310', 'A002', '2025-03-23 08:37:04', 'Successfully logged out !', '192.168.237.1', 'Chrome on Windows 10'),
(5, '#USRe44b8', 'A001', '2025-03-23 08:37:26', 'Successfully logged in !', '192.168.237.1', 'Chrome on Windows 10'),
(6, '#USRe44b8', 'A003', '2025-03-23 08:37:37', 'Updated profile image', '192.168.237.1', 'Chrome on Windows 10'),
(7, '#USRe44b8', 'A002', '2025-03-23 08:37:44', 'Successfully logged out !', '192.168.237.1', 'Chrome on Windows 10'),
(8, '#USR47310', 'A001', '2025-03-23 08:39:16', 'Successfully logged in !', '192.168.237.1', 'Chrome on Windows 10');

-- --------------------------------------------------------

--
-- Table structure for table `focusrecord`
--

DROP TABLE IF EXISTS `focusrecord`;
CREATE TABLE IF NOT EXISTS `focusrecord` (
  `timeTrackingId` varchar(10) NOT NULL,
  `userId` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `taskId` varchar(10) NOT NULL,
  `startTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `endTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duration` int NOT NULL,
  `timeTrackingDate` date NOT NULL,
  PRIMARY KEY (`timeTrackingId`),
  KEY `fk_userId_user` (`userId`),
  KEY `fk_taskId_task` (`taskId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goal`
--

DROP TABLE IF EXISTS `goal`;
CREATE TABLE IF NOT EXISTS `goal` (
  `goalId` varchar(10) NOT NULL,
  `goalName` text NOT NULL,
  `goalType` enum('short-term','long-term') NOT NULL,
  `completionStatus` enum('incomplete','completed') NOT NULL,
  `createdDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` varchar(10) NOT NULL,
  `completedDateTime` timestamp NULL DEFAULT NULL,
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `goal`
--
DROP TRIGGER IF EXISTS `GoalStatusUpdate`;
DELIMITER $$
CREATE TRIGGER `GoalStatusUpdate` BEFORE UPDATE ON `goal` FOR EACH ROW IF IFNULL(NEW.completionStatus, '') <> IFNULL(OLD.completionStatus, '') THEN
	SET NEW.completedDateTime = NOW();
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `projectId` varchar(10) NOT NULL,
  `projectName` varchar(50) NOT NULL,
  `projectCreatedDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `projectDeadline` timestamp NULL DEFAULT NULL,
  `projectStart` timestamp NULL DEFAULT NULL,
  `projectEnd` timestamp NULL DEFAULT NULL,
  `projectStatus` enum('pending','in progress','completed','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `workspaceId` varchar(10) NOT NULL,
  `projectDescriptionUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `projectDescription` text,
  PRIMARY KEY (`projectId`),
  KEY `workspaceId` (`workspaceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `project`
--
DROP TRIGGER IF EXISTS `projectDescriptionUpdate`;
DELIMITER $$
CREATE TRIGGER `projectDescriptionUpdate` BEFORE UPDATE ON `project` FOR EACH ROW IF IFNULL(NEW.projectDescription, '') <> IFNULL(OLD.projectDescription, '') THEN
	SET NEW.projectDescriptionUpdate = NOW();
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `projectfiles`
--

DROP TABLE IF EXISTS `projectfiles`;
CREATE TABLE IF NOT EXISTS `projectfiles` (
  `fileId` int NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) NOT NULL,
  `fileType` varchar(100) NOT NULL,
  `fileSize` int NOT NULL,
  `fileData` longblob NOT NULL,
  `projectId` varchar(10) NOT NULL,
  `uploadDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` varchar(10) NOT NULL,
  PRIMARY KEY (`fileId`),
  KEY `projectId` (`projectId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectsubtask`
--

DROP TABLE IF EXISTS `projectsubtask`;
CREATE TABLE IF NOT EXISTS `projectsubtask` (
  `projectSubTaskId` varchar(10) NOT NULL,
  `projectSubTaskName` varchar(100) NOT NULL,
  `createdDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assignedMemberId` varchar(10) DEFAULT NULL,
  `projectSubTaskPriority` enum('high','medium','low','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `projectSubTaskEstimate` int DEFAULT NULL,
  `projectSubTaskAssignedDate` timestamp NULL DEFAULT NULL,
  `projectSubTaskDueDate` timestamp NULL DEFAULT NULL,
  `projectSubTaskStart` timestamp NULL DEFAULT NULL,
  `projectSubTaskEnd` timestamp NULL DEFAULT NULL,
  `projectSubTaskStatus` enum('pending','in progress','completed','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `projectTaskId` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`projectSubTaskId`),
  KEY `projectsubtask_ibfk_1` (`projectTaskId`),
  KEY `projectsubtask_ibfk_2` (`assignedMemberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projecttask`
--

DROP TABLE IF EXISTS `projecttask`;
CREATE TABLE IF NOT EXISTS `projecttask` (
  `projectTaskId` varchar(10) NOT NULL,
  `projectTaskName` varchar(100) NOT NULL,
  `createdDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `projectId` varchar(10) NOT NULL,
  PRIMARY KEY (`projectTaskId`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remembermetoken`
--

DROP TABLE IF EXISTS `remembermetoken`;
CREATE TABLE IF NOT EXISTS `remembermetoken` (
  `tokenId` binary(32) NOT NULL,
  `userid` varchar(10) NOT NULL,
  `createdDateTime` datetime NOT NULL,
  `expiryDateTime` datetime NOT NULL,
  PRIMARY KEY (`tokenId`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `task_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `task_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `task_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `category` enum('urgent','plan ahead','handle fast','on hold') NOT NULL,
  `complete_status` enum('past_date','doing','done') NOT NULL,
  `completed_date` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` varchar(10) NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timeline`
--

DROP TABLE IF EXISTS `timeline`;
CREATE TABLE IF NOT EXISTS `timeline` (
  `schedule_id` varchar(10) NOT NULL,
  `time_plan` time DEFAULT NULL,
  `plan_date` date DEFAULT NULL,
  `task_id` varchar(10) NOT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `userId` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `profile_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `joinedDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userId`, `name`, `email`, `password`, `profile_img`, `joinedDateTime`) VALUES
('#USR2346c', 'Ng Yvonne', 'ngyvonne@gmail.com', '$2y$10$f6Oe.EXgm3glsNrUyHsib.9eY7kfFlHxVPPqfZ5MnVhEXXvYawcMK', NULL, '2025-03-23 08:31:09'),
('#USR43cc7', 'Lum Han Xun', 'lumhanxun@gmail.com', '$2y$10$NpSoPHSe6kNXxhCC7K6qpOPA9SdDnKd8CpR7KICSwFNi94e44bH66', NULL, '2025-03-23 08:30:46'),
('#USR46faa', 'Paureen Tan Nie Nie', 'paureentannienie@gmail.com', '$2y$10$U7vzsIFCLrGxUH3azX9z4e5aftcAhqS2F0ecW1YM9l7LfQX0vMTfS', NULL, '2025-03-23 08:31:50'),
('#USR47310', 'Mr. Firdaus', 'mrfirdaus@gmail.com', '$2y$10$u3GtJFf3k4kl6jrKGdszheMCRD0yPyKOhfxXm4czX8aplk6s3IXtO', 'uploads/384.jpg', '2025-03-23 08:29:15'),
('#USR4f6c7', 'Lim Chee Xuan', 'limcheexuan@gmail.com', '$2y$10$YQVLlBDPqhhd23s5UbRaHuBUGYgfVVywP5s.wiY5xSIoDo8Rk2iK2', NULL, '2025-03-23 08:30:19'),
('#USR5664e', 'APU', 'apu@gmail.com', '$2y$10$gvb.qwMkHa542qPqsyifduNwRonrHCKWe3AM/bAvIUEBa.v6C3GSi', NULL, '2025-03-23 08:35:04'),
('#USR5edf5', 'Anonymous', 'anonymous@gmail.com', '$2y$10$bBUTtiMk.QNbWje/co40luGesJy9fOM2f1MOuCeO1eGFgDC1GBNXC', NULL, '2025-03-23 08:34:28'),
('#USR63a29', 'John Doe', 'johndoe@gmail.com', '$2y$10$TQXf1ztOtIEi1ePwWDa3eeVuu6yYGSOdOSZ/X1oqpWDJ0l2gIiQRC', NULL, '2025-03-23 08:32:42'),
('#USRa0c93', 'Doe Doe', 'doedoe@gmail.com', '$2y$10$XRCP87XGX5MBjWwc1bWprO0ZTo2zVZt3LWLW0/EbvhSKewCu6AEAe', NULL, '2025-03-23 08:34:00'),
('#USRa1351', 'Mary Doe', 'marydoe@gmail.com', '$2y$10$UZPVwmkymIWklmmhFxIRU.cLQ4LRNSFa.nmEifOER2FFtMmDh9qaS', NULL, '2025-03-23 08:33:36'),
('#USRdb7a0', 'Phang Shea Wen', 'phangsheawen@gmail.com', '$2y$10$nJQsClvmtv.Nvq0QXFu.iuhZAfJJF7KGE6tWEWKLIMJTc5hqufVLe', NULL, '2025-03-23 08:32:19'),
('#USRe44b8', 'Mr. Daniel', 'mrdaniel@gmail.com', '$2y$10$NS.eYCX1zPz8FHfCaEZ5JedujQGxw13W.Y.u9HpBV4IzccPcgEbYy', 'uploads/124.jpg', '2025-03-23 08:29:43'),
('#USReb598', 'Michelle', 'michelleSmiley@gmail.com', '$2y$10$b99R4nNqy0LA7SD/sMwjKOQHb8CcEGi8fTA3Q1wquVrUBwQW4DWEe', NULL, '2025-03-23 08:28:44');

-- --------------------------------------------------------

--
-- Table structure for table `workspace`
--

DROP TABLE IF EXISTS `workspace`;
CREATE TABLE IF NOT EXISTS `workspace` (
  `workspaceId` varchar(10) NOT NULL,
  `workspaceName` varchar(20) NOT NULL,
  `createdDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ownerId` varchar(10) NOT NULL,
  `workspaceDescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `descriptionUpdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`workspaceId`),
  KEY `ownerId` (`ownerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `workspace`
--
DROP TRIGGER IF EXISTS `descriptionUpdate`;
DELIMITER $$
CREATE TRIGGER `descriptionUpdate` BEFORE UPDATE ON `workspace` FOR EACH ROW IF IFNULL(NEW.workspaceDescription, '') <> IFNULL(OLD.workspaceDescription, '') THEN
	SET NEW.descriptionUpdate = NOW();
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `workspaceinvitation`
--

DROP TABLE IF EXISTS `workspaceinvitation`;
CREATE TABLE IF NOT EXISTS `workspaceinvitation` (
  `invitationId` int NOT NULL AUTO_INCREMENT,
  `workspaceId` varchar(10) NOT NULL,
  `senderId` varchar(10) NOT NULL,
  `receiverId` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sendDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `invitationStatus` enum('accept','reject') DEFAULT NULL,
  PRIMARY KEY (`invitationId`),
  KEY `workspaceId` (`workspaceId`),
  KEY `senderId` (`senderId`),
  KEY `receivedId` (`receiverId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workspacemember`
--

DROP TABLE IF EXISTS `workspacemember`;
CREATE TABLE IF NOT EXISTS `workspacemember` (
  `workspaceId` varchar(10) NOT NULL,
  `memberId` varchar(10) NOT NULL,
  `joinedDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`workspaceId`,`memberId`),
  KEY `memberId` (`memberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD CONSTRAINT `activitylog_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `activitylog_ibfk_2` FOREIGN KEY (`actionId`) REFERENCES `action` (`actionId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `focusrecord`
--
ALTER TABLE `focusrecord`
  ADD CONSTRAINT `focusrecord_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `task` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `focusrecord_ibfk_2` FOREIGN KEY (`taskId`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `goal`
--
ALTER TABLE `goal`
  ADD CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`workspaceId`) REFERENCES `workspace` (`workspaceId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `projectfiles`
--
ALTER TABLE `projectfiles`
  ADD CONSTRAINT `projectfiles_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `project` (`projectId`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `projectfiles_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `projectsubtask`
--
ALTER TABLE `projectsubtask`
  ADD CONSTRAINT `projectsubtask_ibfk_1` FOREIGN KEY (`projectTaskId`) REFERENCES `projecttask` (`projectTaskId`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `projectsubtask_ibfk_2` FOREIGN KEY (`assignedMemberId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `projecttask`
--
ALTER TABLE `projecttask`
  ADD CONSTRAINT `projecttask_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `project` (`projectId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `remembermetoken`
--
ALTER TABLE `remembermetoken`
  ADD CONSTRAINT `remembermetoken_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `timeline`
--
ALTER TABLE `timeline`
  ADD CONSTRAINT `timeline_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `workspace`
--
ALTER TABLE `workspace`
  ADD CONSTRAINT `workspace_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `workspaceinvitation`
--
ALTER TABLE `workspaceinvitation`
  ADD CONSTRAINT `workspaceinvitation_ibfk_1` FOREIGN KEY (`workspaceId`) REFERENCES `workspace` (`workspaceId`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `workspaceinvitation_ibfk_2` FOREIGN KEY (`senderId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `workspaceinvitation_ibfk_3` FOREIGN KEY (`receiverId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `workspacemember`
--
ALTER TABLE `workspacemember`
  ADD CONSTRAINT `workspacemember_ibfk_1` FOREIGN KEY (`workspaceId`) REFERENCES `workspace` (`workspaceId`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `workspacemember_ibfk_2` FOREIGN KEY (`memberId`) REFERENCES `user` (`userId`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
