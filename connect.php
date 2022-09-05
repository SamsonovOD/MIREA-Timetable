<?php
	$conn = new mysqli("localhost", "root", "", "schedule");
	if ($conn->connect_error){die("Connection failed: " . $conn->connect_error);}

	$conn->query("SET NAMES utf8");
?>