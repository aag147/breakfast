<?php

// FIRST HEADER		
require('../headers/setup.php');


Header('Content-Type:text/html; charset=ISO-8859-1');
/*************** AJAX ***************/


try{ 
	$conn = new PDO("mysql:host=".DB_SERVER.";port=3306;dbname=".DB_NAME, DB_USER, DB_PASSWORD);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	


	$type = isset($_POST['type']) ? $_POST['type'] : '';
	$errmsg = array(0);
	switch ($type){
		case 'new':			
			// Variables from form
			$name = utf8_decode(isset($_POST['name']) ? $_POST['name'] : '');
			$email = utf8_decode(isset($_POST['email']) ? $_POST['email'] : '');
			$project_id = utf8_decode(isset($_POST['project_id']) ? $_POST['project_id'] : '');

			$check_name_db = $conn->prepare("SELECT COUNT(participant_id) as C FROM breakfast_participants WHERE participant_name = :name AND project_id = :project_id LIMIT 1");
			$check_name_db->bindParam(':name', $name);		
			$check_name_db->bindParam(':project_id', $project_id);		
			$check_name_db->execute();
			$check_name = $check_name_db->fetchColumn();

			/*** ERROR CHECKING ***/	
			// Empty inputs
			if (empty($name) OR empty($email) OR empty($project_id)){$errmsg[0] = -1; goto newError;}		
			// Double name
			if ($check_name > 0){$errmsg[0] = -2; goto newError;}	

			
			/*** INSERT ***/
			$new_participant = $conn->prepare("INSERT INTO breakfast_participants (participant_name, participant_email, project_id) VALUES (:name, :email, :project_id)");
			$new_participant->bindParam(':name', $name);
			$new_participant->bindParam(':email', $email);
			$new_participant->bindParam(':project_id', $project_id);
			$new_participant->execute();
			
			$participant_id = $conn->lastInsertId('breakfast_participants');
		
			$errmsg[0] = 1;
			newError:
			echo json_encode($errmsg);
			exit;
			
		case 'edit':
			// Variables from form
			$name = utf8_decode(isset($_POST['name']) ? $_POST['name'] : '');
			$email = utf8_decode(isset($_POST['email']) ? $_POST['email'] : '');
			$project_id = utf8_decode(isset($_POST['project_id']) ? $_POST['project_id'] : '');
			$participant_id = utf8_decode(isset($_POST['participant_id']) ? $_POST['participant_id'] : '');

			$check_name_db = $conn->prepare("SELECT COUNT(participant_id) as C FROM breakfast_participants WHERE participant_name = :name AND project_id = :project_id AND participant_id <> :participant_id LIMIT 1");
			$check_name_db->bindParam(':name', $name);		
			$check_name_db->bindParam(':project_id', $project_id);		
			$check_name_db->bindParam(':participant_id', $participant_id);		
			$check_name_db->execute();
			$check_name = $check_name_db->fetchColumn();

			/*** ERROR CHECKING ***/	
			// Empty inputs
			if (empty($name) OR empty($email) OR empty($project_id) OR empty($participant_id)){$errmsg[0] = -1; goto editError;}		
			// Double name
			if ($check_name > 0){$errmsg[0] = -2; goto editError;}	
	
			/*** UPDATE ***/
			$new_participant = $conn->prepare("UPDATE breakfast_participants SET participant_name = :name, participant_email = :email WHERE project_id = :project_id AND participant_id = :participant_id");
			$new_participant->bindParam(':name', $name);
			$new_participant->bindParam(':email', $email);
			$new_participant->bindParam(':project_id', $project_id);		
			$new_participant->bindParam(':participant_id', $participant_id);
			$new_participant->execute();
					
			$errmsg[0] = 1;
			editError:
			echo json_encode($errmsg);
			exit;
	}
	
	$conn = null;
} catch(PDOException $e) {
	echo 'ERROR: ' . $e->getMessage();
}
?>