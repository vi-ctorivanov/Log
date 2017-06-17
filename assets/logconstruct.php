<?php
include 'assets/logcredentials.php';
include 'assets/loginterfaces.php';

//creates connection to database
function connect() {
	global $servername;
	global $username;
	global $password;
	global $database;
	$conn = new mysqli($servername, $username, $password, $database);
	return $conn;
}

//returns single number through request query ($q = query, $e = select result)
function getnum($q, $e) {
	$conn = connect();
	$r = "";
	$result = $conn->query($q);
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$r = $row[$e];
	}
	$conn->close();
	return $r;
}

//checks if given l ($l) is project or task
function checkType($l) {
	$conn = connect();
	//true = task / false = project
	$type = null;
	$result = $conn->query('select * from task where name = '."'".$l."'".';');

	if ($result->num_rows > 0) {
		$type = true;
	} else $type = false;

	$conn->close();
	return $type;
}

//check under what division activity is according to string ($s)
function checkSector($s) {
	switch ($s) {
		case "Design":
		case "Development":
			return "#02f2aa";
			break;

		case "Graphic":
		case "Illustration":
		case "3D":
		case "Modeling":
		case "Filming":
		case "Photography":
		case "Editing":
			return "#f91364";
			break;

		case "Audio":
		case "Acoustic":
		case "Recording":
		case "Performance":
			return "#005FDB";
			break;

		case "Writing":
		case "Linguistics":
			return "#592FB6";
			break;

		case "Research":
		case "Maintenance":
		case "Event":
			return "#F0F0F0";
			break;
	}
	return null;
}

//logic and pageflow for log layout
function loadlog() {
	global $l;
	if ($l == 'home') {
		$query = '';
		home();
	}
	else if ($l == 'tasks') {
		$query = 'select task.name as title, sum(log.time) as hours, count(*) as logs from log left join task on task.id = log.task_id group by title order by hours desc;';
		measures($query, number_format(getnum("select sum(time) as num_hours from log;", "num_hours"), 0));
	} else if ($l == 'projects') {
		$query = 'select project.name as title, sum(log.time) as hours, count(*) as logs from log left join project on project.id = log.project_id group by title order by hours desc;';
		measures($query, number_format(getnum("select sum(time) as num_hours from log;", "num_hours"), 0));
	} else if ($l == 'logs') {
		$query = 'select log.date, log.time, project.name as project, task.name as task, log.details from log left join project on project.id = log.project_id left join task on task.id = log.task_id order by log.id asc;';
		loglist($query);
	} else {
		if (checkType($l)) spec($l, 'task');
		else spec($l, 'project');
	}
}
?>