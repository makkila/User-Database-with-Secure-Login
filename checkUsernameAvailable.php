<?php
	$servername = "localhost";
	$DBusername = "root";
	$DBpassword = "password";
	$db = new PDO("mysql:host=$servername;dbname=secure_login", $DBusername, $DBpassword);
	// Set PDO error mode to exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Prepare statement to select username column and fetch contents
	$takenUsernames = $db->prepare("SELECT username FROM users WHERE username=?");
	$takenUsernames->execute(array($_POST["username"]));
	
	// Check for taken username
	if ($takenUsernames->fetch()) {
    die('<li class="error">That username is already taken</li>');
  } else {
		die("");
	}
?>