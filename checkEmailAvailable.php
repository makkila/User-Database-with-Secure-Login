<?php
	$servername = "localhost";
	$DBusername = "root";
	$DBpassword = "password";
	$db = new PDO("mysql:host=$servername;dbname=secure_login", $DBusername, $DBpassword);
	// Set PDO error mode to exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Prepare statement to select email column and fetch contents
	$existingEmails = $db->prepare("SELECT email FROM users WHERE email=?");
	$existingEmails->execute(array($_POST["email"]));
	
	// Check for taken email
	if ($existingEmails->fetch()) {
    die('<li class="error">An account already exists for that email</li>');
	} else {
		die("");
	}
?>