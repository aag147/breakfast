<?php
// Defines database specifications
define('DB_SERVER', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASSWORD', '123456');
define('DB_NAME', 'breakfast');


try{ 
	$conn = new PDO("mysql:host=".DB_SERVER.";port=3306;dbname=".DB_NAME, DB_USER, DB_PASSWORD);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
	
	
	/*** Create missing tables ***/
	// Projects
	$createProjectsTable = $conn->prepare("
			CREATE TABLE IF NOT EXISTS `breakfast_projects` (
			  `project_id` int(11) NOT NULL AUTO_INCREMENT,
			  `project_name` varchar(100) CHARACTER SET utf8 NOT NULL,
			  `project_password` varchar(100) CHARACTER SET utf8 NOT NULL,
			  `project_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `project_monday` int(11) NOT NULL,
			  `project_tuesday` int(11) NOT NULL,
			  `project_wednesday` int(11) NOT NULL,
			  `project_thursday` int(11) NOT NULL,
			  `project_friday` int(11) NOT NULL,
			  `project_saturday` int(11) NOT NULL,
			  `project_sunday` int(11) NOT NULL,
			  PRIMARY KEY (`project_id`),
			  UNIQUE KEY (`project_name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci");
	$createProjectsTable->execute();	
	// Participants
	$createParticipantsTable = $conn->prepare("
			CREATE TABLE IF NOT EXISTS `breakfast_participants` (
			  `participant_id` int(11) NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `participant_name` varchar(100) CHARACTER SET utf8 NOT NULL,
			  `participant_email` varchar(100) CHARACTER SET utf8 NOT NULL,
			  `participant_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `participant_lastTime` date NOT NULL,
			  `participant_attendance_count` int(11) NOT NULL,
			  `participant_chef_count` int(11) NOT NULL,
			  `participant_asleep` int(11) NOT NULL,
			  PRIMARY KEY (`participant_id`),
			  UNIQUE KEY (`project_id`,`participant_email`),
			  FOREIGN KEY (`project_id`) REFERENCES breakfast_projects(`project_id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci");
	$createParticipantsTable->execute();
	// Products
	$createProductsTable = $conn->prepare("
			CREATE TABLE IF NOT EXISTS `breakfast_products` (
			  `product_id` int(11) NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `product_name` varchar(100) CHARACTER SET utf8 NOT NULL,
			  `product_status` int(11) NOT NULL,
			  `product_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`product_id`),
			  UNIQUE KEY (`project_id`,`product_name`),
			  FOREIGN KEY (`project_id`) REFERENCES breakfast_projects(`project_id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci");
	$createProductsTable->execute();
	// Breakfasts
	$createBreakfastsTable = $conn->prepare("
			CREATE TABLE IF NOT EXISTS `breakfast_breakfasts` (
			  `breakfast_id` int(11) NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `breakfast_date` date NOT NULL,
			  `breakfast_weekday` varchar(100) CHARACTER SET utf8 NOT NULL,
			  `breakfast_chef` int(11) NOT NULL,
			  `breakfast_done` int(11) NOT NULL,
			  `breakfast_notified` int(11) NOT NULL,
			  `breakfast_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `breakfast_asleep` int(11) NOT NULL,
			  PRIMARY KEY (`breakfast_id`),
			  KEY (`breakfast_chef`),
			  UNIQUE KEY (`project_id`,`breakfast_date`),
			  FOREIGN KEY (`project_id`) REFERENCES breakfast_projects(`project_id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci");
	$createBreakfastsTable->execute();
	// Registrations
	$createRegistrationsTable = $conn->prepare("
			CREATE TABLE IF NOT EXISTS `breakfast_registrations` (
			  `registration_id` int(11) NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `breakfast_id` int(11) NOT NULL,
			  `participant_id` int(11) NOT NULL,
			  `participant_attending` int(11) NOT NULL,
			  `registration_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`registration_id`),
			  FOREIGN KEY (`project_id`) REFERENCES breakfast_projects(`project_id`) ON DELETE CASCADE,
			  FOREIGN KEY (`breakfast_id`) REFERENCES breakfast_breakfasts(`breakfast_id`) ON DELETE CASCADE,
			  FOREIGN KEY (`participant_id`) REFERENCES breakfast_participants(`participant_id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci");
	$createRegistrationsTable->execute();
	// Sessions
	$createProjectsSessionsTable = $conn->prepare("
			CREATE TABLE IF NOT EXISTS `breakfast_projects_sessions` (
			  `session_id` int(11) NOT NULL AUTO_INCREMENT,
			  `project_id` int(11) NOT NULL,
			  `session_hash` varchar(100) CHARACTER SET utf8 NOT NULL,
			  `session_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`session_id`),
			  FOREIGN KEY (`project_id`) REFERENCES breakfast_projects(`project_id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci");
	$createProjectsSessionsTable->execute();
	
	$conn = null;
} catch(PDOException $e) {
	echo 'ERROR: ' . $e->getMessage();
}
?>